<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorReview;
use Illuminate\Http\Request;

class VendorReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new vendor review from a customer.
     */
    public function store(Request $request, int $vendorId)
    {
        $request->validate([
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'nullable|string|max:1000',
            'order_id'   => 'required|integer|exists:orders,id',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        $vendor = Vendor::findOrFail($vendorId);

        // Verify the order belongs to this user
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Prevent duplicate review for same vendor + order
        $exists = VendorReview::where('vendor_id', $vendorId)
            ->where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You have already reviewed this seller for this order.');
        }

        VendorReview::create([
            'vendor_id'  => $vendor->id,
            'user_id'    => auth()->id(),
            'order_id'   => $order->id,
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'review'     => $request->review,
            'is_visible' => true,
        ]);

        return redirect()->back()->with('success', 'Thank you for reviewing this seller!');
    }
}
