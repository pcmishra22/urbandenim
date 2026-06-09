@extends('layouts.eshopper')

@section('title', 'Shop - Jeanzo')

@section('content')

@include('front.partials.page-banner', ['title' => 'shop', 'breadcrumb' => 'Shop'])

    <!-- Shop Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">

            <!-- Sidebar Start -->
            <div class="col-lg-3 col-md-12">

                <!-- Categories -->
                <div class="border-bottom mb-4 pb-4">
                    <h5 class="font-weight-semi-bold mb-4">Filter by Category</h5>
                    <div class="d-flex flex-column">
                        <a href="{{ route('products.index') }}" class="mb-2 {{ !request('category') ? 'text-primary font-weight-bold' : 'text-dark' }}">
                            All Categories
                        </a>
                        @foreach(\App\Models\Category::where('is_active',true)->withCount('products')->get() as $cat)
                        <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
                           class="d-flex justify-content-between mb-2 {{ request('category') == $cat->id ? 'text-primary font-weight-bold' : 'text-dark' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="badge border font-weight-normal">{{ $cat->products_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="border-bottom mb-4 pb-4">
                    <h5 class="font-weight-semi-bold mb-4">Filter by price</h5>
                    <form method="GET" action="{{ route('products.index') }}">
                        @foreach(request()->except(['price_min','price_max','page']) as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        @php
                            $priceRanges = [
                                '' => ['label' => 'All Price', 'min' => '', 'max' => ''],
                                '0-500' => ['label' => '₹0 - ₹500', 'min' => 0, 'max' => 500],
                                '500-1000' => ['label' => '₹500 - ₹1000', 'min' => 500, 'max' => 1000],
                                '1000-2000' => ['label' => '₹1000 - ₹2000', 'min' => 1000, 'max' => 2000],
                                '2000-5000' => ['label' => '₹2000 - ₹5000', 'min' => 2000, 'max' => 5000],
                                '5000+' => ['label' => '₹5000+', 'min' => 5000, 'max' => ''],
                            ];
                            $currentRange = request('price_min','') . '-' . request('price_max','');
                        @endphp
                        @foreach($priceRanges as $key => $range)
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="radio" class="custom-control-input" id="price-{{ $loop->index }}"
                                   name="price_range" value="{{ $key }}"
                                   {{ (request('price_range', '') === $key) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="custom-control-label" for="price-{{ $loop->index }}">{{ $range['label'] }}</label>
                        </div>
                        @endforeach
                    </form>
                </div>

                <!-- Brand Filter -->
                @php $brands = \App\Models\Brand::has('products')->take(8)->get(); @endphp
                @if($brands->isNotEmpty())
                <div class="mb-5">
                    <h5 class="font-weight-semi-bold mb-4">Filter by Brand</h5>
                    <div class="d-flex flex-column">
                        @foreach($brands as $brand)
                        <a href="{{ route('products.index', array_merge(request()->except(['brand','page']), ['brand' => $brand->id])) }}"
                           class="mb-2 {{ request('brand') == $brand->id ? 'text-primary font-weight-bold' : 'text-dark' }}">
                            {{ $brand->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <!-- Sidebar End -->

            <!-- Products Start -->
            <div class="col-lg-9 col-md-12">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <form action="{{ route('products.index') }}" method="GET" class="flex-grow-1 mr-3">
                                @foreach(request()->except(['search','page']) as $k => $v)
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endforeach
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-transparent text-primary" style="cursor:pointer;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="dropdown ml-4">
                                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                                    Sort by: {{ request('sort','Latest') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}">Latest</a>
                                    <a class="dropdown-item" href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}">Price: Low to High</a>
                                    <a class="dropdown-item" href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}">Price: High to Low</a>
                                    <a class="dropdown-item" href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => 'name_asc'])) }}">Name A-Z</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Products list (initial) + Infinite Scroll appends here --}}
                    <div class="row pb-3" id="products-container">
                        @foreach($products as $product)
                            @include('front.partials.product-card-grid', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Sentinel for IntersectionObserver --}}
                    <div class="col-12" id="products-scroll-sentinel">
                        @if($products->hasMorePages())
                            <div class="text-center py-3 text-muted">
                                Loading more...
                            </div>
                        @endif
                    </div>

                    <input type="hidden" id="products-next-page" value="{{ $products->hasMorePages() ? $products->currentPage()+1 : '' }}">
                    <input type="hidden" id="products-has-more" value="{{ $products->hasMorePages() ? 1 : 0 }}">
                    <input type="hidden" id="products-page-size" value="24">

                    {{-- Pagination fallback (optional): still render for non-JS users --}}
                    @if($products->hasPages())
                        <div class="col-12 pb-1">
                            <nav>
                                <ul class="pagination justify-content-center mb-3">
                                    {{-- Previous --}}
                                    <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $products->previousPageUrl() }}">Previous</a>
                                    </li>
                                    @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                    <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                    @endforeach
                                    <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $products->nextPageUrl() }}">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif

                </div>
            </div>
            <!-- Products End -->

        </div>
    </div>
    <!-- Shop End -->

@push('scripts')
<script>
(function(){
    const sentinel = document.getElementById('products-scroll-sentinel');
    const container = document.getElementById('products-container');
    const nextPageInput = document.getElementById('products-next-page');
    const hasMoreInput = document.getElementById('products-has-more');

    if(!sentinel || !container || !nextPageInput || !hasMoreInput) return;

    let loading = false;

    const buildAjaxUrl = (page) => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        return url.pathname.replace(/\/$/, '') + '/ajax?' + url.searchParams.toString().replace('page=' + page, 'page=' + page);
    };

    // Create stable endpoint base: /products/ajax
    const ajaxEndpointBase = '{{ url('/products/ajax') }}';

    const getFiltersParams = () => {
        const url = new URL(window.location.href);
        // keep everything except page (we'll set it explicitly)
        url.searchParams.delete('page');
        return url.searchParams;
    };

    const loadMore = async () => {
        const hasMore = String(hasMoreInput.value) === '1';
        if(!hasMore || loading) return;

        const nextPage = parseInt(nextPageInput.value, 10);
        if(!nextPage) return;

        loading = true;

        try {
            const params = getFiltersParams();
            params.set('page', String(nextPage));

            const res = await fetch(ajaxEndpointBase + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if(!res.ok) throw new Error('Request failed: ' + res.status);
            const data = await res.json();

            if(data && data.html){
                container.insertAdjacentHTML('beforeend', data.html);
            }

            hasMoreInput.value = data.hasMore ? '1' : '0';
            nextPageInput.value = data.nextPage ? String(data.nextPage) : '';

            if(!data.hasMore){
                const spinner = sentinel.querySelector('.text-muted');
                if(spinner) spinner.remove();
            }
        } catch (e) {
            console.error(e);
        } finally {
            loading = false;
        }
    };

    const observer = new IntersectionObserver((entries) => {
        if(entries.some(e => e.isIntersecting)){
            loadMore();
        }
    }, { root: null, rootMargin: '200px', threshold: 0 });

    observer.observe(sentinel);
})();
</script>
@endpush

@endsection

