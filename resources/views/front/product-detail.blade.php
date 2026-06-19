@extends('layouts.eshopper')
@section('title', ($product->meta_title ?: $product->name) . ' - Jeanzo')
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 155))
@section('canonical', $product->canonical_url ?: route('products.detail', $product->slug))
@section('og_type', 'product')
@section('og_title', $product->meta_title ?: $product->name)
@section('og_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 155))
@section('og_image', $product->images->first() ? asset('storage/products/' . $product->id . '/images/' . $product->images->first()->image) : asset('eshopper/img/og-default.jpg'))

@push('json_ld')
@php
    $price      = number_format((float)$product->price, 2, '.', '');
    $inStock    = ($product->variants->isNotEmpty() || ($product->quantity ?? 0) > 0);
    $ratingVal  = round($product->reviews ? $product->reviews->where('is_approved', true)->avg('rating') : 0, 1);
    $ratingCnt  = $product->reviews ? $product->reviews->where('is_approved', true)->count() : 0;
    $imgUrl     = $product->images->first() ? asset('storage/products/' . $product->id . '/images/' . $product->images->first()->image) : asset('eshopper/img/og-default.jpg');
    $jsonld = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product->name,
        'description' => strip_tags($product->short_description ?? $product->description ?? ''),
        'image'       => $imgUrl,
        'sku'         => $product->sku ?? '',
        'brand'       => ['@type' => 'Brand', 'name' => optional($product->brand)->name ?? 'Jeanzo'],
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => route('products.detail', $product->slug),
            'priceCurrency' => 'INR',
            'price'         => $price,
            'availability'  => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'seller'        => ['@type' => 'Organization', 'name' => 'Jeanzo'],
        ],
    ];
    if ($ratingCnt > 0) {
        $jsonld['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $ratingVal,
            'reviewCount' => $ratingCnt,
        ];
    }
