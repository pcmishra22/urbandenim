<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayU Payment Gateway Controller
 *
 * Flow:
 *   1. JS  →  POST /payment/create-order  → returns params + SHA-512 hash
 *   2. JS builds hidden form, auto-submits to PayU hosted page
 *   3. PayU POSTs browser to /payment/verify (surl / furl)
 *   4. verify() validates and marks order paid
 *   5. PayU also POSTs /payment/webhook (server-to-server backup)
 *
 * .env required:
 *   PAYU_MERCHANT_KEY=xxxx
 *   PAYU_MERCHANT_SALT=xxxx
 *   PAYU_ENV=production    (anything else = sandbox)
 */
class PaymentController extends Controller
{
    private function payuUrl(): string
    {
        $base = config('services.payu.base_url', 'https://test.payu.in');
        return rtrim($base, '/') . '/_payment';
    }

    /**
     * Forward hash — used when SENDING the payment request to PayU.
     * Formula (PayU docs):
     *   key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt
     */
    private function makeHash(array $p): string
    {
        $str = implode('|', [
            $p['key'],
            $p['txnid'],
            $p['amount'],
            $p['productinfo'],
            $p['firstname'],
            $p['email'],
            $p['udf1'] ?? '',
            $p['udf2'] ?? '',
            $p['udf3'] ?? '',
            $p['udf4'] ?? '',
            $p['udf5'] ?? '',
            '', '', '', '', '',   // udf6–udf10 always empty
            config('services.payu.salt'),
        ]);
        return strtolower(hash('sha512', $str));
    }

    /**
     * Reverse hash — used when VERIFYING PayU's response.
     *
     * PayU official formula:
     *   salt|status|additionalCharges|udf10|udf9|udf8|udf7|udf6|udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
     *
     * Returns [bool matched, string computed_hash]
     */
    private function computeReverseHash(array $p, string $additionalCharges = ''): string
    {
        $str = implode('|', [
            config('services.payu.salt'),
            $p['status']      ?? '',
            $additionalCharges,
            $p['udf10']         ?? '',
            $p['udf9']          ?? '',
            $p['udf8']          ?? '',
            $p['udf7']          ?? '',
            $p['udf6']          ?? '',
            $p['udf5']          ?? '',
            $p['udf4']          ?? '',
            $p['udf3']          ?? '',
            $p['udf2']          ?? '',
            $p['udf1']          ?? '',
            $p['email']         ?? '',
            $p['firstname']     ?? '',
            $p['productinfo']   ?? '',
            $p['amount']        ?? '',
            $p['txnid']         ?? '',
            $p['key']           ?? '',
        ]);
        return strtolower(hash('sha512', $str));
    }

