<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorReview;
use Illuminate\Http\Request;

class VendorReviewReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vendor replies to a review on their own store.
     */
    public function reply(Request $request, int $reviewId)
    {
        $request->validate([
            'vendor_reply' => 'required|string|max:500',
        ]);

        $vendor = auth()->user()->vendor;
        if (!$vendor) {
            abort(403, 'Vendor account not found.');
        }

        $review = VendorReview::where('id', $reviewId)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        $review->update(['vendor_reply' => $request->vendor_reply]);

        return redirect()->back()->with('success', 'Your reply has been posted.');
    }
}
