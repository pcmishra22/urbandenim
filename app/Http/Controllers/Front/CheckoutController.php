<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\CartService;
use App\Models\ProductVariant;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

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
            return Redirect::route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal   = $this->cartService->getSubtotal();
        $shipping   = $this->calculateShipping($subtotal);
        $discount   = Session::get('coupon_discount', 0);
        $couponCode = Session::get('coupon_code');
        $grandTotal = max(0, $subtotal + $shipping - $discount);

        if (!Auth::check()) {
            return View::make('front.checkout-identity', compact(
                'cartItems', 'subtotal', 'shipping', 'discount', 'couponCode', 'grandTotal'
            ));
        }

        $addresses      = collect();
        $prefillAddress = null;
        /** @var \App\Models\User|null $currentUser */
        $currentUser    = Auth::user();
        $isGuest        = $currentUser?->is_guest ?? true;

        if (!$isGuest) {
            // Load saved addresses
            if (Schema::hasTable('customer_addresses') && $currentUser) {
                $addresses = $currentUser->addresses()->orderByDesc('is_default')->get();
                $prefillAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
            }

            // Fallback: pre-fill from user's most recent order if no saved address
            if (!$prefillAddress) {
                $lastOrder = Order::where('user_id', Auth::id())
                    ->whereNotNull('shipping_street')
                    ->latest()
                    ->first();
                if ($lastOrder) {
                    $prefillAddress = (object)[
                        'full_name'   => $lastOrder->shipping_full_name,
                        'phone'       => $lastOrder->shipping_phone,
                        'street'      => $lastOrder->shipping_street,
                        'city'        => $lastOrder->shipping_city,
                        'state'       => $lastOrder->shipping_state,
                        'postal_code' => $lastOrder->shipping_postal_code,
                        'country'     => $lastOrder->shipping_country ?? 'India',
                    ];
                }
            }
        }

        return View::make('front.checkout', compact(
            'cartItems', 'subtotal', 'shipping', 'discount', 'couponCode',
            'grandTotal', 'addresses', 'isGuest', 'prefillAddress'
        ));
    }

    public function identify(Request $request)
    {
        // Already authenticated — no need to go through the identity gateway again.
        // Redirect directly to the checkout form.
        if (Auth::check()) {
            return Redirect::route('checkout.index');
        }

        if ($request->has('continue_without_email')) {
            $guestEmail = \sprintf('guest_%s_%s@jeanzo.in', \time(), Str::lower(Str::random(8)));
            $guestUser = User::create([
                'name' => 'Guest Customer',
                'email' => $guestEmail,
                'password' => Str::random(24),
                'role' => 'customer',
                'is_guest' => true,
                'email_verified_at' => \now(),
            ]);

            Auth::login($guestUser);

            return Redirect::route('checkout.index')
                ->with('guest_message', 'Guest checkout account created. Continue with your shipping details.');
        }

        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = \strtolower($request->input('email'));
        $user = User::where('email', $email)
            ->where('role', 'customer')
            ->first();

        if ($user) {
            Auth::login($user);

            return Redirect::route('checkout.index')
                ->with('guest_message', 'Welcome back! Your saved shipping details will be pre-filled.');
        }

        // Guard: if this email already belongs to a non-customer account (admin/vendor),
        // do NOT create a duplicate row or log them in — that would escalate their
        // privileges or expose their identity to a storefront session.
        $existingNonCustomer = User::where('email', $email)
            ->whereIn('role', ['admin', 'vendor'])
            ->exists();

        if ($existingNonCustomer) {
            return Redirect::route('checkout.identify')
                ->withErrors(['email' => 'This email address is already associated with an account. Please use a different email or contact support.'])
                ->withInput();
        }

        $guestUser = User::create([
            'name' => Str::before($email, '@') ?: 'Guest Customer',
            'email' => $email,
            'password' => Str::random(24),
            'role' => 'customer',
            'is_guest' => true,
            'email_verified_at' => \now(),
        ]);

        Auth::login($guestUser);

        return Redirect::route('checkout.index')
            ->with('guest_message', 'Account created for this email. Complete checkout with your shipping details.');
    }

    public function confirmation($orderId)
    {
        // No auth check here — session may be lost after PayU external redirect.
        $order = Order::with('products.images')->findOrFail($orderId);

        return view('front.checkout-confirmation', compact('order'));
    }

    public function store(Request $request)
    {
        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return Redirect::route('cart.index')->with('error', 'Your cart is empty.');
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
            'guest_email'          => 'nullable|email|max:255',
        ]);

        // Guest checkout: auto-create or find guest account
        if (!Auth::check()) {
            $guestEmail = $request->guest_email
                ?? ($request->shipping_phone . '@guest.jeanzo.in');

            $guestUser = User::firstOrCreate(
                ['email' => $guestEmail],
                [
                    'name'     => $request->shipping_full_name,
                    'password' => Hash::make(Str::random(24)),
                    'role'     => 'customer',
                    'is_guest' => true,
                ]
            );

            // Bug 4 guard: firstOrCreate may return an existing admin/vendor row if the
            // derived email collides. Never log in a non-customer via this path.
            if ($guestUser->role !== 'customer') {
                return Redirect::back()->with('error', 'Unable to create guest session. Please contact support.');
            }

            Auth::login($guestUser);
        }

        // Only COD hits this route directly.
        if ($request->payment_method !== 'cod') {
            return Redirect::back()->with('error', 'Please use the online payment option properly.');
        }

        $order = $this->createOrder($request, 'pending');

        // Save address for future checkouts (logged-in non-guest users)
        $this->saveShippingAddress($request, Auth::user());

        $this->cartService->clear();
        Session::forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        $this->sendOrderEmails($order);

        // If guest, log them out after order so they don't stay "logged in"
        $wasGuest = Auth::user()->is_guest ?? false;
        if ($wasGuest) {
            Auth::logout();
            $request->session()->regenerateToken();
        }

        return Redirect::route('checkout.confirmation', $order->id)
            ->with('success', "Order #{$order->id} placed successfully!")
            ->with('guest_order', $wasGuest)
            ->with('guest_email', $order->user->email ?? null);
    }

    /**
     * Called via AJAX for UPI/card payments.
     * Creates the order in DB with payment_status = 'awaiting_payment'.
     */
    public function storePending(Request $request)
    {
        Log::info('storePending called', [
            'payment_method' => $request->input('payment_method'),
            'user_id' => Auth::check() ? Auth::id() : null,
        ]);

        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return \response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $request->validate([
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

        // Re-establish identity if session was lost between identity gateway and payment.
        // This mirrors the same guest-recovery logic in store() for COD.
        if (!Auth::check()) {
            $guestEmail = $request->input('guest_email')
                ?? ($request->input('shipping_phone') . '@guest.jeanzo.in');

            $guestUser = User::firstOrCreate(
                ['email' => $guestEmail],
                [
                    'name'               => $request->input('shipping_full_name', 'Guest Customer'),
                    'password'           => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                    'role'               => 'customer',
                    'is_guest'           => true,
                    'email_verified_at'  => now(),
                ]
            );

            // Only allow actual customer rows (guards against an edge-case where
            // firstOrCreate returned an existing admin/vendor row for that email).
            if ($guestUser->role !== 'customer') {
                Log::warning('storePending: guest email matched a non-customer account', [
                    'email' => $guestEmail,
                    'role'  => $guestUser->role,
                ]);
                return \response()->json(['error' => 'Unable to create guest session. Please contact support.'], 422);
            }

            Auth::login($guestUser);
        }

        try {
            $order = $this->createOrder($request, 'awaiting_payment');
            $this->saveShippingAddress($request, Auth::user());
        } catch (\Throwable $e) {
            Log::error('storePending failed', [
                'error' => $e->getMessage(),
                'payment_method' => $request->input('payment_method'),
                'user_id' => Auth::check() ? Auth::id() : null,
                'trace' => $e->getTraceAsString(),
            ]);
            return \response()->json(['error' => 'Could not create order: ' . $e->getMessage()], 500);
        }

        return \response()->json([
            'success' => true,
            'order_id' => $order->id,
            'total' => $order->total_price,
        ]);
    }

    protected function createOrder(Request $request, string $paymentStatus): Order
    {
        $cartItems = $this->cartService->getCartWithProducts();
        $subtotal  = $this->cartService->getSubtotal();
        $shipping  = $this->calculateShipping($subtotal);
        $discount  = Session::get('coupon_discount', 0);
        $couponCode = Session::get('coupon_code');

        if (
            (!$discount)
            && $request->filled('coupon_code')
            && Schema::hasTable('coupons')
        ) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon && $coupon->isValidForUser(Auth::user())) {
                $discount   = $this->couponService->calculateDiscount($coupon, $subtotal);
                $couponCode = strtoupper($request->coupon_code);
                $this->couponService->applyCoupon($coupon);
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'              => Auth::id(),
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
                $product   = $item['product'];
                $variantId = $item['variant_id'] ?? ($item['options']['variant_id'] ?? null);

                $order->products()->attach($product->id, [
                    'product_variant_id' => $variantId,
                    'quantity'           => $item['quantity'],
                    'price'              => $product->sale_price ?? $product->price,
                ]);

                // Only decrement stock for confirmed COD orders.
                // For UPI/card (awaiting_payment), stock is decremented when
                // payment is confirmed via PaymentController::verify() / webhook().
                if ($variantId && $paymentStatus === 'pending') {
                    ProductVariant::where('id', $variantId)
                        ->decrement('quantity', $item['quantity']);
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
        // Customer confirmation email
        try {
            Mail::to($order->user->email)->send(new OrderConfirmedMail($order));
            Log::info('Order confirmation email sent', ['order_id' => $order->id, 'to' => $order->user->email]);
        } catch (\Throwable $e) {
            Log::error('Order confirmation email FAILED', [
                'order_id' => $order->id,
                'to'       => $order->user->email ?? 'unknown',
                'error'    => $e->getMessage(),
                'mailer'   => config('mail.default'),
            ]);
        }

        // Admin notification email
        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            // Fallback to env if no admin user found or DB has placeholder email
            $adminEmails = array_filter($adminEmails, fn($e) => !str_contains($e, 'example.com'));
            if (empty($adminEmails)) {
                $adminEmails = [env('ADMIN_EMAIL', 'support@jeanzo.in')];
            }
            Mail::to($adminEmails)->send(new NewOrderAdminMail($order));
            Log::info('Admin order email sent', ['order_id' => $order->id, 'to' => $adminEmails]);
        } catch (\Throwable $e) {
            Log::error('Admin order email FAILED', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'mailer'   => config('mail.default'),
            ]);
        }
    }

    protected function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 500 ? 0.0 : 50.0;
    }

    /**
     * Save the shipping address to customer_addresses for future pre-fill.
     * Creates or updates the default address for this user.
     */
    protected function saveShippingAddress(Request $request, ?object $user): void
    {
        if (!$user || ($user->is_guest ?? false)) return;
        if (!Schema::hasTable('customer_addresses')) return;

        try {
            \App\Models\CustomerAddress::updateOrCreate(
                [
                    'user_id'      => $user->id,
                    'is_default'   => true,
                ],
                [
                    'address_type' => 'home',
                    'full_name'    => $request->shipping_full_name,
                    'phone'        => $request->shipping_phone,
                    'street'       => $request->shipping_street,
                    'city'         => $request->shipping_city,
                    'state'        => $request->shipping_state,
                    'postal_code'  => $request->shipping_postal_code,
                    'country'      => $request->shipping_country ?? 'India',
                    'is_default'   => true,
                ]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not save shipping address', ['error' => $e->getMessage()]);
        }
    }

}
