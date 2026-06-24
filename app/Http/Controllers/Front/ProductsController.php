<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /** Number of products per page / per infinite-scroll batch */
    private const PER_PAGE = 15;

    // ─────────────────────────────────────────────────────────────────────────
    // Full page load  →  /products
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $products   = $this->buildQuery($request)->paginate(self::PER_PAGE)->withQueryString();
        $brands     = Brand::has('products')->get();
        $categories = Category::where('is_active', true)->withCount('products')->get();

        return view('front.products', compact('products', 'brands', 'categories'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Infinite-scroll AJAX  →  /products/ajax
    // Returns JSON: { html, hasMore, nextPage }
    // ─────────────────────────────────────────────────────────────────────────
    public function ajaxLoad(Request $request)
    {
        $products = $this->buildQuery($request)->paginate(self::PER_PAGE)->withQueryString();

        return response()->json([
            'html'     => view('front.partials.product-list-grid', ['products' => $products])->render(),
            'hasMore'  => $products->hasMorePages(),
            'nextPage' => $products->hasMorePages() ? $products->currentPage() + 1 : null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared filter + sort query  (single source of truth)
    // ─────────────────────────────────────────────────────────────────────────
    private function buildQuery(Request $request): Builder
    {
        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'brand', 'variants'])
            ->withCount(['reviews as reviews_count' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as reviews_avg_rating'  => fn($q) => $q->where('is_approved', true)], 'rating');

        // ── Sort ──────────────────────────────────────────────────────────────
        switch ($request->get('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc');        break;
            case 'price_desc': $query->orderBy('price', 'desc');       break;
            case 'name_asc':   $query->orderBy('name', 'asc');         break;
            default:           $query->orderBy('created_at', 'desc');  break;
        }

        // ── Price range ───────────────────────────────────────────────────────
        if ($request->filled('price_range') && $request->price_range !== '') {
            [$min, $max] = explode('-', $request->price_range . '-');
            if ($min !== '') $query->where('price', '>=', (float) $min);
            if ($max !== '') $query->where('price', '<=', (float) $max);
        }
        if ($request->filled('price_min')) $query->where('price', '>=', (float) $request->price_min);
        if ($request->filled('price_max')) $query->where('price', '<=', (float) $request->price_max);

        // ── Category (supports parent → children expansion) ───────────────────
        if ($request->filled('category')) {
            $requestedIds  = array_map('intval', (array) $request->category);
            $allIds        = collect($requestedIds);

            $topCategories = Category::whereIn('id', $requestedIds)
                ->whereNull('parent_id')
                ->pluck('id');

            if ($topCategories->isNotEmpty()) {
                $childIds = Category::whereIn('parent_id', $topCategories)->pluck('id');
                $allIds   = $allIds->merge($childIds);
            }

            $query->whereIn('category_id', $allIds->unique()->values()->all());
        }

        // ── Brand ─────────────────────────────────────────────────────────────
        if ($request->filled('brand')) {
            $query->whereIn('brand_id', (array) $request->brand);
        }

        // ── Search ────────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',        'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('sku',         'like', "%$s%");
            });
        }

        return $query;
    }
}
