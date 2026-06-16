@extends('layouts.eshopper')

@section('title', 'Jeanzo — Premium Denim for Men & Women')

@push('styles')
<style>
/* ================================================================
   JEANZO HOME — Wrangler-inspired layout
   Each section has exactly 8px top margin matching Wrangler gaps
   ================================================================ */

/* ── Global spacing token ────────────────────────────────────── */
.jz-section { margin-top: 20px; }

/* ── Hero Banner ─────────────────────────────────────────────── */
.hero-wrap {
    position: relative;
    width: 100%;
    height: 600px;
    overflow: hidden;
    margin-top: 20px;         /* 20px gap below header nav */
    background: #111;
}
.hero-wrap .carousel,
.hero-wrap .carousel-inner,
.hero-wrap .carousel-item { height: 100%; }
.hero-wrap .carousel-item img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    /* Keep banner images bright */
    filter: brightness(1) contrast(1);
}
.hero-caption {
    position: absolute; bottom: 0; left: 0; right: 0; top: 0;
    display: flex; flex-direction: column;
    align-items: flex-start; justify-content: center;
    padding: 0 7% 60px;
    z-index: 5;
}
.hero-caption .hero-eyebrow {
    font-size: .7rem; font-weight: 700;
    letter-spacing: 4px; text-transform: uppercase;
    color: #D19C97; margin-bottom: 14px;
}
.hero-caption h1 {
    font-size: clamp(2rem, 4.5vw, 3.8rem);
    font-weight: 900; color: #fff; line-height: 1.08;
    text-transform: uppercase; margin-bottom: 18px;
    max-width: 560px; letter-spacing: -0.5px;
}
.hero-caption .hero-sub {
    font-size: .95rem; color: rgba(255,255,255,.75);
    margin-bottom: 34px; max-width: 400px; line-height: 1.6;
}
.hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-hero-solid {
    display: inline-block;
    background: #fff; color: #1a1a1a;
    font-size: .75rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 14px 38px; text-decoration: none;
    border: 2px solid #fff; transition: all .2s;
}
.btn-hero-solid:hover { background: transparent; color: #fff; text-decoration: none; }
.btn-hero-ghost {
    display: inline-block;
    background: transparent; color: #fff;
    font-size: .75rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 14px 38px; text-decoration: none;
    border: 2px solid rgba(255,255,255,.65); transition: all .2s;
}
.btn-hero-ghost:hover { background: #fff; color: #1a1a1a; border-color: #fff; text-decoration: none; }
/* Carousel indicators */
.hero-wrap .carousel-indicators { bottom: 20px; }
.hero-wrap .carousel-indicators li {
    width: 24px; height: 3px; border-radius: 0;
    background: rgba(255,255,255,.4); border: none; margin: 0 4px;
    transition: background .2s, width .2s;
}
.hero-wrap .carousel-indicators .active { background: #fff; width: 40px; }
/* Carousel arrows */
.hero-wrap .carousel-control-prev,
.hero-wrap .carousel-control-next {
    width: 44px; opacity: 1;
}
.hero-wrap .carousel-arrow {
    background: rgba(0,0,0,.4); border: none;
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .9rem;
    transition: background .2s;
}
.hero-wrap .carousel-arrow:hover { background: rgba(0,0,0,.7); }

/* ── Signup Offer Strip ──────────────────────────────────────── */
.offer-strip {
    background: #D19C97;
    padding: 0;
    margin-top: 20px;
}
.offer-strip-inner {
    display: flex; align-items: center; justify-content: center;
    gap: 32px; flex-wrap: wrap;
    padding: 22px 40px;
    text-align: center;
}
.offer-strip .offer-text h3 {
    font-size: 1.35rem; font-weight: 900; color: #fff;
    text-transform: uppercase; letter-spacing: 1px; margin: 0 0 4px;
}
.offer-strip .offer-text p {
    font-size: .85rem; color: rgba(255,255,255,.88); margin: 0;
}
.offer-strip .offer-input-wrap { display: flex; gap: 0; margin-left: 12px; }
.offer-strip .offer-input-wrap input {
    border: 2px solid rgba(255,255,255,.7);
    background: rgba(255,255,255,.15);
    color: #fff; padding: 10px 18px;
    font-size: .82rem; outline: none; border-right: none;
    min-width: 240px; border-radius: 0;
}
.offer-strip .offer-input-wrap input::placeholder { color: rgba(255,255,255,.7); }
.offer-strip .offer-input-wrap input:focus { background: rgba(255,255,255,.25); }
.offer-strip .offer-input-wrap button {
    background: #1a1a1a; color: #fff; border: 2px solid #1a1a1a;
    padding: 10px 24px; font-size: .75rem; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer;
    transition: background .2s, border-color .2s; border-radius: 0;
}
.offer-strip .offer-input-wrap button:hover { background: #333; border-color: #333; }
.offer-strip .offer-badge {
    background: #fff; color: #D19C97;
    font-size: 1.1rem; font-weight: 900;
    padding: 12px 20px; text-align: center;
    border-radius: 0; min-width: 90px;
    line-height: 1.2;
}
.offer-strip .offer-badge span { font-size: .65rem; font-weight: 700; display: block; text-transform: uppercase; letter-spacing: 1px; }

/* ── Shop Men / Shop Women ───────────────────────────────────── */
.gender-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}
.gender-panel {
    position: relative; overflow: hidden;
    height: 520px; display: block;
    text-decoration: none;
}
.gender-panel img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    transition: transform .7s ease;
    filter: brightness(.58);
}
.gender-panel:hover img { transform: scale(1.04); filter: brightness(.45); text-decoration: none; }
.gender-panel-body {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 0 0 48px 44px;
}
.gender-panel-body .gp-label {
    font-size: .65rem; font-weight: 700; letter-spacing: 3px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 8px;
}
.gender-panel-body h2 {
    font-size: 2.6rem; font-weight: 900; color: #fff;
    text-transform: uppercase; letter-spacing: -0.5px;
    margin-bottom: 20px; line-height: 1;
}
.btn-gender-cta {
    display: inline-block;
    border: 2px solid rgba(255,255,255,.8);
    color: #fff; background: transparent;
    font-size: .72rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 11px 30px; text-decoration: none;
    transition: all .2s;
}
.btn-gender-cta:hover { background: #fff; color: #1a1a1a; border-color: #fff; text-decoration: none; }

/* ── Section heading ─────────────────────────────────────────── */
.jz-heading {
    text-align: center;
    padding: 48px 20px 10px;
}
.jz-heading .eyebrow {
    font-size: .68rem; font-weight: 700; letter-spacing: 3.5px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 10px;
}
.jz-heading h2 {
    font-size: 1.85rem; font-weight: 900;
    text-transform: uppercase; color: #1a1a1a;
    letter-spacing: .5px; margin-bottom: 10px;
}
.jz-heading p {
    color: #888; font-size: .88rem; max-width: 440px; margin: 0 auto 0;
    line-height: 1.6;
}

/* ── Shop by Collection ──────────────────────────────────────── */
.collection-grid {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}
/* adaptive: 1 item → full width, 2 → 50/50, 3 → thirds, 4+ → 4 cols */
.collection-grid.cols-1 { grid-template-columns: 1fr; }
.collection-grid.cols-2 { grid-template-columns: 1fr 1fr; }
.collection-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.collection-grid.cols-4 { grid-template-columns: repeat(4, 1fr); }
.collection-grid.cols-5 { grid-template-columns: repeat(5, 1fr); }
/* For odd counts like 5, first card spans 2 columns */
.collection-grid.cols-5 .coll-card:first-child { grid-column: span 2; }

.coll-card {
    position: relative; overflow: hidden;
    height: 400px; display: block; text-decoration: none;
    background: #eee;
}
.coll-card.tall { height: 520px; }
.coll-card img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    transition: transform .6s ease;
    filter: brightness(.68);
}
.coll-card:hover img { transform: scale(1.05); filter: brightness(.5); }
.coll-card-body {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 20px 24px 28px;
    background: linear-gradient(to top, rgba(0,0,0,.72) 0%, transparent 100%);
}
.coll-card-body .coll-eyebrow {
    font-size: .62rem; font-weight: 700; letter-spacing: 2.5px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 5px;
}
.coll-card-body h3 {
    font-size: 1.3rem; font-weight: 800; color: #fff;
    text-transform: uppercase; margin-bottom: 14px; line-height: 1.15;
}
.btn-coll-link {
    display: inline-block; font-size: .7rem; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
    color: #fff; text-decoration: none;
    border-bottom: 1.5px solid rgba(255,255,255,.55);
    padding-bottom: 2px; transition: color .2s, border-color .2s;
}
.btn-coll-link:hover { color: #D19C97; border-color: #D19C97; text-decoration: none; }

/* ── Shop by Fit ─────────────────────────────────────────────── */
.fit-grid {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}
.fit-card {
    position: relative; overflow: hidden;
    height: 460px; display: block; text-decoration: none;
    background: #111;
}
.fit-card img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    transition: transform .6s ease;
    filter: brightness(.72);
}
.fit-card:hover img { transform: scale(1.06); filter: brightness(.5); }
.fit-card-body {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: flex-end;
    padding-bottom: 36px; text-align: center;
}
.fit-card-body h4 {
    font-size: 1rem; font-weight: 900; color: #fff;
    text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px;
}
.fit-badge {
    display: inline-block;
    background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.5);
    color: #fff; font-size: .65rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 7px 20px; transition: all .2s;
}
.fit-card:hover .fit-badge { background: #fff; color: #1a1a1a; border-color: #fff; }

/* ── Values/Promise strip ────────────────────────────────────── */
.promise-strip {
    background: #1a1a1a;
    padding: 52px 0;
    margin-top: 20px;
}
.promise-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    max-width: 1140px; margin: 0 auto;
}
.promise-item {
    display: flex; flex-direction: column;
    align-items: center; text-align: center;
    padding: 24px 28px;
    border-right: 1px solid rgba(255,255,255,.08);
}
.promise-item:last-child { border-right: none; }
.promise-icon { font-size: 1.7rem; color: #D19C97; margin-bottom: 14px; }
.promise-item h5 {
    font-size: .8rem; font-weight: 700; color: #fff;
    text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;
}
.promise-item p { font-size: .77rem; color: rgba(255,255,255,.45); line-height: 1.6; margin: 0; }

/* ── Trending Products ───────────────────────────────────────── */
.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 20px;
}
.jz-product-card { background: #fff; }
.jz-product-card .prod-img {
    position: relative; overflow: hidden;
    padding-top: 125%;
    background: #f5f5f5;
}
.jz-product-card .prod-img img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    transition: transform .45s ease;
}
.jz-product-card:hover .prod-img img { transform: scale(1.07); }
.prod-img .prod-hover {
    position: absolute; bottom: -52px; left: 0; right: 0;
    background: rgba(26,26,26,.88);
    display: flex; align-items: center; justify-content: center;
    gap: 20px; padding: 13px;
    transition: bottom .3s ease;
}
.jz-product-card:hover .prod-hover { bottom: 0; }
.prod-hover a, .prod-hover button {
    color: #fff; font-size: .9rem; background: none;
    border: none; cursor: pointer; padding: 0;
    transition: color .15s; text-decoration: none;
}
.prod-hover a:hover, .prod-hover button:hover { color: #D19C97; }
.jz-product-card .prod-info { padding: 13px 2px 16px; }
.prod-info .prod-cat {
    font-size: .62rem; font-weight: 600; letter-spacing: 1.5px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 4px;
}
.prod-info .prod-name {
    display: block; font-size: .88rem; font-weight: 600;
    color: #1a1a1a; text-decoration: none;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 6px; line-height: 1.3;
}
.prod-info .prod-name:hover { color: #D19C97; }
.prod-info .prod-price { font-size: .95rem; font-weight: 800; color: #1a1a1a; }
.prod-info .prod-price .original {
    font-size: .8rem; color: #aaa; font-weight: 400;
    text-decoration: line-through; margin-left: 6px;
}
.prod-stars { font-size: .7rem; color: #D19C97; margin-bottom: 4px; }
.prod-badge {
    position: absolute; top: 10px; left: 10px;
    background: #D19C97; color: #fff;
    font-size: .6rem; font-weight: 800;
    letter-spacing: 1px; text-transform: uppercase;
    padding: 3px 8px; z-index: 2;
}

/* ── Newsletter ──────────────────────────────────────────────── */
.newsletter-band {
    background: #f3f3f0;
    padding: 64px 20px;
    margin-top: 20px;
    text-align: center;
}
.newsletter-band .nl-eyebrow {
    font-size: .68rem; font-weight: 700; letter-spacing: 3px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 10px;
}
.newsletter-band h3 {
    font-size: 1.75rem; font-weight: 900;
    text-transform: uppercase; color: #1a1a1a; margin-bottom: 8px;
}
.newsletter-band p { color: #888; font-size: .88rem; margin-bottom: 28px; }
.nl-form { display: flex; max-width: 440px; margin: 0 auto; }
.nl-form input {
    flex: 1; border: 1.5px solid #ccc; border-right: none;
    padding: 12px 18px; font-size: .84rem; border-radius: 0;
    outline: none;
}
.nl-form input:focus { border-color: #1a1a1a; }
.nl-form button {
    background: #1a1a1a; color: #fff; border: none;
    padding: 0 28px; font-size: .72rem; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer;
    transition: background .2s; border-radius: 0;
}
.nl-form button:hover { background: #D19C97; }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 992px) {
    .hero-wrap { height: 420px; }
    .gender-grid { grid-template-columns: 1fr; gap: 20px; }
    .gender-panel { height: 340px; }
    .products-grid { grid-template-columns: repeat(2, 1fr); }
    .promise-grid { grid-template-columns: repeat(2, 1fr); }
    .promise-item:nth-child(2) { border-right: none; }
    .fit-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 576px) {
    .hero-wrap { height: 320px; }
    .hero-caption h1 { font-size: 1.6rem; }
    .products-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .collection-grid { grid-template-columns: 1fr !important; }
    .coll-card { height: 300px; }
    .offer-strip-inner { flex-direction: column; gap: 16px; }
    .offer-strip .offer-input-wrap { width: 100%; flex-direction: column; }
    .offer-strip .offer-input-wrap input { border-right: 2px solid rgba(255,255,255,.7); min-width: 0; width: 100%; }
    .offer-strip .offer-input-wrap button { padding: 12px; }
    .gender-panel { height: 280px; }
    .gender-panel-body h2 { font-size: 1.8rem; }
}
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════
     1. HERO CAROUSEL — Full width, 8px below header
     ════════════════════════════════════════════════════ --}}
<div class="hero-wrap">
    <div id="hero-carousel" class="carousel slide h-100" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner h-100">
            @php
                $slides = $banners ?? collect();
                $fallbackSlides = [
                    [
                        'img'    => asset('eshopper/img/carousel-1.jpg'),
                        'label'  => 'New Season Collection',
                        'title'  => "Built to Last.\nMade to Move.",
                        'sub'    => 'Premium denim crafted for every adventure.',
                        'link'   => route('products.index'),
                        'cta'    => 'Shop Now',
                    ],
                    [
                        'img'    => asset('eshopper/img/carousel-2.jpg'),
                        'label'  => 'Spring / Summer 2025',
                        'title'  => "Fit for Every\nMoment.",
                        'sub'    => 'The perfect pair of jeans for every day.',
                        'link'   => route('products.index'),
                        'cta'    => 'Explore Fits',
                    ],
                ];
            @endphp

            @if($slides->isNotEmpty())
                @foreach($slides as $i => $slide)
                @php
                    $imgUrl = $slide->image_url ?? '';
                    if ($imgUrl && !str_starts_with($imgUrl,'http') && !str_starts_with($imgUrl,'/')) {
                        $imgUrl = asset('storage/' . $imgUrl);
                    } elseif ($imgUrl) {
                        $imgUrl = asset($imgUrl);
                    } else {
                        $imgUrl = asset('eshopper/img/carousel-' . (($i % 2) + 1) . '.jpg');
                    }
                @endphp
                <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                    <img src="{{ $imgUrl }}" alt="{{ $slide->title ?? '' }}">
                    <div class="hero-caption">
                        @if(!empty($slide->subtitle))<div class="hero-eyebrow">{{ $slide->subtitle }}</div>@endif
                        <h1>{{ $slide->title ?? '' }}</h1>
                        @if($slide->link_url ?? null)
                            <div class="hero-btns">
                                <a href="{{ $slide->link_url }}" class="btn-hero-solid">Shop Now</a>
                                <a href="{{ route('products.index') }}" class="btn-hero-ghost">Explore All</a>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                @foreach($fallbackSlides as $i => $fb)
                <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                    <img src="{{ $fb['img'] }}" alt="{{ $fb['title'] }}">
                    <div class="hero-caption">
                        <div class="hero-eyebrow">{{ $fb['label'] }}</div>
                        <h1>{{ $fb['title'] ?? '' }}</h1>
                        <div class="hero-sub">{{ $fb['sub'] }}</div>
                        <div class="hero-btns">
                            <a href="{{ $fb['link'] }}" class="btn-hero-solid">{{ $fb['cta'] }}</a>
                            <a href="{{ route('products.index') }}" class="btn-hero-ghost">View All</a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <ol class="carousel-indicators">
            @php $totalSlides = $slides->isNotEmpty() ? $slides->count() : count($fallbackSlides); @endphp
            @for($si = 0; $si < $totalSlides; $si++)
            <li data-target="#hero-carousel" data-slide-to="{{ $si }}" class="{{ $si===0?'active':'' }}"></li>
            @endfor
        </ol>
        <a class="carousel-control-prev" href="#hero-carousel" data-slide="prev">
            <span class="carousel-arrow"><i class="fas fa-chevron-left"></i></span>
        </a>
        <a class="carousel-control-next" href="#hero-carousel" data-slide="next">
            <span class="carousel-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     2. SIGNUP OFFER STRIP — 10% off first order
     ════════════════════════════════════════════════════ --}}
<div class="offer-strip jz-section">
    <div class="offer-strip-inner">
        <div class="offer-badge">
            10%<br><span>OFF</span>
        </div>
        <div class="offer-text">
            <h3>Sign Up &amp; Get 10% Off Your First Order</h3>
            <p>Join the Jeanzo family. Exclusive deals, new arrivals &amp; style tips straight to your inbox.</p>
        </div>
        <div class="offer-input-wrap">
            <input type="email" placeholder="Your email address" id="offer-email">
            <button type="button" onclick="handleOfferSignup()">Get 10% Off</button>
        </div>
    </div>
</div>
<script>
function handleOfferSignup(){
    var email = document.getElementById('offer-email');
    if(!email || !email.value.trim()) { email && email.focus(); return; }
    email.value = '';
    email.placeholder = '✓ You\'re in! Check your email.';
}
</script>

{{-- ════════════════════════════════════════════════════
     3. SHOP MEN / SHOP WOMEN — Equal split full width
     ════════════════════════════════════════════════════ --}}
@php
    $mensCat   = \App\Models\Category::where('slug','mens-jeans')->first();
    $womensCat = \App\Models\Category::whereIn('slug',['womens-jeans','womens-denim'])->first();

    /* helper: build a reliable image URL from a category's image_url field */
    function jzCatImg($cat, $fallbackNum = 1) {
        if (!$cat || !$cat->image_url) {
            return asset('eshopper/img/cat-' . $fallbackNum . '.jpg');
        }
        $url = $cat->image_url;
        if (str_starts_with($url,'http') || str_starts_with($url,'/')) {
            return asset($url);
        }
        return asset('storage/' . $url);
    }
@endphp
<!--
<div class="gender-grid">
    {{-- MEN --}}
    <a href="{{ $mensCat ? route('products.index', ['category' => $mensCat->id]) : route('products.index', ['gender'=>'men']) }}"
       class="gender-panel">
        <img src="{{ jzCatImg($mensCat, 1) }}" alt="Shop Men">
        <div class="gender-panel-body">
            <div class="gp-label">New Collection</div>
            <h2>Shop Men</h2>
            <span class="btn-gender-cta">Shop Now</span>
        </div>
    </a>
    {{-- WOMEN --}}
    <a href="{{ $womensCat ? route('products.index', ['category' => $womensCat->id]) : route('products.index', ['gender'=>'women']) }}"
       class="gender-panel">
        <img src="{{ jzCatImg($womensCat, 2) }}" alt="Shop Women">
        <div class="gender-panel-body">
            <div class="gp-label">New Collection</div>
            <h2>Shop Women</h2>
            <span class="btn-gender-cta">Shop Now</span>
        </div>
    </a>
</div>
-->
{{-- ════════════════════════════════════════════════════
     4. TOP CATEGORIES
     ════════════════════════════════════════════════════ --}}
@php
    $allActive = \App\Models\Category::where('is_active', true)->get();

    /* recursive product count per category subtree */
    $prodCountById = \App\Models\Product::where('is_active', true)
        ->whereNotNull('category_id')
        ->selectRaw('category_id, count(*) as cnt')
        ->groupBy('category_id')
        ->pluck('cnt','category_id')->toArray();

    $subtreeCount = function($id) use (&$subtreeCount, $allActive, $prodCountById) {
        $n = $prodCountById[$id] ?? 0;
        foreach ($allActive->where('parent_id', $id) as $ch) {
            $n += $subtreeCount($ch->id);
        }
        return $n;
    };
@endphp

{{-- ════════════════════════════════════════════════════
     5. SHOP BY COLLECTION — adaptive grid, full width
     ════════════════════════════════════════════════════ --}}
@php
    /* All categories with products make good "collections".
       Root-level ones work best visually. */
    $collections = $allActive
        ->whereNull('parent_id')
        ->map(fn($c) => tap($c, fn($c2) => $c2->dyn_count = $subtreeCount($c2->id)))
        ->filter(fn($c) => $c->dyn_count > 0)
        ->values();

    $collCount = $collections->count();

    /* pick grid class */
    $collGrid = match(true) {
        $collCount >= 5 => 'cols-5',
        $collCount == 4 => 'cols-4',
        $collCount == 3 => 'cols-3',
        $collCount == 2 => 'cols-2',
        default         => 'cols-1',
    };

    $collFbImages = [
        asset('eshopper/img/cat-1.jpg'),
        asset('eshopper/img/cat-2.jpg'),
        asset('eshopper/img/cat-3.jpg'),
        asset('eshopper/img/cat-4.jpg'),
        asset('eshopper/img/cat-5.jpg'),
    ];
    $collLabels = ["Men's Denim","Women's Denim","Premium","Kids","Accessories"];
@endphp

<div class="jz-heading">
    <div class="eyebrow">Curated For You</div>
    <h2>Shop By Collection</h2>
    <p>Discover the season's best, across every style.</p>
</div>

<div class="collection-grid {{ $collGrid }} px-0">
    @forelse($collections as $idx => $col)
    <a href="{{ route('products.index', ['category' => $col->id]) }}"
       class="coll-card {{ $collCount <= 2 ? 'tall' : '' }}">
        <img src="{{ jzCatImg($col, ($idx % 5) + 1) }}" alt="{{ $col->name }}">
        <div class="coll-card-body">
            <div class="coll-eyebrow">Collection</div>
            <h3>{{ $col->name }}</h3>
            <span class="btn-coll-link">Shop Now →</span>
        </div>
    </a>
    @empty
    {{-- absolute fallback --}}
    @foreach($collLabels as $fi => $fl)
    <a href="{{ route('products.index') }}" class="coll-card">
        <img src="{{ $collFbImages[$fi] }}" alt="{{ $fl }}">
        <div class="coll-card-body">
            <div class="coll-eyebrow">Collection</div>
            <h3>{{ $fl }}</h3>
            <span class="btn-coll-link">Shop Now →</span>
        </div>
    </a>
    @endforeach
    @endforelse
</div>

{{-- ════════════════════════════════════════════════════
     6. SHOP BY FIT — ALL fit sub-categories with products
     ════════════════════════════════════════════════════ --}}
@php
    /* ALL categories (at any depth) that have direct active products */
    $fitCats = $allActive
        ->filter(fn($c) => !is_null($c->parent_id))
        ->filter(fn($c) => ($prodCountById[$c->id] ?? 0) > 0)
        ->values();

    /* fallback: any child category with products in subtree */
    if ($fitCats->isEmpty()) {
        $fitCats = $allActive
            ->filter(fn($c) => !is_null($c->parent_id))
            ->filter(fn($c) => $subtreeCount($c->id) > 0)
            ->values();
    }

    $fitFbImages = [
        asset('eshopper/img/cat-1.jpg'), asset('eshopper/img/cat-2.jpg'),
        asset('eshopper/img/cat-3.jpg'), asset('eshopper/img/cat-4.jpg'),
    ];
    $fitFbNames = ['Slim Fit','Regular Fit','Skinny Fit','Relaxed Fit'];
@endphp

<div class="jz-heading">
    <div class="eyebrow">Find Your Style</div>
    <h2>Shop By Fit</h2>
    <p>Every body. Every occasion. Every fit you need.</p>
</div>

@if($fitCats->isNotEmpty())
{{-- Always 4 columns per row, show every category --}}
<div class="fit-grid px-0" style="grid-template-columns: repeat(4, 1fr);">
    @foreach($fitCats as $idx => $fit)
    <a href="{{ route('products.index', ['category' => $fit->id]) }}" class="fit-card">
        <img src="{{ jzCatImg($fit, ($idx % 4) + 1) }}" alt="{{ $fit->name }}">
        <div class="fit-card-body">
            <h4>{{ $fit->name }}</h4>
            <span class="fit-badge">Shop Now</span>
        </div>
    </a>
    @endforeach
</div>
@else
<div class="fit-grid px-0" style="grid-template-columns: repeat(4, 1fr);">
    @foreach($fitFbNames as $fi => $fn)
    <a href="{{ route('products.index') }}" class="fit-card">
        <img src="{{ $fitFbImages[$fi] }}" alt="{{ $fn }}">
        <div class="fit-card-body">
            <h4>{{ $fn }}</h4>
            <span class="fit-badge">Shop Now</span>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- ════════════════════════════════════════════════════
     7. FIT FOR EVERY MOMENT — Values/Promise strip
     ════════════════════════════════════════════════════ --}}
<div class="promise-strip">
    <div class="jz-heading" style="padding:0 20px 32px; background:transparent;">
        <div class="eyebrow" style="color:#D19C97;">Our Promise</div>
        <h2 style="color:#fff; margin-bottom:0;">Fit For Every Moment</h2>
    </div>
    <div class="promise-grid px-xl-5">
        <div class="promise-item">
            <div class="promise-icon"><i class="fas fa-award"></i></div>
            <h5>Premium Quality</h5>
            <p>Only the finest fabrics, cut and stitched for lasting wear.</p>
        </div>
        <div class="promise-item">
            <div class="promise-icon"><i class="fas fa-shipping-fast"></i></div>
            <h5>Fast Delivery</h5>
            <p>Free shipping above ₹999. Delivered fast, right to your door.</p>
        </div>
        <div class="promise-item">
            <div class="promise-icon"><i class="fas fa-undo-alt"></i></div>
            <h5>Easy Returns</h5>
            <p>Not happy? Return within 14 days — no questions asked.</p>
        </div>
        <div class="promise-item">
            <div class="promise-icon"><i class="fas fa-headset"></i></div>
            <h5>24/7 Support</h5>
            <p>Our team is always here for you, day or night.</p>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     8. EXPLORE TRENDING PRODUCTS
     ════════════════════════════════════════════════════ --}}
