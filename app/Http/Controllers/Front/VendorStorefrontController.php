<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorReview;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorStorefrontController extends Controller
{
    public function show(Request $request, string $slug)
    {
        // Find vendor by matching slug of shop_name
        $vendor = Vendor::where('approval_status', 'approved')
            ->where('is_active', true)
            ->get()
            ->first(fn($v) => Str::slug($v->shop_name) === $slug);

        abort_if(!$vendor, 404);

        // Base query for this vendor's active products
        $query = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->with(['images', 'reviews', 'brand', 'vendor', 'category']);

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Sort
        match ($request->get('sort', 'latest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // Categories this vendor sells in (with product counts)
        $categories = Category::whereHas('products', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->where('is_active', true);
            })
            ->withCount(['products' => fn($q) => $q->where('vendor_id', $vendor->id)->where('is_active', true)])
            ->get();

        // Vendor reviews
        $vendorReviews = VendorReview::where('vendor_id', $vendor->id)
            ->visible()
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $avgRating   = $vendor->avg_rating;
        $reviewCount = $vendor->review_count;

        return view('front.vendor-storefront', compact(
            'vendor',
            'products',
            'categories',
            'vendorReviews',
            'avgRating',
            'reviewCount'
        ));
    }
}
