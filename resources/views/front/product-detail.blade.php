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
                @php
                    $displayPrice  = $product->jeanzo_price ?: ($product->sale_price ?? $product->price);
                    $originalPrice = $product->price;
                    $discount      = ($originalPrice > 0 && $displayPrice < $originalPrice)
                        ? round((1 - $displayPrice / $originalPrice) * 100) : 0;
                @endphp
                <div class="d-flex align-items-center mb-3">
                    <h3 class="font-weight-bold mb-0 mr-3" style="color:var(--j-primary);font-size:1.8rem;">
                        ₹{{ number_format($displayPrice, 2) }}
                    </h3>
                    @if($discount > 0)
                    <h5 class="text-muted mb-0 mr-2"><del>₹{{ number_format($originalPrice, 2) }}</del></h5>
                    <span class="j-badge" style="background:#d4edda;color:#155724;font-size:.85rem;">
                        {{ $discount }}% OFF
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

                <!-- Sold by / Vendor -->
                <div class="mb-3">
                    @if($product->vendor)
                        <span style="font-size:.82rem;color:#555;">
                            <i class="fa fa-store mr-1" style="color:var(--j-primary);"></i>
                            Sold by: <strong style="color:#222;">{{ $product->vendor->shop_name }}</strong>
                        </span>
                    @else
                        <span style="font-size:.82rem;color:#555;">
                            <i class="fa fa-store mr-1" style="color:var(--j-primary);"></i>
                            Sold by: <strong style="color:#222;">Jeanzo</strong>
                        </span>
                    @endif
                </div>

                <!-- Variants -->
                @if($product->variants->isNotEmpty())
                {{-- ══ STEP 1: Pick Your Size ══ --}}
                <style>
                .jz-size-btn {
                    display:inline-flex;flex-direction:column;align-items:center;justify-content:center;
                    min-width:62px;min-height:52px;padding:6px 12px;
                    border:2.5px solid #ccc;border-radius:10px;
                    background:#fff;cursor:pointer;
                    font-size:.9rem;font-weight:700;color:#333;
                    transition:all .15s;line-height:1.2;
                }
                .jz-size-btn:hover:not(.jz-oos) {
                    border-color:#D19C97;background:#fff4f2;color:#D19C97;
                    transform:translateY(-2px);box-shadow:0 4px 12px rgba(209,156,151,.3);
                }
                .jz-size-btn.jz-selected {
                    border-color:#D19C97!important;background:#D19C97!important;
                    color:#fff!important;transform:translateY(-2px);
                    box-shadow:0 4px 16px rgba(209,156,151,.5)!important;
                }
                .jz-size-btn.jz-oos { border-color:#eee;background:#f8f8f8;color:#ccc;cursor:not-allowed; }
                .sz-main { font-size:.92rem;font-weight:800; }
                .sz-sub  { font-size:.6rem;font-weight:500;opacity:.8;line-height:1; }
                .sz-oos  { font-size:.55rem;color:#e74c3c;font-weight:600;line-height:1; }
                .jz-oos .sz-oos { color:#bbb; }
                #add-to-cart-button:disabled { opacity:.4;cursor:not-allowed; }
                @keyframes atcReady {
                    0%,100% { box-shadow:0 0 0 0 rgba(209,156,151,.7); }
                    50%     { box-shadow:0 0 0 10px rgba(209,156,151,0); }
                }
                @keyframes slideDown {
                    from { opacity:0;transform:translateY(-8px); }
                    to   { opacity:1;transform:translateY(0); }
                }
                .shake { animation:shake .3s ease; }
                @keyframes shake {
                    0%,100% { transform:translateX(0); }
                    25% { transform:translateX(-6px); }
                    75% { transform:translateX(6px); }
                }
                </style>

                <div id="step-size-wrap" style="
                    background:linear-gradient(135deg,#fff4f2 0%,#fff9f8 100%);
                    border:2.5px solid #D19C97;border-radius:14px;padding:16px 18px;margin-bottom:18px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                        <span style="background:#D19C97;color:#fff;font-size:.7rem;font-weight:800;padding:3px 10px;border-radius:20px;flex-shrink:0;">STEP 1 OF 2</span>
                        <strong style="font-size:.95rem;color:#1a1a1a;">👇 Select Your Size First</strong>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px;">
                        @foreach($product->variants as $variant)
                        @php $isOos = $variant->quantity <= 0; @endphp
                        <button type="button"
                                class="jz-size-btn {{ $isOos ? 'jz-oos' : '' }}"
                                data-vid="{{ $variant->id }}"
                                data-label="{{ $variant->waist_size }}{{ $variant->length ? '×'.$variant->length : '' }}{{ $variant->color ? ' · '.$variant->color : '' }}"
                                {{ $isOos ? 'disabled' : '' }}>
                            <span class="sz-main">{{ $variant->waist_size }}</span>
                            @if($variant->length)<span class="sz-sub">L{{ $variant->length }}</span>@endif
                            @if($isOos)<span class="sz-oos">Out</span>@endif
                        </button>
                        @endforeach
                    </div>
                    <div id="size-hint" style="font-size:.82rem;color:#c0392b;font-weight:600;">⚠️ Pick a size to enable Add to Cart</div>
                    <div id="size-chosen" style="font-size:.85rem;color:#27ae60;font-weight:700;display:none;">✅ Size: <span id="chosen-label"></span></div>
                </div>
                @endif

                {{-- ══ STEP 2: Add to Cart + Cart Panel ══ --}}
                <meta name="atc-product-id" content="{{ $product->id }}">
                <div style="margin-bottom:20px;">
                    <div id="atc-size-warn" style="display:none;margin-bottom:10px;
                        background:#fff3cd;border:1.5px solid #ffc107;border-radius:8px;
                        padding:10px 14px;font-size:.88rem;font-weight:600;color:#856404;">
                        ⬆️ Please select a size above first
                    </div>
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:10px;">
                        {{-- Qty stepper --}}
                        <div class="input-group quantity" style="width:120px;flex-shrink:0;">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm btn-minus"><i class="fa fa-minus"></i></button>
                            </div>
                            <input type="text" id="atc-qty" class="form-control form-control-sm text-center" value="1" min="1" max="99">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm btn-plus"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        {{-- Add to Cart --}}
                        <button type="button" id="add-to-cart-button" class="btn btn-primary"
                                style="min-width:160px;font-weight:700;font-size:1rem;padding:10px 20px;border-radius:10px;"
                                {{ $product->variants->isNotEmpty() ? 'disabled' : '' }}>
                            <i class="fa fa-cart-plus mr-1"></i><span id="atc-label">Add to Cart</span>
                        </button>
                        {{-- Wishlist --}}
                        @auth
                        <form method="POST" action="{{ route('wishlist.add') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-secondary" title="Wishlist">
                                <i class="far fa-heart" style="color:#D19C97;"></i>
                                <span class="d-none d-sm-inline ml-1">Wishlist</span>
                            </button>
                        </form>
                        @endauth
                    </div>

                    {{-- Cart Success Panel --}}
                    <div id="cart-success-panel" style="display:none;margin-top:16px;">
                        <div style="border:2px solid #27ae60;border-radius:14px;overflow:hidden;
                                    box-shadow:0 6px 24px rgba(39,174,96,.15);animation:slideDown .3s ease;">
                            <div style="background:#27ae60;padding:10px 16px;display:flex;align-items:center;gap:8px;">
                                <i class="fa fa-check-circle" style="color:#fff;"></i>
                                <span style="color:#fff;font-weight:700;font-size:.92rem;flex:1;">Added to Cart!</span>
                                <button onclick="document.getElementById('cart-success-panel').style.display='none';"
                                        style="background:none;border:none;color:rgba(255,255,255,.8);font-size:1.3rem;cursor:pointer;padding:0;line-height:1;">×</button>
                            </div>
                            <div style="padding:12px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f0f0f0;background:#fff;">
                                @if($product->images && $product->images->isNotEmpty())
                                <img src="{{ $product->images->first()->url ?? '' }}"
                                     style="width:50px;height:50px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                                @endif
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.84rem;font-weight:600;color:#222;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->name }}</div>
                                    <div style="font-size:.8rem;color:#D19C97;font-weight:700;">₹{{ number_format($product->jeanzo_price ?: ($product->sale_price ?? $product->price), 2) }}</div>
                                    <div id="panel-size-label" style="font-size:.75rem;color:#888;"></div>
                                </div>
                                <div style="text-align:right;flex-shrink:0;">
                                    <div style="font-size:.7rem;color:#aaa;">In cart</div>
                                    <div id="panel-cart-count" style="font-size:.95rem;font-weight:800;color:#333;"></div>
                                </div>
                            </div>
                            <div style="padding:12px 16px;display:flex;gap:10px;background:#fff;">
                                <a href="{{ route('cart.index') }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:2px solid #D19C97;border-radius:10px;font-weight:700;font-size:.88rem;color:#D19C97;text-decoration:none;">
                                    <i class="fa fa-shopping-bag"></i> View Cart
                                </a>
                                <a href="{{ route('checkout.index') }}" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;background:#D19C97;border:2px solid #D19C97;border-radius:10px;font-weight:700;font-size:.88rem;color:#fff;text-decoration:none;">
                                    <i class="fa fa-bolt"></i> Place Order
                                </a>
                            </div>
                        </div>
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
                            <div style="background:#e8f5e9;border:1.5px solid #a5d6a7;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-undo fa-lg mb-1" style="color:#27ae60;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#1b5e20;">Easy Returns</div>
                                <div style="font-size:.65rem;color:#388e3c;font-weight:600;">7 Days Free</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f8f9fa;border-radius:10px;padding:10px 6px;">
                                <i class="fa fa-truck fa-lg mb-1" style="color:#2980b9;"></i>
                                <div style="font-size:.72rem;font-weight:700;color:#333;">FREE Shipping</div>
                                <div style="font-size:.65rem;color:#777;">On Every Order</div>
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
$(document).ready(function () {

    /* ══════════════════════════════════════
       SIZE + ADD TO CART — jQuery, reliable
       ══════════════════════════════════════ */

    var selectedVid   = null;
    var selectedLabel = '';
    var isAdding      = false;
    var hasVariants   = $('.jz-size-btn').length > 0;
    var productId     = $('meta[name="atc-product-id"]').attr('content');
    var csrfToken     = $('meta[name="csrf-token"]').attr('content');

    // ── Size button click ──
    $(document).on('click', '.jz-size-btn:not(.jz-oos)', function () {
        $('.jz-size-btn').removeClass('jz-selected');
        $(this).addClass('jz-selected');
        selectedVid   = $(this).data('vid');
        selectedLabel = $(this).data('label');

        $('#size-hint').hide();
        $('#size-chosen').css('display','flex');
        $('#chosen-label').text(selectedLabel);
        $('#atc-size-warn').hide();
        $('#cart-success-panel').hide();

        // Enable Add to Cart
        $('#add-to-cart-button').prop('disabled', false)
            .css({'background':'','border-color':''});

        if (window.innerWidth < 768) {
            setTimeout(function () {
                document.getElementById('add-to-cart-button')
                    .scrollIntoView({behavior:'smooth', block:'center'});
            }, 200);
        }
    });

    // ── Add to Cart click ──
    $(document).on('click', '#add-to-cart-button', function () {
        if (isAdding) return;

        if (hasVariants && !selectedVid) {
            $('#atc-size-warn').show();
            var wrap = document.getElementById('step-size-wrap');
            if (wrap) {
                wrap.classList.remove('shake');
                void wrap.offsetWidth;
                wrap.classList.add('shake');
                wrap.scrollIntoView({behavior:'smooth', block:'center'});
            }
            return;
        }

        isAdding = true;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#atc-label').text('Adding…');

        var qty = parseInt($('#atc-qty').val()) || 1;

        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                _token:     csrfToken,
                product_id: productId,
                quantity:   qty,
                variant_id: selectedVid || ''
            },
            success: function (res) {
                if (res.success) {
                    $btn.prop('disabled', false)
                        .css({background:'#27ae60', borderColor:'#27ae60'});
                    $('#atc-label').text('✓ Added!');

                    var count = parseInt(res.cart_count) || 1;
                    $('a[href*="/cart"] .badge').text(count).show();

                    $('#panel-size-label').text(selectedLabel ? 'Size: ' + selectedLabel : '');
                    $('#panel-cart-count').text(count + ' item' + (count !== 1 ? 's' : ''));
                    $('#cart-success-panel').show();
                    document.getElementById('cart-success-panel')
                        .scrollIntoView({behavior:'smooth', block:'nearest'});

                    setTimeout(function () {
                        $btn.css({background:'', borderColor:''});
                        $('#atc-label').text('Add to Cart');
                        isAdding = false;
                    }, 2500);
                } else {
                    $btn.prop('disabled', false);
                    $('#atc-label').text('Add to Cart');
                    isAdding = false;
                    alert(res.message || 'Could not add to cart.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $('#atc-label').text('Add to Cart');
                isAdding = false;
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message : 'Error. Please try again.';
                alert(msg);
            }
        });
    });

    // ── Qty stepper ──
    $(document).on('click', '.btn-plus', function () {
        var $i = $(this).closest('.quantity').find('input');
        $i.val(Math.min(99, (parseInt($i.val()) || 1) + 1));
    });
    $(document).on('click', '.btn-minus', function () {
        var $i = $(this).closest('.quantity').find('input');
        $i.val(Math.max(1, (parseInt($i.val()) || 1) - 1));
    });

    // ── Star rating ──
    $(document).on('click', '.star-btn', function () {
        var v = $(this).data('val');
        $('#rating-val').val(v);
        $('.star-btn').each(function () {
            $(this).toggleClass('fas', $(this).data('val') <= v)
                   .toggleClass('far', $(this).data('val') >  v);
        });
    });

});
</script>
@endpush
@endsection