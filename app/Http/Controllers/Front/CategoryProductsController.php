<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryProductsController extends Controller
{
    /**
     * Slug → additional name keywords to match products across ALL categories.
     *
     * When a customer clicks a subcategory (e.g. "Women's Wide Leg Jeans"),
     * products are returned if they:
     *   (a) belong to this category or its children by category_id, OR
     *   (b) contain one of these keywords in their product name
     *
     * This is display-only — no data is moved or changed.
     */
    private const NAME_SEARCH_MAP = [
        // ── Women's fit / silhouette ─────────────────────────────────────────
        'womens-wide-leg-jeans'       => ['Wide-Leg', 'Wide Leg'],
        'womens-flared-jeans'         => ['Flared', 'Flare'],
        'womens-bootcut-jeans'        => ['Bootcut', 'Boot-Cut', 'Boot Cut'],
        'womens-boyfriend-jeans'      => ['Boyfriend'],
        'womens-girlfriend-jeans'     => ['Girlfriend'],
        'womens-mom-jeans'            => ['Mom Jeans', 'Mom Jean'],
        'womens-relaxed-fit-jeans'    => ['Relaxed Fit', 'Relaxed-Fit'],
        'womens-skinny-jeans'         => ['Skinny', 'Skin Fit'],
        'womens-slim-fit-jeans'       => ['Slim Fit', 'Slim-Fit'],
        'womens-straight-leg-jeans'   => ['Straight Leg', 'Straight-Leg', 'Straight Fit'],

        // ── Women's rise ─────────────────────────────────────────────────────
        'womens-low-rise-jeans'       => ['Low Rise', 'Low-Rise'],
        'womens-mid-rise-jeans'       => ['Mid Rise', 'Mid-Rise'],
        'womens-high-rise-jeans'      => ['High-Rise', 'High Rise'],

        // ── Women's style / finish ───────────────────────────────────────────
        'womens-distressed-jeans'     => ['Distressed'],
        'womens-ripped-jeans'         => ['Ripped'],
        'womens-stretch-jeans'        => ['Stretch'],
        'womens-jeggings'             => ['Jegging'],
        'womens-cropped-jeans'        => ['Cropped'],
        'womens-vintage-jeans'        => ['Vintage'],
        'womens-cargo-jeans'          => ['Cargo'],

        // ── Men's ────────────────────────────────────────────────────────────
        'mens-wide-leg-jeans'         => ['Wide-Leg', 'Wide Leg'],
        'mens-relaxed-fit-jeans'      => ['Relaxed Fit', 'Relaxed-Fit'],
        'mens-bootcut-jeans'          => ['Bootcut', 'Boot Cut'],
        'mens-slim-fit-jeans'         => ['Slim Fit', 'Slim-Fit'],
        'mens-skinny-fit-jeans'       => ['Skinny'],
        'mens-straight-fit-jeans'     => ['Straight Fit', 'Straight-Leg'],
        'mens-regular-fit-jeans'      => ['Regular Fit', 'Regular-Fit'],
        'mens-tapered-fit-jeans'      => ['Tapered', 'Tapered Fit'],
        'mens-loose-fit-jeans'        => ['Loose Fit', 'Loose-Fit'],
        'mens-athletic-fit-jeans'     => ['Athletic Fit', 'Athletic-Fit'],
        'mens-cargo-jeans'            => ['Cargo'],
    ];

    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Collect this category + children + grandchildren IDs
        $categoryIds = collect([$category->id]);
        $childIds    = Category::where('parent_id', $category->id)->pluck('id');
        $categoryIds = $categoryIds->merge($childIds);
        if ($childIds->isNotEmpty()) {
            $grandChildIds = Category::whereIn('parent_id', $childIds)->pluck('id');
            $categoryIds   = $categoryIds->merge($grandChildIds);
        }
        $categoryIds = $categoryIds->unique()->values()->all();

        // Name keywords defined for this slug (may be empty)
        $nameKeywords = self::NAME_SEARCH_MAP[$slug] ?? [];

        // Derive gender strictly from the slug prefix so a keyword like
        // "Slim Fit" can never leak across men's/women's pages.
        $genderFilter = null;
        if (str_starts_with($slug, 'mens-')) {
            $genderFilter = 'men';
        } elseif (str_starts_with($slug, 'womens-')) {
            $genderFilter = 'women';
        }

        // ── Base product query ────────────────────────────────────────────────
        $query = Product::where('is_active', true)
            ->with(['category', 'images', 'brand', 'variants'])
            ->where(function ($q) use ($categoryIds, $nameKeywords) {
                // Products belonging to this category (or its children) by ID
                $q->whereIn('category_id', $categoryIds);

                // OR products whose name contains one of the mapped keywords
                foreach ($nameKeywords as $keyword) {
                    $q->orWhere('name', 'like', "%{$keyword}%");
                }
            })
            // Gender guard: prevents the name-keyword OR-match above from
            // pulling in the opposite gender's products (e.g. "Slim Fit"
            // matching both "Men's Slim Fit Jeans" and "Women's Slim Fit Jeans").
            ->when($genderFilter, fn($q) => $q->where('gender', $genderFilter));

        // ── Sorting ───────────────────────────────────────────────────────────
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_asc':  $query->orderBy('price', 'asc');                          break;
            case 'price_desc': $query->orderBy('price', 'desc');                         break;
            case 'popular':    $query->withCount('orders')->orderByDesc('orders_count'); break;
            default:           $query->orderBy('created_at', 'desc');                    break;
        }

        // ── Filters ───────────────────────────────────────────────────────────
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : explode(',', $request->brand);
            $query->whereIn('brand_id', $brands);
        }

        if ($request->filled('size')) {
            $sizes = is_array($request->size) ? $request->size : explode(',', $request->size);
            $query->whereHas('variants', fn($q) => $q->whereIn('waist_size', $sizes));
        }

        if ($request->filled('color')) {
            $colors = is_array($request->color) ? $request->color : explode(',', $request->color);
            $query->whereHas('variants', fn($q) => $q->whereIn('color', $colors));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name',        'like', "%$s%")
                ->orWhere('description', 'like', "%$s%")
                ->orWhere('sku',         'like', "%$s%"));
        }

        // ── Sidebar filter options ────────────────────────────────────────────
        // Build a sub-query that mirrors the same category_id + name-keyword
        // logic so the available sizes/colors shown match the actual product set.
        $filterProductIds = Product::where('is_active', true)
            ->where(function ($q) use ($categoryIds, $nameKeywords) {
                $q->whereIn('category_id', $categoryIds);
                foreach ($nameKeywords as $keyword) {
                    $q->orWhere('name', 'like', "%{$keyword}%");
                }
            })
            ->when($genderFilter, fn($q) => $q->where('gender', $genderFilter))
            ->pluck('id');

        $sizes = DB::table('product_variants')
            ->whereIn('product_id', $filterProductIds)
            ->distinct()
            ->pluck('waist_size')
            ->filter()
            ->values();

        $colors = DB::table('product_variants')
            ->whereIn('product_id', $filterProductIds)
            ->distinct()
            ->pluck('color')
            ->filter()
            ->values();

        $brands   = Brand::where('is_active', true)->get();
        $products = $query->paginate(12);

        return view('front.products-by-category', compact(
            'category', 'products', 'brands', 'sizes', 'colors', 'sortBy'
        ));
    }
}
