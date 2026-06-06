<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Razorpay payment gateway integration.
 *
 * Setup:
 *   1. composer require razorpay/razorpay
 *   2. Add to .env:
 *        RAZORPAY_KEY_ID=rzp_test_XXXX
 *        RAZORPAY_KEY_SECRET=XXXX
 *   3. Add to config/services.php:
 *        'razorpay' => ['key' => env('RAZORPAY_KEY_ID'), 'secret' => env('RAZORPAY_KEY_SECRET')],
 */
class PaymentController extends Controller
{
    public function createOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        $order   = Order::where('user_id', auth()->id())->findOrFail($orderId);

        if ($order->payment_status === 'paid') {
            return response()->json(['error' => 'Order already paid.'], 400);
        }

        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $rzpOrder = $api->order->create([
                'receipt'  => 'order_' . $order->id,
                'amount'   => (int) round($order->total_price * 100), // paise
                'currency' => 'INR',
                'notes'    => ['order_id' => $order->id],
            ]);

            // Store Razorpay order ID on the order
            $order->update(['razorpay_order_id' => $rzpOrder->id]);

            return response()->json([
                'razorpay_order_id' => $rzpOrder->id,
                'amount'            => $rzpOrder->amount,
                'currency'          => 'INR',
                'key'               => config('services.razorpay.key'),
                'name'              => 'EShopper',
                'description'       => 'Order #' . $order->id,
                'prefill' => [
                    'name'    => auth()->user()->name,
                    'email'   => auth()->user()->email,
                    'contact' => $order->shipping_phone ?? '',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay createOrder failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'order_id'            => 'required|integer',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($request->order_id);

        try {
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Verify signature
            $attributes = [
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ];
            $api->utility->verifyPaymentSignature($attributes);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            Log::warning('Razorpay signature mismatch', ['order_id' => $order->id]);
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        // Mark order as paid
        $order->update([
            'payment_status'       => 'paid',
            'razorpay_payment_id'  => $request->razorpay_payment_id,
        ]);

        // Send emails
        $order->load('products', 'user');
        try { Mail::to($order->user->email)->send(new OrderConfirmedMail($order)); }
        catch (\Throwable $e) { Log::warning('Order confirm email failed', ['e' => $e->getMessage()]); }
        try {
            $admins = User::where('role', 'admin')->pluck('email')->toArray();
            if ($admins) Mail::to($admins)->send(new NewOrderAdminMail($order));
        } catch (\Throwable $e) { Log::warning('Admin order email failed', ['e' => $e->getMessage()]); }

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', 'Payment successful! Order confirmed.');
    }
}
