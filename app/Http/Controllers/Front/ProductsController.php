<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'brand', 'variants'])
            ->withCount(['reviews as reviews_count' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as reviews_avg_rating' => fn($q) => $q->where('is_approved', true)], 'rating');

        // Sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name_asc':   $query->orderBy('name', 'asc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        // Price range filter (from sidebar radio)
        if ($request->filled('price_range') && $request->price_range !== '') {
            [$min, $max] = explode('-', $request->price_range . '-');
            if ($min !== '') $query->where('price', '>=', (float)$min);
            if ($max !== '') $query->where('price', '<=', (float)$max);
        }
        // Also support direct min/max
        if ($request->filled('price_min')) $query->where('price', '>=', $request->price_min);
        if ($request->filled('price_max')) $query->where('price', '<=', $request->price_max);

        // Category filter
        if ($request->filled('category')) {
            $query->whereIn('category_id', (array)$request->category);
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->whereIn('brand_id', (array)$request->brand);
        }

        // Search (column is 'name', with fallback accessor 'title')
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('sku', 'like', "%$s%");
            });
        }

        $products   = $query->paginate(24)->withQueryString();

        $brands     = Brand::has('products')->get();
        $categories = Category::where('is_active', true)->withCount('products')->get();

        return view('front.products', compact('products', 'brands', 'categories'));
    }
}