@php
    /* Get featured products first, fall back to newest */
    $trending = \App\Models\Product::with('images')
        ->withCount(['reviews as rev_count' => fn($q) => $q->where('is_approved',true)])
        ->withAvg(['reviews as rev_avg' => fn($q) => $q->where('is_approved',true)],'rating')
        ->where('is_active', true)
        ->where('is_featured', true)
        ->latest()->take(8)->get();

    if ($trending->count() < 4) {
        $featuredIds = $trending->pluck('id')->toArray();
        $extra = \App\Models\Product::with('images')
            ->withCount(['reviews as rev_count' => fn($q) => $q->where('is_approved',true)])
            ->withAvg(['reviews as rev_avg' => fn($q) => $q->where('is_approved',true)],'rating')
            ->where('is_active', true)
            ->whereNotIn('id', $featuredIds)
            ->latest()->take(8 - $trending->count())->get();
        $trending = $trending->merge($extra);
    }

    /* Build image URL for a product */
    function jzProdImg($product, $fallbackIdx = 1) {
        $img = $product->images->first();
        if ($img && $img->image) {
            $path = 'products/' . $product->id . '/images/' . $img->image;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            }
        }
        return asset('storage/default.jpeg');
    }
@endphp

<div class="jz-heading">
    <div class="eyebrow">Staff Picks</div>
    <h2>Explore Trending</h2>
    <p>The styles everyone's reaching for right now.</p>
