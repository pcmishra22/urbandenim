<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CouponService $couponService
    ) {}

    public function index()
    {
        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal   = $this->cartService->getSubtotal();
        $shipping   = $this->calculateShipping($subtotal);
        $discount   = session('coupon_discount', 0);
        $couponCode = session('coupon_code');
        $grandTotal = max(0, $subtotal + $shipping - $discount);

        $addresses = collect();
        if (auth()->check() && Schema::hasTable('customer_addresses')) {
            $addresses = auth()->user()->addresses()->get();
        }

        return view('front.checkout', compact(
            'cartItems', 'subtotal', 'shipping', 'discount', 'couponCode', 'grandTotal', 'addresses'
        ));
    }

    public function confirmation($orderId)
    {
        $order = Order::with('products.images')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('front.checkout-confirmation', compact('order'));
    }

    /**
     * COD: save order + items, clear cart, send emails, redirect to confirmation.
     * UPI/Card: handled via storePending → PaymentController → verify.
     */
    public function store(Request $request)
    {
        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'shipping_full_name'   => 'required|string|max:255',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_street'      => 'required|string|max:255',
            'shipping_city'        => 'required|string|max:255',
            'shipping_state'       => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country'     => 'required|string|max:255',
            'payment_method'       => 'required|in:cod,card,upi',
            'coupon_code'          => 'nullable|string|max:50',
            'notes'                => 'nullable|string|max:500',
        ]);

        // Only COD hits this route directly — UPI/card go through storePending
        if ($request->payment_method !== 'cod') {
            return redirect()->back()->with('error', 'Please use the online payment option properly.');
        }

        $order = $this->createOrder($request, 'pending');

        $this->cartService->clear();
        session()->forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        $this->sendOrderEmails($order);

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', "Order #{$order->id} placed successfully!");
    }

    /**
     * Called via AJAX for UPI/card payments.
     * Creates the order in DB with payment_status = 'awaiting_payment',
     * returns order_id as JSON so the frontend can initiate PayU Hosted Checkout.
     */
    public function storePending(Request $request)
    {
        Log::info('storePending called', [
            'payment_method' => $request->input('payment_method'),
            'expectsJson' => $request->expectsJson(),
            'isAjax' => $request->ajax(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ]);

        // Accept even if headers don't mark it as ajax/JSON; many frontends don't set them.
        if (!$request->expectsJson() && !$request->ajax()) {
            Log::warning('storePending header mismatch (continuing anyway)', [
                'expectsJson' => $request->expectsJson(),
                'isAjax' => $request->ajax(),
                'payment_method' => $request->input('payment_method'),
                'user_id' => auth()->check() ? auth()->id() : null,
            ]);
        }



        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $validated = $request->validate([
            'shipping_full_name'   => 'required|string|max:255',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_street'      => 'required|string|max:255',
            'shipping_city'        => 'required|string|max:255',
            'shipping_state'       => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country'     => 'required|string|max:255',
            'payment_method'       => 'required|in:card,upi',
            'coupon_code'          => 'nullable|string|max:50',
            'notes'                => 'nullable|string|max:500',
        ]);

        try {
            $order = $this->createOrder($request, 'awaiting_payment');
        } catch (\Throwable $e) {
            Log::error('storePending failed', [
                'error' => $e->getMessage(),
                'payment_method' => $request->input('payment_method'),
                'user_id' => auth()->check() ? auth()->id() : null,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Could not create order: ' . $e->getMessage()], 500);
        }


        return response()->json([
            'success'  => true,
            'order_id' => $order->id,
            'total'    => $order->total_price,
        ]);
    }

    /* ── Shared helpers ─────────────────────────────────── */

    protected function createOrder(Request $request, string $paymentStatus): Order
    {
        $cartItems  = $this->cartService->getCartWithProducts();
        $subtotal   = $this->cartService->getSubtotal();
        $shipping   = $this->calculateShipping($subtotal);
        $discount   = session('coupon_discount', 0);
        $couponCode = session('coupon_code');

        if (!$discount && $request->filled('coupon_code') && Schema::hasTable('coupons')) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon && $coupon->isValidForUser(auth()->user())) {
                $discount   = $this->couponService->calculateDiscount($coupon, $subtotal);
                $couponCode = strtoupper($request->coupon_code);
                $this->couponService->applyCoupon($coupon);
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'              => auth()->id(),
                'subtotal'             => $subtotal,
                'shipping_cost'        => $shipping,
                'discount_amount'      => $discount,
                'coupon_code'          => $couponCode,
                'total_price'          => $total,
                'status'               => 'pending',
                'payment_method'       => $request->payment_method,
                'payment_status'       => $paymentStatus,
                'shipping_full_name'   => $request->shipping_full_name,
                'shipping_phone'       => $request->shipping_phone,
                'shipping_street'      => $request->shipping_street,
                'shipping_city'        => $request->shipping_city,
                'shipping_state'       => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country'     => $request->shipping_country,
                'notes'                => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                $product = $item['product'];
                $variantId = $item['variant_id'] ?? ($item['options']['variant_id'] ?? null);

                $order->products()->attach($product->id, [
                    'product_variant_id' => $variantId,
                    'quantity'           => $item['quantity'],
                    'price'              => $product->sale_price ?? $product->price,
                ]);

                if ($variantId) {
                    ProductVariant::where('id', $variantId)->decrement('quantity', $item['quantity']);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $order;
    }

    public function sendOrderEmails(Order $order): void
    {
        try {
            Mail::to($order->user->email)->send(new OrderConfirmedMail($order));
        } catch (\Throwable $e) {
            Log::warning('Order confirmation email failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            if ($adminEmails) {
                Mail::to($adminEmails)->send(new NewOrderAdminMail($order));
            }
        } catch (\Throwable $e) {
            Log::warning('Admin order email failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }

    protected function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 500 ? 0.0 : 50.0;
    }
}
