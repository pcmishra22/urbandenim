@extends('layouts.eshopper')

@section('title', 'Shop - Jeanzo')

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'shop', 'breadcrumb' => 'Shop', 'showCategories' => false])

<div class="container-fluid px-xl-5 py-4" style="background:#faf8f8;">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="j-section" style="position:sticky;top:80px;">
                <!-- Categories -->
                <div class="j-section-title">Category</div>
                <div class="d-flex flex-column mb-4">
                    <a href="{{ route('products.index') }}"
                       class="px-2 py-2 rounded mb-1 {{ !request('category') ? 'font-weight-bold' : 'text-dark' }}"
                       style="{{ !request('category') ? 'background:var(--j-primary-lt);color:var(--j-primary);' : '' }}">All</a>
                    @foreach(\App\Models\Category::where('is_active',true)->withCount('products')->get() as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
                       class="px-2 py-2 rounded mb-1 d-flex justify-content-between {{ request('category')==$cat->id ? 'font-weight-bold' : 'text-dark' }}"
                       style="{{ request('category')==$cat->id ? 'background:var(--j-primary-lt);color:var(--j-primary);' : '' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="j-badge" style="background:#eee;color:#666;font-size:.7rem;">{{ $cat->products_count }}</span>
                    </a>
                    @endforeach
                </div>

                <!-- Price -->
                <div class="j-section-title">Price</div>
                <form method="GET" action="{{ route('products.index') }}" class="mb-4">
                    @foreach(request()->except(['price_range','page']) as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                    @foreach([''=> 'All Prices','0-500'=>'₹0 – ₹500','500-1000'=>'₹500 – ₹1000','1000-2000'=>'₹1000 – ₹2000','2000-5000'=>'₹2000 – ₹5000','5000+'=>'₹5000+'] as $key=>$label)
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" class="custom-control-input" id="pr{{ $loop->index }}" name="price_range" value="{{ $key }}"
                               {{ request('price_range','')===$key ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="custom-control-label" for="pr{{ $loop->index }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </form>

                <!-- Brand -->
                @php $brands = \App\Models\Brand::has('products')->take(10)->get(); @endphp
                @if($brands->isNotEmpty())
                <div class="j-section-title">Brand</div>
                <div class="d-flex flex-column">
                    @foreach($brands as $brand)
                    <a href="{{ route('products.index', array_merge(request()->except(['brand','page']),['brand'=>$brand->id])) }}"
                       class="px-2 py-2 rounded mb-1 {{ request('brand')==$brand->id ? 'font-weight-bold' : 'text-dark' }}"
                       style="{{ request('brand')==$brand->id ? 'background:var(--j-primary-lt);color:var(--j-primary);' : '' }}">
                        {{ $brand->name }}
                    </a>
                    @endforeach
                </div>
                @endif
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
            @if(request()->hasAny(['search','category','brand','price_range']))
            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <small class="text-muted mr-1">Filters:</small>
                @if(request('search'))<span class="j-badge" style="background:#eee;color:#333;">Search: {{ request('search') }}</span>@endif
                @if(request('price_range'))<span class="j-badge" style="background:#eee;color:#333;">Price: {{ request('price_range') }}</span>@endif
                <a href="{{ route('products.index', request()->except(['search','category','brand','price_range','page'])) }}" class="j-badge" style="background:#fde;color:#c00;">✕ Clear all</a>
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