</div>

<div class="products-grid px-2 px-xl-4 jz-section">
    @forelse($trending as $idx => $product)
    @php
        $imgUrl  = jzProdImg($product, ($idx % 8) + 1);
        $stars   = round($product->rev_avg ?? 0);
        $hasSale = $product->sale_price && $product->sale_price < $product->price;
        $catName = optional($product->category)->name;
    @endphp
    <div class="jz-product-card">
        <div class="prod-img">
            @if($hasSale)<span class="prod-badge">Sale</span>@elseif($product->is_featured)<span class="prod-badge" style="background:#1a1a1a;">Featured</span>@endif
            <img src="{{ $imgUrl }}"
                 alt="{{ $product->name }}"
                 onerror="this.onerror=null;this.src='{{ asset('storage/default.jpeg') }}'">
            <div class="prod-hover">
                <a href="{{ route('products.detail', $product->slug) }}" title="View"><i class="fas fa-eye"></i></a>
                @auth
                <a href="{{ route('wishlist.add', $product->id) }}" title="Wishlist"><i class="fas fa-heart"></i></a>
                @endauth
                <a href="{{ route('cart.add', $product->id) }}" title="Add to Cart"><i class="fas fa-shopping-bag"></i></a>
            </div>
        </div>
        <div class="prod-info">
            @if($catName)<div class="prod-cat">{{ $catName }}</div>@endif
            <div class="prod-stars">
                @for($s=1;$s<=5;$s++)<i class="{{ $s<=$stars?'fas':'far' }} fa-star"></i>@endfor
                @if($product->rev_count > 0)<small class="text-muted ml-1" style="font-size:.68rem;">({{ $product->rev_count }})</small>@endif
            </div>
            <a href="{{ route('products.detail', $product->slug) }}" class="prod-name">{{ $product->name }}</a>
            <div class="prod-price">
                ₹{{ number_format($hasSale ? $product->sale_price : $product->price, 0) }}
                @if($hasSale)<span class="original">₹{{ number_format($product->price, 0) }}</span>@endif
            </div>
        </div>
    </div>
    @empty
    <div class="jz-section text-center py-5" style="grid-column:1/-1;">
        <p class="text-muted">Products coming soon.</p>
        <a href="{{ route('products.index') }}" class="btn-hero-solid" style="color:#1a1a1a;border-color:#1a1a1a;display:inline-block;">Browse All</a>
    </div>
    @endforelse
</div>

@if($trending->isNotEmpty())
<div class="text-center mt-4 mb-2">
    <a href="{{ route('products.index') }}"
       style="display:inline-block;background:#1a1a1a;color:#fff;font-size:.75rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:14px 44px;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='#D19C97'"
       onmouseout="this.style.background='#1a1a1a'">
        View All Products
    </a>
</div>
@endif

{{-- ════════════════════════════════════════════════════
     9. NEWSLETTER BAND
     ════════════════════════════════════════════════════ --}}
<div class="newsletter-band">
    <div class="nl-eyebrow">Stay in the Loop</div>
    <h3>Join the Jeanzo Family</h3>
    <p>Exclusive deals, new arrivals, and style inspiration — delivered free.</p>
    <form class="nl-form" onsubmit="return nlSubmit(this)">
        <input type="email" placeholder="Your email address" required>
        <button type="submit">Subscribe</button>
    </form>
</div>
<script>
function nlSubmit(f){
    var inp = f.querySelector('input');
    if(inp){ inp.value=''; inp.placeholder='✓ Thanks for subscribing!'; }
    return false;
}
</script>

@endsection
