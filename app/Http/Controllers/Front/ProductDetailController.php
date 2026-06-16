<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display the specified product detail page.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Fetch the product by slug with all necessary relationships
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'brand', 'images', 'reviews', 'variants' => function($q) {
                $q->where('is_active', true)->where('quantity', '>', 0);
            }])
            ->firstOrFail();

        // Fetch related products (same category, excluding the current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images', 'reviews'])
            ->take(20)
            ->get();

        return view('front.product-detail', compact('product', 'relatedProducts'));
    }

    /**
     * Store a product review.
     */
    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
        ]);

        $product = Product::findOrFail($id);

        // Prevent duplicate reviews from same user
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
