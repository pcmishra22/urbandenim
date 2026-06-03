@extends('layouts.eshopper')

@section('title', 'My Wishlist - EShopper')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">My Wishlist</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Wishlist</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Wishlist Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">

            @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
            @endif

            @if($wishlistItems->isEmpty())
            <div class="col-12 text-center py-5">
                <i class="far fa-heart fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Your wishlist is empty</h4>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-3 px-5">Browse Products</a>
            </div>
            @else
            @foreach($wishlistItems as $item)
            @php $product = $item->product; @endphp
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                        @if($product && $product->images && $product->images->isNotEmpty())
                            <img class="img-fluid w-100" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
                        @else
                            <img class="img-fluid w-100" src="{{ asset('eshopper/img/product-1.jpg') }}" alt="">
                        @endif
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">{{ $product->name ?? 'Product' }}</h6>
                        <div class="d-flex justify-content-center">
                            <h6>₹{{ number_format($product->sale_price ?? $product->price ?? 0, 2) }}</h6>
                            @if($product && $product->sale_price && $product->sale_price < $product->price)
                                <h6 class="text-muted ml-2"><del>₹{{ number_format($product->price, 2) }}</del></h6>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-light border p-0">
                        <div class="d-flex">
                            <form method="POST" action="{{ route('wishlist.move-to-cart') }}" class="flex-grow-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-sm text-dark w-100 border-right p-2">
                                    <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                                </button>
                            </form>
                            <form method="POST" action="{{ route('wishlist.remove') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-sm text-dark p-2">
                                    <i class="fas fa-times text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($wishlistItems->hasPages())
            <div class="col-12 mt-2">
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item {{ $wishlistItems->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $wishlistItems->previousPageUrl() }}">Previous</a>
                        </li>
                        @foreach($wishlistItems->getUrlRange(1, $wishlistItems->lastPage()) as $page => $url)
                        <li class="page-item {{ $wishlistItems->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                        @endforeach
                        <li class="page-item {{ $wishlistItems->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $wishlistItems->nextPageUrl() }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
            @endif

        </div>
    </div>
    <!-- Wishlist End -->

@endsection
