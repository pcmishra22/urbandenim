<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayU Payment Gateway Controller
 *
 * PayU uses a server-side form POST redirect — no SDK required.
 *
 * Flow:
 *   1. JS calls POST /payment/create-order  → returns params + SHA-512 hash
 *   2. JS auto-submits hidden form to PayU hosted page
 *   3. PayU redirects to /payment/verify (surl / furl)
 *   4. /payment/verify validates hash, marks order paid
 *   5. PayU also POSTs to /payment/webhook (async backup)
 *
 * Required .env:
 *   PAYU_MERCHANT_KEY=your_key
 *   PAYU_MERCHANT_SALT=your_salt
 *   PAYU_ENV=production        (or 'sandbox' for testing)
 */
class PaymentController extends Controller
{
    private function payuUrl(): string
    {
        $env = config('services.payu.env', 'sandbox');
        return $env === 'production'
            ? 'https://secure.payu.in/_payment'
            : 'https://test.payu.in/_payment';
    }

    /**
     * SHA-512 hash for payment request.
     * Formula: key|txnid|amount|productinfo|firstname|email|udf1–5||||||salt
     */
    private function makeHash(array $p): string
    {
        $str = implode('|', [
            $p['key'], $p['txnid'], $p['amount'], $p['productinfo'],
            $p['firstname'], $p['email'],
            $p['udf1'] ?? '', $p['udf2'] ?? '', $p['udf3'] ?? '',
            $p['udf4'] ?? '', $p['udf5'] ?? '',
            '', '', '', '', '',
            config('services.payu.salt'),
        ]);
        return strtolower(hash('sha512', $str));
    }

    /**
     * Verify response hash from PayU callback.
     * Reverse formula: salt|status||||||udf5–1|email|firstname|productinfo|amount|txnid|key
     */
    private function verifyHash(array $p): bool
    {
        $str = implode('|', [
            config('services.payu.salt'),
            $p['status']      ?? '',
            '', '', '', '', '', '',
            $p['udf5']        ?? '', $p['udf4'] ?? '', $p['udf3'] ?? '',
            $p['udf2']        ?? '', $p['udf1'] ?? '',
            $p['email']       ?? '',
            $p['firstname']   ?? '',
            $p['productinfo'] ?? '',
            $p['amount']      ?? '',
            $p['txnid']       ?? '',
            $p['key']         ?? '',
        ]);
        return strtolower(hash('sha512', $str)) === strtolower($p['hash'] ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1: JS calls this to get PayU form params + hash
    // POST /payment/create-order
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
            Log::error('PayU: PAYU_MERCHANT_KEY / PAYU_MERCHANT_SALT not set in .env');
            return response()->json(['error' => 'Payment gateway not configured.'], 500);
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
            'udf2'        => '', 'udf3'  => '', 'udf4' => '', 'udf5' => '',
        ];

        $params['hash'] = $this->makeHash($params);

        $order->update(['payu_txnid' => $txnid]);

        Log::info('PayU: createOrder', ['order_id' => $order->id, 'txnid' => $txnid]);

        return response()->json([
            'payu_url' => $this->payuUrl(),
            'params'   => $params,
            'order_id' => $order->id,
            'amount'   => $amount,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2: PayU redirects browser here after payment (surl / furl)
    // POST /payment/verify  — CSRF exempt
    // ─────────────────────────────────────────────────────────────────────────
    public function verify(Request $request)
    {
        $all = $request->all();

        Log::info('PayU verify', ['txnid' => $all['txnid'] ?? null, 'status' => $all['status'] ?? null]);

        // Locate order via udf1 (our order ID stored in PayU params)
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
                ->with('error', 'Order not found. Please contact support with TXN: ' . $txnid);
        }

        // Idempotent — already marked paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Your Order #' . $order->id . ' is confirmed!');
        }

        // Verify hash
        if (!$this->verifyHash($all)) {
            Log::error('PayU verify: hash mismatch', ['order_id' => $order->id]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment verification failed. If amount was deducted, contact support with Order #' . $order->id);
        }

        $status = strtolower($all['status'] ?? '');

        if ($status !== 'success') {
            Log::warning('PayU verify: payment not success', ['status' => $status, 'order_id' => $order->id]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment was not successful for Order #' . $order->id
                    . ' (status: ' . ($all['status'] ?? 'unknown') . '). If money was deducted, contact support.');
        }

        $payuPaymentId = $all['mihpayid'] ?? $txnid;

        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'processing',
            'payu_txnid'      => $txnid,
            'payu_payment_id' => $payuPaymentId,
        ]);

        Log::info('PayU verify: paid', ['order_id' => $order->id, 'mihpayid' => $payuPaymentId]);

        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order = $order->fresh(['products', 'user']);
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order #' . $order->id . ' confirmed.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3: PayU server-to-server webhook (async backup)
    // POST /payment/webhook  — CSRF exempt
    // ─────────────────────────────────────────────────────────────────────────
    public function webhook(Request $request)
    {
        $all = $request->all();

        Log::info('PayU webhook', ['txnid' => $all['txnid'] ?? null, 'status' => $all['status'] ?? null]);

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
            'payu_txnid'      => $all['txnid']     ?? null,
            'payu_payment_id' => $all['mihpayid']  ?? $all['txnid'] ?? null,
        ]);

        Log::info('PayU webhook: paid', ['order_id' => $order->id]);

        try {
            app(\App\Services\CartService::class)->clear();
            session()->forget(['coupon_code', 'coupon_discount']);
            $order = $order->fresh(['products', 'user']);
            app(CheckoutController::class)->sendOrderEmails($order);
        } catch (\Exception $e) {
            Log::error('PayU webhook post-payment error', ['error' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }
}
