<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorOrderController extends Controller
{
    private function getVendor(): Vendor
    {
        $vendor = Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }
        return $vendor;
    }

    /**
     * List orders that contain at least one product belonging to this vendor.
     */
    public function index(Request $request)
    {
        $vendor = $this->getVendor();

        $query = Order::with(['user'])
            ->whereHas('products', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $query->where('id', $request->input('search'));
        }

        $orders = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $statuses = ['pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];

        return view('vendor.orders.index', compact('orders', 'statuses', 'vendor'));
    }

    /**
     * Show order detail — only the items belonging to this vendor.
     */
    public function show(Order $order)
    {
        $vendor = $this->getVendor();

        // Verify this order has at least one product from this vendor
        $hasVendorProducts = $order->products()->where('vendor_id', $vendor->id)->exists();
        if (!$hasVendorProducts) {
            abort(403, 'This order does not contain your products.');
        }

        // Load only this vendor's products in the order
        $order->load([
            'user',
            'products' => function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            },
        ]);

        return view('vendor.orders.show', compact('order', 'vendor'));
    }
}
