@extends('layouts.eshopper')

@section('title', 'EShopper - Home')

@section('navbar-extra')
<div id="header-carousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        @php $banners = \App\Models\Banner::where('is_active', true)->orderBy('sort_order')->take(3)->get(); @endphp
        @if($banners->isNotEmpty())
            @foreach($banners as $i => $banner)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" style="height: 410px;">
                <img class="img-fluid w-100 h-100" style="object-fit:cover;" src="{{ $banner->image_url ? asset('storage/' . $banner->image_url) : asset('eshopper/img/carousel-1.jpg') }}" alt="{{ $banner->title }}">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        @if($banner->subtitle)<h4 class="text-light text-uppercase font-weight-medium mb-3">{{ $banner->subtitle }}</h4>@endif
                        <h3 class="display-4 text-white font-weight-semi-bold mb-4">{{ $banner->title }}</h3>
                        @if($banner->link_url)<a href="{{ $banner->link_url }}" class="btn btn-light py-2 px-3">Shop Now</a>@endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="carousel-item active" style="height: 410px;">
            <img class="img-fluid w-100 h-100" style="object-fit:cover;" src="{{ asset('eshopper/img/carousel-1.jpg') }}" alt="">
            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="p-3" style="max-width: 700px;">
                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% Off Your First Order</h4>
                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Fashionable Dress</h3>
                    <a href="{{ route('products.index') }}" class="btn btn-light py-2 px-3">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="carousel-item" style="height: 410px;">
            <img class="img-fluid w-100 h-100" style="object-fit:cover;" src="{{ asset('eshopper/img/carousel-2.jpg') }}" alt="">
            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="p-3" style="max-width: 700px;">
                    <h4 class="text-light text-uppercase font-weight-medium mb-3">10% Off Your First Order</h4>
                    <h3 class="display-4 text-white font-weight-semi-bold mb-4">Reasonable Price</h3>
                    <a href="{{ route('products.index') }}" class="btn btn-light py-2 px-3">Shop Now</a>
                </div>
            </div>
        </div>
        @endif
    </div>
    <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
        <div class="btn btn-dark" style="width: 45px; height: 45px;"><span class="carousel-control-prev-icon mb-n2"></span></div>
    </a>
    <a class="carousel-control-next" href="#header-carousel" data-slide="next">
        <div class="btn btn-dark" style="width: 45px; height: 45px;"><span class="carousel-control-next-icon mb-n2"></span></div>
    </a>
</div>
@endsection

