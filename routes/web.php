<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\DashboardController;
use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $banners = Banner::where('type', 'homepage')
        ->where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->get();

    return view('front.index', compact('banners'));
});

Route::get('/sitemap.xml', function () {
    $products   = \App\Models\Product::where('is_active', true)
                    ->select('id', 'slug', 'name', 'updated_at')
                    ->with(['images' => fn($q) => $q->select('product_id', 'image')->limit(1)])
                    ->get();
    $categories = \App\Models\Category::where('is_active', true)->select('id', 'slug', 'name', 'updated_at')->get();
    $posts      = \App\Models\BlogPost::where('status', 'published')
                    ->select('slug', 'updated_at')
                    ->latest('updated_at')
                    ->get();

    return response()->view('front.sitemap', compact('products', 'categories', 'posts'))
                     ->header('Content-Type', 'application/xml');
});

Route::get('/merchant-feed.xml', function () {
    $products = \App\Models\Product::where('is_active', true)
                    ->with(['images', 'variants', 'brand', 'category'])
                    ->get();

    return response()->view('front.merchant-feed', compact('products'))
                     ->header('Content-Type', 'application/xml');
});

Route::get('/robots.txt', function () {
    $content = implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /admin/',
        'Disallow: /checkout',
        'Disallow: /cart',
        'Disallow: /profile',
        'Disallow: /profile/',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /forgot-password',
        'Disallow: /reset-password',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ]);
    return response($content, 200)->header('Content-Type', 'text/plain');
});

// =======================================
// CUSTOMER ROUTES
// =======================================
Route::prefix('')->name('customer.')->group(function () {
    // Login routes (unauthenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');

        // Password Reset Routes
        Route::get('/forgot-password', [CustomerAuthController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [CustomerAuthController::class, 'reset'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'customerDashboard'])->name('dashboard');
    });
});

