<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register route middleware aliases
        $middleware->alias([
            'admin'  => \App\Http\Middleware\EnsureAdmin::class,
            'vendor' => \App\Http\Middleware\EnsureVendor::class,
            'role'   => \App\Http\Middleware\CheckRole::class,
        ]);
        // Tell Laravel's auth middleware to redirect unauthenticated users to
        // the customer login page (our login route is named 'customer.login')
        $middleware->redirectGuestsTo(fn () => route('customer.login'));
        // Exclude Cashfree callbacks from CSRF — Cashfree GETs/POSTs back from an external
        // server with no Laravel session/token. Security is enforced by the Cashfree SDK
        // order status verification (PGFetchOrder) and webhook HMAC-SHA256 signature check.
        $middleware->validateCsrfTokens(except: [
            'payment/verify',
            'payment/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
