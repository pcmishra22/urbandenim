<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in as an administrator.');
        }

        if (auth()->user()->role !== 'admin') {
            // Vendor tried to access admin panel — redirect to their own panel
            if (auth()->user()->role === 'vendor') {
                return redirect()->route('vendor.dashboard')->with('error', 'Vendors cannot access the admin panel.');
            }
            // Customer or others
            auth()->logout();
            return redirect()->route('admin.login')->with('error', 'Access denied. Admin accounts only.');
        }

        return $next($request);
    }
}
