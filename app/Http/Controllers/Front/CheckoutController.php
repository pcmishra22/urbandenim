<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderAlert;
use App\Notifications\PaymentAlert;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CouponService $couponService;

    public function __construct(CartService $cartService, CouponService $couponService)
    {
        $this->cartService   = $cartService;
        $this->couponService = $couponService;
    }

    /**
     * Display the checkout page with cart summary and saved addresses.
     */
    public function index()
    {
        $cartItems = $this->cartService->getCartWithProducts();

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal  = $this->cartService->getSubtotal();
        $addresses = auth()->check() ? auth()->user()->addresses()->get() : collect();

        return view('front.checkout', compact('cartItems', 'subtotal', 'addresses'));
    }

    /**
     * Show the order confirmation / thank-you page.
     */
    public function confirmation($orderId)
    {
        $order = Order::with('products.images', 'user')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('front.checkout-confirmation', compact('order'));
    }

    /**
     * Process and place the order.
     */
    public function store(Request $request)
    {
        $cartItems = $this->cartService->getCartWithProducts();

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'shipping_full_name' => 'required|string|max:255',
            'shipping_phone'     => 'required|string|max:20',
            'shipping_street'    => 'required|string|max:255',
            'shipping_city'      => 'required|string|max:255',
            'shipping_state'     => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country'   => 'required|string|max:255',
            'payment_method'     => 'required|in:cod,card,upi',
            'coupon_code'        => 'nullable|string|max:50',
            'notes'              => 'nullable|string|max:500',
        ]);

        $subtotal     = $this->cartService->getSubtotal();
        $shippingCost = $this->calculateShipping($subtotal);
        $discount     = 0;
        $couponCode   = null;

        // Validate and apply coupon
        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon && $coupon->isValidForUser(auth()->user())) {
                $discount   = $this->couponService->calculateDiscount($coupon, $subtotal);
                $couponCode = strtoupper($request->coupon_code);
                $this->couponService->applyCoupon($coupon);
            }
        }

        $total = max(0, $subtotal + $shippingCost - $discount);

        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                'user_id'              => auth()->id(),
                'subtotal'             => $subtotal,
                'shipping_cost'        => $shippingCost,
                'discount_amount'      => $discount,
                'coupon_code'          => $couponCode,
                'total_price'          => $total,
                'status'               => 'pending',
                'payment_method'       => $request->payment_method,
                'payment_status'       => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'shipping_full_name'   => $request->shipping_full_name,
                'shipping_phone'       => $request->shipping_phone,
                'shipping_street'      => $request->shipping_street,
                'shipping_city'        => $request->shipping_city,
                'shipping_state'       => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country'     => $request->shipping_country,
                'notes'                => $request->notes,
            ]);

            // Attach order items
            foreach ($cartItems as $item) {
                $product = $item['product'];
                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Could not place order. Please try again.');
        }

        // Clear cart
        $this->cartService->clear();

        // Notify admins
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new OrderAlert($order, 'new_order'));

            if ($order->payment_status === 'paid') {
                Notification::send($admins, new PaymentAlert([
                    'order_id' => $order->id,
                    'amount'   => $order->total_price,
                    'currency' => 'INR',
                    'status'   => 'completed',
                    'message'  => "Payment completed for Order #{$order->id}.",
                ]));
            }
        } catch (\Throwable $e) {
            // Notification failure should not block the user
        }

        return redirect()->route('checkout.confirmation', $order->id)
            ->with('success', "Order #{$order->id} placed successfully!");
    }

    /**
     * Simple shipping calculation — free above ₹500.
     */
    protected function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 500 ? 0.0 : 50.0;
    }
}
