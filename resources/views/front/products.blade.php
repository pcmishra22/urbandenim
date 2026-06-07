@extends('layouts.eshopper')

@section('title', 'Shop - EShopper')

@section('content')

    @include('front.partials.page-banner', ['title' => 'Our Shop', 'breadcrumb' => 'Shop'])

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

                    @forelse($products as $product)
                    <div class="col-lg-4 col-md-6 col-sm-12 pb-1">
                        <div class="card product-item border-0 mb-4">
                            <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            @php
                            $img = $product->images->first() ?? null;
                            $relativePath = $img ? 'products/' . $product->id . '/images/' . ($img->image ?? '') : '';
                            $publicUrl    = $relativePath ? asset('storage/' . $relativePath) : asset('eshopper/img/product-1.jpg');
                            $fallbackUrl  = asset('storage/default.jpeg');
                            $imgSrc       = ($relativePath && file_exists(public_path('storage/' . $relativePath))) ? $publicUrl : ($product->images->isNotEmpty() ? $fallbackUrl : asset('eshopper/img/product-1.jpg'));
                            $detailUrl    = route('products.detail', $product->slug);
                            $reviewCount  = $product->reviews_count ?? 0;
                            $avgRating    = round($product->reviews_avg_rating ?? 0, 1);
                        @endphp
                        <a href="{{ $detailUrl }}">
                            <img class="img-fluid w-100" src="{{ $imgSrc }}" alt="{{ $product->name }}">
                        </a>
                            </div>
                            <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                                <a href="{{ $detailUrl }}" class="text-dark text-decoration-none">
                                    <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
                                </a>
                                <div class="d-flex justify-content-center">
                                    <h6>₹{{ number_format($product->sale_price ?? $product->price, 2) }}</h6>
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <h6 class="text-muted ml-2"><del>₹{{ number_format($product->price, 2) }}</del></h6>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center bg-light border">
                                <div class="text-warning" style="font-size:13px;letter-spacing:1px;">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($avgRating >= $s)<i class="fas fa-star"></i>
                                        @elseif($avgRating >= $s - 0.5)<i class="fas fa-star-half-alt"></i>
                                        @else<i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    @if($reviewCount > 0)
                                        <a href="{{ $detailUrl }}#reviews" class="text-muted text-decoration-none">{{ $reviewCount }} {{ \Str::plural('review', $reviewCount) }}</a>
                                    @else
                                        No reviews yet
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Clear Filters</a>
                    </div>
                    @endforelse

                    <!-- Pagination -->
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

@endsection
