@extends('layouts.eshopper')

@section('title', $product->name . ' - EShopper')

@section('content')
    @include('front.partials.page-banner', ['title' => 'Shop Detail', 'breadcrumb' => 'Shop Detail'])
</div>
    </div>
    <!-- Page Header End -->

    <!-- Shop Detail Start -->
    <div class="container-fluid py-5">
        <div class="row px-xl-5">

            <!-- Product Images -->
            <div class="col-lg-5 pb-5">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner border">
@if($product->images && $product->images->isNotEmpty())
                            @foreach($product->images as $i => $image)
                                @php
                                    $filename = $image->image ?? '';
                                    $relativePath = 'products/' . $product->id . '/images/' . $filename;
                                    $publicUrl = asset('storage/' . $relativePath);
                                    $fallbackUrl = asset('storage/default.jpeg');
                                @endphp
                                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                    <img class="w-100" style="height:400px;object-fit:contain;" src="{{ file_exists(public_path('storage/' . $relativePath)) ? $publicUrl : $fallbackUrl }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img class="w-100" style="height:400px;object-fit:contain;" src="{{ asset('storage/default.jpeg') }}" alt="{{ $product->name }}">
                            </div>
                        @endif
                    </div>
                    @if($product->images && $product->images->count() > 1)
                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                    </a>
                    @endif
                </div>
                <!-- Thumbnails -->
                @if($product->images && $product->images->count() > 1)
                <div class="d-flex mt-3">
                    @foreach($product->images->take(5) as $i => $image)
                        @php
                            $filename = $image->image ?? '';
                            $relativePath = 'products/' . $product->id . '/images/' . $filename;
                            $publicUrl = asset('storage/' . $relativePath);
                            $fallbackUrl = asset('storage/default.jpeg');
                        @endphp
                        <a href="#product-carousel" data-target="#product-carousel" data-slide-to="{{ $i }}" class="mr-2">
                            <img class="border" style="width:60px;height:60px;object-fit:cover;" src="{{ file_exists(public_path('storage/' . $relativePath)) ? $publicUrl : $fallbackUrl }}" alt="">
                        </a>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="col-lg-7 pb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <h3 class="font-weight-semi-bold">{{ $product->name }}</h3>

                <!-- Rating -->
                @php $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0; $reviewCount = $product->reviews ? $product->reviews->count() : 0; @endphp
                <div class="d-flex mb-3">
                    <div class="text-primary mr-2">
                        @for($s = 1; $s <= 5; $s++)
                            @if($s <= floor($avgRating))
                                <small class="fas fa-star"></small>
                            @elseif($s - 0.5 <= $avgRating)
                                <small class="fas fa-star-half-alt"></small>
                            @else
                                <small class="far fa-star"></small>
                            @endif
                        @endfor
                    </div>
                    <small class="pt-1">({{ $reviewCount }} Reviews)</small>
                </div>

                <!-- Price -->
                <div class="d-flex mb-3">
                    <h3 class="font-weight-semi-bold mb-0">₹{{ number_format($product->sale_price ?? $product->price, 2) }}</h3>
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <h5 class="text-muted ml-3 mt-1"><del>₹{{ number_format($product->price, 2) }}</del></h5>
                        <span class="badge badge-primary ml-2 align-self-center">
                            -{{ round((1 - $product->sale_price / $product->price) * 100) }}%
                        </span>
                    @endif
                </div>

                <p class="mb-4">{{ $product->description }}</p>

                <!-- Category & Brand -->
                <div class="mb-2">
                    @if($product->category)
                        <span class="text-dark font-weight-medium">Category:</span>
                        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="ml-1">{{ $product->category->name }}</a>
                    @endif
                </div>
                @if($product->brand)
                <div class="mb-4">
                    <span class="text-dark font-weight-medium">Brand:</span>
                    <span class="ml-1">{{ $product->brand->name }}</span>
                </div>
                @endif

                <!-- Variants -->
                @if($product->variants->isNotEmpty())
                <div class="d-flex mb-3">
                    <p class="text-dark font-weight-medium mb-0 mr-3">Select Size:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->variants as $variant)
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input variant-radio" 
                                   id="variant-{{ $variant->id }}" 
                                   name="variant_id" 
                                   value="{{ $variant->id }}" 
                                   form="add-to-cart-form" required>
                            <label class="custom-control-label" for="variant-{{ $variant->id }}">
                                {{ $variant->waist_size }} {{ $variant->color ? '- ' . $variant->color : '' }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add to Cart -->
                <div class="d-flex align-items-center mb-4 pt-2">
                    <form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form" class="d-flex align-items-center">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="input-group quantity mr-3" style="width: 130px;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-minus"><i class="fa fa-minus"></i></button>
                            </div>
                            <input type="text" name="quantity" class="form-control bg-secondary text-center" value="1" min="1">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-plus"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="fa fa-shopping-cart mr-1"></i> Add To Cart
                        </button>
                    </form>
                    @auth
                    <form method="POST" action="{{ route('wishlist.add') }}" class="ml-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-outline-primary px-3 text-dark">
                            <i class="far fa-heart mr-1"></i> Wishlist
                        </button>
                    </form>
                    @endauth
                </div>

                <!-- Share -->
                <div class="d-flex pt-2">
                    <p class="text-dark font-weight-medium mb-0 mr-2">Share on:</p>
                    <div class="d-inline-flex">
                        <a class="text-dark px-2" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a class="text-dark px-2" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($product->name) }}" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a class="text-dark px-2" href="https://www.pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}" target="_blank"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs: Description / Info / Reviews -->
        <div class="row px-xl-5">
            <div class="col">
                <div class="nav nav-tabs justify-content-center border-secondary mb-4">
                    <a class="nav-item nav-link active" data-toggle="tab" href="#tab-description">Description</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#tab-info">Information</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#tab-reviews">
                        Reviews ({{ $reviewCount }})
                    </a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-description">
                        <h4 class="mb-3">Product Description</h4>
                        <p>{{ $product->description ?? 'No description available.' }}</p>
                    </div>
                    <div class="tab-pane fade" id="tab-info">
                        <h4 class="mb-3">Additional Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    @if($product->sku)<li class="list-group-item px-0">SKU: {{ $product->sku }}</li>@endif
                                    @if($product->category)<li class="list-group-item px-0">Category: {{ $product->category->name }}</li>@endif
                                    @if($product->brand)<li class="list-group-item px-0">Brand: {{ $product->brand->name }}</li>@endif
                                    @if($product->gender)<li class="list-group-item px-0">Gender: {{ ucfirst($product->gender) }}</li>@endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    @if($product->color_family)<li class="list-group-item px-0">Colour: {{ $product->color_family }}</li>@endif
                                    @if($product->age_group)<li class="list-group-item px-0">Age Group: {{ $product->age_group }}</li>@endif
                                    <li class="list-group-item px-0">Availability: {{ $product->quantity > 0 ? 'In Stock (' . $product->quantity . ')' : 'Out of Stock' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-reviews">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-4">{{ $reviewCount }} review{{ $reviewCount != 1 ? 's' : '' }} for "{{ $product->name }}"</h4>
                                @if($product->reviews && $product->reviews->isNotEmpty())
                                    @foreach($product->reviews->take(5) as $review)
                                    <div class="media mb-4">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3" style="width:45px;height:45px;min-width:45px;">
                                            <i class="fa fa-user text-white"></i>
                                        </div>
                                        <div class="media-body">
                                            <h6>{{ $review->user->name ?? 'Anonymous' }}<small> - <i>{{ $review->created_at->format('d M Y') }}</i></small></h6>
                                            <div class="text-primary mb-2">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i class="{{ $s <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <p>{{ $review->review_text }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-4">Leave a review</h4>
                                @auth
                                <form method="POST" action="{{ route('products.review', $product->id) ?? '#' }}">
                                    @csrf
                                    <div class="d-flex my-3">
                                        <p class="mb-0 mr-2">Your Rating * :</p>
                                        <div class="text-primary" id="star-rating">
                                            @for($s = 1; $s <= 5; $s++)
                                            <i class="far fa-star star-btn" data-val="{{ $s }}" style="cursor:pointer;font-size:1.2rem;"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="rating-val" value="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Your Review *</label>
                                        <textarea name="review_text" rows="5" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-primary px-3">Leave Your Review</button>
                                    </div>
                                </form>
                                @else
                                <div class="alert alert-info">
                                    <a href="{{ route('customer.login') }}">Login</a> to leave a review.
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
        <div class="row px-xl-5 mt-5">
            <div class="col-12 text-center mb-4">
                <h2 class="section-title px-5"><span class="px-2">Related Products</span></h2>
            </div>
            @foreach($relatedProducts as $related)
            @include('front.partials.product-card', ['product' => $related])
            @endforeach
        </div>
        @endif
    </div>
    <!-- Shop Detail End -->

@endsection

@push('scripts')
<script>
// Quantity +/-
$(document).on('click', '.btn-plus', function() {
    var $input = $(this).closest('.quantity').find('input');
    var val = parseInt($input.val()) || 1;
    $input.val(val + 1);
});
$(document).on('click', '.btn-minus', function() {
    var $input = $(this).closest('.quantity').find('input');
    var val = parseInt($input.val()) || 1;
    if (val > 1) $input.val(val - 1);
});
// Star rating
$('.star-btn').on('click', function() {
    var val = $(this).data('val');
    $('#rating-val').val(val);
    $('.star-btn').each(function() {
        $(this).toggleClass('fas', $(this).data('val') <= val)
               .toggleClass('far', $(this).data('val') > val);
    });
});
</script>
@endpush
