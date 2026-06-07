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
     * Create a Razorpay order for an already-saved pending order.
     */
    public function createOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'awaiting_payment')
            ->findOrFail($request->order_id);

        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $rzpOrder = $api->order->create([
                'receipt'  => 'order_' . $order->id,
                'amount'   => (int) round($order->total_price * 100), // in paise
                'currency' => 'INR',
                'notes'    => ['order_id' => $order->id],
            ]);

            $order->update(['razorpay_order_id' => $rzpOrder->id]);

            return response()->json([
                'razorpay_order_id' => $rzpOrder->id,
                'order_id'          => $order->id,
                'amount'            => $rzpOrder->amount,
                'currency'          => 'INR',
                'key'               => config('services.razorpay.key'),
                'name'              => config('app.name', 'EShopper'),
                'description'       => 'Order #' . $order->id,
                'prefill'           => [
                    'name'    => auth()->user()->name,
                    'email'   => auth()->user()->email,
                    'contact' => $order->shipping_phone ?? '',
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Razorpay createOrder failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    /**
     * Verify Razorpay signature, mark order paid, send emails.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'order_id'            => 'required|integer',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($request->order_id);

        // Verify signature
        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            Log::warning('Razorpay signature mismatch', ['order_id' => $order->id]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment verification failed. Please contact support with Order #' . $order->id);
        }

        // Mark paid
        $order->update([
            'payment_status'      => 'paid',
            'razorpay_payment_id' => $request->razorpay_payment_id,
        ]);

        // Clear cart + coupon now (after successful payment)
        app(\App\Services\CartService::class)->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        // Send emails
        $order->load('products', 'user');
        app(CheckoutController::class)->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Your order has been confirmed.');
    }
}