// Global Email Verification Routes (Required for 'verified' middleware)
Route::get('/email/verify', function () {
    return view('auth.customer.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back()->with('status', 'already-verified');
    }

    $user = $request->user();

    $verificationUrl = Illuminate\Support\Facades\URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    \Illuminate\Support\Facades\Mail::to($user->email)->send(
        (new \App\Mail\VerifyEmailMail($user, $verificationUrl))->subject('Verify Your Email Address — Jeanzo')
    );

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');


// =======================================
// ADMIN ROUTES
// =======================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Login routes (unauthenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

        // Password Reset Routes
        Route::get('/forgot-password', [AdminAuthController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [AdminAuthController::class, 'reset'])->name('password.update');
    });

    // Dashboard routes (authenticated admin only)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.submit');

        // Category management
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        // Customer management
        Route::get('/customers', [\App\Http\Controllers\Admin\CustomerManagementController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}/orders', [\App\Http\Controllers\Admin\CustomerManagementController::class, 'showOrders'])->name('customers.orders');
        Route::get('/customers/{customer}/wallet', [\App\Http\Controllers\Admin\CustomerManagementController::class, 'showWallet'])->name('customers.wallet');
        Route::get('/customers/{customer}/addresses', [\App\Http\Controllers\Admin\CustomerManagementController::class, 'showAddresses'])->name('customers.addresses');
        Route::post('/customers/{customer}/toggle-block', [\App\Http\Controllers\Admin\CustomerManagementController::class, 'toggleBlock'])->name('customers.toggleBlock');

        // Vendor management
        Route::resource('vendors', \App\Http\Controllers\Admin\VendorManagementController::class);
        Route::post('/vendors/{vendor}/approve', [\App\Http\Controllers\Admin\VendorManagementController::class, 'approve'])->name('vendors.approve');
        Route::post('/vendors/{vendor}/reject', [\App\Http\Controllers\Admin\VendorManagementController::class, 'reject'])->name('vendors.reject');

        // Vendor KYC management
        Route::post('/vendors/kyc/{kyc}/approve', [\App\Http\Controllers\Admin\VendorManagementController::class, 'approveKyc'])->name('vendors.kyc.approve');
        Route::post('/vendors/kyc/{kyc}/reject', [\App\Http\Controllers\Admin\VendorManagementController::class, 'rejectKyc'])->name('vendors.kyc.reject');

        // Vendor settlement management
        Route::post('/vendors/settlements/{settlement}/approve', [\App\Http\Controllers\Admin\VendorManagementController::class, 'approveSettlement'])->name('vendors.settlements.approve');


        // Coupon Management
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

        // Order management (Admin)
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderAdminController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderAdminController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\OrderAdminController::class, 'downloadInvoice'])->name('orders.invoice');
        Route::get('/orders/{order}/shipping-label', [\App\Http\Controllers\Admin\OrderAdminController::class, 'downloadShippingLabel'])->name('orders.shippingLabel');

        // Product management
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::post('/products/{product}/generate-images', [\App\Http\Controllers\Admin\GenerateProductImagesController::class, 'generate'])->name('products.generate-images');

        // Inventory management (Admin)
        Route::get('/inventory', [\App\Http\Controllers\Admin\InventoryAdminController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/alerts', [\App\Http\Controllers\Admin\InventoryAdminController::class, 'alerts'])->name('inventory.alerts');
        Route::get('/inventory/history', [\App\Http\Controllers\Admin\InventoryAdminController::class, 'history'])->name('inventory.history');
        Route::get('/inventory/warehouses', [\App\Http\Controllers\Admin\InventoryAdminController::class, 'warehouses'])->name('inventory.warehouses');
        Route::post('/inventory/adjust', [\App\Http\Controllers\Admin\InventoryAdminController::class, 'adjustStock'])->name('inventory.adjust');

        // Delivery & Logistics Management
        Route::resource('couriers', \App\Http\Controllers\Admin\CourierController::class);
        Route::resource('shipping_rules', \App\Http\Controllers\Admin\ShippingRuleController::class);
        Route::resource('delivery_charges', \App\Http\Controllers\Admin\DeliveryChargeController::class);
        Route::resource('shipments', \App\Http\Controllers\Admin\ShipmentController::class);

        // Return & Refund management
        Route::get('/returns', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'index'])->name('returns.index');
        Route::get('/returns/{return}', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'show'])->name('returns.show');

        Route::post('/returns/{return}/refunds/approve', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'approveRefund'])->name('returns.refunds.approve');
        Route::post('/returns/{return}/pickups/request', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'requestReversePickup'])->name('returns.pickups.request');
        Route::post('/returns/{return}/exchanges/approve', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'approveExchange'])->name('returns.exchanges.approve');
        Route::post('/returns/{return}/wallet/refund', [\App\Http\Controllers\Admin\ReturnsAdminController::class, 'refundToWallet'])->name('returns.wallet.refund');


        // Blog management
        Route::get('/blogs', [\App\Http\Controllers\Admin\BlogPostController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/create', [\App\Http\Controllers\Admin\BlogPostController::class, 'create'])->name('blogs.create');
        Route::post('/blogs', [\App\Http\Controllers\Admin\BlogPostController::class, 'store'])->name('blogs.store');
        Route::get('/blogs/{blog_post}/edit', [\App\Http\Controllers\Admin\BlogPostController::class, 'edit'])->name('blogs.edit');
        Route::put('/blogs/{blog_post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{blog_post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'destroy'])->name('blogs.destroy');

        Route::get('/blogs/featured', function () {
            return redirect()->route('admin.blogs.index', ['featured' => 1]);
        })->name('blogs.featured');

        Route::get('/blogs/seo', function () {
            return redirect()->route('admin.blogs.index', ['seo' => 1]);
        })->name('blogs.seo');


        // Blog categories
        Route::get('/blog-categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'index'])->name('blog.categories.index');
        Route::get('/blog-categories/create', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'create'])->name('blog.categories.create');
        Route::post('/blog-categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'store'])->name('blog.categories.store');
        Route::get('/blog-categories/{blog_category}/edit', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'edit'])->name('blog.categories.edit');
        Route::put('/blog-categories/{blog_category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'update'])->name('blog.categories.update');
        Route::delete('/blog-categories/{blog_category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');

        // Blog tags
        Route::get('/blog-tags', [\App\Http\Controllers\Admin\BlogTagController::class, 'index'])->name('blog.tags.index');
        Route::get('/blog-tags/create', [\App\Http\Controllers\Admin\BlogTagController::class, 'create'])->name('blog.tags.create');
        Route::post('/blog-tags', [\App\Http\Controllers\Admin\BlogTagController::class, 'store'])->name('blog.tags.store');
        Route::get('/blog-tags/{blog_tag}/edit', [\App\Http\Controllers\Admin\BlogTagController::class, 'edit'])->name('blog.tags.edit');
        Route::put('/blog-tags/{blog_tag}', [\App\Http\Controllers\Admin\BlogTagController::class, 'update'])->name('blog.tags.update');
        Route::delete('/blog-tags/{blog_tag}', [\App\Http\Controllers\Admin\BlogTagController::class, 'destroy'])->name('blog.tags.destroy');


        // Banner management
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);

        // Keep the old "banners.index" route name usage working with query filters.
        // Route::resource('banners', ...) already provides admin.banners.index.


        // SEO management
        Route::get('/seo', function () {
            return view('admin.seo.index');
        })->name('seo.index');

        // Homepage management
        Route::get('/homepage', function () {
            return view('admin.homepage.index');
        })->name('homepage.index');

        // Notification management
        Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::post('/notifications/clear', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'clearAll'])->name('notifications.clearAll');

        // Review management
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);
        // Site settings & social links
        Route::get('/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])->name('settings.update');
        // Newsletter subscribers
        Route::get('/newsletter', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'newsletters'])->name('newsletter.index');
        Route::post('/newsletter/{subscriber}/toggle', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'toggleSubscriber'])->name('newsletter.toggle');
        Route::delete('/newsletter/{subscriber}', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'destroySubscriber'])->name('newsletter.destroy');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::post('/reviews/{review}/spam', [\App\Http\Controllers\Admin\ReviewController::class, 'markSpam'])->name('reviews.markSpam');
        Route::post('/reviews/{review}/featured', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleFeatured'])->name('reviews.toggleFeatured');

        // Audit Logs management
        Route::get('/audit-logs', function () {
            return view('admin.audit_logs.index');
        })->name('auditlogs.index');

        // Brand management
        Route::get('/brands', [\App\Http\Controllers\Admin\BrandManagementController::class, 'index'])->name('brands.index');
        Route::get('/brands/create', [\App\Http\Controllers\Admin\BrandManagementController::class, 'create'])->name('brands.create');
        Route::post('/brands', [\App\Http\Controllers\Admin\BrandManagementController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit', [\App\Http\Controllers\Admin\BrandManagementController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [\App\Http\Controllers\Admin\BrandManagementController::class, 'destroy'])->name('brands.destroy');

        // CMS Management
        Route::get('/cms/pages', [\App\Http\Controllers\Admin\CMSManagementController::class, 'pagesIndex'])->name('cms.pages.index');
        Route::get('/cms/pages/{slug}/edit', [\App\Http\Controllers\Admin\CMSManagementController::class, 'pagesEdit'])->name('cms.pages.edit');
        Route::put('/cms/pages/{slug}', [\App\Http\Controllers\Admin\CMSManagementController::class, 'pagesUpdate'])->name('cms.pages.update');

        Route::get('/cms/faqs', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsIndex'])->name('cms.faqs.index');
        Route::get('/cms/faqs/create', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsCreate'])->name('cms.faqs.create');
        Route::post('/cms/faqs', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsStore'])->name('cms.faqs.store');
        Route::get('/cms/faqs/{faq}/edit', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsEdit'])->name('cms.faqs.edit');
        Route::put('/cms/faqs/{faq}', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsUpdate'])->name('cms.faqs.update');
        Route::delete('/cms/faqs/{faq}', [\App\Http\Controllers\Admin\CMSManagementController::class, 'faqsDestroy'])->name('cms.faqs.destroy');
    });
    // Moved inside admin group for security and consistent prefixing
    Route::delete('/product-images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.images.delete');
});

// =======================================
// VENDOR ROUTES
// =======================================
Route::prefix('vendor')->name('vendor.')->group(function () {
    // Login routes (unauthenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [VendorAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [VendorAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [VendorAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [VendorAuthController::class, 'register'])->name('register.submit');

        // Password Reset Routes
        Route::get('/forgot-password', [VendorAuthController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [VendorAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [VendorAuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [VendorAuthController::class, 'reset'])->name('password.update');
    });

    // Authenticated vendor-only routes
    Route::middleware(['auth', 'vendor'])->group(function () {
        Route::post('/logout', [VendorAuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Vendor\VendorDashboardController::class, 'dashboard'])->name('dashboard');

        // Reviews & Ratings
        Route::get('/reviews', [\App\Http\Controllers\Vendor\VendorDashboardController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Vendor\VendorReviewReplyController::class, 'reply'])->name('review.reply');

        // Return Requests
        Route::get('/returns', [\App\Http\Controllers\Vendor\VendorReturnController::class, 'index'])->name('returns.index');
        Route::get('/returns/{return}', [\App\Http\Controllers\Vendor\VendorReturnController::class, 'show'])->name('returns.show');
        Route::post('/returns/{return}/update', [\App\Http\Controllers\Vendor\VendorReturnController::class, 'updateStatus'])->name('returns.update');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Vendor\VendorDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\Vendor\VendorDashboardController::class, 'updateProfile'])->name('profile.update');

        // Products — vendor sees & manages ONLY their own products
        Route::resource('products', \App\Http\Controllers\Vendor\VendorProductController::class);
        Route::delete('/product-images/{image}', [\App\Http\Controllers\Vendor\VendorProductController::class, 'deleteImage'])->name('products.images.delete');

        // Orders — vendor sees only orders containing their products
        Route::get('/orders', [\App\Http\Controllers\Vendor\VendorOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Vendor\VendorOrderController::class, 'show'])->name('orders.show');

        // Inventory — vendor sees only their product variants
        Route::get('/inventory', [\App\Http\Controllers\Vendor\VendorInventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/alerts', [\App\Http\Controllers\Vendor\VendorInventoryController::class, 'alerts'])->name('inventory.alerts');
        Route::post('/inventory/adjust', [\App\Http\Controllers\Vendor\VendorInventoryController::class, 'adjustStock'])->name('inventory.adjust');
    });
});

// =======================================
// FRONTEND PAGES (CozaStore)
// =======================================

Route::get('/products', [\App\Http\Controllers\Front\ProductsController::class, 'index'])->name('products.index');

// Infinite-scroll AJAX endpoint
Route::get('/products/ajax', [\App\Http\Controllers\Front\ProductsController::class, 'ajaxLoad'])->name('products.ajaxLoad');

// SEO-friendly category URLs: /mens-jeans, /womens-slim-fit-jeans etc.
// Must be defined BEFORE /products/{slug} to avoid conflict
Route::get('/products/category/{slug}', [\App\Http\Controllers\Front\CategoryProductsController::class, 'show'])
    ->name('products.category');

// Clean SEO category URL: /{category-slug}
// Excludes all reserved top-level paths so they don't get swallowed
Route::get('/{categorySlug}', [\App\Http\Controllers\Front\ProductsController::class, 'bySlug'])
    ->where('categorySlug', '^(?!about|blog|brands|cart|checkout|contact|email|faq|help|products|robots\.txt|sitemap\.xml|privacy\-policy|shipping\-policy|return\-refund\-policy|cancellation\-policy|terms\-and\-conditions|payment|wishlist|profile|account|login|register|vendor|admin)[a-z][a-z0-9\-]+$')
    ->name('products.bySlug');

Route::get('/products/{slug}', [\App\Http\Controllers\Front\ProductDetailController::class, 'show'])
    ->name('products.detail');

// Checkout — open to guests (GuestIdentity handled in controller)
Route::get('/checkout', [\App\Http\Controllers\Front\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/identify', [\App\Http\Controllers\Front\CheckoutController::class, 'identify'])->name('checkout.identify');
Route::post('/checkout/lookup-email', [\App\Http\Controllers\Front\CheckoutController::class, 'lookupEmail'])->name('checkout.lookup-email');
Route::post('/checkout/guest-skip', [\App\Http\Controllers\Front\CheckoutController::class, 'guestSkip'])->name('checkout.guest-skip');
Route::post('/checkout', [\App\Http\Controllers\Front\CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/pending', [\App\Http\Controllers\Front\CheckoutController::class, 'storePending'])->name('checkout.store-pending');
// Confirmation has no auth — session may be gone after external payment redirect
Route::get('/checkout/confirmation/{orderId}', [\App\Http\Controllers\Front\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/cart', [\App\Http\Controllers\Front\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [\App\Http\Controllers\Front\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [\App\Http\Controllers\Front\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [\App\Http\Controllers\Front\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\Front\CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [\App\Http\Controllers\Front\CartController::class, 'getCount'])->name('cart.count');

Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\Front\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [\App\Http\Controllers\Front\WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [\App\Http\Controllers\Front\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/move-to-cart', [\App\Http\Controllers\Front\WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
    Route::get('/wishlist/count', [\App\Http\Controllers\Front\WishlistController::class, 'getCount'])->name('wishlist.count');
    Route::post('/wishlist/is-in-wishlist', [\App\Http\Controllers\Front\WishlistController::class, 'isInWishlist'])->name('wishlist.is-in-wishlist');

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\Front\ProfileController::class, 'dashboard'])->name('profile.dashboard');
    Route::get('/profile/personal-info', [\App\Http\Controllers\Front\ProfileController::class, 'editPersonalInfo'])->name('profile.personal-info');
    Route::post('/profile/personal-info', [\App\Http\Controllers\Front\ProfileController::class, 'updatePersonalInfo'])->name('profile.update-personal-info');
    Route::get('/profile/change-password', [\App\Http\Controllers\Front\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/change-password', [\App\Http\Controllers\Front\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/addresses', [\App\Http\Controllers\Front\ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::get('/profile/addresses/create', [\App\Http\Controllers\Front\ProfileController::class, 'createAddress'])->name('profile.address.create');
    Route::post('/profile/addresses', [\App\Http\Controllers\Front\ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::get('/profile/addresses/{id}/edit', [\App\Http\Controllers\Front\ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/profile/addresses/{id}', [\App\Http\Controllers\Front\ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/addresses/{id}', [\App\Http\Controllers\Front\ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::get('/profile/orders', [\App\Http\Controllers\Front\ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{id}', [\App\Http\Controllers\Front\ProfileController::class, 'orderDetails'])->name('profile.order-details');
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');


// ── Legal / Policy pages ───────────────────────────────────────────────────
Route::get('/terms-and-conditions', fn() => view('front.legal.terms'))->name('legal.terms');
Route::get('/privacy-policy',       fn() => view('front.legal.privacy'))->name('legal.privacy');
Route::get('/return-refund-policy', fn() => view('front.legal.refund'))->name('legal.refund');
Route::get('/shipping-policy',      fn() => view('front.legal.shipping'))->name('legal.shipping');
Route::get('/cancellation-policy',  fn() => view('front.legal.cancellation'))->name('legal.cancellation');

Route::get('/about', function () {
    return view('front.about');
})->name('about');

// ── Vendor storefront pages (/brands/{slug}) ───────────────────────────────
Route::get('/brands/{slug}', [\App\Http\Controllers\Front\VendorStorefrontController::class, 'show'])->name('brands.show');

Route::post('/products/{id}/review', [\App\Http\Controllers\Front\ProductDetailController::class, 'storeReview'])->middleware('auth')->name('products.review');
Route::post('/vendor/{vendor}/review', [\App\Http\Controllers\Front\VendorReviewController::class, 'store'])->middleware('auth')->name('vendor.review.store');
Route::match(['get','post'], '/contact', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('post')) {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:2000',
        ]);

        \Illuminate\Support\Facades\Mail::to(env('ADMIN_EMAIL', 'support@jeanzo.in'))
            ->send(new \App\Mail\ContactMail(
                senderName:    $request->name,
                senderEmail:   $request->email,
                contactSubject: $request->subject,
                userMessage:   $request->message,
            ));

        return redirect()->route('contact')->with('success', 'Your message has been sent. We will get back to you shortly!');
    }
    return view('front.contact');
})->name('contact');

// ── Front: FAQ, Help, Newsletter ──────────────────────────
Route::get('/faq', [\App\Http\Controllers\Front\FaqController::class, 'index'])->name('faq');
Route::get('/help', [\App\Http\Controllers\Front\FaqController::class, 'help'])->name('help');
Route::post('/newsletter/subscribe', [\App\Http\Controllers\Front\FaqController::class, 'newsletter'])->name('newsletter.subscribe');

// Exit intent popup — capture WhatsApp or email
Route::post('/exit-capture', function (\Illuminate\Http\Request $request) {
    $contact = trim($request->input('contact', ''));
    if (empty($contact)) {
        return response()->json(['ok' => false], 422);
    }

    $isPhone = preg_match('/^[\d\s\+\-]{7,15}$/', $contact);
    $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);

    if (!$isPhone && !$isEmail) {
        return response()->json(['ok' => false], 422);
    }

    \App\Models\NewsletterSubscriber::firstOrCreate(
        $isEmail ? ['email' => $contact] : ['whatsapp' => $contact],
        [
            'name'      => 'Exit Popup Lead',
            'email'     => $isEmail ? $contact : null,
            'whatsapp'  => $isPhone ? $contact : null,
            'is_active' => true,
            'source'    => 'exit_popup',
        ]
    );

    return response()->json(['ok' => true]);
})->name('exit.capture');

// ── Front: Payment gateway (PayU) ──────────
Route::post('/payment/create-order', [\App\Http\Controllers\PaymentController::class, 'createOrder'])->name('payment.create-order')->middleware('auth');

// PayU return URL after payment (browser redirect)
// CSRF-exempt via bootstrap/app.php validateCsrfTokens(except: ['payment/verify', 'payment/webhook'])
Route::match(['get', 'post'], '/payment/verify', [\App\Http\Controllers\PaymentController::class, 'verify'])->name('payment.verify');

// PayU webhook (server-to-server, no CSRF)
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

// ── Front: Profile extras ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::patch('/profile/orders/{id}/cancel', [\App\Http\Controllers\Front\ProfileController::class, 'cancelOrder'])->name('profile.cancel-order');
    Route::post('/profile/orders/{id}/reorder', [\App\Http\Controllers\Front\ProfileController::class, 'reorder'])->name('profile.reorder');

    // Return Requests
    Route::get('/profile/orders/{id}/return', [\App\Http\Controllers\Front\ReturnRequestController::class, 'create'])->name('profile.return.create');
    Route::post('/profile/orders/{id}/return', [\App\Http\Controllers\Front\ReturnRequestController::class, 'store'])->name('profile.return.store');
    Route::get('/profile/reviews', [\App\Http\Controllers\Front\ProfileController::class, 'reviews'])->name('profile.reviews');
    Route::post('/coupon/apply', [\App\Http\Controllers\Front\CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [\App\Http\Controllers\Front\CouponController::class, 'remove'])->name('coupon.remove');

});
