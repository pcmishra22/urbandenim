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

        // Exclude PayU callbacks from CSRF — PayU POSTs back from an external server
        // with no Laravel session/token. Security is enforced by SHA-512 hash verification.
        $middleware->validateCsrfTokens(except: [
            'payment/verify',
            'payment/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
