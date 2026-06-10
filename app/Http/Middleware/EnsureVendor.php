<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendor
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('vendor.login')->with('error', 'Please log in as a vendor.');
        }

        if (auth()->user()->role !== 'vendor') {
            // If an admin accidentally hits /vendor — send to admin dashboard
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Admins use the admin panel.');
            }
            auth()->logout();
            return redirect()->route('vendor.login')->with('error', 'Access denied. Vendor accounts only.');
        }

        return $next($request);
    }
}
