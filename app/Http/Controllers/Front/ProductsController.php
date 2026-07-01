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

    /** Set by bySlug() for the duration of the request; used by buildQuery() */
    private ?string $forceGender = null;

    /**
     * Same keyword map as CategoryProductsController.
     * When resolving a category by slug, products are included if they
     * belong to the category by ID *or* their name contains one of these
     * keywords — so "Women's Wide Leg Jeans" shows all wide-leg products
     * regardless of which category they were assigned to.
     */
    private const NAME_SEARCH_MAP = [
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
        'womens-low-rise-jeans'       => ['Low Rise', 'Low-Rise'],
        'womens-mid-rise-jeans'       => ['Mid Rise', 'Mid-Rise'],
        'womens-high-rise-jeans'      => ['High-Rise', 'High Rise'],
        'womens-distressed-jeans'     => ['Distressed'],
        'womens-ripped-jeans'         => ['Ripped'],
        'womens-stretch-jeans'        => ['Stretch'],
        'womens-jeggings'             => ['Jegging'],
        'womens-cropped-jeans'        => ['Cropped'],
        'womens-vintage-jeans'        => ['Vintage'],
        'womens-cargo-jeans'          => ['Cargo'],
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

    // ─────────────────────────────────────────────────────────────────────────
    // Full page load  →  /products
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $products   = $this->buildQuery($request)->paginate(self::PER_PAGE)->withQueryString();
        $brands     = Brand::has('products')->get();
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $smartProductCountBycat = $this->buildSmartCountMap();

        return view('front.products', compact('products', 'brands', 'categories', 'smartProductCountBycat'));
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
    // $forceCategoryIds  – pre-expanded list of category IDs (set by bySlug)
    // $forceNameKeywords – name keywords to OR-match  (set by bySlug)
    // ─────────────────────────────────────────────────────────────────────────
    private function buildQuery(Request $request, array $forceCategoryIds = [], array $forceNameKeywords = []): Builder
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

        // ── Category ──────────────────────────────────────────────────────────
        if (!empty($forceCategoryIds)) {
            // Called from bySlug: IDs already expanded, keywords already resolved
            $allIds       = $forceCategoryIds;
            $nameKeywords = $forceNameKeywords;
            $query->where(function ($q) use ($allIds, $nameKeywords) {
                $q->whereIn('category_id', $allIds);
                foreach ($nameKeywords as $keyword) {
                    $q->orWhere('name', 'like', "%{$keyword}%");
                }
            });
            // Gender guard: prevents the name-keyword OR-match above from
            // pulling in the opposite gender's products (e.g. "Slim Fit"
            // matching both "Men's Slim Fit Jeans" and "Women's Slim Fit Jeans").
            if ($this->forceGender) {
                $query->where('gender', $this->forceGender);
            }
        } elseif ($request->filled('category')) {
            // Called from index/ajaxLoad: expand top-level → children
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

    // ─────────────────────────────────────────────────────────────────────────
    // SEO-friendly category URL  →  /{category-slug}
    // e.g. /mens-regular-fit-jeans, /womens-slim-fit-jeans
    // Resolves category by slug, then renders the same products view with
    // the category pre-filtered and a proper canonical URL set.
    // Also handles legacy ?category=ID redirects: if someone lands on
    // /products?category=5, the sidebar filter links will use slug URLs.
    // ─────────────────────────────────────────────────────────────────────────
    public function bySlug(string $categorySlug, Request $request)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Collect this category + children + grandchildren IDs
        $categoryIds = collect([$category->id]);
        $childIds    = Category::where('parent_id', $category->id)->pluck('id');
        $categoryIds = $categoryIds->merge($childIds);
        if ($childIds->isNotEmpty()) {
            $grandChildIds = Category::whereIn('parent_id', $childIds)->pluck('id');
            $categoryIds   = $categoryIds->merge($grandChildIds);
        }
        $categoryIds = $categoryIds->unique()->values()->all();

        // Name keywords for this slug (empty array if not in map)
        $nameKeywords = self::NAME_SEARCH_MAP[$categorySlug] ?? [];

        // Derive gender strictly from the slug prefix so a keyword like
        // "Slim Fit" can never leak across men's/women's pages.
        if (str_starts_with($categorySlug, 'mens-')) {
            $this->forceGender = 'men';
        } elseif (str_starts_with($categorySlug, 'womens-')) {
            $this->forceGender = 'women';
        } else {
            $this->forceGender = null;
        }

        // Pass IDs and keywords directly — do NOT merge into $request to avoid
        // array values being serialized into query strings by withQueryString()
        $products   = $this->buildQuery($request, $categoryIds, $nameKeywords)
                           ->paginate(self::PER_PAGE)
                           ->withQueryString();
        $brands     = Brand::has('products')->get();
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $smartProductCountBycat = $this->buildSmartCountMap();

        return view('front.products', compact('products', 'brands', 'categories', 'category', 'smartProductCountBycat'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Build a category_id → product_count map that respects NAME_SEARCH_MAP.
    //
    // For categories that have name keywords defined, the count includes ALL
    // active products whose name matches those keywords (across any category),
    // not just products directly assigned by category_id.
    // ─────────────────────────────────────────────────────────────────────────
    private function buildSmartCountMap(): array
    {
        // Base: direct count by category_id
        $directCounts = \App\Models\Product::where('is_active', true)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->toArray();

        // Build a slug → category_id lookup
        $slugToId = Category::where('is_active', true)
            ->pluck('id', 'slug')
            ->toArray();

        // For each slug that has name keywords, compute the true count
        foreach (self::NAME_SEARCH_MAP as $slug => $keywords) {
            if (!isset($slugToId[$slug])) continue;
            $catId = $slugToId[$slug];

            // Collect all category IDs in this subtree
            $subtreeIds = collect([$catId]);
            $childIds   = Category::where('parent_id', $catId)->pluck('id');
            $subtreeIds = $subtreeIds->merge($childIds);
            if ($childIds->isNotEmpty()) {
                $grandChildIds = Category::whereIn('parent_id', $childIds)->pluck('id');
                $subtreeIds    = $subtreeIds->merge($grandChildIds);
            }
            $subtreeIds = $subtreeIds->unique()->values()->all();

            // Gender guard: same fix as bySlug() — a keyword like "Slim Fit"
            // must not count opposite-gender products.
            $slugGender = null;
            if (str_starts_with($slug, 'mens-')) {
                $slugGender = 'men';
            } elseif (str_starts_with($slug, 'womens-')) {
                $slugGender = 'women';
            }

            // Count: in subtree by ID OR name matches keyword
            $count = \App\Models\Product::where('is_active', true)
                ->where(function ($q) use ($subtreeIds, $keywords) {
                    $q->whereIn('category_id', $subtreeIds);
                    foreach ($keywords as $kw) {
                        $q->orWhere('name', 'like', "%{$kw}%");
                    }
                })
                ->when($slugGender, fn($q) => $q->where('gender', $slugGender))
                ->count();

            $directCounts[$catId] = $count;
        }

        return $directCounts;
    }
}
