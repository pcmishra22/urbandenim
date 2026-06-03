<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class CategoryProductsController extends Controller
{
    /**
     * Display products for a specific category.
     *
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show($slug, Request $request)
    {
        // Fetch the category by slug or fail
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['category', 'images', 'brand']);

        // Sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('orders')->orderByDesc('orders_count');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : explode(',', $request->brand);
            $query->whereIn('brand_id', $brands);
        }

        // Filter by size (through product variants)
        if ($request->filled('size')) {
            $sizes = is_array($request->size) ? $request->size : explode(',', $request->size);
            $query->whereHas('variants', function ($q) use ($sizes) {
                $q->whereIn('size', $sizes);
            });
        }

        // Filter by color (through product variants)
        if ($request->filled('color')) {
            $colors = is_array($request->color) ? $request->color : explode(',', $request->color);
            $query->whereHas('variants', function ($q) use ($colors) {
                $q->whereIn('color', $colors);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            });
        }

        // Get all available filter options for this category
        $brands = Brand::where('is_active', true)->get();
        
        // Get sizes and colors specific to this category
        $sizes = \DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.category_id', $category->id)
            ->distinct('size')
            ->pluck('size')
            ->filter()
            ->values();
        
        $colors = \DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.category_id', $category->id)
            ->distinct('color')
            ->pluck('color')
            ->filter()
            ->values();

        $products = $query->paginate(12);

        return view('front.products-by-category', compact('category', 'products', 'brands', 'sizes', 'colors', 'sortBy'));
    }
}
