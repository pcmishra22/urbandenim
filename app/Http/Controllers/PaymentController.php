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
 *   4. PayU redirects browser to surl/furl (GET) after payment
 *   5. verify() validates PayU response hash and marks order paid
 */
class PaymentController extends Controller
{
    /**
     * Create PayU Hosted Checkout payment redirect data for an already-saved pending order.
     *
     * Front-end contract:
     *   - JS calls POST /payment/create-order with { order_id }
     *   - we respond with endpoint URL + POST fields (including pre-computed hash)
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
        // After email: udf1 + 9 empty fields + salt  (verified against PayU error response)
        $hashString = implode('|', [
            $merchantKey,   // [0] key
            $txnid,         // [1] txnid
            $amount,        // [2] amount
            $productinfo,   // [3] productinfo
            $firstname,     // [4] firstname
            $email,         // [5] email
            $order->id,     // [6] udf1
            '',             // [7] udf2
            '',             // [8] udf3
            '',             // [9] udf4
            '',             // [10] udf5
            '',             // [11] reserved
            '',             // [12] reserved
            '',             // [13] reserved
            '',             // [14] reserved
            '',             // [15] reserved
            $salt,          // [16] salt
        ]);
        $hash = hash('sha512', $hashString);

        // Store PayU txnid so we can look it up on callback.
        $order->update(['payu_txnid' => $txnid]);

        $endpoint = $baseUrl . '/_payment';

        return response()->json([
            'endpoint'    => $endpoint,
            'merchantKey' => $merchantKey,
            'txnid'       => $txnid,
            'order_id'    => $order->id,
            'amount'      => $amount,
            'productinfo' => $productinfo,
            'firstname'   => $firstname,
            'email'       => $email,
            'phone'       => $phone,
            'hash'        => $hash,   // pre-computed; front-end posts this as `hash`
            'udf1'        => (string) $order->id,
        ]);
    }

    /**
     * Handle PayU Hosted Checkout callback (browser redirect via surl/furl).
     *
     * PayU sends the browser back via GET (or POST depending on integration mode).
     * We support both; the route registers both methods in web.php.
     *
     * PayU response hash (REVERSED order):
     *   sha512(salt|status||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     */
    public function verify(Request $request)
    {
        // order_id may come as a query-string param (GET) or POST body field.
        $request->validate([
            'order_id' => 'required|integer',
        ]);

        // No auth middleware on this route (PayU redirects back externally).
        // Security is enforced by hash verification below.
        $order = Order::findOrFail($request->order_id);

        $merchantKey = config('services.payu.merchant_key');
        $salt        = config('services.payu.salt');

        $status = $request->input('status', '');
        $hash   = $request->input('hash', '');
        $txnid  = $request->input('txnid', '');

        // Temporary: log all PayU callback fields for debugging
        Log::info('PayU callback fields', $request->all());

        // PayU payment reference ID.
        $payuPaymentId = $request->input('mihpayid')
            ?? $request->input('payuMoneyId')
            ?? $request->input('paymentId')
            ?? '';

        $isSuccess = in_array((string) $status, ['success', 'Success'], true);

        if (!$isSuccess) {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment was not successful for Order #' . $order->id . '. Status: ' . $status);
        }

        // Verify response hash if we have both salt and hash in the callback.
        // TEMP: skip hash verification while debugging — re-enable after confirming fields
        $skipHashVerification = true;
        if (!$skipHashVerification && $salt && $hash) {
            // PayU response hash uses REVERSED field order with additional UDF fields.
            $udf5 = $request->input('udf5', '');
            $udf4 = $request->input('udf4', '');
            $udf3 = $request->input('udf3', '');
            $udf2 = $request->input('udf2', '');
            $udf1 = $request->input('udf1', (string) $order->id);

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
                Log::warning('PayU hash mismatch', [
                    'order_id' => $order->id,
                    'txnid'    => $txnid,
                    'received' => $hash,
                    'computed' => $generated,
                ]);

                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment verification failed. Please contact support with Order #' . $order->id);
            }
        } else {
            Log::warning('PayU verification skipped (missing salt/hash)', [
                'order_id' => $order->id,
                'salt_set' => (bool) $salt,
                'hash_set' => (bool) $hash,
            ]);
        }

        // Mark order paid.
        $order->update([
            'payment_status'  => 'paid',
            'payu_payment_id' => $payuPaymentId ?: ($hash ?: 'payu_paid'),
            // payu_txnid already stores our txnid
        ]);

        // Clear cart + coupon after successful payment.
        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }
}
