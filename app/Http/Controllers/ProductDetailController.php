<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\VendorReview;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'brand', 'images', 'reviews.user', 'vendor', 'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('waist_size')->orderBy('length');
            }])
            ->firstOrFail();

        // ── Ratings ──────────────────────────────────────────────────
        $approvedReviews = $product->reviews
            ? $product->reviews->where('is_approved', true)
            : collect();
        $avgRating   = round($approvedReviews->avg('rating') ?? 0, 1);
        $reviewCount = $approvedReviews->count();

        // ── Pricing ───────────────────────────────────────────────────
        $originalPrice = (float) $product->price;
        $salePrice     = $product->sale_price ? (float) $product->sale_price : null;
        $jeanzoPrice   = $product->jeanzo_price ? (float) $product->jeanzo_price : null;

        // Priority: jeanzo_price → sale_price → price
        $displayPrice = $jeanzoPrice ?? $salePrice ?? $originalPrice;

        $discount = 0;
        if ($displayPrice < $originalPrice && $originalPrice > 0) {
            $discount = (int) round((($originalPrice - $displayPrice) / $originalPrice) * 100);
        }

        // ── Stock ─────────────────────────────────────────────────────
        if ($product->variants->isNotEmpty()) {
            $totalStock = $product->variants->sum('quantity');
        } else {
            $totalStock = (int) ($product->quantity ?? 0);
        }

        $stockAvailable = $totalStock > 0;
        $canPurchase    = $stockAvailable;

        if (!$stockAvailable) {
            $stockMessage = 'Out of Stock';
        } elseif ($totalStock <= 5) {
            $stockMessage = 'Only ' . $totalStock . ' left — Order soon!';
        } else {
            $stockMessage = 'In Stock — Ready to Ship';
        }

        // ── Vendor reviews ────────────────────────────────────────────
        $vendorReviews     = collect();
        $vendorAvgRating   = 0;
        $vendorReviewCount = 0;
        if ($product->vendor) {
            $vendorReviews     = VendorReview::where('vendor_id', $product->vendor_id)
                ->visible()->with('user')->latest()->take(6)->get();
            $vendorAvgRating   = $product->vendor->avg_rating ?? 0;
            $vendorReviewCount = $product->vendor->review_count ?? 0;
        }

        // ── Related products ──────────────────────────────────────────
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images', 'reviews'])
            ->take(20)
            ->get();

        return view('front.product-detail', compact(
            'product',
            'avgRating',
            'reviewCount',
            'displayPrice',
            'originalPrice',
            'discount',
            'totalStock',
            'stockAvailable',
            'stockMessage',
            'canPurchase',
            'relatedProducts',
            'vendorReviews',
            'vendorAvgRating',
            'vendorReviewCount'
        ));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
        ]);

        $product = Product::findOrFail($id);

        $existing = Review::where('product_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => auth()->id(),
            'rating'      => $request->rating,
            'review_text' => $request->review_text,
            'status'      => 'pending',
            'is_approved' => false,
        ]);

        return redirect()->back()->with('success', 'Your review has been submitted and is pending approval. Thank you!');
    }
}