@endphp
<script type="application/ld+json">{{ json_encode($jsonld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</script>
@endpush

@section('content')
@include('front.partials.design-system')

@php $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0; $reviewCount = $product->reviews ? $product->reviews->count() : 0; @endphp

<div class="container-fluid px-xl-5 py-3" style="background:#faf8f8;">
    <div class="row">

        <!-- Images -->
        <div class="col-lg-5 mb-4">
            <div class="j-section p-2">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" style="border-radius:10px;overflow:hidden;background:#f5f5f5;">
                        @if($product->images && $product->images->isNotEmpty())
                            @foreach($product->images as $i => $image)
                            @php $rel='products/'.$product->id.'/images/'.($image->image??''); $url=asset('storage/'.$rel); $fb=asset('storage/default.jpeg'); @endphp
                            <div class="carousel-item {{ $i===0?'active':'' }}">
                                <img class="w-100" style="height:400px;object-fit:contain;" src="{{ file_exists(public_path('storage/'.$rel))?$url:$fb }}" alt="{{ $product->name }}">
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
                        <span style="background:var(--j-primary);border-radius:50%;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-angle-left text-white"></i></span>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <span style="background:var(--j-primary);border-radius:50%;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-angle-right text-white"></i></span>
                    </a>
                    @endif
                </div>
                @if($product->images && $product->images->count() > 1)
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    @foreach($product->images->take(5) as $i => $image)
                    @php $rel='products/'.$product->id.'/images/'.($image->image??''); $url=asset('storage/'.$rel); $fb=asset('storage/default.jpeg'); @endphp
                    <a href="#product-carousel" data-target="#product-carousel" data-slide-to="{{ $i }}"
                       style="border:2px solid transparent;border-radius:6px;overflow:hidden;transition:.2s;"
                       onmouseover="this.style.borderColor='var(--j-primary)'" onmouseout="this.style.borderColor='transparent'">
                        <img style="width:60px;height:60px;object-fit:cover;" src="{{ file_exists(public_path('storage/'.$rel))?$url:$fb }}" alt="">
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Info -->
        <div class="col-lg-7 mb-4">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <a href="{{ route('cart.index') }}" class="btn btn-sm btn-primary ml-2"><i class="fa fa-shopping-bag mr-1"></i>View Cart</a>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="j-section">
                <!-- Breadcrumb -->
                <nav class="mb-2" style="font-size:.82rem;">
                    <a href="{{ route('products.index') }}" style="color:var(--j-primary);">Shop</a>
                    @if($product->category) › <a href="{{ route('products.index',['category'=>$product->category_id]) }}" style="color:var(--j-primary);">{{ $product->category->name }}</a> @endif
                    › <span class="text-muted">{{ Str::limit($product->name,30) }}</span>
                </nav>

                <h2 class="font-weight-bold mb-2" style="font-size:1.4rem;color:#2d2d2d;">{{ $product->name }}</h2>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-2">
                        @for($s=1;$s<=5;$s++)
                        <i class="{{ $s<=floor($avgRating)?'fas':($s-0.5<=$avgRating?'fas fa-star-half-alt':'far') }} fa-star" style="color:#f39c12;font-size:.85rem;"></i>
                        @endfor
                    </div>
                    <small class="text-muted">{{ number_format($avgRating,1) }} ({{ $reviewCount }} reviews)</small>
                    @if($product->is_featured)<span class="j-badge ml-2" style="background:#fff3cd;color:#856404;"><i class="fa fa-star mr-1" style="font-size:.7rem;"></i>Featured</span>@endif
                </div>

                <!-- Price -->
                <div class="d-flex align-items-center mb-3">
                    <h3 class="font-weight-bold mb-0 mr-3" style="color:var(--j-primary);font-size:1.8rem;">
                        ₹{{ number_format($product->sale_price ?? $product->price, 2) }}
                    </h3>
                    @if($product->sale_price && $product->sale_price < $product->price)
                    <h5 class="text-muted mb-0 mr-2"><del>₹{{ number_format($product->price,2) }}</del></h5>
                    <span class="j-badge" style="background:#d4edda;color:#155724;font-size:.85rem;">
                        {{ round((1-$product->sale_price/$product->price)*100) }}% OFF
                    </span>
                    @endif
                </div>

                @if($product->short_description)
                <p class="text-muted mb-3" style="font-size:.9rem;line-height:1.6;">{{ $product->short_description }}</p>
                @endif

                <!-- Meta chips -->
                <div class="d-flex flex-wrap mb-4" style="gap:8px;">
                    @if($product->category)
                    <span class="j-badge" style="background:var(--j-primary-lt);color:var(--j-primary);">
                        <i class="fa fa-folder mr-1" style="font-size:.7rem;"></i>{{ $product->category->name }}
                    </span>
                    @endif
                    @if($product->brand)
                    <span class="j-badge" style="background:#eee;color:#555;">
                        <i class="fa fa-tag mr-1" style="font-size:.7rem;"></i>{{ $product->brand->name }}
                    </span>
                    @endif
                    @if($product->gender)
                    <span class="j-badge" style="background:#eee;color:#555;">{{ ucfirst($product->gender) }}</span>
                    @endif
                    @if($product->color_family)
                    <span class="j-badge" style="background:#eee;color:#555;">{{ ucfirst($product->color_family) }}</span>
                    @endif
                </div>

                <!-- Sold by -->
                @if($product->sold_by)
                <div class="mb-3 small text-muted"><i class="fa fa-store mr-1"></i>Sold by: <strong>{{ $product->sold_by }}</strong></div>
                @endif

                <!-- Variants -->
                @if($product->variants->isNotEmpty())
                <div class="mb-4">
                    <p class="font-weight-bold mb-2" style="font-size:1rem;color:#2d2d2d;letter-spacing:.3px;">
                        <i class="fa fa-ruler-horizontal mr-1" style="color:var(--j-primary);font-size:.85rem;"></i>
                        Select Size: <span id="selected-size-label" style="color:var(--j-primary);font-weight:800;"></span>
                    </p>
                    <style>
                    .size-btn {
                        display: inline-flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-width: 64px;
                        height: 52px;
                        padding: 0 14px;
                        border: 2px solid #d0d0d0;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: .9rem;
                        font-weight: 700;
                        transition: all .18s ease;
                        user-select: none;
                        background: #fff;
                        color: #333;
                        box-shadow: 0 1px 3px rgba(0,0,0,.06);
                        position: relative;
                    }
                    .size-btn:hover:not(.size-btn-oos) {
                        border-color: var(--j-primary);
                        background: var(--j-primary-lt);
                        color: var(--j-primary);
                        box-shadow: 0 2px 8px rgba(209,156,151,.25);
                    }
                    .size-btn.size-btn-selected {
                        border-color: var(--j-primary) !important;
                        background: var(--j-primary) !important;
                        color: #fff !important;
                        box-shadow: 0 3px 12px rgba(209,156,151,.45) !important;
                        transform: translateY(-1px);
                    }
                    .size-btn.size-btn-oos {
                        border-color: #e8e8e8;
                        background: #f8f8f8;
                        color: #bbb;
                        cursor: not-allowed;
                        text-decoration: line-through;
                    }
                    .size-btn .oos-tag {
                        font-size: .58rem;
                        font-weight: 600;
                        color: #e74c3c;
                        text-decoration: none;
                        line-height: 1;
                        margin-top: 2px;
                        letter-spacing: .3px;
                    }
                    .size-btn.size-btn-oos .oos-tag { color: #ccc; }
                    </style>
                    <div class="d-flex flex-wrap" style="gap:10px;">
                        @foreach($product->variants as $variant)
                        @php $isOos = $variant->quantity <= 0; @endphp
                        <button type="button"
                                class="size-btn {{ $isOos ? 'size-btn-oos' : '' }}"
                                data-variant-id="{{ $variant->id }}"
                                data-variant-label="{{ $variant->waist_size }}{{ $variant->length ? '×'.$variant->length : '' }}{{ $variant->color ? ' · '.$variant->color : '' }}"
                                data-variant-qty="{{ $variant->quantity }}"
                                data-variant-price="{{ $variant->price ?? $product->price }}"
                                {{ $isOos ? 'disabled' : '' }}>
                            <span>{{ $variant->waist_size }}</span>
                            @if($variant->length)<small style="font-size:.65rem;font-weight:500;line-height:1;">L{{ $variant->length }}</small>@endif
                            @if($isOos)<span class="oos-tag">Out of Stock</span>@endif
                        </button>
                        @endforeach
                    </div>
                    {{-- Hidden radio group for form submission --}}
                    @foreach($product->variants as $variant)
                    <input type="radio" name="variant_id" id="variant-{{ $variant->id }}"
                           value="{{ $variant->id }}" form="add-to-cart-form"
                           style="display:none;" {{ $variant->quantity <= 0 ? 'disabled' : '' }}>
                    @endforeach
                </div>
                @endif

                <!-- Add to cart -->
                <div class="mb-4">
                    @if($product->variants->isNotEmpty())
                        <div id="select-size-message" class="mb-3 align-items-center"
                             style="background:#fff3cd;border:1.5px solid #ffc107;border-radius:8px;padding:8px 14px;font-size:.88rem;font-weight:600;color:#856404;display:none;">
                            <i class="fa fa-exclamation-triangle mr-2" style="color:#e67e22;"></i> Please select a size before adding to cart
                        </div>
                    @endif
                    <form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                            {{-- Qty stepper --}}
                            <div class="input-group quantity" style="width:120px;flex-shrink:0;">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-sm btn-minus"><i class="fa fa-minus"></i></button>
                                </div>
                                <input type="text" name="quantity" class="form-control form-control-sm text-center bg-light" value="1" min="1">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-sm btn-plus"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            {{-- Add to cart --}}
                            <button type="submit" class="btn btn-primary" style="min-width:140px;" id="add-to-cart-button" {{ $product->variants->isNotEmpty() ? 'disabled' : '' }}>
                                <i class="fa fa-cart-plus mr-1"></i> Add to Cart
                            </button>

                        </div>
                    </form>

                    {{-- Go to Cart + Wishlist on separate row --}}

                    <div class="d-flex align-items-center mt-3" style="gap:10px;flex-wrap:wrap;">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary" style="min-width:130px;">
                            <i class="fa fa-shopping-bag mr-1"></i> View Cart
                        </a>
                        @auth
                        <form method="POST" action="{{ route('wishlist.add') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-secondary" style="min-width:50px;" title="Add to Wishlist">
                                <i class="far fa-heart" style="color:var(--j-primary);"></i> Wishlist
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>

                <!-- ── Trust Badges ── -->
                <div class="mt-3 pt-3 border-top">
                    <div class="row text-center" style="row-gap:10px;">
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-lock fa-lg mb-1" style="color:#27ae60;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">100% Secure</div>
                                <div style="font-size:.65rem;color:#777;">Payments</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-undo fa-lg mb-1" style="color:#e67e22;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">Easy Returns</div>
                                <div style="font-size:.65rem;color:#777;">Within 7 Days</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-truck fa-lg mb-1" style="color:#2980b9;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">Free Shipping</div>
                                <div style="font-size:.65rem;color:#777;">Above ₹999</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-money-bill-wave fa-lg mb-1" style="color:#8e44ad;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">Cash on</div>
                                <div style="font-size:.65rem;color:#777;">Delivery</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-star fa-lg mb-1" style="color:#f39c12;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">Premium</div>
                                <div style="font-size:.65rem;color:#777;">Quality Denim</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-map-marker-alt fa-lg mb-1" style="color:#c0392b;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">Made in</div>
                                <div style="font-size:.65rem;color:#777;">India 🇮🇳</div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Share -->
                <div class="d-flex align-items-center pt-3 mt-2" style="gap:10px;">
                    <small class="text-muted">Share:</small>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-twitter"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($product->name.' '.request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="j-section">
                <ul class="nav mb-4" style="border-bottom:2px solid var(--j-primary-lt);">
                    <li class="nav-item">
                        <a class="nav-link active px-4 py-2 font-weight-bold" data-toggle="tab" href="#tab-description"
                           style="color:var(--j-primary);border-bottom:3px solid var(--j-primary);">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-2" data-toggle="tab" href="#tab-info" style="color:#555;">Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-2" data-toggle="tab" href="#tab-reviews" style="color:#555;">Reviews ({{ $reviewCount }})</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-description">
                        <p style="line-height:1.8;color:#555;">{{ $product->description ?? 'No description available.' }}</p>
                    </div>
                    <div class="tab-pane fade" id="tab-info">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" style="width:140px;">Wash</td><td>{{ $product->wash ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Shade</td><td>{{ $product->shade ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Length</td><td>{{ $product->length ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Stretch</td><td>{{ $product->stretch ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Waist Rise</td><td>{{ $product->waist_rise ?? '—' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" style="width:140px;">Fabric</td><td>{{ $product->fabric ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Brand</td><td>{{ $product->brand->name ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Country</td><td>{{ $product->country_of_origin ?? '—' }}</td></tr>
                                    @if($product->sku)<tr><td class="text-muted">SKU</td><td>{{ $product->sku }}</td></tr>@endif
                                    <tr><td class="text-muted">Gender</td><td>{{ ucfirst($product->gender ?? '—') }}</td></tr>
                                    @php
                                        // Stock = sum of all active variant quantities (if variants exist)
                                        // Fall back to product-level quantity if no variants
                                        $totalStock = $product->variants->isNotEmpty()
                                            ? $product->variants->sum('quantity')
                                            : ($product->quantity ?? 0);
                                    @endphp
                                    <tr><td class="text-muted">Stock</td>
                                        <td><span class="j-badge {{ $totalStock > 0 ? 'j-badge-delivered' : 'j-badge-cancelled' }}">{{ $totalStock > 0 ? 'In Stock ('.$totalStock.' units)' : 'Out of Stock' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-reviews">
                        <div class="row">
                            <div class="col-md-6">
                                @if($product->reviews && $product->reviews->isNotEmpty())
                                    @foreach($product->reviews->take(5) as $review)
                                    <div class="d-flex mb-4 gap-3">
                                        <div style="width:40px;height:40px;border-radius:50%;background:var(--j-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fa fa-user text-white" style="font-size:.85rem;"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size:.9rem;">{{ $review->user->name ?? 'Anonymous' }}</strong>
                                            <small class="text-muted ml-2">{{ $review->created_at->format('d M Y') }}</small>
                                            <div class="mt-1 mb-1">
                                                @for($s=1;$s<=5;$s++)<i class="{{ $s<=$review->rating?'fas':'far' }} fa-star" style="color:#f39c12;font-size:.8rem;"></i>@endfor
                                            </div>
                                            <p class="mb-0 small text-muted">{{ $review->review_text }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No reviews yet. Be the first!</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Write a Review</h6>
                                @auth
                                <form method="POST" action="{{ route('products.review', $product->id) ?? '#' }}">
                                    @csrf
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="text-muted small mr-2">Rating:</span>
                                        <div id="star-rating">
                                            @for($s=1;$s<=5;$s++)
                                            <i class="far fa-star star-btn" data-val="{{ $s }}" style="cursor:pointer;font-size:1.3rem;color:#f39c12;"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="rating-val" value="0">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="review_text" rows="4" class="form-control" placeholder="Share your experience…" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4">Submit Review</button>
                                </form>
                                @else
                                <div class="alert alert-light border">
                                    <a href="{{ route('customer.login') }}" style="color:var(--j-primary);">Login</a> to leave a review.
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-4">
        <h4 class="font-weight-bold mb-4" style="border-left:4px solid var(--j-primary);padding-left:12px;">Related Products</h4>
        <div class="row">
            @foreach($relatedProducts as $related)
            @include('front.partials.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
/* ── Qty stepper ── */
$(document).on('click', '.btn-plus', function () {
    var $i = $(this).closest('.quantity').find('input');
    $i.val((parseInt($i.val()) || 1) + 1);
});
$(document).on('click', '.btn-minus', function () {
    var $i = $(this).closest('.quantity').find('input');
    var v  = parseInt($i.val()) || 1;
    if (v > 1) $i.val(v - 1);
});

/* ── Star rating ── */
$('.star-btn').on('click', function () {
    var v = $(this).data('val');
    $('#rating-val').val(v);
    $('.star-btn').each(function () {
        $(this).toggleClass('fas', $(this).data('val') <= v)
               .toggleClass('far', $(this).data('val') >  v);
    });
});

/* ── Size / Variant selection ── */
$(document).on('click', '.size-btn:not(.size-btn-oos)', function () {
    var variantId    = $(this).data('variant-id');
    var variantLabel = $(this).data('variant-label');
    var variantQty   = parseInt($(this).data('variant-qty')) || 0;

    // Visually select
    $('.size-btn').removeClass('size-btn-selected');
    $(this).addClass('size-btn-selected');

    // Check the hidden radio
    $('input[name="variant_id"]').prop('checked', false);
    $('#variant-' + variantId).prop('checked', true);

    // Update label
    $('#selected-size-label').text(variantLabel);

    // Enable/disable Add to Cart
    if (variantQty > 0) {
        $('#add-to-cart-button').prop('disabled', false);
        $('#select-size-message').hide();
    } else {
        $('#add-to-cart-button').prop('disabled', true);
        $('#select-size-message').show();
    }

    // Cap max qty stepper to available stock
    var $qtyInput = $('input[name="quantity"]');
    var currentQty = parseInt($qtyInput.val()) || 1;
    if (currentQty > variantQty) $qtyInput.val(Math.max(1, variantQty));
});

/* ── Init on load ── */
(function () {
    var hasVariants = $('.size-btn').length > 0;
    if (!hasVariants) return;

    // Start with Add to Cart disabled until size is picked
    $('#add-to-cart-button').prop('disabled', true);
    $('#select-size-message').show();
})();
</script>
@endpush
@endsection