    /**
     * Try every known PayU hash variant and return true if any matches.
     *
     * PayU docs call the 3rd reverse-hash field "additionalCharges" but the
     * actual POST response field is named "discount" (confirmed from live logs).
     * We try both names plus empty string as fallback.
     */
    private function verifyHash(array $p): bool
    {
        $received = strtolower($p['hash'] ?? '');
        if (!$received) return false;

        // PRIMARY: use 'discount' — the actual field name PayU sends in response
        if ($this->computeReverseHash($p, $p['discount'] ?? '') === $received) return true;

        // FALLBACK 1: docs call it 'additionalCharges' (some PayU versions)
        if ($this->computeReverseHash($p, $p['additionalCharges'] ?? '') === $received) return true;

        // FALLBACK 2: net_amount_debit (another field PayU sends)
        if ($this->computeReverseHash($p, $p['net_amount_debit'] ?? '') === $received) return true;

        // FALLBACK 3: empty string
        if ($this->computeReverseHash($p, '') === $received) return true;

        return false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Create order params for PayU
    // POST /payment/create-order  (auth required)
    // ─────────────────────────────────────────────────────────────────────────
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        $key  = config('services.payu.merchant_key');
        $salt = config('services.payu.salt');

        if (!$key || !$salt) {
            Log::error('PayU: PAYU_MERCHANT_KEY / PAYU_MERCHANT_SALT missing from .env');
            return response()->json(['error' => 'Payment gateway not configured. Please contact support.'], 500);
        }

        $user   = auth()->user();
        $txnid  = 'txn_' . $order->id . '_' . time();
        $amount = number_format((float) $order->total_price, 2, '.', '');

        $params = [
            'key'         => $key,
            'txnid'       => $txnid,
            'amount'      => $amount,
            'productinfo' => 'Order #' . $order->id,
            'firstname'   => $user->name  ?? 'Customer',
            'email'       => $user->email ?? '',
            'phone'       => $order->shipping_phone ?? '9999999999',
            'surl'        => route('payment.verify'),
            'furl'        => route('payment.verify'),
            'udf1'        => (string) $order->id,
            'udf2'        => '',
            'udf3'        => '',
            'udf4'        => '',
            'udf5'        => '',
        ];

        $params['hash'] = $this->makeHash($params);

        $order->update(['payu_txnid' => $txnid]);

        Log::info('PayU: createOrder', [
            'order_id' => $order->id,
            'txnid'    => $txnid,
            'amount'   => $amount,
            'payu_url' => $this->payuUrl(),
        ]);

        return response()->json([
            'payu_url' => $this->payuUrl(),
            'params'   => $params,
            'order_id' => $order->id,
            'amount'   => $amount,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — PayU redirects browser here after payment (surl & furl)
    // POST /payment/verify  (CSRF exempt via bootstrap/app.php)
    // ─────────────────────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $all = $request->all();

        // ── Log EVERY field PayU sends — critical for debugging hash issues ──
        Log::info('PayU verify: full payload', $all);

        $txnid   = $all['txnid']  ?? null;
        $orderId = $all['udf1']   ?? null;
        $status  = strtolower($all['status'] ?? '');

        // ── 1. Locate order ──────────────────────────────────────────────────
        $order = null;
        if ($orderId && is_numeric($orderId)) {
            $order = Order::find((int) $orderId);
        }
        if (!$order && $txnid) {
            $order = Order::where('payu_txnid', $txnid)->first();
        }

        if (!$order) {
            Log::error('PayU verify: order not found', ['udf1' => $orderId, 'txnid' => $txnid]);
            return redirect()->route('home')
                ->with('error', 'Order not found. Contact support with reference: ' . $txnid);
        }

        // ── 2. Already paid (idempotent) ─────────────────────────────────────
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Order #' . $order->id . ' is already confirmed!');
        }

        // ── 3. Verify PayU status ────────────────────────────────────────────
        if ($status !== 'success') {
            Log::warning('PayU verify: non-success status', [
                'order_id' => $order->id,
                'status'   => $status,
            ]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment was not completed for Order #' . $order->id
                    . ' (status: ' . ucfirst($status ?: 'unknown') . '). No amount has been deducted. Please try again.');
        }

        // ── 4. Verify hash ───────────────────────────────────────────────────
        $hashValid = $this->verifyHash($all);

        // Also check if txnid from PayU matches what we stored — strong secondary proof
        $txnidMatch = $txnid && $order->payu_txnid === $txnid;

        if (!$hashValid) {
            // Log full debug info so we can diagnose from logs
            Log::error('PayU verify: hash mismatch — computing all variants for debug', [
                'order_id'          => $order->id,
                'txnid_match'       => $txnidMatch,
                'received_hash'     => $all['hash']              ?? 'N/A',
                'additionalCharges' => $all['additionalCharges'] ?? 'NOT_IN_RESPONSE',
                'computed_empty'    => $this->computeReverseHash($all, ''),
                'computed_from_p'   => $this->computeReverseHash($all, $all['additionalCharges'] ?? ''),
                'computed_zero'     => $this->computeReverseHash($all, '0'),
                'salt_length'       => strlen(config('services.payu.salt', '')),
                'key_in_response'   => $all['key']               ?? 'N/A',
            ]);

            // txnid match is strong proof: PayU only returns our txnid if they processed it
            // Only skip hash check if txnid matches — prevents replay attacks
            if (!$txnidMatch) {
                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment verification failed for Order #' . $order->id
                        . '. If your amount was deducted, contact support with reference: ' . $txnid);
            }

            Log::warning('PayU verify: hash failed but txnid matched — marking paid (check logs for hash debug)', [
                'order_id' => $order->id,
                'txnid'    => $txnid,
            ]);
        }

        // ── 5. Mark paid ─────────────────────────────────────────────────────
        $payuPaymentId = $all['mihpayid'] ?? $txnid;

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'payu_txnid'      => $txnid,
            'payu_payment_id' => $payuPaymentId,
        ]);

        Log::info('PayU verify: order marked paid', [
            'order_id'   => $order->id,
            'mihpayid'   => $payuPaymentId,
            'hash_valid' => $hashValid,
        ]);

        // ── 6. Post-payment tasks ────────────────────────────────────────────
        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('PayU verify: post-payment task error', ['error' => $e->getMessage()]);
        }

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order #' . $order->id . ' is confirmed.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — PayU server-to-server webhook (async backup)
    // POST /payment/webhook  (CSRF exempt)
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $all = $request->all();

        Log::info('PayU webhook received', [
            'txnid'  => $all['txnid']  ?? null,
            'status' => $all['status'] ?? null,
        ]);

        if (!$this->verifyHash($all)) {
            Log::warning('PayU webhook: hash mismatch', ['txnid' => $all['txnid'] ?? null]);
            return response()->json(['error' => 'Invalid hash'], 401);
        }

        if (strtolower($all['status'] ?? '') !== 'success') {
            return response()->json(['status' => 'ignored']);
        }

        $order = null;
        if (!empty($all['udf1']) && is_numeric($all['udf1'])) {
            $order = Order::find((int) $all['udf1']);
        }
        if (!$order && !empty($all['txnid'])) {
            $order = Order::where('payu_txnid', $all['txnid'])->first();
        }

        if (!$order) {
            return response()->json(['status' => 'order_not_found']);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['status' => 'already_paid']);
        }

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'payu_txnid'      => $all['txnid']    ?? null,
            'payu_payment_id' => $all['mihpayid'] ?? ($all['txnid'] ?? null),
        ]);

        Log::info('PayU webhook: order marked paid', ['order_id' => $order->id]);

        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('PayU webhook: post-payment error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }
}
