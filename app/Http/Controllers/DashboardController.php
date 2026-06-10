<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show customer dashboard
     */
    public function customerDashboard()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->paginate(10);
        
        return view('dashboard.customer', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_products' => \App\Models\Product::count(),
            'total_orders' => \App\Models\Order::count(),
            'total_revenue' => \App\Models\Order::sum('total_price'),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(),
            'shipped_orders' => \App\Models\Order::where('status', 'shipped')->count(),
            'delivered_orders' => \App\Models\Order::where('status', 'delivered')->count(),
            'orders_by_status' => \App\Models\Order::select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
        ];

        $recent_orders = \App\Models\Order::latest()->take(10)->get();
        $recent_users = \App\Models\User::latest()->take(10)->get();

        return view('dashboard.admin', [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'recent_users' => $recent_users,
        ]);
    }

    /**
     * Show vendor dashboard — redirect to scoped vendor panel.
     */
    public function vendorDashboard()
    {
        return redirect()->route('vendor.dashboard');
    }
}
