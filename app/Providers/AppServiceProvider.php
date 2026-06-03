<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define role gates
        Gate::define('admin', fn (User $user) => $user->role === 'admin');
        Gate::define('vendor', fn (User $user) => $user->role === 'vendor');
        Gate::define('customer', fn (User $user) => $user->role === 'customer');

        // Compound gates for role combinations
        Gate::define('admin-or-vendor', fn (User $user) => in_array($user->role, ['admin', 'vendor']));

        // Use Bootstrap 5 pagination views
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Share cart count and wishlist count with all front views
        View::composer('front.partials.header', function ($view) {
            $cartService = app(CartService::class);
            $cartCount = $cartService->getCount();

            $wishlistCount = 0;
            if (auth()->check()) {
                $wishlistCount = auth()->user()->wishlists()->count();
            }

            $view->with([
                'headerCartCount'     => $cartCount,
                'headerWishlistCount' => $wishlistCount,
            ]);
        });
    }
}