@section('content')

    <!-- Featured Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 pb-3">
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                    <h1 class="fa fa-check text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0">Quality Product</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                    <h1 class="fa fa-shipping-fast text-primary m-0 mr-2"></h1>
                    <h5 class="font-weight-semi-bold m-0">Free Shipping</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                    <h1 class="fas fa-exchange-alt text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0">14-Day Return</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="d-flex align-items-center border mb-4" style="padding: 30px;">
                    <h1 class="fa fa-phone-volume text-primary m-0 mr-3"></h1>
                    <h5 class="font-weight-semi-bold m-0">24/7 Support</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Featured End -->

    <!-- Categories Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 pb-3">
            @php $categories = \App\Models\Category::where('is_active', true)->withCount('products')->take(6)->get(); @endphp
            @forelse($categories as $category)
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">{{ $category->products_count }} Products</p>
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="cat-img position-relative overflow-hidden mb-3">
                        @if($category->image)
                            <img class="img-fluid" src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                        @else
                            <img class="img-fluid" src="{{ asset('eshopper/img/cat-1.jpg') }}" alt="{{ $category->name }}">
                        @endif
                    </a>
                    <h5 class="font-weight-semi-bold m-0">{{ $category->name }}</h5>
                </div>
            </div>
            @empty
            @foreach(['cat-1','cat-2','cat-3','cat-4','cat-5','cat-6'] as $i => $img)
            <div class="col-lg-4 col-md-6 pb-1">
                <div class="cat-item d-flex flex-column border mb-4" style="padding: 30px;">
                    <p class="text-right">15 Products</p>
                    <a href="{{ route('products.index') }}" class="cat-img position-relative overflow-hidden mb-3">
                        <img class="img-fluid" src="{{ asset('eshopper/img/' . $img . '.jpg') }}" alt="Category">
                    </a>
                    <h5 class="font-weight-semi-bold m-0">Category {{ $i + 1 }}</h5>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
    <!-- Categories End -->

    <!-- Offer Start -->
    <div class="container-fluid offer pt-5">
        <div class="row px-xl-5">
            <div class="col-md-6 pb-4">
                <div class="position-relative bg-secondary text-center text-md-right text-white mb-2 py-5 px-5">
                    <img src="{{ asset('eshopper/img/offer-1.png') }}" alt="">
                    <div class="position-relative" style="z-index: 1;">
                        <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                        <h1 class="mb-4 font-weight-semi-bold">Spring Collection</h1>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pb-4">
                <div class="position-relative bg-secondary text-center text-md-left text-white mb-2 py-5 px-5">
                    <img src="{{ asset('eshopper/img/offer-2.png') }}" alt="">
                    <div class="position-relative" style="z-index: 1;">
                        <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                        <h1 class="mb-4 font-weight-semi-bold">Winter Collection</h1>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Offer End -->

    <!-- Trending Products Start -->
    <div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Trending Products</span></h2>
        </div>
        <div class="row px-xl-5 pb-3">
            @php $featuredProducts = \App\Models\Product::with('images')->where('is_active', true)->where('is_featured', true)->take(8)->get(); @endphp
            @forelse($featuredProducts as $product)
            @include('front.partials.product-card', ['product' => $product])
            @empty
            @for($i = 1; $i <= 8; $i++)
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                        <img class="img-fluid w-100" src="{{ asset('eshopper/img/product-' . $i . '.jpg') }}" alt="">
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">Colorful Stylish Shirt</h6>
                        <div class="d-flex justify-content-center">
                            <h6>$123.00</h6>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light border">
                        <a href="{{ route('products.index') }}" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                        <a href="{{ route('products.index') }}" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                    </div>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
    <!-- Products End -->

    <!-- Subscribe Start -->
    <div class="container-fluid bg-secondary my-5">
        <div class="row justify-content-md-center py-5 px-xl-5">
            <div class="col-md-6 col-12 py-5">
                <div class="text-center mb-2 pb-2">
                    <h2 class="section-title px-5 mb-3"><span class="bg-secondary px-2">Stay Updated</span></h2>
                    <p>Subscribe to our newsletter to get the latest deals, new arrivals, and exclusive offers.</p>
                </div>
                <form action="#">
                    <div class="input-group">
                        <input type="email" class="form-control border-white p-4" placeholder="Enter your email">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-4">Subscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Subscribe End -->

    <!-- Just Arrived Start -->
    <div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Just Arrived</span></h2>
        </div>
        <div class="row px-xl-5 pb-3">
            @php $newProducts = \App\Models\Product::with('images')->where('is_active', true)->latest()->take(8)->get(); @endphp
            @forelse($newProducts as $product)
            @include('front.partials.product-card', ['product' => $product])
            @empty
            @for($i = 1; $i <= 8; $i++)
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                        <img class="img-fluid w-100" src="{{ asset('eshopper/img/product-' . $i . '.jpg') }}" alt="">
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">Colorful Stylish Shirt</h6>
                        <div class="d-flex justify-content-center">
                            <h6>$123.00</h6>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light border">
                        <a href="{{ route('products.index') }}" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a>
                        <a href="{{ route('products.index') }}" class="btn btn-sm text-dark p-0"><i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart</a>
                    </div>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
    <!-- Just Arrived End -->

    <!-- Vendors Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">
            <div class="col">
                <div class="owl-carousel vendor-carousel">
                    @foreach(range(1,8) as $i)
                    <div class="vendor-item border p-4">
                        <img src="{{ asset('eshopper/img/vendor-' . $i . '.jpg') }}" alt="">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Vendors End -->

@endsection
