@extends('layouts.eshopper')

@section('content')
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">My Wishlist</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="{{ route('profile.dashboard') }}">My Account</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Wishlist</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">
                <h5 class="font-weight-semi-bold mb-4">My Wishlist ({{ $wishlistItems->total() }} items)</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @if($wishlistItems->isEmpty())
                    <div class="text-center py-5 bg-light">
                        <i class="fa fa-heart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Your wishlist is empty</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Browse Products</a>
                    </div>
                @else
                    <div class="row">
                        @foreach($wishlistItems as $item)
                            @php $product = $item->product; @endphp
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="product-item bg-light">
                                    <div class="product-img position-relative overflow-hidden">
                                        @if($product->images && $product->images->count() > 0)
                                            <img class="img-fluid w-100" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}">
                                        @else
                                            <img class="img-fluid w-100" src="{{ asset('eshopper/img/product-1.jpg') }}" alt="{{ $product->name }}">
                                        @endif
                                        <div class="product-action">
                                            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-outline-dark btn-square"><i class="fa fa-shopping-cart"></i></button>
                                            </form>
                                            <form method="POST" action="{{ route('wishlist.remove') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <button type="submit" class="btn btn-outline-dark btn-square"><i class="fa fa-times"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-center py-4">
                                        <a class="h6 text-decoration-none text-truncate" href="{{ route('products.detail', $product->slug) }}">{{ $product->name }}</a>
                                        <div class="d-flex align-items-center justify-content-center mt-2">
                                            <h5>${{ number_format($product->price, 2) }}</h5>
                                            @if($product->compare_price && $product->compare_price > $product->price)
                                                <h6 class="text-muted ml-2"><del>${{ number_format($product->compare_price, 2) }}</del></h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $wishlistItems->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
