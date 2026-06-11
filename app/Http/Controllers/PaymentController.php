<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayU Hosted Checkout (redirect) payment gateway controller.
 *
 * Flow:
 *   1. JS calls checkout.store-pending  → order created, payment_status = awaiting_payment
 *   2. JS calls payment.create-order    → PayU hash + POST fields returned
 *   3. JS auto-submits a hidden form to PayU hosted checkout URL
 *   4. PayU redirects browser to surl/furl (GET) OR posts server callback (POST)
 *   5. verify() validates PayU response hash and marks order paid
 *
 * CSRF note: /payment/verify is excluded from CSRF in bootstrap/app.php
 * because PayU POSTs back from an external server with no Laravel session token.
 * Security is enforced by SHA-512 hash verification instead.
 */
class PaymentController extends Controller
{
    /**
     * Create PayU Hosted Checkout payment redirect data for an already-saved pending order.
     */
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        $merchantKey = config('services.payu.merchant_key');
        $salt        = config('services.payu.salt');
        $baseUrl     = rtrim(config('services.payu.base_url'), '/');

        if (!$merchantKey || !$salt) {
            return response()->json(['error' => 'PayU credentials are not fully configured.'], 500);
        }

        $txnid       = 'order_' . $order->id . '_' . time();
        $amount      = number_format((float) $order->total_price, 2, '.', '');
        $productinfo = 'Order #' . $order->id;
        $firstname   = auth()->user()->name ?? '';
        $email       = auth()->user()->email ?? '';
        $phone       = $order->shipping_phone ?? '';

        // PayU request hash — 17 parts, 16 pipes:
        // key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt
        $hashString = implode('|', [
            $merchantKey,
            $txnid,
            $amount,
            $productinfo,
            $firstname,
            $email,
            $order->id,  // udf1
            '', '', '', '',  // udf2-udf5
            '', '', '', '', '',  // reserved fields
            $salt,
        ]);
        $hash = hash('sha512', $hashString);

        $order->update(['payu_txnid' => $txnid]);

        return response()->json([
            'endpoint'    => $baseUrl . '/_payment',
            'merchantKey' => $merchantKey,
            'txnid'       => $txnid,
            'order_id'    => $order->id,
            'amount'      => $amount,
            'productinfo' => $productinfo,
            'firstname'   => $firstname,
            'email'       => $email,
            'phone'       => $phone,
            'hash'        => $hash,
            'udf1'        => (string) $order->id,
        ]);
    }

    /**
     * Handle PayU Hosted Checkout callback.
     *
     * Accepts both GET (browser redirect via surl/furl) and POST (server-to-server callback).
     * Route is CSRF-exempt — see bootstrap/app.php.
     *
     * Direct browser visit with no PayU fields (e.g. /payment/verify?order_id=77 typed
     * manually) is handled gracefully — shown an order status page instead of crashing.
     *
     * PayU response hash (REVERSED order):
     *   sha512(salt|status||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     */
    public function verify(Request $request)
    {
        // order_id comes as query-string on GET redirects, or in POST body.
        $orderId = $request->query('order_id') ?? $request->input('order_id');

        if (!$orderId || !is_numeric($orderId)) {
            return redirect()->route('home')
                ->with('error', 'Invalid payment callback. No order ID provided.');
        }

        $order = Order::find((int) $orderId);

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found.');
        }

        // ── Direct browser visit with no PayU fields ─────────────────────────
        // Someone typed the URL or refreshed the page after payment. Show order status.
        $status = $request->input('status', '');
        $hash   = $request->input('hash', '');
        $txnid  = $request->input('txnid', '');

        if ($status === '' && $hash === '' && $txnid === '') {
            // No PayU callback data — just show current order status
            if ($order->payment_status === 'paid') {
                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('success', 'Your order #' . $order->id . ' has already been paid.');
            }

            // Order not yet paid — show a friendly status page instead of 419
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('info', 'Awaiting payment confirmation for Order #' . $order->id . '. If you completed the payment, it may take a moment to reflect.');
        }

        // ── Full PayU callback ────────────────────────────────────────────────
        Log::info('PayU callback received', [
            'order_id' => $orderId,
            'status'   => $status,
            'txnid'    => $txnid,
            'method'   => $request->method(),
        ]);

        // Already paid — idempotent, safe to redirect again
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Payment already confirmed for Order #' . $order->id . '.');
        }

        $merchantKey = config('services.payu.merchant_key');
        $salt        = config('services.payu.salt');

        $payuPaymentId = $request->input('mihpayid')
            ?? $request->input('payuMoneyId')
            ?? $request->input('paymentId')
            ?? '';

        $isSuccess = in_array((string) $status, ['success', 'Success'], true);

        if (!$isSuccess) {
            Log::warning('PayU payment not successful', [
                'order_id' => $order->id,
                'status'   => $status,
                'txnid'    => $txnid,
            ]);

            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment was not successful for Order #' . $order->id . '. Status: ' . $status);
        }

        // ── Hash verification ─────────────────────────────────────────────────
        if ($salt && $hash) {
            $udf1 = $request->input('udf1', (string) $order->id);
            $udf2 = $request->input('udf2', '');
            $udf3 = $request->input('udf3', '');
            $udf4 = $request->input('udf4', '');
            $udf5 = $request->input('udf5', '');

            // PayU response hash uses REVERSED field order vs request hash
            $hashString = implode('|', [
                $salt,
                $status,
                '',          // additionalCharges (empty if none)
                $udf5,
                $udf4,
                $udf3,
                $udf2,
                $udf1,
                $request->input('email', $order->user->email ?? ''),
                $request->input('firstname', $order->user->name ?? ''),
                $request->input('productinfo', 'Order #' . $order->id),
                $request->input('amount', number_format((float) $order->total_price, 2, '.', '')),
                $txnid,
                $merchantKey,
            ]);

            $generated = hash('sha512', $hashString);

            if (!hash_equals($generated, (string) $hash)) {
                Log::warning('PayU hash mismatch — possible tampering', [
                    'order_id' => $order->id,
                    'txnid'    => $txnid,
                    'received' => $hash,
                    'computed' => $generated,
                ]);

                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment verification failed for Order #' . $order->id . '. Please contact support.');
            }
        } else {
            Log::warning('PayU hash verification skipped — salt or hash missing', [
                'order_id' => $order->id,
                'salt_set' => (bool) $salt,
                'hash_set' => (bool) $hash,
            ]);
        }

        // ── Mark order paid ───────────────────────────────────────────────────
        $order->update([
            'payment_status'  => 'paid',
            'status'          => 'confirmed',
            'payu_payment_id' => $payuPaymentId ?: ($hash ?: 'payu_paid'),
        ]);

        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Your order #' . $order->id . ' has been confirmed.');
    }
}
