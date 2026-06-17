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
 *   1. JS  →  POST /payment/create-order   → returns params + SHA-512 hash
 *   2. JS builds hidden form, auto-submits to PayU hosted page
 *   3. PayU POSTs browser to /payment/verify (surl / furl)
 *   4. verify() checks hash, marks order paid
 *   5. PayU also POSTs to /payment/webhook (server-to-server backup)
 *
 * .env required:
 *   PAYU_MERCHANT_KEY=xxxx
 *   PAYU_MERCHANT_SALT=xxxx
 *   PAYU_ENV=production        (anything else = sandbox / test.payu.in)
 */
class PaymentController extends Controller
{
    private function payuUrl(): string
    {
        // services.php stores the full base_url already
        $base = config('services.payu.base_url', 'https://test.payu.in');
        return rtrim($base, '/') . '/_payment';
    }

    /**
     * Forward hash (for payment REQUEST).
     * Formula: key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt
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
            '', '', '', '', '',          // udf6–udf10 (empty)
            config('services.payu.salt'),
        ]);
        return strtolower(hash('sha512', $str));
    }

    /**
     * Reverse hash (for verifying PayU RESPONSE).
     *
     * Official PayU formula:
     *   salt|status|additionalCharges|udf10|udf9|udf8|udf7|udf6|udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
     *
     * CRITICAL: PayU sometimes includes a non-empty 'additionalCharges' field
     * in the response POST. Using '' instead of the actual value causes hash mismatch.
     */
    private function verifyHash(array $p): bool
    {
        $str = implode('|', [
            config('services.payu.salt'),
            $p['status']             ?? '',
            $p['additionalCharges']  ?? '',   // ← must use actual value if present
            $p['udf10']              ?? '',
            $p['udf9']               ?? '',
            $p['udf8']               ?? '',
            $p['udf7']               ?? '',
            $p['udf6']               ?? '',
            $p['udf5']               ?? '',
            $p['udf4']               ?? '',
            $p['udf3']               ?? '',
            $p['udf2']               ?? '',
            $p['udf1']               ?? '',
            $p['email']              ?? '',
            $p['firstname']          ?? '',
            $p['productinfo']        ?? '',
            $p['amount']             ?? '',
            $p['txnid']              ?? '',
            $p['key']                ?? '',
        ]);
        return strtolower(hash('sha512', $str)) === strtolower($p['hash'] ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — JS calls this to get PayU form params + hash
    // POST /payment/create-order   (auth required)
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

        Log::info('PayU createOrder', ['order_id' => $order->id, 'txnid' => $txnid, 'amount' => $amount]);

        return response()->json([
            'payu_url' => $this->payuUrl(),
            'params'   => $params,
            'order_id' => $order->id,
            'amount'   => $amount,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — PayU redirects browser here after payment (surl & furl)
    // POST /payment/verify   (CSRF exempt — set in bootstrap/app.php)
    // ─────────────────────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $all = $request->all();

        Log::info('PayU verify received', [
            'txnid'              => $all['txnid']              ?? null,
            'status'             => $all['status']             ?? null,
            'mihpayid'           => $all['mihpayid']           ?? null,
            'additionalCharges'  => $all['additionalCharges']  ?? 'NOT_PRESENT',
            'udf1'               => $all['udf1']               ?? null,
        ]);

        // ── 1. Locate order ──────────────────────────────────────────────────
        $orderId = $all['udf1'] ?? null;
        $txnid   = $all['txnid'] ?? null;

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

        // ── 2. Idempotent — already paid ────────────────────────────────────
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Order #' . $order->id . ' is confirmed!');
        }

        // ── 3. Verify hash ───────────────────────────────────────────────────
        $hashValid = $this->verifyHash($all);
        $status    = strtolower($all['status'] ?? '');

        if (!$hashValid) {
            // Log full payload for debugging — do NOT expose to user
            Log::error('PayU verify: hash mismatch', [
                'order_id'           => $order->id,
                'txnid'              => $txnid,
                'status'             => $status,
                'additionalCharges'  => $all['additionalCharges'] ?? 'N/A',
                'received_hash'      => $all['hash'] ?? 'N/A',
                'all_keys'           => array_keys($all),
            ]);

            // Hash failed — do NOT mark as paid; send user to confirmation with error
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error',
                    'Payment verification failed for Order #' . $order->id .
                    '. If your amount was deducted, please contact us with reference: ' . $txnid);
        }

        // ── 4. Check PayU status ─────────────────────────────────────────────
        if ($status !== 'success') {
            Log::warning('PayU verify: non-success status', [
                'order_id' => $order->id,
                'status'   => $status,
            ]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error',
                    'Payment failed for Order #' . $order->id .
                    ' (status: ' . ucfirst($status) . '). No amount has been deducted. Please try again.');
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
            'order_id'  => $order->id,
            'mihpayid'  => $payuPaymentId,
        ]);

        // ── 6. Post-payment tasks ────────────────────────────────────────────
        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('PayU verify: post-payment task failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order #' . $order->id . ' is confirmed.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — PayU server-to-server webhook (async backup)
    // POST /payment/webhook   (CSRF exempt)
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $all = $request->all();

        Log::info('PayU webhook received', [
            'txnid'  => $all['txnid']  ?? null,
            'status' => $all['status'] ?? null,
        ]);

        if (!$this->verifyHash($all)) {
            Log::warning('PayU webhook: hash mismatch');
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
