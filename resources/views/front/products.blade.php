@extends('layouts.eshopper')

@section('title', 'Shop - Jeanzo')

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'shop', 'breadcrumb' => 'Shop', 'showCategories' => false])

<div class="container-fluid px-xl-5 py-4" style="background:#faf8f8;">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="j-section" style="position:sticky;top:80px;max-height:calc(100vh - 100px);overflow-y:auto;">

                <style>
                /* ---- Filter Accordion ---- */
                .filter-accordion-btn {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    width: 100%;
                    background: none;
                    border: none;
                    padding: 10px 0;
                    font-size: 1rem;
                    font-weight: 700;
                    color: var(--j-dark);
                    cursor: pointer;
                    border-bottom: 2px solid var(--j-primary-lt);
                    margin-bottom: 0;
                }
                .filter-accordion-btn .fa-chevron-down {
                    transition: transform .22s;
                    font-size: .75rem;
                    color: var(--j-muted);
                }
                .filter-accordion-btn.collapsed .fa-chevron-down { transform: rotate(-90deg); }
                .filter-accordion-body { padding: 10px 0 6px; }
                /* category tree */
                .cat-tree-item { display: flex; align-items: center; justify-content: space-between; padding: 5px 6px; border-radius: 6px; cursor: pointer; font-size: .85rem; color: #444; text-decoration: none; transition: background .13s, color .13s; }
                .cat-tree-item:hover { background: var(--j-primary-lt); color: var(--j-primary); text-decoration: none; }
                .cat-tree-item.active { background: var(--j-primary-lt); color: var(--j-primary); font-weight: 700; }
                .cat-tree-group-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--j-muted); padding: 10px 6px 4px; }
                .cat-children { padding-left: 10px; }
                .cat-count { font-size: .68rem; background: #eee; color: #888; border-radius: 10px; padding: 1px 7px; }
                /* color swatches */
                .color-swatch { width: 20px; height: 20px; border-radius: 50%; display: inline-block; border: 2px solid #ddd; cursor: pointer; transition: transform .13s, border-color .13s; }
                .color-swatch:hover, .color-swatch.active { border-color: var(--j-primary); transform: scale(1.15); }
                /* size pills */
                .size-pill { display: inline-block; padding: 3px 10px; border: 1.5px solid #ddd; border-radius: 20px; font-size: .78rem; cursor: pointer; margin: 3px 3px 3px 0; transition: background .13s, border-color .13s; text-decoration: none; color: #444; }
                .size-pill:hover, .size-pill.active { background: var(--j-primary); border-color: var(--j-primary); color: #fff; text-decoration: none; }
                </style>

                @php
                    // Helper: get all descendant category IDs (including self)
                    // to check if a category has products anywhere in its subtree
                    function getAllDescendantIds(\App\Models\Category $cat): array {
                        $ids = [$cat->id];
                        foreach ($cat->children as $child) {
                            $ids = array_merge($ids, getAllDescendantIds($child));
                        }
                        return $ids;
                    }

                    // Load full 3-level tree first
                    $topCats = \App\Models\Category::where('is_active', true)
                        ->whereNull('parent_id')
                        ->with(['children' => function($q){
                            $q->where('is_active', true)
                              ->with(['children' => function($q2){
                                  $q2->where('is_active', true);
                              }]);
                        }])
                        ->get();

                    // For each leaf/node, count products in its entire subtree
                    // Use a single query to get all category IDs that have products
                    $catsWithProducts = \App\Models\Product::where('is_active', true)
                        ->whereNotNull('category_id')
                        ->pluck('category_id')
                        ->unique()
                        ->toArray();
                    $catsWithProductsSet = array_flip($catsWithProducts);

                    // Count direct products per category
                    $productCountBycat = \App\Models\Product::where('is_active', true)
                        ->whereNotNull('category_id')
                        ->selectRaw('category_id, count(*) as total')
                        ->groupBy('category_id')
                        ->pluck('total', 'category_id')
                        ->toArray();

                    // Check if a category or any descendant has products
                    $catHasAnyProducts = function(\App\Models\Category $cat) use (&$catHasAnyProducts, $catsWithProductsSet) {
                        if (isset($catsWithProductsSet[$cat->id])) return true;
                        foreach ($cat->children as $child) {
                            if ($catHasAnyProducts($child)) return true;
                        }
                        return false;
                    };

                    // Get total product count for a category including all its descendants
                    $getCatTotalCount = function(\App\Models\Category $cat) use (&$getCatTotalCount, $productCountBycat) {
                        $total = $productCountBycat[$cat->id] ?? 0;
                        foreach ($cat->children as $child) {
                            $total += $getCatTotalCount($child);
                        }
                        return $total;
                    };
                @endphp

                {{-- ===== CATEGORY ACCORDION ===== --}}
                <button class="filter-accordion-btn" type="button" data-toggle="collapse" data-target="#filter-cat" aria-expanded="true">
                    Category <i class="fas fa-chevron-down"></i>
                </button>
                <div class="collapse show filter-accordion-body" id="filter-cat">
                    <a href="{{ route('products.index') }}"
                       class="cat-tree-item {{ !request('category') ? 'active' : '' }}">
                        All Products
                    </a>
                    @foreach($topCats as $top)
                        @if(!$catHasAnyProducts($top)) @continue @endif
                        @if($top->children->isNotEmpty())
                            {{-- Top-level parent with children --}}
                            @php $topTotal = $getCatTotalCount($top); @endphp
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $top->id])) }}"
                               class="cat-tree-item {{ request('category')==$top->id ? 'active' : '' }}">
                                <span>{{ $top->name }}</span>
                                @if($topTotal > 0)<span class="cat-count">{{ $topTotal }}</span>@endif
                            </a>
                            {{-- Sub-groups (level 2) --}}
                            @foreach($top->children as $group)
                                @if(!$catHasAnyProducts($group)) @continue @endif
                                @if($group->children->isNotEmpty())
                                    {{-- Filter grandchildren to only those with direct products --}}
                                    @php $visibleGrandchildren = $group->children->filter(fn($c) => $catHasAnyProducts($c)); @endphp
                                    @if($visibleGrandchildren->isNotEmpty())
                                        <div class="cat-tree-group-label">{{ $group->name }}</div>
                                        <div class="cat-children">
                                            @foreach($visibleGrandchildren as $child)
                                            @php $childTotal = $getCatTotalCount($child); @endphp
                                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $child->id])) }}"
                                               class="cat-tree-item {{ request('category')==$child->id ? 'active' : '' }}">
                                                <span>{{ $child->name }}</span>
                                                @if($childTotal > 0)<span class="cat-count">{{ $childTotal }}</span>@endif
                                            </a>
                                            @endforeach
                                        </div>
                                    @elseif(isset($catsWithProductsSet[$group->id]))
                                        @php $groupTotal = $getCatTotalCount($group); @endphp
                                        <div class="cat-children">
                                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $group->id])) }}"
                                               class="cat-tree-item {{ request('category')==$group->id ? 'active' : '' }}">
                                                <span>{{ $group->name }}</span>
                                                @if($groupTotal > 0)<span class="cat-count">{{ $groupTotal }}</span>@endif
                                            </a>
                                        </div>
                                    @endif
                                @elseif(isset($catsWithProductsSet[$group->id]))
                                    @php $groupTotal = $getCatTotalCount($group); @endphp
                                    <div class="cat-children">
                                        <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $group->id])) }}"
                                           class="cat-tree-item {{ request('category')==$group->id ? 'active' : '' }}">
                                            <span>{{ $group->name }}</span>
                                            @if($groupTotal > 0)<span class="cat-count">{{ $groupTotal }}</span>@endif
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        @elseif(isset($catsWithProductsSet[$top->id]))
                            @php $topTotal = $getCatTotalCount($top); @endphp
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $top->id])) }}"
                               class="cat-tree-item {{ request('category')==$top->id ? 'active' : '' }}">
                                <span>{{ $top->name }}</span>
                                @if($topTotal > 0)<span class="cat-count">{{ $topTotal }}</span>@endif
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- ===== PRICE ACCORDION ===== --}}
                <button class="filter-accordion-btn mt-3" type="button" data-toggle="collapse" data-target="#filter-price" aria-expanded="true">
                    Price <i class="fas fa-chevron-down"></i>
                </button>
                <div class="collapse show filter-accordion-body" id="filter-price">
                    <form method="GET" action="{{ route('products.index') }}">
                        @foreach(request()->except(['price_range','page']) as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                        @foreach([''=> 'All Prices','0-500'=>'₹0 – ₹500','500-1000'=>'₹500 – ₹1,000','1000-2000'=>'₹1,000 – ₹2,000','2000-5000'=>'₹2,000 – ₹5,000','5000+'=>'₹5,000+'] as $key=>$label)
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" id="pr{{ $loop->index }}" name="price_range" value="{{ $key }}"
                                   {{ request('price_range','')===$key ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="custom-control-label" for="pr{{ $loop->index }}" style="font-size:.85rem;">{{ $label }}</label>
                        </div>
                        @endforeach
                    </form>
                </div>

                {{-- ===== COLOR ACCORDION ===== --}}
                <button class="filter-accordion-btn mt-3" type="button" data-toggle="collapse" data-target="#filter-color" aria-expanded="false">
                    Color <i class="fas fa-chevron-down"></i>
                </button>
                <div class="collapse filter-accordion-body" id="filter-color">
                    @php
                        $colors = [
                            'Black'       => '#1a1a1a',
                            'White'       => '#f5f5f5',
                            'Blue'        => '#2563eb',
                            'Dark Blue'   => '#1e3a5f',
                            'Light Blue'  => '#60a5fa',
                            'Grey'        => '#9ca3af',
                            'Brown'       => '#92400e',
                            'Indigo'      => '#3730a3',
                            'Green'       => '#16a34a',
                            'Red'         => '#dc2626',
                        ];
                    @endphp
                    <div class="d-flex flex-wrap" style="gap:8px;padding-top:6px;">
                        @foreach($colors as $label => $hex)
                        <a href="{{ route('products.index', array_merge(request()->except(['color','page']), ['color' => strtolower($label)])) }}"
                           title="{{ $label }}"
                           style="background:{{ $hex }}; {{ $hex === '#f5f5f5' ? 'border-color:#bbb;' : '' }}"
                           class="color-swatch {{ request('color') === strtolower($label) ? 'active' : '' }}"></a>
                        @endforeach
                    </div>
                    @if(request('color'))
                    <a href="{{ route('products.index', request()->except(['color','page'])) }}" class="d-inline-block mt-2" style="font-size:.75rem;color:var(--j-primary);">✕ Clear color</a>
                    @endif
                </div>

                {{-- ===== SIZE ACCORDION ===== --}}
                <button class="filter-accordion-btn mt-3" type="button" data-toggle="collapse" data-target="#filter-size" aria-expanded="false">
                    Size <i class="fas fa-chevron-down"></i>
                </button>
                <div class="collapse filter-accordion-body" id="filter-size">
                    <div style="padding-top:6px;">
                        @foreach(['28','30','32','34','36','38','40','42','XS','S','M','L','XL','XXL'] as $sz)
                        <a href="{{ route('products.index', array_merge(request()->except(['size','page']), ['size' => $sz])) }}"
                           class="size-pill {{ request('size') === $sz ? 'active' : '' }}">{{ $sz }}</a>
                        @endforeach
                    </div>
                    @if(request('size'))
                    <a href="{{ route('products.index', request()->except(['size','page'])) }}" class="d-inline-block mt-2" style="font-size:.75rem;color:var(--j-primary);">✕ Clear size</a>
                    @endif
                </div>

                {{-- ===== BRAND ACCORDION ===== --}}
                @php $brands = \App\Models\Brand::has('products')->take(12)->get(); @endphp
                @if($brands->isNotEmpty())
                <button class="filter-accordion-btn mt-3" type="button" data-toggle="collapse" data-target="#filter-brand" aria-expanded="false">
                    Brand <i class="fas fa-chevron-down"></i>
                </button>
                <div class="collapse filter-accordion-body" id="filter-brand">
                    @foreach($brands as $brand)
                    <a href="{{ route('products.index', array_merge(request()->except(['brand','page']),['brand'=>$brand->id])) }}"
                       class="cat-tree-item {{ request('brand')==$brand->id ? 'active' : '' }}">
                        {{ $brand->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                <script>
                // Keep accordion chevron rotated when collapsed
                document.querySelectorAll('.filter-accordion-btn').forEach(function(btn){
                    var target = document.querySelector(btn.getAttribute('data-target'));
                    if(!target) return;
                    target.addEventListener('hide.bs.collapse', function(){ btn.classList.add('collapsed'); });
                    target.addEventListener('show.bs.collapse', function(){ btn.classList.remove('collapsed'); });
                    if(!target.classList.contains('show')) btn.classList.add('collapsed');
                });
                </script>

            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-9">
            <!-- Toolbar -->
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <form action="{{ route('products.index') }}" method="GET" class="flex-grow-1 mr-2" style="max-width:380px;">
                    @foreach(request()->except(['search','page']) as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products…" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text" style="background:var(--j-primary);border:none;color:#fff;cursor:pointer;">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="dropdown ml-auto">
                    <button class="btn btn-outline-secondary dropdown-toggle btn-sm px-4" type="button" data-toggle="dropdown">
                        <i class="fa fa-sort mr-1"></i>Sort: {{ ucfirst(str_replace('_',' ',request('sort','Latest'))) }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach(['latest'=>'Latest','price_asc'=>'Price: Low to High','price_desc'=>'Price: High to Low','name_asc'=>'Name A-Z'] as $sv=>$sl)
                        <a class="dropdown-item {{ request('sort')===$sv ? 'active' : '' }}"
                           href="{{ route('products.index', array_merge(request()->except('sort'),['sort'=>$sv])) }}">{{ $sl }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Active filters -->
            @if(request()->hasAny(['search','category','brand','price_range','color','size']))
            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <small class="text-muted mr-1">Filters:</small>
                @if(request('search'))<span class="j-badge" style="background:#eee;color:#333;">Search: {{ request('search') }}</span>@endif
                @if(request('price_range'))<span class="j-badge" style="background:#eee;color:#333;">Price: {{ request('price_range') }}</span>@endif
                @if(request('color'))<span class="j-badge" style="background:#eee;color:#333;">Color: {{ ucfirst(request('color')) }}</span>@endif
                @if(request('size'))<span class="j-badge" style="background:#eee;color:#333;">Size: {{ request('size') }}</span>@endif
                <a href="{{ route('products.index', request()->except(['search','category','brand','price_range','color','size','page'])) }}" class="j-badge" style="background:#fde;color:#c00;">✕ Clear all</a>
            </div>
            @endif

            <!-- Grid -->
            <div class="row" id="products-container">
                @foreach($products as $product)
                    @include('front.partials.product-card-grid', ['product' => $product])
                @endforeach
            </div>

            @if($products->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-search fa-3x mb-3" style="color:var(--j-primary);opacity:.4;"></i>
                <h5 class="text-muted">No products found</h5>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2 px-5">Clear Filters</a>
            </div>
            @endif

            <!-- Infinite scroll sentinel -->
            <div id="products-scroll-sentinel" class="text-center py-3">
                @if($products->hasMorePages())
                    <div class="spinner-border spinner-border-sm" style="color:var(--j-primary);" role="status"></div>
                    <span class="text-muted ml-2 small">Loading more…</span>
                @endif
            </div>
            <input type="hidden" id="products-next-page" value="{{ $products->hasMorePages() ? $products->currentPage()+1 : '' }}">
            <input type="hidden" id="products-has-more" value="{{ $products->hasMorePages() ? 1 : 0 }}">

            @if($products->hasPages())
            <nav class="mt-2"><ul class="pagination justify-content-center">
                <li class="page-item {{ $products->onFirstPage()?'disabled':'' }}">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}">‹</a></li>
                @foreach($products->getUrlRange(1,$products->lastPage()) as $page=>$url)
                <li class="page-item {{ $products->currentPage()==$page?'active':'' }}">
                    <a class="page-link" href="{{ $url }}"
                       style="{{ $products->currentPage()==$page?'background:var(--j-primary);border-color:var(--j-primary);':'' }}">{{ $page }}</a></li>
                @endforeach
                <li class="page-item {{ $products->hasMorePages()?'':'disabled' }}">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}">›</a></li>
            </ul></nav>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var sentinel=document.getElementById('products-scroll-sentinel'),
        container=document.getElementById('products-container'),
        nextPageInput=document.getElementById('products-next-page'),
        hasMoreInput=document.getElementById('products-has-more'),
        loading=false;
    if(!sentinel||!container) return;
    var base='{{ url("/products/ajax") }}';
    function loadMore(){
        if(String(hasMoreInput.value)!=='1'||loading) return;
        var nextPage=parseInt(nextPageInput.value,10); if(!nextPage) return;
        loading=true;
        var params=new URL(window.location.href).searchParams;
        params.set('page',String(nextPage));
        fetch(base+'?'+params.toString(),{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(function(r){return r.json();}).then(function(data){
            if(data&&data.html) container.insertAdjacentHTML('beforeend',data.html);
            hasMoreInput.value=data.hasMore?'1':'0';
            nextPageInput.value=data.nextPage?String(data.nextPage):'';
            if(!data.hasMore) sentinel.innerHTML='';
        }).catch(function(e){console.error(e);}).finally(function(){loading=false;});
    }
    new IntersectionObserver(function(e){if(e.some(function(x){return x.isIntersecting;}))loadMore();},{rootMargin:'200px'}).observe(sentinel);
})();
</script>
@endpush
@endsection
