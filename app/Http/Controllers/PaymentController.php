<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayU Payment Gateway Controller
 *
 * PayU uses a server-side form POST (redirect) model — no SDK required.
 * Flow:
 *   1. JS calls POST /payment/create-order  → returns PayU form params + hash
 *   2. JS auto-submits a hidden form to PayU's hosted page
 *   3. PayU redirects browser to /payment/verify (surl/furl)
 *   4. /payment/verify validates the hash and marks order paid
 *   5. PayU also POSTs to /payment/webhook for async confirmation
 *
 * Required .env keys:
 *   PAYU_MERCHANT_KEY=your_key
 *   PAYU_MERCHANT_SALT=your_salt
 *   PAYU_ENV=production   (or leave blank / 'sandbox' for test)
 */
class PaymentController extends Controller
{
    /**
     * Return the PayU base URL for the current environment.
     */
    private function payuBaseUrl(): string
    {
        return config('services.payu.base_url', 'https://test.payu.in');
    }

    /**
     * Compute the PayU SHA-512 hash for a payment request.
     *
     * Formula (PayU docs):
     *   key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt
     */
    private function computeHash(array $p): string
    {
        $salt = config('services.payu.salt');

        $hashStr = implode('|', [
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
            '', '', '', '', '',  // udf6–udf10 empty
            $salt,
        ]);

        return strtolower(hash('sha512', $hashStr));
    }

    /**
     * Verify the hash returned by PayU in the response.
     *
     * Reverse hash formula:
     *   salt|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
     */
    private function verifyResponseHash(array $p): bool
    {
        $salt = config('services.payu.salt');

        $hashStr = implode('|', [
            $salt,
            $p['status']    ?? '',
            '', '', '', '',     // additionalCharges and padding
            '', '',             // udf10, udf9
            $p['udf5']      ?? '',
            $p['udf4']      ?? '',
            $p['udf3']      ?? '',
            $p['udf2']      ?? '',
            $p['udf1']      ?? '',
            $p['email']     ?? '',
            $p['firstname'] ?? '',
            $p['productinfo'] ?? '',
            $p['amount']    ?? '',
            $p['txnid']     ?? '',
            $p['key']       ?? '',
        ]);

        return strtolower(hash('sha512', $hashStr)) === strtolower($p['hash'] ?? '');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 1. CREATE ORDER — returns PayU params + hash to JS
    // POST /payment/create-order  { order_id }
    // ──────────────────────────────────────────────────────────────────────────
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        $key  = config('services.payu.merchant_key');
        $salt = config('services.payu.salt');

        if (!$key || !$salt) {
            Log::error('PayU: credentials not set in .env (PAYU_MERCHANT_KEY / PAYU_MERCHANT_SALT)');
            return response()->json(['error' => 'Payment gateway not configured.'], 500);
        }

        $user    = auth()->user();
        $txnid   = 'txn_' . $order->id . '_' . time();
        $amount  = number_format((float) $order->total_price, 2, '.', '');

        $surl = route('payment.verify');   // success URL
        $furl = route('payment.verify');   // failure URL (same handler — we check status)

        $params = [
            'key'         => $key,
            'txnid'       => $txnid,
            'amount'      => $amount,
            'productinfo' => 'Order #' . $order->id,
            'firstname'   => $user->name  ?? 'Customer',
            'email'       => $user->email ?? '',
            'phone'       => $order->shipping_phone ?? '9999999999',
            'surl'        => $surl,
            'furl'        => $furl,
            'udf1'        => (string) $order->id,  // carry our order ID through PayU
            'udf2'        => '',
            'udf3'        => '',
            'udf4'        => '',
            'udf5'        => '',
        ];

        $params['hash'] = $this->computeHash($params);

        // Persist txnid so we can look up the order in the callback
        $order->update(['payu_txnid' => $txnid]);

        Log::info('PayU: order params created', [
            'order_id' => $order->id,
            'txnid'    => $txnid,
        ]);

        return response()->json([
            'payu_url'   => $this->payuBaseUrl() . '/_payment',
            'params'     => $params,
            'order_id'   => $order->id,
            'amount'     => $amount,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 2. VERIFY — PayU redirects browser here after payment (surl / furl)
    // POST /payment/verify  (PayU posts form data)
    // ──────────────────────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $all = $request->all();

        Log::info('PayU verify callback', [
            'txnid'  => $all['txnid']  ?? null,
            'status' => $all['status'] ?? null,
        ]);

        // Try to find order via udf1 (our order ID) or txnid stored on order
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
            return redirect()->route('home')->with('error', 'Order not found. Please contact support.');
        }

        // Already paid — idempotent
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Payment already confirmed for Order #' . $order->id . '.');
        }

        // Validate hash
        if (!$this->verifyResponseHash($all)) {
            Log::error('PayU verify: hash mismatch', ['order_id' => $order->id, 'txnid' => $txnid]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment verification failed (hash mismatch). Contact support with Order #' . $order->id . '.');
        }

        $status = strtolower($all['status'] ?? '');

        if ($status !== 'success') {
            Log::warning('PayU verify: payment not successful', [
                'order_id' => $order->id,
                'status'   => $status,
            ]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment not successful for Order #' . $order->id
                    . ' (status: ' . ($all['status'] ?? 'unknown') . '). If amount was deducted, contact support.');
        }

        $payuPaymentId = $all['mihpayid'] ?? ($all['payuMoneyId'] ?? $txnid);

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'payu_txnid'      => $txnid,
            'payu_payment_id' => $payuPaymentId,
        ]);

        Log::info('PayU verify: order paid', ['order_id' => $order->id, 'mihpayid' => $payuPaymentId]);

        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order = $order->fresh(['products', 'user']);
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order #' . $order->id . ' confirmed.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // 3. WEBHOOK — PayU server-to-server async confirmation (optional but good)
    // POST /payment/webhook  (CSRF-exempt)
    // ──────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $all = $request->all();

        Log::info('PayU webhook received', [
            'txnid'  => $all['txnid']  ?? null,
            'status' => $all['status'] ?? null,
        ]);

        if (!$this->verifyResponseHash($all)) {
            Log::warning('PayU webhook: hash mismatch');
            return response()->json(['error' => 'Invalid hash'], 401);
        }

        $status = strtolower($all['status'] ?? '');
        if ($status !== 'success') {
            return response()->json(['status' => 'ignored']);
        }

        $txnid  = $all['txnid'] ?? null;
        $udf1   = $all['udf1']  ?? null;

        $order = null;
        if ($udf1 && is_numeric($udf1)) {
            $order = Order::find((int) $udf1);
        }
        if (!$order && $txnid) {
            $order = Order::where('payu_txnid', $txnid)->first();
        }

        if (!$order) {
            Log::warning('PayU webhook: order not found', ['txnid' => $txnid, 'udf1' => $udf1]);
            return response()->json(['status' => 'order_not_found']);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['status' => 'already_paid']);
        }

        $payuPaymentId = $all['mihpayid'] ?? $txnid;

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'payu_txnid'      => $txnid,
            'payu_payment_id' => $payuPaymentId,
        ]);

        Log::info('PayU webhook: order paid', ['order_id' => $order->id]);

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
