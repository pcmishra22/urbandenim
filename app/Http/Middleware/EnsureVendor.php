<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendor
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'vendor') {
            abort(403, 'Only vendors can access this resource');
        }

        return $next($request);
    }
}
