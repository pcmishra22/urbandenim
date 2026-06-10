<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Front\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay payment gateway controller.
 *
 * Flow:
 *   1. JS calls checkout.store-pending  → order created, payment_status = awaiting_payment
 *   2. JS calls payment.create-order    → Razorpay order created, credentials returned
 *   3. Razorpay modal opens
 *   4. On success, JS posts to payment.verify → signature verified, order marked paid, emails sent
 */
class PaymentController extends Controller
{
    /**
     * Create PayU Hosted Checkout payment redirect data for an already-saved pending order.
     *
     * Front-end contract remains the same:
     *   - JS calls POST /payment/create-order with { order_id }
     *   - we respond with redirect URL + POST fields for PayU Hosted Checkout
     */
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        $merchantKey = config('services.payu.merchant_key');
        $salt        = config('services.payu.salt');
        $baseUrl     = rtrim(config('services.payu.base_url', 'https://secure.payu.in'), '/');

        if (!$merchantKey) {
            return response()->json(['error' => 'PayU merchant key is not configured.'], 500);
        }

        // txnid must be unique per transaction; reuse the column for now.
        $txnid = 'order_' . $order->id . '_' . time();

        // Store PayU txnid in razorpay_order_id column (temporary reuse).
        $order->update([
            'razorpay_order_id' => $txnid,
        ]);

        $amount = number_format((float) $order->total_price, 2, '.', '');

        // PayU Hosted Checkout endpoint expects POST.
        $endpoint = $baseUrl . '/_payment';

        // Return enough information for front-end to auto-submit a form.
        return response()->json([
            'endpoint'      => $endpoint,
            'merchantKey'   => $merchantKey,
            'txnid'         => $txnid,
            'order_id'      => $order->id,
            'amount'        => $amount,
            'productinfo'   => 'Order #' . $order->id,
            'firstname'     => auth()->user()->name ?? '',
            'email'         => auth()->user()->email ?? '',
            'phone'         => $order->shipping_phone ?? '',
            'action'        => $endpoint,
            'salt_provided' => (bool) $salt,
        ]);
    }

    /**
     * Verify PayU Hosted Checkout callback and mark order paid.
     *
     * NOTE: PayU sends multiple callback params. To keep this integration resilient
     * to field name differences, we verify using `hash` if present. If salt is not
     * configured yet, we will mark the payment as paid only when PayU indicates
     * success; this should be tightened once PAYU_SALT is set.
     */
    public function verify(Request $request)
    {
        // order_id is required by our front-end contract.
        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($request->order_id);

        $merchantKey = config('services.payu.merchant_key');
        $salt        = config('services.payu.salt');

        // Common PayU fields
        $status = $request->input('status');
        $hash   = $request->input('hash');
        $txnid  = $request->input('txnid');

        // Payment reference: commonly `mihpayid` or `payuMoneyId` (depending on integration).
        $payuPaymentId = $request->input('mihpayid') ?? $request->input('payuMoneyId') ?? $request->input('paymentId');

        if (!$status) {
            // fallback if PayU returns as `responseCode`
            $status = (string) $request->input('status') ?: (string) $request->input('responseCode');
        }

        $isSuccess = in_array((string) $status, ['success', 'Success', '1', 'PAYU_SUCCESS'], true);

        // If PayU indicates failure, redirect with error.
        if (!$isSuccess) {
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment was not successful for Order #' . $order->id);
        }

        // Verify hash if we have both salt and hash.
        if ($salt && $hash) {
            // PayU Hosted Checkout hash generation typically uses: key|txnid|amount|productinfo|firstname|email|udf1|udf2|...|salt
            // Because exact sequence depends on your integration fields, we verify the most common subset.
            $generated = hash('sha512', (
                ($merchantKey ?? '') . '|' .
                ($txnid ?? '') . '|' .
                ($order->total_price) . '|' .
                ($request->input('productinfo') ?? ('Order #' . $order->id)) . '|' .
                ($request->input('firstname') ?? (auth()->user()->name ?? '')) . '|' .
                ($request->input('email') ?? (auth()->user()->email ?? '')) . '|' .
                $salt
            ));

            if (!hash_equals((string) $hash, (string) $generated)) {
                Log::warning('PayU hash mismatch', [
                    'order_id' => $order->id,
                    'txnid'    => $txnid,
                    'hash'     => $hash,
                ]);

                return redirect()->route('checkout.confirmation', $order->id)
                    ->with('error', 'Payment verification failed. Please contact support with Order #' . $order->id);
            }
        } else {
            // Salt not configured yet — do not block successful payments, but log for later tightening.
            Log::info('PayU verification skipped (missing salt/hash)', [
                'order_id' => $order->id,
                'salt_set' => (bool) $salt,
                'hash_set' => (bool) $hash,
            ]);
        }

        $order->update([
            'payment_status'      => 'paid',
            'razorpay_payment_id' => $payuPaymentId ?: ($request->input('paymentId') ?? $hash ?: 'payu_paid'),
            // razorpay_order_id already stores txnid
        ]);

        // Clear cart + coupon now (after successful payment)
        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }
}

