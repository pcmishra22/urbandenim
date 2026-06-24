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

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Cart hits "Proceed to Checkout"
    //   • Logged-in user  → go straight to checkout form
    //   • Guest           → show checkout page with inline email gateway
    // ─────────────────────────────────────────────────────────────────────────
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

        $addresses      = collect();
        $prefillAddress = null;

        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();
        $isGuest     = $currentUser?->is_guest ?? true;   // unauthenticated also treated as guest

        // Pre-fill addresses only for real (non-guest) logged-in customers
        if ($currentUser && !$isGuest) {
            if (Schema::hasTable('customer_addresses')) {
                $addresses      = $currentUser->addresses()->orderByDesc('is_default')->get();
                $prefillAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
            }

            // Fallback: last order shipping details
            if (!$prefillAddress) {
                $lastOrder = Order::where('user_id', $currentUser->id)
                    ->whereNotNull('shipping_street')
                    ->latest()
                    ->first();
                if ($lastOrder) {
                    $prefillAddress = (object) [
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

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2A — AJAX: user typed an email and clicked "Continue"
    //
    //   Scenario A (existing customer)  → auto-login, return saved address
    //   Scenario B (new email)          → create guest account, auto-login
    //   Guard                           → reject admin/vendor emails
    // ─────────────────────────────────────────────────────────────────────────
    public function lookupEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $email = strtolower(trim($request->input('email')));

        // Never allow admin/vendor accounts to be used via this path
        if (User::where('email', $email)->whereIn('role', ['admin', 'vendor'])->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This email is associated with a staff account. Please use a different email or sign in instead.',
            ], 422);
        }

        $existing = User::where('email', $email)->where('role', 'customer')->first();
        $isNew    = ($existing === null);

        if ($isNew) {
            // Scenario B — create a guest account with the real email provided
            $user = User::create([
                'name'              => Str::before($email, '@') ?: 'Guest Customer',
                'email'             => $email,
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'customer',
                'is_guest'          => true,
                'email_verified_at' => now(),
            ]);
        } else {
            $user = $existing;
        }

        Auth::login($user);

        // Build address payload for JS pre-fill
        $address = null;

        // Try saved addresses table first (works for returning real customers)
        if (!$user->is_guest && Schema::hasTable('customer_addresses')) {
            $addr = $user->addresses()->where('is_default', true)->first()
                 ?? $user->addresses()->latest()->first();
            if ($addr) {
                $address = [
                    'full_name'   => $addr->full_name,
                    'phone'       => $addr->phone,
                    'street'      => $addr->street,
                    'city'        => $addr->city,
                    'state'       => $addr->state,
                    'postal_code' => $addr->postal_code,
                    'country'     => $addr->country,
                ];
            }
        }

        // Fallback: last order (covers returning guest who gave the same email)
        if (!$address) {
            $lastOrder = Order::where('user_id', $user->id)
                ->whereNotNull('shipping_street')
                ->latest()
                ->first();
            if ($lastOrder) {
                $address = [
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

        return response()->json([
            'status'      => 'ok',
            'is_existing' => !$isNew,
            'address'     => $address,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2B — AJAX: user clicked "Continue without email"
    //
    //   Scenario C — generate a ghost email, create guest account, auto-login
    // ─────────────────────────────────────────────────────────────────────────
    public function guestSkip(Request $request)
    {
        if (Auth::check()) {
            // Already authenticated — nothing to do
            return response()->json(['status' => 'ok']);
        }

        $ghostEmail = sprintf('guest_%s_%s@jeanzo.in', time(), Str::lower(Str::random(8)));

        $guestUser = User::create([
            'name'              => 'Guest Customer',
            'email'             => $ghostEmail,
            'password'          => Hash::make(Str::random(32)),
            'role'              => 'customer',
            'is_guest'          => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($guestUser);

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Legacy full-page POST fallback (kept for no-JS / direct navigation)
    // ─────────────────────────────────────────────────────────────────────────
    public function identify(Request $request)
    {
        if (Auth::check()) {
            return Redirect::route('checkout.index');
        }

        if ($request->has('continue_without_email')) {
            $ghostEmail = sprintf('guest_%s_%s@jeanzo.in', time(), Str::lower(Str::random(8)));
            $guestUser  = User::create([
                'name'              => 'Guest Customer',
                'email'             => $ghostEmail,
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'customer',
                'is_guest'          => true,
                'email_verified_at' => now(),
            ]);
            Auth::login($guestUser);
            return Redirect::route('checkout.index')
                ->with('guest_message', 'Continuing as guest — fill in your shipping details below.');
        }

        $request->validate(['email' => 'required|email|max:255']);
        $email = strtolower(trim($request->input('email')));

        // Guard admin/vendor
        if (User::where('email', $email)->whereIn('role', ['admin', 'vendor'])->exists()) {
            return Redirect::route('checkout.index')
                ->withErrors(['email' => 'This email is associated with a staff account. Please use a different email.'])
                ->withInput();
        }

        $user = User::where('email', $email)->where('role', 'customer')->first();

        if (!$user) {
            $user = User::create([
                'name'              => Str::before($email, '@') ?: 'Guest Customer',
                'email'             => $email,
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'customer',
                'is_guest'          => true,
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);

        $msg = $user->wasRecentlyCreated
            ? 'Account created — fill in your shipping details below.'
            : 'Welcome back! Your saved shipping details have been pre-filled.';

        return Redirect::route('checkout.index')->with('guest_message', $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Order confirmation page
    // ─────────────────────────────────────────────────────────────────────────
    public function confirmation($orderId)
    {
        // No auth check — session may be gone after PayU external redirect
        $order = Order::with('products.images')->findOrFail($orderId);
        return view('front.checkout-confirmation', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // COD order submission
    // ─────────────────────────────────────────────────────────────────────────
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

        // Safety net: if session was lost between email lookup and form submit,
        // re-establish guest identity so the order can still be saved.
        if (!Auth::check()) {
            $this->ensureGuestSession($request);
        }

        if ($request->payment_method !== 'cod') {
            return Redirect::back()->with('error', 'Please use the online payment option properly.');
        }

        $order = $this->createOrder($request, 'pending');
        $this->saveShippingAddress($request, Auth::user());
        $this->cartService->clear();
        Session::forget(['coupon_code', 'coupon_discount']);

        $order->load('products', 'user');
        $this->sendOrderEmails($order);

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

    // ─────────────────────────────────────────────────────────────────────────
    // UPI / Card — create pending order via AJAX before PayU redirect
    // ─────────────────────────────────────────────────────────────────────────
    public function storePending(Request $request)
    {
        Log::info('storePending called', [
            'payment_method' => $request->input('payment_method'),
            'user_id'        => Auth::check() ? Auth::id() : null,
        ]);

        $cartItems = $this->cartService->getCartWithProducts();
        if (empty($cartItems)) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
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

        if (!Auth::check()) {
            $this->ensureGuestSession($request);
        }

        try {
            $order = $this->createOrder($request, 'awaiting_payment');
            $this->saveShippingAddress($request, Auth::user());
        } catch (\Throwable $e) {
            Log::error('storePending failed', [
                'error'          => $e->getMessage(),
                'payment_method' => $request->input('payment_method'),
                'user_id'        => Auth::check() ? Auth::id() : null,
                'trace'          => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Could not create order: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success'  => true,
            'order_id' => $order->id,
            'total'    => $order->total_price,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Safety net: if the user's session expired between email lookup and order
     * submission, recreate a guest account so the order can still be persisted.
     * Prefers guest_email from the form (populated by JS after lookupEmail),
     * then falls back to phone-based ghost email.
     */
    protected function ensureGuestSession(Request $request): void
    {
        $email = $request->input('guest_email')
            ? strtolower(trim($request->input('guest_email')))
            : null;

        // If a real email was provided, try to find the existing customer first
        if ($email) {
            $existingUser = User::where('email', $email)->where('role', 'customer')->first();
            if ($existingUser) {
                Auth::login($existingUser);
                return;
            }
            // Guard: do not create if it's an admin/vendor email
            if (User::where('email', $email)->whereIn('role', ['admin', 'vendor'])->exists()) {
                $email = null; // Fall through to ghost email
            }
        }

        // No email or email was blocked → ghost email
        if (!$email) {
            $email = sprintf('guest_%s_%s@jeanzo.in', time(), Str::lower(Str::random(8)));
        }

        $guestUser = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $request->input('shipping_full_name', 'Guest Customer'),
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'customer',
                'is_guest'          => true,
                'email_verified_at' => now(),
            ]
        );

        if ($guestUser->role !== 'customer') {
            abort(422, 'Unable to create guest session. Please contact support.');
        }

        Auth::login($guestUser);
    }

    protected function createOrder(Request $request, string $paymentStatus): Order
    {
        $cartItems  = $this->cartService->getCartWithProducts();
        $subtotal   = $this->cartService->getSubtotal();
        $shipping   = $this->calculateShipping($subtotal);
        $discount   = Session::get('coupon_discount', 0);
        $couponCode = Session::get('coupon_code');

        if (!$discount && $request->filled('coupon_code') && Schema::hasTable('coupons')) {
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

                // Decrement stock only for COD (confirmed). Card/UPI decrements
                // happen in PaymentController when payment is verified.
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

        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
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

    protected function saveShippingAddress(Request $request, ?object $user): void
    {
        if (!$user || ($user->is_guest ?? false)) return;
        if (!Schema::hasTable('customer_addresses')) return;

        try {
            \App\Models\CustomerAddress::updateOrCreate(
                ['user_id' => $user->id, 'is_default' => true],
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
            Log::warning('Could not save shipping address', ['error' => $e->getMessage()]);
        }
    }
}
