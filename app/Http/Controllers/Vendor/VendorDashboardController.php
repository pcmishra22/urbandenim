<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorDashboardController extends Controller
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
     * Vendor dashboard showing only their own stats.
     */
    public function dashboard()
    {
        $vendor = $this->getVendor();

        $products = Product::where('vendor_id', $vendor->id)->with('variants')->get();
        $total_products   = $products->count();
        $active_products  = $products->where('is_active', true)->count();
        $out_of_stock     = $products->filter(fn($p) => $p->variants->sum('quantity') == 0)->count();
        $low_stock        = $products->filter(fn($p) => $p->variants->sum('quantity') > 0 && $p->variants->sum('quantity') < 5)->count();
        $total_value      = $products->sum(fn($p) => $p->price * $p->variants->sum('quantity'));

        // Orders containing this vendor's products
        $total_orders  = Order::whereHas('products', fn($q) => $q->where('vendor_id', $vendor->id))->count();
        $pending_orders = Order::whereHas('products', fn($q) => $q->where('vendor_id', $vendor->id))
                               ->where('status', 'pending')->count();

        $recent_products = Product::where('vendor_id', $vendor->id)
            ->with(['category', 'variants'])
            ->latest()->take(5)->get();

        return view('vendor.dashboard', compact(
            'vendor', 'total_products', 'active_products', 'out_of_stock',
            'low_stock', 'total_value', 'total_orders', 'pending_orders', 'recent_products'
        ));
    }

    /**
     * Show vendor profile.
     */
    public function profile()
    {
        $vendor = $this->getVendor();
        $user   = Auth::user();
        return view('vendor.profile', compact('vendor', 'user'));
    }

    /**
     * Update vendor profile (name, shop_name, password).
     */
    public function updateProfile(Request $request)
    {
        $vendor = $this->getVendor();
        $user   = Auth::user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'shop_name'  => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:6|confirmed',
        ]);

        $user->update(['name' => $request->name]);
        $vendor->update(['shop_name' => $request->shop_name]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show vendor's reviews and ratings.
     */
    public function reviews()
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) abort(403);

        $reviews = \App\Models\VendorReview::where('vendor_id', $vendor->id)
            ->visible()
            ->with(['user', 'product'])
            ->latest()
            ->paginate(15);

        $avgRating    = round(\App\Models\VendorReview::where('vendor_id', $vendor->id)->visible()->avg('rating') ?? 0, 1);
        $totalReviews = \App\Models\VendorReview::where('vendor_id', $vendor->id)->visible()->count();

        return view('vendor.reviews', compact('reviews', 'avgRating', 'totalReviews'));
    }
}
