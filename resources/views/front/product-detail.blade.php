@extends('layouts.eshopper')
@section('title', ($product->meta_title ?: $product->name) . ' | Jeanzo')
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 155))
@section('canonical', $product->canonical_url ?: route('products.detail', $product->slug))
@section('og_type', 'product')
@section('og_title', $product->meta_title ?: $product->name)
@section('og_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 155))
@section('og_image', $product->images->first() ? asset('storage/products/' . $product->id . '/images/' . $product->images->first()->image) : asset('eshopper/img/og-default.jpg'))

@push('json_ld')
@php
    $price   = number_format((float)$product->price, 2, '.', '');
    $inStock = ($product->variants->isNotEmpty() || ($product->quantity ?? 0) > 0);
    $imgUrl  = $product->images->first() ? asset('storage/products/' . $product->id . '/images/' . $product->images->first()->image) : asset('eshopper/img/og-default.jpg');
    $jsonld  = [
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
    if ($reviewCount > 0) {
        $jsonld['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $avgRating,
            'reviewCount' => $reviewCount,
        ];
    }
@endphp
<script type="application/ld+json">{{ json_encode($jsonld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</script>
@endpush

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* ── Site Theme Color ── */
    :root {
        --site-primary: #D19C97;
        --site-primary-dark: #b8807a;
        --site-primary-light: #f7eded;
        --pd-border: #e5e5e5;
    }

    /* Scoped to product-detail page only */
    #pd-page * { box-sizing: border-box; }

    /* Override any global font-family reset that would break icon fonts */
    #pd-page i.fas, #pd-page i.far, #pd-page i.fal, #pd-page i.fad {
        font-family: "Font Awesome 5 Free" !important;
        display: inline-block !important;
        visibility: visible !important;
    }
    #pd-page i.fab {
        font-family: "Font Awesome 5 Brands" !important;
        display: inline-block !important;
        visibility: visible !important;
    }
    #pd-page .fas { font-weight: 900 !important; }
    #pd-page .far { font-weight: 400 !important; }
    #pd-page .fab { font-weight: 400 !important; }

    /* Apply custom font only to non-icon elements */
    #pd-page p, #pd-page h1, #pd-page h2, #pd-page h3, #pd-page h4,
    #pd-page span, #pd-page div, #pd-page button, #pd-page a, #pd-page label,
    #pd-page input, #pd-page textarea, #pd-page td, #pd-page th {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ─────────────────────────────────────
       1. BREADCRUMB — site theme color
    ───────────────────────────────────── */
    .pd-breadcrumb a {
        color: var(--site-primary) !important;
        text-decoration: none;
    }
    .pd-breadcrumb a:hover {
        color: var(--site-primary-dark) !important;
        text-decoration: underline;
    }

    /* ─────────────────────────────────────
       2. IMAGE ZOOM — hover on desktop, pinch & double-tap on mobile
    ───────────────────────────────────── */

    .pd-img-wrapper { overflow: hidden; }
    .pd-img-aspect  { overflow: hidden; cursor: zoom-in; }

    #pd-main-img {
        display: block;
        width: 100%; height: 100%;
        object-fit: cover;
        transform-origin: center center;
        transition: transform .3s ease;
        cursor: zoom-in;
        user-select: none;
        -webkit-user-select: none;
    }
    /* Desktop: hover zoom follows mouse */
    @media (min-width: 768px) {
        .pd-img-aspect.zoomed #pd-main-img { cursor: zoom-out; }
    }
    /* Mobile: show grab cursor when zoomed in */
    @media (max-width: 767px) {
        .pd-img-aspect { touch-action: none; } /* we handle touch manually */
        .pd-img-aspect.zoomed { cursor: grab; }
        .pd-img-wrapper { touch-action: none; }
    }

    .pd-thumb { flex-shrink: 0; border-radius: 8px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: border-color .2s, box-shadow .2s; aspect-ratio: 1; }
    .pd-thumb.active, .pd-thumb:hover { border-color: var(--site-primary); box-shadow: 0 0 0 1px var(--site-primary); }
    .pd-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 6px; }

    /* ─────────────────────────────────────
       3. WISHLIST ICON — visible on image
    ───────────────────────────────────── */
    #pd-wish-btn, .pd-wish-link {
        background: #fff !important;
        width: 44px !important;
        height: 44px !important;
        border-radius: 50% !important;
        border: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 2px 12px rgba(0,0,0,.18) !important;
        cursor: pointer !important;
        transition: background .2s, box-shadow .2s !important;
        text-decoration: none !important;
    }
    #pd-wish-btn:hover, .pd-wish-link:hover {
        background: var(--site-primary-light) !important;
        box-shadow: 0 4px 16px rgba(209,156,151,.4) !important;
    }
    #pd-wish-btn i, .pd-wish-link i {
        color: var(--site-primary) !important;
        font-size: 1.1rem !important;
        display: inline-block !important;
        visibility: visible !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 400 !important;
    }

    /* ─────────────────────────────────────
       4. RATING STARS — visible below title
    ───────────────────────────────────── */
    .pd-star { color: #f59e0b !important; font-size: 1.15rem !important; display: inline-block !important; visibility: visible !important; font-family: "Font Awesome 5 Free" !important; }
    .pd-star-empty { color: #f59e0b !important; font-size: 1.15rem !important; opacity: 0.35; display: inline-block !important; visibility: visible !important; font-family: "Font Awesome 5 Free" !important; }

    /* ─────────────────────────────────────
       5. COLOR PANEL — below rating
    ───────────────────────────────────── */
    .pd-color-swatch {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1.5px #d1d5db;
        cursor: pointer;
        transition: box-shadow .2s, transform .15s;
        display: inline-block;
    }
    .pd-color-swatch:hover { transform: scale(1.12); }
    .pd-color-swatch.pd-selected {
        box-shadow: 0 0 0 3px #fff, 0 0 0 5px var(--site-primary);
    }

    /* ─────────────────────────────────────
       6. SIZE BOX — site theme background (not sky blue)
    ───────────────────────────────────── */
    .pd-size-btn {
        border: 2px solid #d1d5db;
        border-radius: 10px;
        padding: 10px 0;
        font-size: .85rem;
        font-weight: 600;
        background: #fff;
        cursor: pointer;
        transition: all .18s;
        color: #111;
    }
    .pd-size-btn:hover:not(:disabled) {
        border-color: var(--site-primary);
        color: var(--site-primary);
    }
    .pd-size-btn.pd-selected {
        background: var(--site-primary) !important;
        color: #fff !important;
        border-color: var(--site-primary) !important;
    }
    .pd-size-btn:disabled { color: #d1d5db; cursor: not-allowed; opacity: .6; }

    /* ─────────────────────────────────────
       7. SIZE CHART / FIT GUIDE ICONS — visible
    ───────────────────────────────────── */
    #pd-size-chart-btn,
    #pd-fit-guide-btn {
        background: none;
        border: none;
        color: var(--site-primary) !important;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0;
        text-decoration: none;
    }
    #pd-size-chart-btn i,
    #pd-fit-guide-btn i {
        color: var(--site-primary) !important;
        font-size: .9rem !important;
        display: inline-block !important;
        visibility: visible !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
    }
    #pd-size-chart-btn:hover,
    #pd-fit-guide-btn:hover { color: var(--site-primary-dark) !important; }

    /* ─────────────────────────────────────
       8. QUANTITY MINUS ICON — visible
    ───────────────────────────────────── */
    .pd-qty-btn {
        background: none;
        border: none;
        padding: 10px 16px;
        cursor: pointer;
        font-size: .9rem;
        color: #374151 !important;
        transition: background .15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .pd-qty-btn i {
        color: #374151 !important;
        font-size: .9rem !important;
        display: inline-block !important;
        visibility: visible !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
    }
    .pd-qty-btn:hover { background: #f3f4f6; }

    /* ─────────────────────────────────────
       9. ADD TO CART BUTTON — site theme color (not sky blue)
    ───────────────────────────────────── */
    .pd-btn-cart {
        background: var(--site-primary) !important;
        color: #fff !important;
        border: none !important;
    }
    .pd-btn-cart:hover:not(:disabled) {
        background: var(--site-primary-dark) !important;
    }
    .pd-btn-cart:disabled { opacity: .6; cursor: not-allowed; }
    .pd-btn-buy {
        background: #111 !important;
        color: #fff !important;
        border: none !important;
    }
    .pd-btn-buy:hover { background: #333 !important; }

    /* Button icon visibility */
    .pd-btn-cart i,
    .pd-btn-buy i {
        color: #fff !important;
        display: inline-block !important;
        visibility: visible !important;
        font-size: 1rem !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
    }

    /* ─────────────────────────────────────
       10. TRUST BADGE ICONS — visible
    ───────────────────────────────────── */
    .pd-badge-icon {
        color: var(--site-primary) !important;
        font-size: 1.6rem !important;
        display: block !important;
        visibility: visible !important;
        margin-bottom: 8px !important;
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
    }

    /* ─────────────────────────────────────
       11. CUSTOMER REVIEWS — same design, stars visible
    ───────────────────────────────────── */
    .pd-review-star { color: #f59e0b !important; font-size: 1rem !important; display: inline-block !important; visibility: visible !important; font-family: "Font Awesome 5 Free" !important; }
    .pd-review-bar { background: #e5e7eb; border-radius: 999px; height: 8px; overflow: hidden; }
    .pd-review-bar-fill { background: #f59e0b; height: 100%; border-radius: 999px; }

    /* ─────────────────────────────────────
       12. ACCORDION BOXES — proper box design, not just thin line
    ───────────────────────────────────── */
    .pd-accordion {
        border: 1px solid #e5e7eb !important;
        border-radius: 14px !important;
        background: #fff !important;
        overflow: hidden !important;
        margin-bottom: 10px !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .pd-accordion .pd-acc-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        background: #fafafa;
        border: none;
        cursor: pointer;
        text-align: left;
        outline: none;
    }
    .pd-accordion .pd-acc-btn:focus { outline: none; box-shadow: none; }
    .pd-accordion-content { max-height: 0; overflow: hidden; transition: max-height .3s ease; padding: 0 20px; }
    .pd-accordion.open .pd-accordion-content { max-height: 1200px; padding-bottom: 16px; }
    .pd-accordion.open .pd-acc-icon { transform: rotate(180deg); }
    .pd-accordion.open .pd-acc-btn { background: #fff; border-bottom: 1px solid #f3f4f6; }
    .pd-acc-icon { transition: transform .25s; color: #9ca3af; }

    /* ─────────────────────────────────────
       13. SHARE ICONS — visible and working
    ───────────────────────────────────── */
    .pd-share-link {
        color: #6b7280 !important;
        text-decoration: none !important;
        font-size: 1.15rem !important;
        transition: color .2s !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1.5px solid #e5e7eb;
        background: #fff;
    }
    .pd-share-link i {
        display: inline-block !important;
        visibility: visible !important;
        color: #6b7280 !important;
        font-family: "Font Awesome 5 Free", "Font Awesome 5 Brands" !important;
    }
    .pd-share-link .fab {
        font-family: "Font Awesome 5 Brands" !important;
        font-weight: 400 !important;
    }
    .pd-share-link .fas {
        font-family: "Font Awesome 5 Free" !important;
        font-weight: 900 !important;
    }
    .pd-share-link:hover {
        color: var(--site-primary) !important;
        border-color: var(--site-primary) !important;
        background: var(--site-primary-light) !important;
    }
    .pd-share-link:hover i { color: var(--site-primary) !important; }

    /* ─────────────────────────────────────
       GALLERY — image box height matches right side
    ───────────────────────────────────── */
    .pd-gallery-sticky {
        display: flex;
        flex-direction: column;
        align-self: stretch;
    }
    .pd-img-wrapper {
        position: relative;
        background: #f9fafb;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 12px;
        width: 100%;
        aspect-ratio: 3 / 4;
        min-height: 280px;
    }
    .pd-img-aspect {
        width: 100%;
        height: 100%;
        position: absolute;
        inset: 0;
    }
    .pd-img-aspect img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    /* Fallback for browsers that don't support aspect-ratio */
    @supports not (aspect-ratio: 3/4) {
        .pd-img-wrapper { min-height: 400px; }
    }
    @media (max-width: 767px) {
        .pd-img-wrapper { min-height: 280px; border-radius: 12px; aspect-ratio: 4/3; }
    }
    @media (max-width: 575px) {
        .pd-img-wrapper { min-height: 220px; aspect-ratio: 1/1; }
    }

    @media (min-width: 1024px) {
        .pd-gallery-sticky { position: sticky; top: 96px; }
    }

    /* Horizontal scrollbar hide */
    .pd-scroll-hide::-webkit-scrollbar { display: none; }
    .pd-scroll-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Stock pulse */
    @keyframes pd-pulse { 0%,100%{opacity:1} 50%{opacity:.45} }
    .pd-stock-pulse { animation: pd-pulse 2s cubic-bezier(.4,0,.6,1) infinite; }

    /* Toast */
    #pd-toast { transition: transform .3s ease; }
    #pd-toast.show { transform: translateX(0) !important; }

    /* Sticky mobile bar */
    #pd-sticky { transition: transform .3s ease; }
    #pd-sticky.show { transform: translateY(0) !important; }

    /* Size chart modal */
    #pd-size-modal, #pd-fit-modal { display: none; }
    #pd-size-modal.open, #pd-fit-modal.open { display: flex; }

    /* Cart success panel */
    #pd-cart-panel { display: none; }
    #pd-cart-panel.show { display: block; }

    /* Related product card hover */
    .pd-prod-card .pd-prod-img { transition: transform .3s; }
    .pd-prod-card:hover .pd-prod-img { transform: scale(1.05); }
    .pd-prod-card:hover .pd-wish-btn { opacity: 1 !important; }

    /* Responsive grid */
    #pd-page .pd-two-col { align-items: stretch; }
    @media (max-width: 767px) {
        #pd-page .pd-two-col { flex-direction: column; align-items: stretch; }
        .pd-img-aspect { min-height: 300px; }
    }

    /* View Cart / Checkout button in cart panel */
    .pd-panel-btn-outline {
        flex: 1 1 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px;
        border: 2px solid var(--site-primary);
        border-radius: 10px;
        font-weight: 700;
        font-size: .88rem;
        color: var(--site-primary);
        text-decoration: none;
    }
    .pd-panel-btn-outline i { color: var(--site-primary) !important; display: inline-block !important; }
    .pd-panel-btn-fill {
        flex: 1 1 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px;
        background: var(--site-primary);
        border: 2px solid var(--site-primary);
        border-radius: 10px;
        font-weight: 700;
        font-size: .88rem;
        color: #fff;
        text-decoration: none;
    }
    .pd-panel-btn-fill i { color: #fff !important; display: inline-block !important; }
    .pd-panel-btn-outline:hover { background: var(--site-primary-light); }
    .pd-panel-btn-fill:hover { background: var(--site-primary-dark); border-color: var(--site-primary-dark); }
</style>
@endpush

@section('content')
<div id="pd-page" style="background:#fff; color:#111;">

    {{-- ── Breadcrumb ────────────────────────────────────────── --}}
    <div style="max-width:1280px; margin:0 auto; padding:10px 16px; font-size:.8rem; color:#6b7280;" class="pd-breadcrumb">
        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
            <a href="{{ url('/') }}">Home</a>
            <i class="fas fa-chevron-right" style="font-size:.6rem; color:#9ca3af;"></i>
            <a href="{{ route('products.index') }}">Shop</a>
            @if($product->category)
                <i class="fas fa-chevron-right" style="font-size:.6rem; color:#9ca3af;"></i>
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a>
            @endif
            <i class="fas fa-chevron-right" style="font-size:.6rem; color:#9ca3af;"></i>
            <span style="color:#111;">{{ Str::limit($product->name, 50) }}</span>
        </div>
    </div>

    {{-- ── Main product section ──────────────────────────────── --}}
    <main id="pd-main" style="max-width:1280px; margin:0 auto; padding:0 16px 80px;">
        <div style="display:flex; gap:40px; flex-wrap:wrap;" class="pd-two-col">

            {{-- ════ LEFT: Image Gallery ════ --}}
            <div style="flex:0 0 min(100%, 500px); max-width:min(100%, 500px); width:100%;" class="pd-gallery-sticky">

                {{-- Main image --}}
                <div class="pd-img-wrapper">
                    @if($discount > 0)
                        <div style="position:absolute; top:14px; left:14px; z-index:10; background:#ef4444; color:#fff; padding:4px 12px; border-radius:999px; font-size:.75rem; font-weight:700;">
                            {{ $discount }}% OFF
                        </div>
                    @endif

                    {{-- Wishlist button — FIX #3: always visible --}}
                    @auth
                        <form method="POST" action="{{ route('wishlist.add') }}" id="pd-wish-form" style="position:absolute;top:14px;right:14px;z-index:10;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" id="pd-wish-btn">
                                <i class="far fa-heart"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('customer.login') }}" class="pd-wish-link" style="position:absolute;top:14px;right:14px;z-index:10;">
                            <i class="far fa-heart"></i>
                        </a>
                    @endauth

                    <div class="pd-img-aspect" style="overflow:hidden;">
                        @php
                            $firstImg = $product->images->first();
                            $firstRel = $firstImg ? 'products/'.$product->id.'/images/'.($firstImg->image ?? '') : '';
                            $firstUrl = $firstRel ? (file_exists(public_path('storage/'.$firstRel)) ? asset('storage/'.$firstRel) : asset('storage/default.jpeg')) : asset('storage/default.jpeg');
                        @endphp
                        <img id="pd-main-img" src="{{ $firstUrl }}" alt="{{ $product->name }}"
                             style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </div>

                {{-- Thumbnails — scrollable strip with arrows --}}
                @if($product->images && $product->images->count() > 0)
                <div class="pd-thumb-strip-wrap" style="position:relative; margin-top:8px;">
                    {{-- Prev arrow --}}
                    <button id="pd-thumb-prev" type="button" aria-label="Previous images"
                        style="display:none; position:absolute; left:-14px; top:50%; transform:translateY(-50%);
                               z-index:5; background:#fff; border:1.5px solid #e5e5e5; border-radius:50%;
                               width:30px; height:30px; align-items:center; justify-content:center;
                               cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.12); padding:0; line-height:1;">
                        <i class="fas fa-chevron-left" style="font-size:.65rem; color:#555;"></i>
                    </button>

                    {{-- Scrollable track --}}
                    <div id="pd-thumb-track"
                         style="display:flex; gap:8px; overflow-x:auto; scroll-behavior:smooth;
                                -webkit-overflow-scrolling:touch; scroll-snap-type:x mandatory;
                                scrollbar-width:none; -ms-overflow-style:none; padding:2px 0 4px;">
                        @foreach($product->images as $i => $image)
                            @php
                                $rel = 'products/'.$product->id.'/images/'.($image->image ?? '');
                                $url = file_exists(public_path('storage/'.$rel)) ? asset('storage/'.$rel) : asset('storage/default.jpeg');
                            @endphp
                            <button type="button" class="pd-thumb {{ $i === 0 ? 'active' : '' }}" data-img="{{ $url }}"
                                    style="flex:0 0 calc(20% - 7px); min-width:52px; max-width:80px;
                                           border:none; padding:0; background:none;
                                           aspect-ratio:1; scroll-snap-align:start; border-radius:8px; overflow:hidden;">
                                <img src="{{ $url }}" alt="{{ $product->name }} - image {{ $i+1 }}"
                                     style="width:100%; height:100%; object-fit:cover; border-radius:8px; display:block;">
                            </button>
                        @endforeach
                    </div>

                    {{-- Next arrow --}}
                    <button id="pd-thumb-next" type="button" aria-label="Next images"
                        style="display:none; position:absolute; right:-14px; top:50%; transform:translateY(-50%);
                               z-index:5; background:#fff; border:1.5px solid #e5e5e5; border-radius:50%;
                               width:30px; height:30px; align-items:center; justify-content:center;
                               cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.12); padding:0; line-height:1;">
                        <i class="fas fa-chevron-right" style="font-size:.65rem; color:#555;"></i>
                    </button>
                </div>
                <style>
                    #pd-thumb-track::-webkit-scrollbar { display:none; }
                    .pd-thumb { border-radius:8px; overflow:hidden; border:2px solid transparent;
                                cursor:pointer; transition:border-color .2s, box-shadow .2s; flex-shrink:0; }
                    .pd-thumb.active, .pd-thumb:hover { border-color:var(--site-primary); box-shadow:0 0 0 1px var(--site-primary); }
                    .pd-thumb img { display:block; pointer-events:none; }
                    /* Show arrows when enough images */
                    .pd-thumb-strip-wrap.has-arrows { padding:0 20px; }
                    .pd-thumb-strip-wrap.has-arrows #pd-thumb-prev,
                    .pd-thumb-strip-wrap.has-arrows #pd-thumb-next { display:flex !important; }
                </style>
                @endif
            </div>

            {{-- ════ RIGHT: Product Info ════ --}}
            <div style="flex:1 1 320px; padding-top:4px;">

                {{-- Session flash --}}
                @if(session('success'))
                    <div style="background:#d1fae5; border:1px solid #6ee7b7; border-radius:10px; padding:10px 14px; margin-bottom:16px; font-size:.875rem; color:#065f46; display:flex; justify-content:space-between; align-items:center;">
                        <span>{{ session('success') }}</span>
                        <a href="{{ route('cart.index') }}" style="color:var(--site-primary); font-weight:700; text-decoration:none; margin-left:12px; white-space:nowrap;">View Cart →</a>
                    </div>
                @endif
                @if(session('error'))
                    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:10px; padding:10px 14px; margin-bottom:16px; font-size:.875rem; color:#991b1b;">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Brand + Title --}}
                <div style="margin-bottom:12px;">
                    <p style="font-size:.8rem; color:#6b7280; margin:0 0 4px; font-weight:500; letter-spacing:.06em; text-transform:uppercase;">
                        {{ $product->brand->name ?? 'Jeanzo' }}
                    </p>
                    <h1 style="font-size:1.75rem; font-weight:800; line-height:1.2; margin:0 0 12px; color:#111;">
                        {{ $product->name }}
                    </h1>

                    {{-- FIX #4: Rating stars — always visible --}}
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; font-size:.875rem; margin-bottom:0;">
                        <div style="display:flex; align-items:center; gap:3px;">
                            @for($s = 1; $s <= 5; $s++)
                                @if($s <= floor($avgRating))
                                    <i class="fas fa-star pd-star"></i>
                                @elseif($s - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt pd-star"></i>
                                @else
                                    <i class="fas fa-star pd-star pd-star-empty"></i>
                                @endif
                            @endfor
                            <span style="font-weight:700; margin-left:4px; font-size:.85rem; color:#111;">{{ number_format($avgRating, 1) }}</span>
                        </div>
                        <span style="color:#d1d5db;">|</span>
                        <a href="#pd-reviews" style="color:var(--site-primary); text-decoration:none; font-weight:600;">{{ $reviewCount }} Reviews</a>
                        @if($product->category)
                            <span style="color:#d1d5db;">|</span>
                            <span style="color:#6b7280;">{{ $product->category->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Price --}}
                <div style="margin-bottom:16px;">
                    <div style="display:flex; align-items:baseline; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
                        <span style="font-size:2rem; font-weight:800; color:#111;">₹{{ number_format($displayPrice, 0) }}</span>
                        @if($discount > 0)
                            <span style="font-size:1.1rem; color:#9ca3af; text-decoration:line-through;">₹{{ number_format($originalPrice, 0) }}</span>
                            <span style="background:#dcfce7; color:#15803d; padding:3px 10px; border-radius:6px; font-size:.75rem; font-weight:700;">
                                Save ₹{{ number_format($originalPrice - $displayPrice, 0) }}
                            </span>
                        @endif
                    </div>
                    <p style="font-size:.8rem; color:#6b7280; margin:0;">Inclusive of all taxes</p>
                </div>

                {{-- Color Panel — below price/rating --}}
                @php
                    // Collect unique colors from variants
                    $colorVariants = $product->variants->filter(fn($v) => !empty($v->color))->unique('color')->values();
                    // Also check product-level color
                    $productColor = $product->color ?? $product->color_family ?? null;

                    // Comprehensive color map — covers denim, fashion, and common tones
                    $colorMapFull = [
                        // Blues / Denim
                        'blue'            => '#3b5bdb',
                        'dark blue'       => '#1c2b6e',
                        'navy'            => '#1c2b6e',
                        'navy blue'       => '#1c2b6e',
                        'royal blue'      => '#2563eb',
                        'cobalt'          => '#1d4ed8',
                        'sky blue'        => '#7dd3fc',
                        'light blue'      => '#93c5fd',
                        'baby blue'       => '#bfdbfe',
                        'powder blue'     => '#bae6fd',
                        'denim blue'      => '#4a6fa5',
                        'dark denim'      => '#2d3a6b',
                        'medium blue'     => '#3b82f6',
                        'ice blue'        => '#dbeafe',
                        'classic blue'    => '#2563eb',
                        'indigo'          => '#4338ca',
                        'dark indigo'     => '#312e81',
                        // Washes
                        'dark wash'       => '#2d3561',
                        'mid wash'        => '#5b8cce',
                        'medium wash'     => '#5b8cce',
                        'light wash'      => '#a8c6e8',
                        'acid wash'       => '#9db8d2',
                        'stone wash'      => '#8fadc8',
                        'vintage wash'    => '#7a95b0',
                        // Greens
                        'green'           => '#16a34a',
                        'dark green'      => '#14532d',
                        'olive'           => '#6b7c3f',
                        'olive green'     => '#6b7c3f',
                        'army green'      => '#4b5320',
                        'khaki'           => '#c3a35d',
                        'sage'            => '#84a98c',
                        'sage green'      => '#84a98c',
                        'forest green'    => '#166534',
                        'mint'            => '#6ee7b7',
                        'mint green'      => '#6ee7b7',
                        'bottle green'    => '#1a4731',
                        'teal'            => '#0d9488',
                        'emerald'         => '#059669',
                        // Neutrals / Whites
                        'white'           => '#f8f8f8',
                        'off white'       => '#f5f0eb',
                        'cream'           => '#fffbeb',
                        'ivory'           => '#fef9c3',
                        'ecru'            => '#f5f0e1',
                        'beige'           => '#d4b896',
                        'tan'             => '#c2956c',
                        'sand'            => '#d2b48c',
                        'camel'           => '#c19a6b',
                        // Greys
                        'grey'            => '#9ca3af',
                        'gray'            => '#9ca3af',
                        'light grey'      => '#d1d5db',
                        'light gray'      => '#d1d5db',
                        'dark grey'       => '#4b5563',
                        'dark gray'       => '#4b5563',
                        'charcoal'        => '#374151',
                        'slate'           => '#64748b',
                        'silver'          => '#c0c0c0',
                        // Blacks
                        'black'           => '#111827',
                        'jet black'       => '#0a0a0a',
                        'coal'            => '#1f2937',
                        // Reds / Pinks
                        'red'             => '#dc2626',
                        'dark red'        => '#991b1b',
                        'maroon'          => '#7f1d1d',
                        'burgundy'        => '#7e1f3b',
                        'wine'            => '#6b1a2b',
                        'rose'            => '#fb7185',
                        'pink'            => '#f472b6',
                        'light pink'      => '#fbcfe8',
                        'hot pink'        => '#ec4899',
                        'blush'           => '#fda4af',
                        'coral'           => '#f97316',
                        'peach'           => '#fdba74',
                        // Purples
                        'purple'          => '#7c3aed',
                        'violet'          => '#8b5cf6',
                        'lavender'        => '#c4b5fd',
                        'lilac'           => '#ddd6fe',
                        'mauve'           => '#9b8fa0',
                        'plum'            => '#581c87',
                        // Browns
                        'brown'           => '#78350f',
                        'dark brown'      => '#451a03',
                        'light brown'     => '#a16207',
                        'chocolate'       => '#5c2d0e',
                        'rust'            => '#b45309',
                        'terracotta'      => '#c05621',
                        'brick'           => '#9c2f2f',
                        // Yellows / Oranges
                        'yellow'          => '#facc15',
                        'mustard'         => '#ca8a04',
                        'gold'            => '#d97706',
                        'orange'          => '#ea580c',
                        'amber'           => '#f59e0b',
                        // Multi
                        'multicolor'      => 'linear-gradient(135deg,#f00,#ff0,#0f0,#0ff,#00f,#f0f)',
                        'multi'           => 'linear-gradient(135deg,#f00,#ff0,#0f0,#0ff,#00f,#f0f)',
                        'printed'         => 'linear-gradient(135deg,#f472b6,#fb923c,#facc15)',
                        'floral'          => 'linear-gradient(135deg,#f472b6,#86efac,#fde68a)',
                    ];
                @endphp
                @if($colorVariants->isNotEmpty() || $productColor)
                    <div style="margin-bottom:16px;">
                        <label style="font-size:.88rem; font-weight:700; display:block; margin-bottom:8px;">
                            Color:
                            <span style="font-weight:500; color:#374151; margin-left:2px;" id="pd-color-label">
                                @if($colorVariants->isNotEmpty()){{ ucfirst($colorVariants->first()->color) }}@elseif($productColor){{ ucfirst($productColor) }}@endif
                            </span>
                        </label>
                        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                            @if($colorVariants->isNotEmpty())
                                @foreach($colorVariants as $cv)
                                    @php
                                        $lc = strtolower(trim($cv->color));
                                        $cssColor = $colorMapFull[$lc] ?? '#' . substr(md5($cv->color), 0, 6);
                                        $isGradient = str_contains($cssColor, 'gradient');
                                    @endphp
                                    <button type="button"
                                            class="pd-color-swatch {{ $loop->first ? 'pd-selected' : '' }}"
                                            data-color="{{ $cv->color }}"
                                            title="{{ ucfirst($cv->color) }}"
                                            style="{{ $isGradient ? 'background:'.$cssColor.';' : 'background-color:'.$cssColor.';' }}"
                                            onclick="selectColor(this, '{{ ucfirst($cv->color) }}')">
                                    </button>
                                @endforeach
                            @elseif($productColor)
                                @php
                                    $lc2 = strtolower(trim($productColor));
                                    $cssColor2 = $colorMapFull[$lc2] ?? '#' . substr(md5($productColor), 0, 6);
                                    $isGrad2 = str_contains($cssColor2, 'gradient');
                                @endphp
                                <span class="pd-color-swatch pd-selected"
                                      style="{{ $isGrad2 ? 'background:'.$cssColor2.';' : 'background-color:'.$cssColor2.';' }} cursor:default;"
                                      title="{{ ucfirst($productColor) }}"></span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Stock urgency --}}
                <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:12px 14px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    @if(!$stockAvailable)
                        <div style="width:8px;height:8px;border-radius:50%;background:#ef4444; flex-shrink:0;"></div>
                        <p style="font-size:.85rem; color:#9a3412; font-weight:600; margin:0;">{{ $stockMessage }}</p>
                    @elseif($totalStock <= 10)
                        <div style="width:8px;height:8px;border-radius:50%;background:#f97316; flex-shrink:0;" class="pd-stock-pulse"></div>
                        <p style="font-size:.85rem; color:#9a3412; font-weight:600; margin:0;">Hurry! Only {{ $totalStock }} left in stock</p>
                    @else
                        <div style="width:8px;height:8px;border-radius:50%;background:#22c55e; flex-shrink:0;"></div>
                        <p style="font-size:.85rem; color:#166534; font-weight:600; margin:0;">In Stock — Ready to Ship</p>
                    @endif
                </div>

                {{-- ── Variants / Size Selector ── --}}
                @if($product->variants->isNotEmpty())
                    <div style="margin-bottom:20px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <label style="font-size:.9rem; font-weight:700;">
                                Size
                                @if($product->gender)
                                    <span style="font-weight:400; color:#6b7280; margin-left:4px;">({{ ucfirst($product->gender) }})</span>
                                @endif
                            </label>
                            {{-- FIX #7: Size chart icon visible --}}
                            <button id="pd-size-chart-btn" type="button">
                                <i class="fas fa-ruler"></i> Size Chart
                            </button>
                        </div>

                        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:8px;" id="pd-size-grid">
                            @foreach($product->variants as $variant)
                                @php $isOos = $variant->quantity <= 0; @endphp
                                <button type="button"
                                        class="pd-size-btn"
                                        data-vid="{{ $variant->id }}"
                                        data-label="{{ $variant->waist_size }}{{ $variant->length ? '×'.$variant->length : '' }}{{ $variant->color ? ' · '.$variant->color : '' }}"
                                        {{ $isOos ? 'disabled' : '' }}>
                                    {{ $variant->waist_size }}{{ $variant->length ? ' / L'.$variant->length : '' }}
                                    @if($isOos)<br><span style="font-size:.65rem; font-weight:400;">OOS</span>@endif
                                </button>
                            @endforeach
                        </div>

                        <div id="pd-size-warn" style="display:none; margin-top:8px; font-size:.82rem; color:#dc2626; font-weight:600;">
                            <i class="fas fa-exclamation-circle"></i> Please select a size before adding to cart.
                        </div>
                        <div id="pd-size-chosen" style="display:none; margin-top:8px; font-size:.82rem; color:#16a34a; font-weight:700; align-items:center; gap:5px;">
                            <i class="fas fa-check-circle"></i> Size: <span id="pd-chosen-label"></span>
                        </div>

                        {{-- FIX #7: Fit guide icon visible --}}
                        <button id="pd-fit-guide-btn" type="button" style="margin-top:10px;">
                            <i class="fas fa-info-circle"></i>
                            @if($product->gender)
                                Fit Guide
                                @if($product->model_height) — Model is {{ $product->model_height }} @endif
                            @else
                                Fit Guide
                            @endif
                        </button>
                    </div>
                @endif

                {{-- ── Quantity ── --}}
                <div style="margin-bottom:20px;">
                    <label style="font-size:.9rem; font-weight:700; display:block; margin-bottom:10px;">Quantity</label>
                    <div style="display:flex; align-items:center; border:2px solid #d1d5db; border-radius:10px; width:fit-content; overflow:hidden;">
                        {{-- FIX #8: minus icon visible --}}
                        <button id="pd-qty-dec" type="button" class="pd-qty-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span id="pd-qty-val" style="padding:10px 20px; font-weight:700; font-size:1rem; min-width:52px; text-align:center;">1</span>
                        <button id="pd-qty-inc" type="button" class="pd-qty-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                {{-- ── CTA Buttons ── --}}
                {{-- Add to cart with site theme color, working AJAX --}}
                <meta name="atc-product-id" content="{{ $product->id }}">
                <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                    <button id="pd-add-cart-btn" type="button"
                            class="pd-btn-cart"
                            style="width:100%; border-radius:14px; padding:16px; font-size:1rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background .2s;"
                            {{ !$canPurchase ? 'disabled' : '' }}>
                        <i class="fas fa-shopping-bag"></i>
                        <span id="pd-cart-label">{{ $canPurchase ? 'Add to Cart' : 'Out of Stock' }}</span>
                    </button>
                    @if($canPurchase)
                        <a href="{{ route('checkout.index') }}"
                           class="pd-btn-buy"
                           style="width:100%; border-radius:14px; padding:16px; font-size:1rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; transition:background .2s; box-sizing:border-box;">
                            <i class="fas fa-bolt"></i> Buy Now
                        </a>
                    @endif
                </div>

                {{-- Cart success panel --}}
                <div id="pd-cart-panel" style="margin-bottom:20px;">
                    <div style="border:2px solid #22c55e; border-radius:14px; overflow:hidden; box-shadow:0 6px 24px rgba(34,197,94,.15);">
                        <div style="background:#22c55e; padding:10px 16px; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-check-circle" style="color:#fff; display:inline-block;"></i>
                            <span style="color:#fff; font-weight:700; font-size:.9rem; flex:1;">Added to Cart!</span>
                            <button onclick="document.getElementById('pd-cart-panel').style.display='none';"
                                    style="background:none;border:none;color:rgba(255,255,255,.8);font-size:1.3rem;cursor:pointer;padding:0;line-height:1;">×</button>
                        </div>
                        <div style="padding:12px 16px; display:flex; align-items:center; gap:12px; border-bottom:1px solid #f0f0f0; background:#fff;">
                            @if($product->images && $product->images->isNotEmpty())
                                <img src="{{ $firstUrl }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.84rem;font-weight:600;color:#222;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->name }}</div>
                                <div style="font-size:.8rem;color:var(--site-primary);font-weight:700;">₹{{ number_format($displayPrice, 0) }}</div>
                                <div id="pd-panel-size" style="font-size:.75rem;color:#888;"></div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:.7rem;color:#aaa;">In cart</div>
                                <div id="pd-panel-count" style="font-size:.95rem;font-weight:800;color:#333;"></div>
                            </div>
                        </div>
                        <div style="padding:12px 16px; display:flex; gap:10px; background:#fff; flex-wrap:wrap;">
                            <a href="{{ route('cart.index') }}" class="pd-panel-btn-outline">
                                <i class="fas fa-shopping-bag"></i> View Cart
                            </a>
                            <a href="{{ route('checkout.index') }}" class="pd-panel-btn-fill">
                                <i class="fas fa-bolt"></i> Place Order
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── FIX #10: Trust Badges — icons visible ── --}}
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px; padding-bottom:20px; border-bottom:1px solid #e5e7eb; margin-bottom:20px;">
                    <div style="text-align:center;">
                        <i class="fas fa-truck pd-badge-icon"></i>
                        <p style="font-size:.72rem; font-weight:600; margin:0; color:#374151; line-height:1.3;">Free Shipping</p>
                    </div>
                    <div style="text-align:center;">
                        <i class="fas fa-undo pd-badge-icon"></i>
                        <p style="font-size:.72rem; font-weight:600; margin:0; color:#374151; line-height:1.3;">Easy Returns</p>
                    </div>
                    <div style="text-align:center;">
                        <i class="fas fa-shield-alt pd-badge-icon"></i>
                        <p style="font-size:.72rem; font-weight:600; margin:0; color:#374151; line-height:1.3;">Secure Checkout</p>
                    </div>
                    <div style="text-align:center;">
                        <i class="fas fa-certificate pd-badge-icon"></i>
                        <p style="font-size:.72rem; font-weight:600; margin:0; color:#374151; line-height:1.3;">Quality Assured</p>
                    </div>
                </div>

                {{-- FIX #13: Share icons — visible and working --}}
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #e5e7eb;">
                    <span style="font-size:.8rem; color:#6b7280; font-weight:600;">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                       target="_blank" rel="noopener" class="pd-share-link" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($product->name) }}"
                       target="_blank" rel="noopener" class="pd-share-link" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($product->name.' '.request()->fullUrl()) }}"
                       target="_blank" rel="noopener" class="pd-share-link" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}&description={{ urlencode($product->name) }}"
                       target="_blank" rel="noopener" class="pd-share-link" title="Share on Pinterest">
                        <i class="fab fa-pinterest-p"></i>
                    </a>
                    <button class="pd-share-link" onclick="copyPageUrl(this)" title="Copy link" style="cursor:pointer; border:1.5px solid #e5e7eb; background:#fff;">
                        <i class="fas fa-link"></i>
                    </button>
                </div>

                {{-- ── FIX #12: Accordions — proper box design ── --}}
                <div>
                    {{-- Description --}}
                    <div class="pd-accordion">
                        <button class="pd-acc-btn" type="button">
                            <span style="font-weight:700; font-size:.95rem; color:#111;">Description</span>
                            <i class="fas fa-chevron-down pd-acc-icon"></i>
                        </button>
                        <div class="pd-accordion-content">
                            <div style="font-size:.875rem; color:#4b5563; line-height:1.8;">
                                {!! $product->description ?? '<p>No description available.</p>' !!}
                            </div>
                        </div>
                    </div>

                    {{-- Material & Care --}}
                    <div class="pd-accordion">
                        <button class="pd-acc-btn" type="button">
                            <span style="font-weight:700; font-size:.95rem; color:#111;">Material &amp; Care</span>
                            <i class="fas fa-chevron-down pd-acc-icon"></i>
                        </button>
                        <div class="pd-accordion-content">
                            <div style="font-size:.875rem; color:#4b5563; line-height:1.8;">
                                @if($product->fabric)
                                    <p style="margin:0 0 8px;"><strong>Fabric:</strong> {{ $product->fabric }}</p>
                                @endif
                                @if($product->wash)
                                    <p style="margin:0 0 8px;"><strong>Wash Care:</strong> {{ $product->wash }}</p>
                                @endif
                                @if(!$product->fabric && !$product->wash)
                                    <p style="margin:0;">Machine wash cold. Tumble dry low. Do not bleach. Iron on medium heat.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Size & Fit --}}
                    <div class="pd-accordion">
                        <button class="pd-acc-btn" type="button">
                            <span style="font-weight:700; font-size:.95rem; color:#111;">Size &amp; Fit</span>
                            <i class="fas fa-chevron-down pd-acc-icon"></i>
                        </button>
                        <div class="pd-accordion-content">
                            <div style="font-size:.875rem; color:#4b5563; line-height:1.8;">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0;">
                                    @foreach([
                                        ['Fit Type',   $product->fit_type ?? null],
                                        ['Stretch',    $product->stretch ?? null],
                                        ['Waist Rise', $product->waist_rise ?? null],
                                        ['Gender',     ucfirst($product->gender ?? '')],
                                        ['SKU',        $product->sku ?? null],
                                        ['Brand',      $product->brand->name ?? null],
                                        ['Country',    $product->country_of_origin ?? null],
                                    ] as [$label, $val])
                                        @if($val)
                                            <div style="padding:8px 0; border-bottom:1px solid #f3f4f6; font-size:.82rem;">
                                                <span style="color:#9ca3af; display:block; margin-bottom:2px;">{{ $label }}</span>
                                                <span style="color:#111; font-weight:600;">{{ $val }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping & Returns --}}
                    <div class="pd-accordion">
                        <button class="pd-acc-btn" type="button">
                            <span style="font-weight:700; font-size:.95rem; color:#111;">Shipping &amp; Returns</span>
                            <i class="fas fa-chevron-down pd-acc-icon"></i>
                        </button>
                        <div class="pd-accordion-content">
                            <div style="font-size:.875rem; color:#4b5563; line-height:1.8; display:flex; flex-direction:column; gap:12px;">
                                <div>
                                    <p style="font-weight:700; color:#111; margin:0 0 4px;"><i class="fas fa-truck" style="color:var(--site-primary);margin-right:5px;"></i>Free Shipping</p>
                                    <p style="margin:0;">Free standard shipping on orders above ₹999. Delivery in 3–5 business days.</p>
                                </div>
                                <div>
                                    <p style="font-weight:700; color:#111; margin:0 0 4px;"><i class="fas fa-undo" style="color:var(--site-primary);margin-right:5px;"></i>Easy Returns</p>
                                    <p style="margin:0;">7-day return policy. Free returns &amp; exchanges. Item must be unworn with tags attached.</p>
                                </div>
                                <div>
                                    <p style="font-weight:700; color:#111; margin:0 0 4px;"><i class="fas fa-money-bill" style="color:var(--site-primary);margin-right:5px;"></i>COD Available</p>
                                    <p style="margin:0;">Cash on Delivery available on all orders. Additional ₹50 COD fee applies.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- end right col --}}
        </div>{{-- end main grid --}}

        {{-- ── Related Products ── --}}
        @if($relatedProducts->isNotEmpty())
            <section style="margin-top:80px;">
                <h2 style="font-size:1.5rem; font-weight:800; margin:0 0 24px; color:#111;">You May Also Like</h2>
                <div style="display:flex; gap:16px; overflow-x:auto; padding-bottom:12px;" class="pd-scroll-hide">
                    @foreach($relatedProducts->take(8) as $related)
                        @php
                            $rImg    = $related->images->first();
                            $rRel    = $rImg ? 'products/'.$related->id.'/images/'.($rImg->image ?? '') : '';
                            $rUrl    = $rRel ? (file_exists(public_path('storage/'.$rRel)) ? asset('storage/'.$rRel) : asset('storage/default.jpeg')) : asset('storage/default.jpeg');
                            $rPrice  = $related->jeanzo_price ?? $related->sale_price ?? $related->price;
                            $rOriginal = $related->price;
                        @endphp
                        <a href="{{ route('products.detail', $related->slug) }}"
                           class="pd-prod-card"
                           style="flex-shrink:0; width:220px; text-decoration:none; color:inherit;">
                            <div style="background:#f9fafb; border-radius:14px; overflow:hidden; aspect-ratio:3/4; margin-bottom:10px; position:relative;">
                                <img src="{{ $rUrl }}" alt="{{ $related->name }}"
                                     class="pd-prod-img"
                                     style="width:100%; height:100%; object-fit:cover; display:block;">
                                @auth
                                    <form method="POST" action="{{ route('wishlist.add') }}" style="position:absolute;top:10px;right:10px;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $related->id }}">
                                        <button type="submit" class="pd-wish-btn"
                                                style="background:#fff;width:32px;height:32px;border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:0;transition:opacity .2s; box-shadow: 0 2px 8px rgba(0,0,0,.15);">
                                            <i class="far fa-heart" style="font-size:.8rem;color:var(--site-primary);"></i>
                                        </button>
                                    </form>
                                @endauth
                            </div>
                            <h4 style="font-size:.85rem; font-weight:600; margin:0 0 4px; color:#111; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $related->name }}</h4>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="color:var(--site-primary); font-weight:700; font-size:.9rem;">₹{{ number_format($rPrice, 0) }}</span>
                                @if($rOriginal > $rPrice)
                                    <span style="color:#9ca3af; font-size:.75rem; text-decoration:line-through;">₹{{ number_format($rOriginal, 0) }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── FIX #11: Customer Reviews — same design, stars visible ── --}}
        <section id="pd-reviews" style="margin-top:80px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:10px;">
                <h2 style="font-size:1.5rem; font-weight:800; margin:0; color:#111;">Customer Reviews</h2>
                @auth
                    <button id="pd-review-toggle"
                            style="font-size:.85rem; color:var(--site-primary); font-weight:700; background:none; border:none; cursor:pointer; text-decoration:underline;">
                        Write a Review
                    </button>
                @else
                    <a href="{{ route('customer.login') }}" style="font-size:.85rem; color:var(--site-primary); font-weight:700; text-decoration:underline;">Login to Review</a>
                @endauth
            </div>

            {{-- Rating summary --}}
            <div style="background:#f9fafb; border-radius:16px; padding:24px; margin-bottom:32px; border:1px solid #e5e7eb;">
                <div style="display:flex; gap:32px; flex-wrap:wrap; align-items:flex-start;">
                    <div style="text-align:center; min-width:120px;">
                        <div style="font-size:3.5rem; font-weight:800; line-height:1; color:#111; margin-bottom:4px;">{{ number_format($avgRating, 1) }}</div>
                        <div style="display:flex; gap:2px; justify-content:center; margin-bottom:6px;">
                            @for($s=1;$s<=5;$s++)
                                @if($s <= floor($avgRating))
                                    <i class="fas fa-star pd-review-star"></i>
                                @elseif($s - 0.5 <= $avgRating)
                                    <i class="fas fa-star-half-alt pd-review-star"></i>
                                @else
                                    <i class="fas fa-star pd-review-star" style="opacity:.3;"></i>
                                @endif
                            @endfor
                        </div>
                        <p style="font-size:.75rem; color:#6b7280; margin:0;">{{ $reviewCount }} reviews</p>
                    </div>
                    @php
                        $approvedReviews = $product->reviews ? $product->reviews->where('is_approved', true) : collect();
                        $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                        foreach($approvedReviews as $rv) { if(isset($starCounts[$rv->rating])) $starCounts[$rv->rating]++; }
                    @endphp
                    <div style="flex:1; min-width:200px; display:flex; flex-direction:column; gap:8px; justify-content:center;">
                        @foreach([5,4,3,2,1] as $star)
                            @php $pct = $reviewCount > 0 ? round($starCounts[$star] / $reviewCount * 100) : 0; @endphp
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-size:.8rem; width:28px; color:#374151;">{{ $star }}<i class="fas fa-star" style="color:#f59e0b; font-size:.65rem; margin-left:1px;"></i></span>
                                <div class="pd-review-bar" style="flex:1;">
                                    <div class="pd-review-bar-fill" style="width:{{ $pct }}%;"></div>
                                </div>
                                <span style="font-size:.8rem; color:#6b7280; width:36px; text-align:right;">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Write review form --}}
            @auth
                <div id="pd-review-form" style="display:none; margin-bottom:32px;">
                    <div style="background:#f9fafb; border-radius:14px; padding:20px; border:1px solid #e5e7eb;">
                        <h3 style="font-size:1rem; font-weight:700; margin:0 0 16px; color:#111;">Write a Review</h3>
                        <form method="POST" action="{{ route('products.review', $product->id) ?? '#' }}">
                            @csrf
                            <div style="margin-bottom:14px;">
                                <label style="font-size:.85rem; font-weight:600; display:block; margin-bottom:6px;">Your Rating</label>
                                <div id="pd-stars">
                                    @for($s=1;$s<=5;$s++)
                                        <i class="far fa-star pd-star-btn" data-val="{{ $s }}"
                                           style="cursor:pointer; font-size:1.6rem; color:#f59e0b; margin-right:4px; display:inline-block;"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="pd-rating-val" value="0">
                            </div>
                            <div style="margin-bottom:14px;">
                                <label style="font-size:.85rem; font-weight:600; display:block; margin-bottom:6px;">Your Review</label>
                                <textarea name="review_text" rows="4"
                                          style="width:100%; border:1.5px solid #d1d5db; border-radius:10px; padding:10px 14px; font-size:.875rem; resize:vertical; outline:none; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif;"
                                          placeholder="Share your experience with this product…" required></textarea>
                            </div>
                            <button type="submit"
                                    style="background:var(--site-primary); color:#fff; border:none; border-radius:10px; padding:12px 28px; font-size:.9rem; font-weight:700; cursor:pointer;">
                                Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            {{-- Review list --}}
            @php $avatarColors = ['#F7EDEC','#f3e8ff','#fce7f3','#dcfce7','#fef9c3']; @endphp
            @if($approvedReviews->isNotEmpty())
                <div style="display:flex; flex-direction:column; gap:0;">
                    @foreach($approvedReviews->take(5) as $idx => $review)
                        @php $bg = $avatarColors[$idx % count($avatarColors)]; $initial = strtoupper(substr($review->user->name ?? 'A', 0, 1)); @endphp
                        <div style="padding:20px 0; border-bottom:1px solid #f3f4f6;">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:10px; gap:12px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:42px; height:42px; border-radius:50%; background:{{ $bg }}; display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--site-primary); flex-shrink:0; font-size:.9rem; border:2px solid var(--site-primary);">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <p style="font-weight:600; font-size:.875rem; margin:0 0 4px;">{{ $review->user->name ?? 'Anonymous' }}</p>
                                        <div style="display:flex; align-items:center; gap:2px; margin-bottom:3px;">
                                            {{-- FIX #11: Review stars visible --}}
                                            @for($s=1;$s<=5;$s++)
                                                <i class="{{ $s <= $review->rating ? 'fas' : 'far' }} fa-star pd-review-star"></i>
                                            @endfor
                                            <span style="font-size:.72rem; color:#9ca3af; margin-left:6px;">Verified Purchase</span>
                                        </div>
                                    </div>
                                </div>
                                <span style="font-size:.75rem; color:#9ca3af; white-space:nowrap;">{{ optional($review->created_at)->diffForHumans() }}</span>
                            </div>
                            <p style="font-size:.875rem; color:#374151; margin:0; line-height:1.7;">{{ $review->review_text }}</p>
                        </div>
                    @endforeach
                </div>
                @if($approvedReviews->count() > 5)
                    <button style="width:100%; margin-top:20px; border:2px solid #e5e7eb; background:#fff; border-radius:12px; padding:14px; font-size:.9rem; font-weight:700; color:#374151; cursor:pointer; transition:border-color .2s; font-family:'Plus Jakarta Sans',sans-serif;"
                            onmouseover="this.style.borderColor='var(--site-primary)'" onmouseout="this.style.borderColor='#e5e7eb'">
                        Load More Reviews
                    </button>
                @endif
            @else
                <p style="color:#6b7280; font-size:.9rem;">No reviews yet. Be the first to share your experience!</p>
            @endif
        </section>

    </main>

    {{-- ── Sticky Add-to-Cart / Post-Cart Bar ── --}}
    <div id="pd-sticky"
         style="position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:1px solid #ebe8e8; padding:10px 16px 12px; z-index:40; transform:translateY(100%); box-shadow:0 -6px 24px rgba(0,0,0,.10); font-family:'Plus Jakarta Sans',sans-serif;">
        <div style="max-width:640px; margin:0 auto; overflow:hidden;">

            {{-- State A: Not yet added — show product info + Add to Cart --}}
            <div id="pd-sticky-default" style="display:flex; align-items:center; gap:12px; flex-wrap:nowrap;">
                <div style="flex:1 1 0%; min-width:0; overflow:hidden;">
                    <p style="font-size:.72rem; color:#9ca3af; margin:0 0 1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;" id="pd-sticky-size">{{ $product->name }}</p>
                    <p style="font-weight:800; font-size:1.05rem; margin:0; color:#111; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">₹{{ number_format($displayPrice, 0) }}</p>
                </div>
                <button id="pd-sticky-btn"
                        style="background:var(--site-primary); color:#fff; border:none; border-radius:12px; padding:13px 28px; font-weight:700; font-size:.9rem; cursor:pointer; white-space:nowrap; display:flex; align-items:center; gap:7px; font-family:'Plus Jakarta Sans',sans-serif; flex-shrink:0; flex-grow:0;">
                    <i class="fas fa-shopping-bag" style="font-size:.85rem;"></i> Add to Cart
                </button>
            </div>

            {{-- State B: Item added — show View Cart + Place Order --}}
            <div id="pd-sticky-added" style="display:none;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                    <div style="width:20px; height:20px; background:#22c55e; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-check" style="color:#fff; font-size:.6rem;"></i>
                    </div>
                    <p style="font-size:.8rem; font-weight:600; color:#16a34a; margin:0;" id="pd-sticky-confirm">Added to cart!</p>
                    <button onclick="switchStickyToDefault()" style="background:none;border:none;color:#9ca3af;font-size:.75rem;cursor:pointer;margin-left:auto;padding:0;font-family:'Plus Jakarta Sans',sans-serif;">✕ Continue shopping</button>
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('cart.index') }}"
                       style="flex:1; display:flex; align-items:center; justify-content:center; gap:7px; padding:13px 10px; border:2px solid var(--site-primary); border-radius:12px; font-weight:700; font-size:.88rem; color:var(--site-primary); text-decoration:none; font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-shopping-bag" style="font-size:.85rem;"></i> View Cart
                    </a>
                    <a href="{{ route('checkout.index') }}"
                       style="flex:1; display:flex; align-items:center; justify-content:center; gap:7px; padding:13px 10px; background:var(--site-primary); border:2px solid var(--site-primary); border-radius:12px; font-weight:700; font-size:.88rem; color:#fff; text-decoration:none; font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-bolt" style="font-size:.85rem;"></i> Place Order
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Toast Notification ── --}}
    <div id="pd-toast"
         style="position:fixed; top:80px; right:16px; background:#111; color:#fff; padding:12px 20px; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.2); z-index:60; transform:translateX(calc(100% + 32px)); display:flex; align-items:center; gap:8px; font-size:.875rem; font-weight:500;">
        <i class="fas fa-check-circle" style="color:#4ade80; display:inline-block;"></i>
        <span id="pd-toast-msg">Done!</span>
    </div>

    {{-- ── Size Chart Modal ── --}}
    <div id="pd-size-modal"
         style="position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:50; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:20px; max-width:680px; width:100%; max-height:90vh; overflow-y:auto;">
            <div style="position:sticky; top:0; background:#fff; border-bottom:1px solid #e5e7eb; padding:20px 24px; display:flex; align-items:center; justify-content:space-between; z-index:1;">
                <h3 style="font-size:1.1rem; font-weight:800; margin:0; color:#111;"><i class="fas fa-ruler" style="color:var(--site-primary); margin-right:8px;"></i>Size Chart</h3>
                <button id="pd-size-close"
                        style="background:none; border:none; cursor:pointer; color:#6b7280; font-size:1.3rem; padding:0; line-height:1;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div style="padding:24px;">
                <p style="font-size:.85rem; color:#6b7280; margin:0 0 16px;">Find your perfect fit. All measurements in inches.</p>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:.875rem;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="padding:12px 16px; text-align:left; font-weight:700; color:#111; border-bottom:1px solid #e5e7eb;">Size</th>
                                <th style="padding:12px 16px; text-align:left; font-weight:700; color:#111; border-bottom:1px solid #e5e7eb;">Waist</th>
                                <th style="padding:12px 16px; text-align:left; font-weight:700; color:#111; border-bottom:1px solid #e5e7eb;">Hip</th>
                                <th style="padding:12px 16px; text-align:left; font-weight:700; color:#111; border-bottom:1px solid #e5e7eb;">Inseam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants->where('quantity', '>', 0) as $v)
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:10px 16px; font-weight:600;">{{ $v->waist_size }}</td>
                                    <td style="padding:10px 16px; color:#374151;">{{ $v->waist_size }}"</td>
                                    <td style="padding:10px 16px; color:#374151;">{{ ($v->waist_size + 8) }}"</td>
                                    <td style="padding:10px 16px; color:#374151;">{{ $v->length ?? 30 }}"</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:20px; background:var(--site-primary-light); border-radius:10px; padding:14px 16px; border-left:3px solid var(--site-primary);">
                    <p style="font-size:.85rem; font-weight:700; color:var(--site-primary); margin:0 0 4px;">
                        <i class="fas fa-lightbulb" style="display:inline-block;"></i> Fit Tip
                    </p>
                    <p style="font-size:.825rem; color:#374151; margin:0;">For a snug fit, size down. For a relaxed fit, size up. Our denim has stretch for comfort.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Fit Guide Modal ── --}}
    <div id="pd-fit-modal"
         style="position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:50; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:20px; max-width:480px; width:100%; overflow:hidden;">
            <div style="border-bottom:1px solid #e5e7eb; padding:20px 24px; display:flex; align-items:center; justify-content:space-between;">
                <h3 style="font-size:1.1rem; font-weight:800; margin:0; color:#111;"><i class="fas fa-info-circle" style="color:var(--site-primary); margin-right:8px;"></i>Fit Guide</h3>
                <button id="pd-fit-close"
                        style="background:none; border:none; cursor:pointer; color:#6b7280; font-size:1.3rem; padding:0; line-height:1;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">
                <div style="display:flex; flex-direction:column; gap:10px; font-size:.875rem;">
                    @if($product->fit_type ?? $product->waist_rise ?? null)
                        @if($product->fit_type ?? null)
                            <div style="padding:12px; background:#f9fafb; border-radius:10px;">
                                <p style="font-weight:700; color:#111; margin:0 0 3px;">Fit Type</p>
                                <p style="color:#4b5563; margin:0;">{{ $product->fit_type }}</p>
                            </div>
                        @endif
                        @if($product->waist_rise ?? null)
                            <div style="padding:12px; background:#f9fafb; border-radius:10px;">
                                <p style="font-weight:700; color:#111; margin:0 0 3px;">Rise</p>
                                <p style="color:#4b5563; margin:0;">{{ $product->waist_rise }}</p>
                            </div>
                        @endif
                    @else
                        <div style="padding:12px; background:#f9fafb; border-radius:10px;">
                            <p style="color:#6b7280; margin:0;">This product fits true to size. If between sizes, we recommend sizing up.</p>
                        </div>
                    @endif
                    @if($product->stretch ?? null)
                        <div style="padding:12px; background:#f9fafb; border-radius:10px;">
                            <p style="font-weight:700; color:#111; margin:0 0 3px;">Stretch</p>
                            <p style="color:#4b5563; margin:0;">{{ $product->stretch }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>{{-- end #pd-page --}}
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── State ── */
    var qty          = 1;
    var selectedVid  = null;
    var selectedLabel= '';
    var isAdding     = false;
    var hasVariants  = document.querySelectorAll('.pd-size-btn').length > 0;

    /* Get product ID and CSRF from meta tags */
    var productId = document.querySelector('meta[name="atc-product-id"]')
                  ? document.querySelector('meta[name="atc-product-id"]').content
                  : null;
    /* Look for csrf-token in both the global header meta and the local page meta */
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.content : null;

    /* Derive cart.add URL safely from window.location */
    var cartAddUrl = window.location.origin + '/cart/add';

    /* ── Sticky state switch helpers ── */
    function switchStickyToAdded(label) {
        var def = document.getElementById('pd-sticky-default');
        var added = document.getElementById('pd-sticky-added');
        var confirm = document.getElementById('pd-sticky-confirm');
        if (def)     def.style.display = 'none';
        if (added)   added.style.display = 'block';
        if (confirm && label) confirm.textContent = 'Added: ' + label + ' — in cart';
    }
    window.switchStickyToDefault = function() {
        var def = document.getElementById('pd-sticky-default');
        var added = document.getElementById('pd-sticky-added');
        if (def)   def.style.display = 'flex';
        if (added) added.style.display = 'none';
    };

    /* ── Helpers ── */
    function showToast(msg, isError) {
        var t = document.getElementById('pd-toast');
        document.getElementById('pd-toast-msg').textContent = msg;
        if (isError) {
            t.style.background = '#dc2626';
        } else {
            t.style.background = '#111';
        }
        t.style.transform = 'translateX(0)';
        setTimeout(function () { t.style.transform = 'translateX(calc(100% + 32px))'; t.style.background = '#111'; }, 3000);
    }

    function openModal(id)  { var el = document.getElementById(id); if(el){ el.style.display='flex'; el.classList.add('open'); } }
    function closeModal(id) { var el = document.getElementById(id); if(el){ el.style.display='none'; el.classList.remove('open'); } }

    /* ── Thumbnail gallery + scrollable strip ── */
    (function() {
        var thumbs   = document.querySelectorAll('.pd-thumb');
        var mainImg  = document.getElementById('pd-main-img');
        var track    = document.getElementById('pd-thumb-track');
        var prevBtn  = document.getElementById('pd-thumb-prev');
        var nextBtn  = document.getElementById('pd-thumb-next');
        var wrap     = document.querySelector('.pd-thumb-strip-wrap');

        // Show arrows only if thumbnails overflow
        function checkArrows() {
            if (!track || !wrap) return;
            if (track.scrollWidth > track.clientWidth + 4) {
                wrap.classList.add('has-arrows');
            } else {
                wrap.classList.remove('has-arrows');
            }
        }
        window.addEventListener('resize', checkArrows);
        checkArrows();

        // Arrow scroll: one thumbnail width at a time
        var SCROLL_AMT = 88; // ~80px thumb + 8px gap
        if (prevBtn) prevBtn.addEventListener('click', function() {
            track.scrollBy({ left: -SCROLL_AMT * 3, behavior: 'smooth' });
        });
        if (nextBtn) nextBtn.addEventListener('click', function() {
            track.scrollBy({ left: SCROLL_AMT * 3, behavior: 'smooth' });
        });

        // Thumbnail click → update main image + scroll active into view
        thumbs.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                thumbs.forEach(function(t){ t.classList.remove('active'); });
                thumb.classList.add('active');
                if (mainImg) {
                    mainImg.style.transition = 'opacity .18s ease';
                    mainImg.style.opacity = '0';
                    setTimeout(function() {
                        mainImg.src = thumb.dataset.img;
                        mainImg.style.opacity = '1';
                    }, 140);
                }
                // Scroll this thumb into view inside the track
                if (track) {
                    var thumbLeft   = thumb.offsetLeft;
                    var thumbRight  = thumbLeft + thumb.offsetWidth;
                    var trackLeft   = track.scrollLeft;
                    var trackRight  = trackLeft + track.clientWidth;
                    if (thumbLeft < trackLeft) {
                        track.scrollBy({ left: thumbLeft - trackLeft - 8, behavior: 'smooth' });
                    } else if (thumbRight > trackRight) {
                        track.scrollBy({ left: thumbRight - trackRight + 8, behavior: 'smooth' });
                    }
                }
            });
        });

        // ── Auto-advance thumbnails every 5 s when idle ──
        var autoTimer;
        var userInteracted = false; // once user taps, auto stops for good on this page

        function getActiveIndex() {
            var idx = 0;
            thumbs.forEach(function(t, i){ if(t.classList.contains('active')) idx = i; });
            return idx;
        }
        function autoAdvance() {
            var cur  = getActiveIndex();
            var next = (cur + 1) % thumbs.length;
            if (!thumbs[next]) return;
            // advance silently (bypass click handler)
            thumbs.forEach(function(t){ t.classList.remove('active'); });
            thumbs[next].classList.add('active');
            if (mainImg) {
                mainImg.style.transition = 'opacity .18s ease';
                mainImg.style.opacity = '0';
                setTimeout(function() {
                    mainImg.src = thumbs[next].dataset.img;
                    mainImg.style.opacity = '1';
                }, 140);
            }
            // scroll thumb into view
            if (track) {
                var th = thumbs[next];
                var tL = th.offsetLeft, tR = tL + th.offsetWidth;
                var trL = track.scrollLeft, trR = trL + track.clientWidth;
                if (tL < trL) track.scrollBy({ left: tL - trL - 8, behavior: 'smooth' });
                else if (tR > trR) track.scrollBy({ left: tR - trR + 8, behavior: 'smooth' });
            }
        }
        function startAuto() {
            stopAuto();
            if (thumbs.length > 1 && !userInteracted) {
                autoTimer = setInterval(autoAdvance, 5000);
            }
        }
        function stopAuto() { clearInterval(autoTimer); autoTimer = null; }

        // Thumbnail click — user took over, stop auto permanently
        thumbs.forEach(function(t) {
            t.addEventListener('click', function() {
                userInteracted = true;
                stopAuto();
            });
        });

        // Desktop hover: pause on strip hover, resume on leave (only if not interacted)
        if (track) {
            track.addEventListener('mouseenter', stopAuto);
            track.addEventListener('mouseleave', function() {
                if (!userInteracted) startAuto();
            });
        }
        startAuto();

        // ── Touch/swipe on main image to change photo (mobile) ──
        var touchStartX = 0;
        var touchStartY = 0;
        var isSwiping   = false;
        var imgWrap = document.querySelector('.pd-img-aspect');
        if (imgWrap) {
            imgWrap.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].clientX;
                touchStartY = e.changedTouches[0].clientY;
                isSwiping   = false;
            }, { passive: true });

            imgWrap.addEventListener('touchmove', function(e) {
                var dx = Math.abs(e.changedTouches[0].clientX - touchStartX);
                var dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
                if (dx > dy && dx > 10) isSwiping = true;
            }, { passive: true });

            imgWrap.addEventListener('touchend', function(e) {
                if (!isSwiping) return; // vertical scroll or tap — ignore
                // Don't swipe images when zoomed in (zoom JS handles touch then)
                if (imgWrap.classList.contains('zoomed')) { isSwiping = false; return; }
                var dx = e.changedTouches[0].clientX - touchStartX;
                if (Math.abs(dx) > 30) {
                    userInteracted = true;
                    stopAuto();
                    var cur  = getActiveIndex();
                    var next = dx < 0
                        ? (cur + 1) % thumbs.length
                        : (cur - 1 + thumbs.length) % thumbs.length;
                    if (thumbs[next]) thumbs[next].click();
                }
                isSwiping = false;
            }, { passive: true });
        }
    })();

    /* ── Unified Image Zoom: desktop hover + mobile double-tap + pinch ── */
    (function() {
        var aspect = document.querySelector('.pd-img-aspect');
        var img    = document.getElementById('pd-main-img');
        if (!aspect || !img) return;

        var isMobile = window.matchMedia('(max-width: 767px)').matches;
        var ZOOM_SCALE_DESKTOP = 2.4;
        var ZOOM_SCALE_MOBILE  = 2.8;

        /* ── DESKTOP: hover to zoom, mouse tracks origin ── */
        if (!isMobile) {
            var desktopZoomed = false;

            aspect.addEventListener('mousemove', function(e) {
                var rect = aspect.getBoundingClientRect();
                var x = ((e.clientX - rect.left) / rect.width  * 100).toFixed(2);
                var y = ((e.clientY - rect.top)  / rect.height * 100).toFixed(2);
                img.style.transformOrigin = x + '% ' + y + '%';
                if (desktopZoomed) return;
                desktopZoomed = true;
                img.style.transform = 'scale(' + ZOOM_SCALE_DESKTOP + ')';
                aspect.classList.add('zoomed');
            });
            aspect.addEventListener('mouseleave', function() {
                desktopZoomed = false;
                img.style.transform = 'scale(1)';
                img.style.transformOrigin = 'center center';
                aspect.classList.remove('zoomed');
            });
            return; // desktop done
        }

        /* ── MOBILE: double-tap to toggle zoom + pinch-to-zoom + drag when zoomed ── */
        var mobileZoomed  = false;
        var currentScale  = 1;
        var originX       = 50; // % from left
        var originY       = 50; // % from top
        var lastTap       = 0;
        var tapTimer      = null;

        // Pinch state
        var pinchStartDist = 0;
        var pinchStartScale = 1;
        var activeTouches  = 0;

        // Drag state (when zoomed)
        var dragStartX = 0, dragStartY = 0;
        var imgTransX  = 0, imgTransY  = 0;
        var dragStartTransX = 0, dragStartTransY = 0;
        var isDragging = false;

        function applyTransform(scale, ox, oy, tx, ty) {
            img.style.transformOrigin = ox + '% ' + oy + '%';
            img.style.transform = 'scale(' + scale + ') translate(' + tx + 'px,' + ty + 'px)';
        }
        function resetZoom() {
            mobileZoomed = false;
            currentScale = 1;
            originX = 50; originY = 50;
            imgTransX = 0; imgTransY = 0;
            img.style.transition = 'transform .3s ease';
            applyTransform(1, 50, 50, 0, 0);
            aspect.classList.remove('zoomed');
            // re-enable swipe after a tick
            setTimeout(function() { aspect.style.touchAction = 'none'; }, 350);
        }
        function zoomInAt(px, py) {
            var rect = aspect.getBoundingClientRect();
            originX  = ((px - rect.left) / rect.width  * 100);
            originY  = ((py - rect.top)  / rect.height * 100);
            currentScale = ZOOM_SCALE_MOBILE;
            imgTransX = 0; imgTransY = 0;
            mobileZoomed = true;
            img.style.transition = 'transform .3s ease';
            applyTransform(currentScale, originX, originY, 0, 0);
            aspect.classList.add('zoomed');
        }

        /* Double-tap detection */
        aspect.addEventListener('touchend', function(e) {
            if (activeTouches > 1) return; // ignore pinch lift
            var now = Date.now();
            var touch = e.changedTouches[0];
            if (now - lastTap < 300) {
                // double tap
                clearTimeout(tapTimer);
                e.preventDefault();
                if (mobileZoomed) {
                    resetZoom();
                } else {
                    zoomInAt(touch.clientX, touch.clientY);
                }
                lastTap = 0;
            } else {
                lastTap = now;
            }
        }, { passive: false });

        /* Pinch-to-zoom */
        function getTouchDist(touches) {
            var dx = touches[0].clientX - touches[1].clientX;
            var dy = touches[0].clientY - touches[1].clientY;
            return Math.sqrt(dx * dx + dy * dy);
        }
        aspect.addEventListener('touchstart', function(e) {
            activeTouches = e.touches.length;
            if (e.touches.length === 2) {
                pinchStartDist  = getTouchDist(e.touches);
                pinchStartScale = currentScale;
                img.style.transition = 'none';
            } else if (e.touches.length === 1 && mobileZoomed) {
                // start drag
                isDragging    = true;
                dragStartX    = e.touches[0].clientX;
                dragStartY    = e.touches[0].clientY;
                dragStartTransX = imgTransX;
                dragStartTransY = imgTransY;
                img.style.transition = 'none';
            }
        }, { passive: true });

        aspect.addEventListener('touchmove', function(e) {
            activeTouches = e.touches.length;
            if (e.touches.length === 2) {
                e.preventDefault();
                var dist  = getTouchDist(e.touches);
                var scale = Math.min(4, Math.max(1, pinchStartScale * (dist / pinchStartDist)));
                currentScale  = scale;
                mobileZoomed  = scale > 1.05;
                // set origin to midpoint of two fingers
                var rect = aspect.getBoundingClientRect();
                var mx = ((e.touches[0].clientX + e.touches[1].clientX) / 2 - rect.left) / rect.width * 100;
                var my = ((e.touches[0].clientY + e.touches[1].clientY) / 2 - rect.top)  / rect.height * 100;
                originX = mx; originY = my;
                applyTransform(scale, mx, my, imgTransX, imgTransY);
                if (mobileZoomed) aspect.classList.add('zoomed');
                else aspect.classList.remove('zoomed');
            } else if (e.touches.length === 1 && isDragging && mobileZoomed) {
                e.preventDefault();
                var ddx = (e.touches[0].clientX - dragStartX) / currentScale;
                var ddy = (e.touches[0].clientY - dragStartY) / currentScale;
                imgTransX = dragStartTransX + ddx;
                imgTransY = dragStartTransY + ddy;
                applyTransform(currentScale, originX, originY, imgTransX, imgTransY);
            }
        }, { passive: false });

        aspect.addEventListener('touchend', function(e) {
            activeTouches = e.touches.length;
            isDragging = false;
            // Snap back if scale dropped below threshold
            if (currentScale < 1.05) resetZoom();
        }, { passive: true });

    })();

    /* ── Color selection (FIX #5) ── */
    window.selectColor = function(btn, colorName) {
        document.querySelectorAll('.pd-color-swatch').forEach(function(s){ s.classList.remove('pd-selected'); });
        btn.classList.add('pd-selected');
        var lbl = document.getElementById('pd-color-label');
        if (lbl) lbl.textContent = '— ' + colorName;
    };

    /* ── Size selection ── */
    function selectSizeBtn(btn) {
        document.querySelectorAll('.pd-size-btn').forEach(function(b){ b.classList.remove('pd-selected'); });
        btn.classList.add('pd-selected');
        selectedVid   = btn.dataset.vid;
        selectedLabel = btn.dataset.label;

        var warn = document.getElementById('pd-size-warn');
        var chosen = document.getElementById('pd-size-chosen');
        if(warn)   warn.style.display = 'none';
        if(chosen) {
            chosen.style.display = 'flex';
            var cl = document.getElementById('pd-chosen-label');
            if(cl) cl.textContent = selectedLabel;
        }

        var stickySize = document.getElementById('pd-sticky-size');
        if(stickySize) stickySize.textContent = 'Size: ' + selectedLabel;
    }

    document.querySelectorAll('.pd-size-btn:not([disabled])').forEach(function (btn) {
        btn.addEventListener('click', function () { selectSizeBtn(btn); });
    });

    /* Auto-select the first available size on page load */
    var firstAvailableSize = document.querySelector('.pd-size-btn:not([disabled])');
    if (firstAvailableSize) { selectSizeBtn(firstAvailableSize); }

    /* ── Quantity ── */
    var qtyVal = document.getElementById('pd-qty-val');
    var qdec = document.getElementById('pd-qty-dec');
    var qinc = document.getElementById('pd-qty-inc');
    if(qdec) qdec.addEventListener('click', function () {
        if(qty > 1) { qty--; if(qtyVal) qtyVal.textContent = qty; }
    });
    if(qinc) qinc.addEventListener('click', function () {
        qty++; if(qtyVal) qtyVal.textContent = qty;
    });

    /* ── FIX #14 & #15: Add to Cart — working AJAX, no CDN URL, proper error handling ── */
    function doAddToCart() {
        if(isAdding) return;

        if(hasVariants && !selectedVid) {
            var warn = document.getElementById('pd-size-warn');
            if(warn) warn.style.display = 'block';
            var grid = document.getElementById('pd-size-grid');
            if(grid) grid.scrollIntoView({ behavior:'smooth', block:'center' });
            return;
        }

        if(!productId) {
            showToast('Product not found. Please refresh.', true);
            return;
        }
        if(!csrfToken) {
            showToast('Security token missing. Please refresh.', true);
            return;
        }

        isAdding = true;
        var btn   = document.getElementById('pd-add-cart-btn');
        var label = document.getElementById('pd-cart-label');
        if(btn)   btn.disabled = true;
        if(label) label.textContent = 'Adding…';

        var payload = { product_id: parseInt(productId), quantity: qty };
        if (selectedVid) { payload.variant_id = parseInt(selectedVid); }

        /* Re-read CSRF token fresh each time (in case it was refreshed) */
        var freshCsrf = document.querySelector('meta[name="csrf-token"]');
        if (freshCsrf) csrfToken = freshCsrf.content;

        fetch(cartAddUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(function(r) {
            if (r.status === 419) {
                /* CSRF token expired — reload to get fresh token */
                throw new Error('Session expired. Refreshing page…');
            }
            if (r.status === 422) {
                return r.json().then(function(d) {
                    var firstErr = d.errors ? Object.values(d.errors)[0][0] : (d.message || 'Validation error');
                    throw new Error(firstErr);
                });
            }
            if (!r.ok) {
                return r.json().catch(function() {
                    throw new Error('Server error (' + r.status + '). Please try again.');
                }).then(function(errData) {
                    throw new Error(errData.message || 'Server error (' + r.status + '). Please try again.');
                });
            }
            return r.json();
        })
        .then(function(res) {
            if(res.success) {
                if(btn)   { btn.disabled = false; btn.style.background = '#22c55e'; }
                if(label) label.textContent = '✓ Added!';

                var count = parseInt(res.cart_count) || 1;

                /* update cart badge in header */
                document.querySelectorAll('[data-cart-count]').forEach(function(el){ el.textContent = count; });
                /* also update any .badge inside cart icon */
                document.querySelectorAll('.header-icon-btn .badge').forEach(function(el){ el.textContent = count; });

                /* show panel */
                var panel = document.getElementById('pd-cart-panel');
                if(panel) { panel.style.display = 'block'; }
                var panelSize = document.getElementById('pd-panel-size');
                var panelCount = document.getElementById('pd-panel-count');
                if(panelSize) panelSize.textContent = selectedLabel ? 'Size: ' + selectedLabel : '';
                if(panelCount) panelCount.textContent = count + ' item' + (count !== 1 ? 's' : '');
                if(panel) panel.scrollIntoView({ behavior:'smooth', block:'nearest' });

                /* switch sticky bar to post-cart state */
                switchStickyToAdded(selectedLabel || '{{ $product->name }}');

                showToast('Added to cart!');

                setTimeout(function () {
                    if(btn)   { btn.style.background = ''; }
                    if(label) label.textContent = 'Add to Cart';
                    if(btn)   btn.disabled = false;
                    isAdding = false;
                }, 2500);
            } else {
                if(btn)   btn.disabled = false;
                if(label) label.textContent = 'Add to Cart';
                isAdding = false;
                showToast(res.message || 'Could not add to cart.', true);
            }
        })
        .catch(function(e) {
            if(btn)   btn.disabled = false;
            if(label) label.textContent = 'Add to Cart';
            isAdding = false;
            var msg = e.message || 'Could not add to cart. Please try again.';
            showToast(msg, true);
            console.error('Cart error:', e);
            /* Auto-reload if session/CSRF expired */
            if (msg.indexOf('Session expired') !== -1) {
                setTimeout(function(){ window.location.reload(); }, 1500);
            }
        });
    }

    var atcBtn = document.getElementById('pd-add-cart-btn');
    if(atcBtn) atcBtn.addEventListener('click', doAddToCart);

    var stickyBtn = document.getElementById('pd-sticky-btn');
    if(stickyBtn) stickyBtn.addEventListener('click', doAddToCart);

    /* ── Sticky mobile bar ── */
    var stickyBar = document.getElementById('pd-sticky');
    var pdMain    = document.getElementById('pd-main');

    function updateStickyPadding() {
        if (!stickyBar || !pdMain) return;
        var barH = stickyBar.offsetHeight;
        var isVisible = window.pageYOffset > 500;
        pdMain.style.paddingBottom = isVisible ? (barH + 16) + 'px' : '80px';
    }

    window.addEventListener('scroll', function () {
        if(!stickyBar) return;
        if(window.pageYOffset > 500) {
            stickyBar.style.transform = 'translateY(0)';
        } else {
            stickyBar.style.transform = 'translateY(100%)';
        }
        updateStickyPadding();
    }, { passive: true });

    /* Recalculate on resize (orientation change, font scaling, etc.) */
    window.addEventListener('resize', updateStickyPadding, { passive: true });

    /* ── Accordions ── */
    document.querySelectorAll('.pd-acc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var acc = btn.closest('.pd-accordion');
            if (acc) acc.classList.toggle('open');
        });
    });

    /* ── Size chart modal ── */
    var scBtn   = document.getElementById('pd-size-chart-btn');
    var scClose = document.getElementById('pd-size-close');
    var scModal = document.getElementById('pd-size-modal');
    if(scBtn)   scBtn.addEventListener('click',  function(){ openModal('pd-size-modal'); });
    if(scClose) scClose.addEventListener('click', function(){ closeModal('pd-size-modal'); });
    if(scModal) scModal.addEventListener('click', function(e){ if(e.target === scModal) closeModal('pd-size-modal'); });

    /* ── Fit guide modal ── */
    var fgBtn   = document.getElementById('pd-fit-guide-btn');
    var fgClose = document.getElementById('pd-fit-close');
    var fgModal = document.getElementById('pd-fit-modal');
    if(fgBtn)   fgBtn.addEventListener('click',  function(){ openModal('pd-fit-modal'); });
    if(fgClose) fgClose.addEventListener('click', function(){ closeModal('pd-fit-modal'); });
    if(fgModal) fgModal.addEventListener('click', function(e){ if(e.target === fgModal) closeModal('pd-fit-modal'); });

    /* ── Escape key closes modals ── */
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') { closeModal('pd-size-modal'); closeModal('pd-fit-modal'); }
    });

    /* ── Review write form toggle ── */
    var revToggle = document.getElementById('pd-review-toggle');
    var revForm   = document.getElementById('pd-review-form');
    if(revToggle && revForm) {
        revToggle.addEventListener('click', function(){
            revForm.style.display = revForm.style.display === 'none' ? 'block' : 'none';
            if(revForm.style.display === 'block') revForm.scrollIntoView({ behavior:'smooth', block:'start' });
        });
    }

    /* ── Star rating for review form ── */
    document.querySelectorAll('.pd-star-btn').forEach(function (star) {
        star.addEventListener('click', function () {
            var val = parseInt(star.dataset.val);
            document.getElementById('pd-rating-val').value = val;
            document.querySelectorAll('.pd-star-btn').forEach(function(s){
                s.classList.toggle('fas', parseInt(s.dataset.val) <= val);
                s.classList.toggle('far', parseInt(s.dataset.val) > val);
            });
        });
        star.addEventListener('mouseover', function () {
            var val = parseInt(star.dataset.val);
            document.querySelectorAll('.pd-star-btn').forEach(function(s){
                s.classList.toggle('fas', parseInt(s.dataset.val) <= val);
                s.classList.toggle('far', parseInt(s.dataset.val) > val);
            });
        });
        star.addEventListener('mouseout', function () {
            var currentVal = parseInt(document.getElementById('pd-rating-val').value) || 0;
            document.querySelectorAll('.pd-star-btn').forEach(function(s){
                s.classList.toggle('fas', parseInt(s.dataset.val) <= currentVal);
                s.classList.toggle('far', parseInt(s.dataset.val) > currentVal);
            });
        });
    });

    /* ── FIX #13: Copy link share button ── */
    window.copyPageUrl = function(btn) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(window.location.href).then(function() {
                showToast('Link copied!');
            }).catch(function() {
                fallbackCopy();
            });
        } else {
            fallbackCopy();
        }
        function fallbackCopy() {
            var ta = document.createElement('textarea');
            ta.value = window.location.href;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); showToast('Link copied!'); } catch(e) { showToast('Copy failed'); }
            document.body.removeChild(ta);
        }
    };

})();
</script>
@endpush
