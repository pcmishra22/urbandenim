@extends('layouts.eshopper')
@section('title', 'Jeanzo — Premium Denim Jeans for Men & Women in India')
@section('meta_description', 'Shop premium denim jeans for men and women at Jeanzo. Slim fit, skinny, straight and relaxed fits. Fast delivery across India. Free shipping above ₹999.')
@section('og_title', 'Jeanzo — Premium Denim Jeans for Men & Women in India')
@section('og_description', 'Shop premium denim jeans for men and women at Jeanzo. Slim fit, skinny, straight and relaxed fits. Fast delivery across India.')
@section('canonical', url('/'))

@push('styles')
<style>
/* =============================================================
   JEANZO HOME — Fully Responsive
   ============================================================= */

/* ── Global ── */
.jz-section { margin-top: 20px; }
section, .jz-section, .gender-grid, .collection-grid, .fit-grid, .products-grid, .promise-grid {
    max-width: 100%; overflow: hidden;
}

/* ── Hero ── */
.hero-wrap {
    position: relative; width: 100%; height: 580px;
    overflow: hidden; background: #111;
}
.hero-wrap .carousel,
.hero-wrap .carousel-inner,
.hero-wrap .carousel-item { height: 100%; }
.hero-wrap .carousel-item img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
}
.hero-caption {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: flex-start; justify-content: center;
    padding: 0 7% 60px; z-index: 5;
}
.hero-eyebrow {
    font-size: .7rem; font-weight: 700;
    letter-spacing: 4px; text-transform: uppercase;
    color: #D19C97; margin-bottom: 14px;
}
.hero-caption h1 {
    font-size: clamp(1.6rem, 4.5vw, 3.6rem);
    font-weight: 900; color: #fff; line-height: 1.08;
    text-transform: uppercase; margin-bottom: 16px;
    max-width: 560px; letter-spacing: -0.5px;
}
.hero-sub {
    font-size: .95rem; color: rgba(255,255,255,.75);
    margin-bottom: 28px; max-width: 400px; line-height: 1.6;
}
.hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-hero-solid {
    display: inline-block; background: #fff; color: #1a1a1a;
    font-size: .75rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 13px 32px; text-decoration: none;
    border: 2px solid #fff; transition: all .2s;
}
.btn-hero-solid:hover { background: transparent; color: #fff; text-decoration: none; }
.btn-hero-ghost {
    display: inline-block; background: transparent; color: #fff;
    font-size: .75rem; font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase;
    padding: 13px 32px; text-decoration: none;
    border: 2px solid rgba(255,255,255,.6); transition: all .2s;
}
.btn-hero-ghost:hover { background: #fff; color: #1a1a1a; border-color: #fff; text-decoration: none; }
.hero-wrap .carousel-indicators { bottom: 16px; }
.hero-wrap .carousel-indicators li {
    width: 24px; height: 3px; background: rgba(255,255,255,.4);
    border: none; margin: 0 4px; border-radius: 0;
}
.hero-wrap .carousel-indicators .active { background: #fff; width: 40px; }
.hero-wrap .carousel-control-prev,
.hero-wrap .carousel-control-next { width: 44px; opacity: 1; }
.hero-wrap .carousel-arrow {
    background: rgba(0,0,0,.4); width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .9rem; transition: background .2s; border: none;
}
.hero-wrap .carousel-arrow:hover { background: rgba(0,0,0,.7); }

/* ── Offer Strip ── */
.offer-strip { background: #D19C97; margin-top: 20px; }
.offer-strip-inner {
    display: flex; align-items: center; justify-content: center;
    gap: 24px; flex-wrap: wrap; padding: 20px 24px; text-align: center;
}
.offer-badge {
    background: #fff; color: #D19C97;
    font-size: 1.1rem; font-weight: 900;
    padding: 12px 18px; min-width: 80px; line-height: 1.2; flex-shrink: 0;
}
.offer-badge span { font-size: .62rem; font-weight: 700; display: block; text-transform: uppercase; letter-spacing: 1px; }
.offer-text h3 { font-size: 1.15rem; font-weight: 900; color: #fff; text-transform: uppercase; margin: 0 0 4px; }
.offer-text p  { font-size: .82rem; color: rgba(255,255,255,.88); margin: 0; }
.offer-input-wrap { display: flex; }
.offer-input-wrap input {
    border: 2px solid rgba(255,255,255,.7); background: rgba(255,255,255,.15);
    color: #fff; padding: 10px 16px; font-size: .82rem; outline: none;
    border-right: none; min-width: 200px; border-radius: 0;
}
.offer-input-wrap input::placeholder { color: rgba(255,255,255,.7); }
.offer-input-wrap button {
    background: #1a1a1a; color: #fff; border: 2px solid #1a1a1a;
    padding: 10px 20px; font-size: .72rem; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer;
    border-radius: 0; white-space: nowrap;
}

/* ── Section Heading ── */
.jz-heading { text-align: center; padding: 40px 20px 8px; }
.jz-heading .eyebrow {
    font-size: .68rem; font-weight: 700; letter-spacing: 3.5px;
    text-transform: uppercase; color: #D19C97; margin-bottom: 10px;
}
.jz-heading h2 {
    font-size: clamp(1.3rem, 3vw, 1.85rem); font-weight: 900;
    text-transform: uppercase; color: #1a1a1a; margin-bottom: 8px;
}
.jz-heading p { color: #888; font-size: .88rem; max-width: 440px; margin: 0 auto; line-height: 1.6; }

/* ── Collections ── */
.collection-grid { display: grid; gap: 16px; margin-top: 16px; }
.collection-grid.cols-1 { grid-template-columns: 1fr; }
.collection-grid.cols-2 { grid-template-columns: 1fr 1fr; }
.collection-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
.collection-grid.cols-4 { grid-template-columns: repeat(4, 1fr); }
.collection-grid.cols-5 { grid-template-columns: repeat(5, 1fr); }
.collection-grid.cols-5 .coll-card:first-child { grid-column: span 2; }
.coll-card {
    position: relative; overflow: hidden; height: 380px;
    display: block; text-decoration: none; background: #eee;
}
.coll-card.tall { height: 500px; }
.coll-card img {
    width: 100%; height: 100%; object-fit: cover; object-position: center top;
    transition: transform .6s ease; filter: brightness(.68);
}
.coll-card:hover img { transform: scale(1.05); filter: brightness(.5); }
.coll-card-body {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 16px 20px 24px;
    background: linear-gradient(to top, rgba(0,0,0,.72) 0%, transparent 100%);
}
.coll-eyebrow { font-size: .6rem; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: #D19C97; margin-bottom: 5px; }
.coll-card-body h3 { font-size: 1.2rem; font-weight: 800; color: #fff; text-transform: uppercase; margin-bottom: 12px; line-height: 1.15; }
.btn-coll-link {
    display: inline-block; font-size: .68rem; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase; color: #fff;
    border-bottom: 1.5px solid rgba(255,255,255,.55); padding-bottom: 2px;
}
.btn-coll-link:hover { color: #D19C97; border-color: #D19C97; text-decoration: none; }

/* ── Fit Grid ── */
.fit-grid { display: grid; gap: 16px; margin-top: 16px; }
.fit-card {
    position: relative; overflow: hidden; height: 420px;
    display: block; text-decoration: none; background: #111;
}
.fit-card img {
    width: 100%; height: 100%; object-fit: cover; object-position: center top;
    transition: transform .6s ease; filter: brightness(.72);
}
.fit-card:hover img { transform: scale(1.06); filter: brightness(.5); }
.fit-card-body {
    position: absolute; inset: 0; display: flex; flex-direction: column;
    align-items: center; justify-content: flex-end;
    padding-bottom: 32px; text-align: center;
}
.fit-card-body h4 { font-size: 1rem; font-weight: 900; color: #fff; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; }
.fit-badge {
    display: inline-block; background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.5); color: #fff;
    font-size: .65rem; font-weight: 800; letter-spacing: 2px;
    text-transform: uppercase; padding: 7px 18px; transition: all .2s;
}
.fit-card:hover .fit-badge { background: #fff; color: #1a1a1a; border-color: #fff; }

/* ── Promise Strip ── */
.promise-strip { background: #1a1a1a; padding: 48px 0; margin-top: 20px; }
.promise-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    max-width: 1140px; margin: 0 auto;
}
.promise-item {
    display: flex; flex-direction: column;
    align-items: center; text-align: center;
    padding: 20px 20px;
    border-right: 1px solid rgba(255,255,255,.08);
}
.promise-item:last-child { border-right: none; }
.promise-icon { font-size: 1.7rem; color: #D19C97; margin-bottom: 12px; }
.promise-item h5 { font-size: .8rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; }
.promise-item p  { font-size: .77rem; color: rgba(255,255,255,.45); line-height: 1.6; margin: 0; }

/* ── Products Grid ── */
.products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 16px; }
.jz-product-card { background: #fff; }
.jz-product-card .prod-img {
    position: relative; overflow: hidden;
    padding-top: 125%; background: #f5f5f5;
}
.jz-product-card .prod-img img {
    position: absolute; inset: 0; width: 100%; height: 100%;
    object-fit: cover; object-position: center top; transition: transform .45s ease;
}
.jz-product-card:hover .prod-img img { transform: scale(1.07); }
.prod-img .prod-hover {
    position: absolute; bottom: -52px; left: 0; right: 0;
    background: rgba(26,26,26,.88); display: flex;
    align-items: center; justify-content: center;
    gap: 20px; padding: 13px; transition: bottom .3s ease;
}
.jz-product-card:hover .prod-hover { bottom: 0; }
.prod-hover a, .prod-hover button {
    color: #fff; font-size: .9rem; background: none;
    border: none; cursor: pointer; padding: 0; transition: color .15s; text-decoration: none;
}
.prod-hover a:hover, .prod-hover button:hover { color: #D19C97; }
.jz-product-card .prod-info { padding: 12px 4px 14px; }
.prod-cat  { font-size: .62rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: #D19C97; margin-bottom: 4px; }
.prod-stars { font-size: .7rem; color: #D19C97; margin-bottom: 4px; }
.prod-name {
    display: block; font-size: .86rem; font-weight: 600; color: #1a1a1a;
    text-decoration: none; white-space: nowrap; overflow: hidden;
    text-overflow: ellipsis; margin-bottom: 5px; line-height: 1.3;
}
.prod-name:hover { color: #D19C97; }
.prod-price { font-size: .92rem; font-weight: 800; color: #1a1a1a; }
.prod-price .original { font-size: .78rem; color: #aaa; font-weight: 400; text-decoration: line-through; margin-left: 6px; }
.prod-badge {
    position: absolute; top: 10px; left: 10px;
    background: #D19C97; color: #fff; font-size: .58rem; font-weight: 800;
    letter-spacing: 1px; text-transform: uppercase; padding: 3px 8px; z-index: 2;
}

/* ── Newsletter ── */
.newsletter-band { background: #f3f3f0; padding: 56px 20px; margin-top: 20px; text-align: center; }
.nl-eyebrow { font-size: .68rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: #D19C97; margin-bottom: 10px; }
.newsletter-band h3 { font-size: clamp(1.3rem, 3vw, 1.75rem); font-weight: 900; text-transform: uppercase; color: #1a1a1a; margin-bottom: 8px; }
.newsletter-band p  { color: #888; font-size: .88rem; margin-bottom: 24px; }
.nl-form { display: flex; max-width: 440px; margin: 0 auto; }
.nl-form input {
    flex: 1; border: 1.5px solid #ccc; border-right: none;
    padding: 12px 16px; font-size: .84rem; border-radius: 0; outline: none;
}
.nl-form input:focus { border-color: #1a1a1a; }
.nl-form button {
    background: #1a1a1a; color: #fff; border: none;
    padding: 0 24px; font-size: .72rem; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer;
    transition: background .2s; border-radius: 0; white-space: nowrap;
}
.nl-form button:hover { background: #D19C97; }

/* ============================================================
   RESPONSIVE BREAKPOINTS
   ============================================================ */

/* ── Tablet (≤ 991px) ── */
@media (max-width: 991px) {
    .hero-wrap        { height: 400px; }
    .hero-caption     { padding: 0 5% 50px; }
    .hero-caption h1  { max-width: 420px; }

    .collection-grid.cols-3,
    .collection-grid.cols-4,
    .collection-grid.cols-5 { grid-template-columns: repeat(2, 1fr) !important; }
    .collection-grid.cols-5 .coll-card:first-child { grid-column: span 1; }
    .coll-card        { height: 300px; }
    .coll-card.tall   { height: 340px; }

    .fit-grid         { grid-template-columns: repeat(2, 1fr) !important; }
    .fit-card         { height: 340px; }

    .products-grid    { grid-template-columns: repeat(2, 1fr); }

    .promise-grid     { grid-template-columns: repeat(2, 1fr); }
    .promise-item:nth-child(2) { border-right: none; }

    .offer-input-wrap input { min-width: 160px; }
}

/* ── Mobile (≤ 575px) ── */
@media (max-width: 575px) {
    /* Hero */
    .hero-wrap        { height: 260px; }
    .hero-caption     { padding: 0 16px 40px; }
    .hero-caption h1  { font-size: 1.35rem; max-width: 100%; }
    .hero-sub         { font-size: .8rem; margin-bottom: 20px; display: none; }
    .hero-eyebrow     { margin-bottom: 8px; }
    .btn-hero-solid,
    .btn-hero-ghost   { padding: 10px 20px; font-size: .68rem; }

    /* Offer strip */
    .offer-strip-inner { flex-direction: column; gap: 12px; padding: 18px 16px; }
    .offer-input-wrap  { width: 100%; }
    .offer-input-wrap input { min-width: 0; flex: 1; }
    .offer-text h3     { font-size: 1rem; }

    /* Collections — single column on mobile */
    .collection-grid  { grid-template-columns: 1fr !important; }
    .collection-grid.cols-5 .coll-card:first-child { grid-column: span 1; }
    .coll-card        { height: 240px; }
    .coll-card.tall   { height: 260px; }
    .coll-card-body h3 { font-size: 1rem; }

    /* Fits — 2 columns on mobile */
    .fit-grid         { grid-template-columns: repeat(2, 1fr) !important; gap: 10px; }
    .fit-card         { height: 220px; }
    .fit-card-body h4 { font-size: .82rem; letter-spacing: 1px; }
    .fit-badge        { font-size: .58rem; padding: 5px 12px; }

    /* Promise — 2 cols on mobile */
    .promise-grid     { grid-template-columns: repeat(2, 1fr); }
    .promise-item     { padding: 16px 12px; }
    .promise-icon     { font-size: 1.4rem; }

    /* Products — 2 columns on mobile */
    .products-grid    { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .prod-info        { padding: 8px 2px 10px; }
    .prod-name        { font-size: .78rem; }
    .prod-price       { font-size: .82rem; }
    .prod-stars       { font-size: .62rem; }
    /* Hover overlay becomes always visible on mobile (no hover) */
    .prod-img .prod-hover { bottom: 0; padding: 8px; gap: 14px; }

    /* Newsletter */
    .nl-form          { flex-direction: column; max-width: 320px; }
    .nl-form input    { border-right: 1.5px solid #ccc; border-bottom: none; }
    .nl-form button   { padding: 12px; }

    /* Section spacing */
    .jz-heading       { padding: 28px 16px 8px; }
    .promise-strip    { padding: 36px 0; }
}

/* ── Small mobile (≤ 380px) ── */
@media (max-width: 380px) {
    .hero-wrap        { height: 220px; }
    .hero-caption h1  { font-size: 1.1rem; }
    .hero-btns        { gap: 8px; }
    .btn-hero-solid,
    .btn-hero-ghost   { padding: 9px 14px; font-size: .62rem; letter-spacing: 1px; }
    .products-grid    { gap: 8px; }
}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     1. HERO CAROUSEL
     ═══════════════════════════════════════════════════════════ --}}
<div class="hero-wrap">
    <div id="hero-carousel" class="carousel slide h-100" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner h-100">
            @php
                $slides = $banners ?? collect();
                $fallbackSlides = [
                    ['img' => asset('eshopper/img/carousel-1.jpg'), 'label' => 'New Season Collection', 'title' => "Built to Last.\nMade to Move.", 'sub' => 'Premium denim crafted for every adventure.', 'link' => route('products.index'), 'cta' => 'Shop Now'],
                    ['img' => asset('eshopper/img/carousel-2.jpg'), 'label' => 'Spring / Summer 2025', 'title' => "Fit for Every\nMoment.",        'sub' => 'The perfect pair of jeans for every day.',  'link' => route('products.index'), 'cta' => 'Explore Fits'],
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
                        <h1>{{ $fb['title'] }}</h1>
                        <p class="hero-sub">{{ $fb['sub'] }}</p>
                        <div class="hero-btns">
                            <a href="{{ $fb['link'] }}" class="btn-hero-solid">{{ $fb['cta'] }}</a>
                            <a href="{{ route('products.index') }}" class="btn-hero-ghost">View All</a>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        @php $totalSlides = $slides->isNotEmpty() ? $slides->count() : count($fallbackSlides); @endphp
        <ol class="carousel-indicators">
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

{{-- ═══════════════════════════════════════════════════════════
     2. OFFER STRIP
     ═══════════════════════════════════════════════════════════ --}}
<div class="offer-strip">
    <div class="offer-strip-inner">
        <div class="offer-badge">10%<br><span>OFF</span></div>
        <div class="offer-text">
            <h3>Sign Up &amp; Get 10% Off Your First Order</h3>
            <p>Join the Jeanzo family — exclusive deals, new arrivals &amp; style tips.</p>
        </div>
        <div class="offer-input-wrap">
            <input type="email" placeholder="Your email address" id="offer-email">
            <button type="button" onclick="handleOfferSignup()">Get 10% Off</button>
        </div>
    </div>
</div>
<script>
function handleOfferSignup(){
    var e=document.getElementById('offer-email');
    if(!e||!e.value.trim()){e&&e.focus();return;}
    e.value=''; e.placeholder='✓ You\'re in! Check your email.';
}
</script>

{{-- ═══════════════════════════════════════════════════════════
     3. SHOP BY COLLECTION
     ═══════════════════════════════════════════════════════════ --}}
@php
    $allActive = \App\Models\Category::where('is_active', true)->get();
    $prodCountById = \App\Models\Product::where('is_active', true)->whereNotNull('category_id')
        ->selectRaw('category_id, count(*) as cnt')->groupBy('category_id')
        ->pluck('cnt','category_id')->toArray();

    $subtreeCount = function($id) use (&$subtreeCount, $allActive, $prodCountById) {
        $n = $prodCountById[$id] ?? 0;
        foreach ($allActive->where('parent_id', $id) as $ch) { $n += $subtreeCount($ch->id); }
        return $n;
    };

    if (!function_exists('jzCatImg')) {
        function jzCatImg($cat, $fallbackNum = 1) {
            if (!$cat || !$cat->image_url) return asset('eshopper/img/cat-' . $fallbackNum . '.jpg');
            $url = $cat->image_url;
            if (str_starts_with($url,'http') || str_starts_with($url,'/')) return asset($url);
            return asset('storage/' . $url);
        }
    }

    $collections = $allActive->whereNull('parent_id')
        ->map(fn($c) => tap($c, fn($c2) => $c2->dyn_count = $subtreeCount($c2->id)))
        ->filter(fn($c) => $c->dyn_count > 0)->values();

    $collCount = $collections->count();
    $collGrid  = match(true) {
        $collCount >= 5 => 'cols-5',
        $collCount == 4 => 'cols-4',
        $collCount == 3 => 'cols-3',
        $collCount == 2 => 'cols-2',
        default         => 'cols-1',
    };
    $collFbImages = [asset('eshopper/img/cat-1.jpg'),asset('eshopper/img/cat-2.jpg'),asset('eshopper/img/cat-3.jpg'),asset('eshopper/img/cat-4.jpg'),asset('eshopper/img/cat-5.jpg')];
    $collLabels   = ["Men's Denim","Women's Denim","Premium","Kids","Accessories"];
@endphp

<div class="jz-heading">
    <div class="eyebrow">Curated For You</div>
    <h2>Shop By Collection</h2>
    <p>Discover the season's best, across every style.</p>
</div>

<div class="collection-grid {{ $collGrid }} px-2 px-xl-4">
    @forelse($collections as $idx => $col)
    <a href="{{ route('products.index', ['category' => $col->id]) }}"
       class="coll-card {{ $collCount <= 2 ? 'tall' : '' }}">
        <img src="{{ jzCatImg($col, ($idx % 5) + 1) }}" alt="{{ $col->name }}" loading="lazy">
        <div class="coll-card-body">
            <div class="coll-eyebrow">Collection</div>
            <h3>{{ $col->name }}</h3>
            <span class="btn-coll-link">Shop Now →</span>
        </div>
    </a>
    @empty
    @foreach($collLabels as $fi => $fl)
    <a href="{{ route('products.index') }}" class="coll-card">
        <img src="{{ $collFbImages[$fi] }}" alt="{{ $fl }}" loading="lazy">
        <div class="coll-card-body">
            <div class="coll-eyebrow">Collection</div>
            <h3>{{ $fl }}</h3>
            <span class="btn-coll-link">Shop Now →</span>
        </div>
    </a>
    @endforeach
    @endforelse
</div>

{{-- ═══════════════════════════════════════════════════════════
     4. SHOP BY FIT
     ═══════════════════════════════════════════════════════════ --}}
@php
    $fitCats = $allActive->filter(fn($c) => !is_null($c->parent_id))
        ->filter(fn($c) => ($prodCountById[$c->id] ?? 0) > 0)->values();

    if ($fitCats->isEmpty()) {
        $fitCats = $allActive->filter(fn($c) => !is_null($c->parent_id))
            ->filter(fn($c) => $subtreeCount($c->id) > 0)->values();
    }

    $fitFbImages = [asset('eshopper/img/cat-1.jpg'),asset('eshopper/img/cat-2.jpg'),asset('eshopper/img/cat-3.jpg'),asset('eshopper/img/cat-4.jpg')];
    $fitFbNames  = ['Slim Fit','Regular Fit','Skinny Fit','Relaxed Fit'];
    $fitCols     = $fitCats->count() >= 4 ? 4 : max(2, $fitCats->count());
@endphp

<div class="jz-heading">
    <div class="eyebrow">Find Your Style</div>
    <h2>Shop By Fit</h2>
    <p>Every body. Every occasion. Every fit you need.</p>
</div>

@if($fitCats->isNotEmpty())
<div class="fit-grid px-2 px-xl-4" style="grid-template-columns: repeat({{ $fitCols }}, 1fr);">
    @foreach($fitCats as $idx => $fit)
    <a href="{{ route('products.index', ['category' => $fit->id]) }}" class="fit-card">
        <img src="{{ jzCatImg($fit, ($idx % 4) + 1) }}" alt="{{ $fit->name }}" loading="lazy">
        <div class="fit-card-body">
            <h4>{{ $fit->name }}</h4>
            <span class="fit-badge">Shop Now</span>
        </div>
    </a>
    @endforeach
</div>
@else
<div class="fit-grid px-2 px-xl-4" style="grid-template-columns: repeat(4, 1fr);">
    @foreach($fitFbNames as $fi => $fn)
    <a href="{{ route('products.index') }}" class="fit-card">
        <img src="{{ $fitFbImages[$fi] }}" alt="{{ $fn }}" loading="lazy">
        <div class="fit-card-body">
            <h4>{{ $fn }}</h4>
            <span class="fit-badge">Shop Now</span>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     5. PROMISE STRIP
     ═══════════════════════════════════════════════════════════ --}}
<div class="promise-strip">
    <div class="jz-heading" style="padding: 0 20px 28px; background: transparent;">
        <div class="eyebrow" style="color:#D19C97;">Our Promise</div>
        <h2 style="color:#fff; margin-bottom:0;">Fit For Every Moment</h2>
    </div>
    <div class="promise-grid px-3 px-xl-5">
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
            <p>Not happy? Return within 7 days — no questions asked.</p>
        </div>
        <div class="promise-item">
            <div class="promise-icon"><i class="fas fa-headset"></i></div>
            <h5>24/7 Support</h5>
            <p>Our team is always here for you, day or night.</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     6. TRENDING PRODUCTS
     ═══════════════════════════════════════════════════════════ --}}
@php
    $trending = \App\Models\Product::with('images')
        ->withCount(['reviews as rev_count' => fn($q) => $q->where('is_approved',true)])
        ->withAvg(['reviews as rev_avg' => fn($q) => $q->where('is_approved',true)],'rating')
        ->where('is_active', true)->where('is_featured', true)
        ->latest()->take(8)->get();

    if ($trending->count() < 4) {
        $extra = \App\Models\Product::with('images')
            ->withCount(['reviews as rev_count' => fn($q) => $q->where('is_approved',true)])
            ->withAvg(['reviews as rev_avg' => fn($q) => $q->where('is_approved',true)],'rating')
            ->where('is_active', true)->whereNotIn('id', $trending->pluck('id')->toArray())
            ->latest()->take(8 - $trending->count())->get();
        $trending = $trending->merge($extra);
    }

    if (!function_exists('jzProdImg')) {
        function jzProdImg($product) {
            $img = $product->images->first();
            if ($img && $img->image) {
                $path = 'products/' . $product->id . '/images/' . $img->image;
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                }
            }
            return asset('storage/default.jpeg');
        }
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
        $imgUrl  = jzProdImg($product);
        $stars   = round($product->rev_avg ?? 0);
        $hasSale = $product->sale_price && $product->sale_price < $product->price;
        $catName = optional($product->category)->name;
    @endphp
    <div class="jz-product-card">
        <div class="prod-img">
            @if($hasSale)
                <span class="prod-badge">Sale</span>
            @elseif($product->is_featured)
                <span class="prod-badge" style="background:#1a1a1a;">Hot</span>
            @endif
            <a href="{{ route('products.detail', $product->slug) }}" title="{{ $product->name }}">
                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy"
                     onerror="this.onerror=null;this.src='{{ asset('storage/default.jpeg') }}'">
            </a>
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
                @if($product->rev_count > 0)<small class="text-muted ml-1" style="font-size:.62rem;">({{ $product->rev_count }})</small>@endif
            </div>
            <a href="{{ route('products.detail', $product->slug) }}" class="prod-name">{{ $product->name }}</a>
            <div class="prod-price">
                ₹{{ number_format($hasSale ? $product->sale_price : $product->price, 0) }}
                @if($hasSale)<span class="original">₹{{ number_format($product->price, 0) }}</span>@endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5" style="grid-column:1/-1;">
        <p class="text-muted">Products coming soon.</p>
        <a href="{{ route('products.index') }}" class="btn-hero-solid" style="color:#1a1a1a;border-color:#1a1a1a;display:inline-block;">Browse All</a>
    </div>
    @endforelse
</div>

@if($trending->isNotEmpty())
<div class="text-center mt-4 mb-2">
    <a href="{{ route('products.index') }}"
       style="display:inline-block;background:#1a1a1a;color:#fff;font-size:.75rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:13px 40px;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='#D19C97'"
       onmouseout="this.style.background='#1a1a1a'">
        View All Products
    </a>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     7. NEWSLETTER
     ═══════════════════════════════════════════════════════════ --}}
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
    var inp=f.querySelector('input');
    if(inp){inp.value='';inp.placeholder='✓ Thanks for subscribing!';}
    return false;
}
</script>

@endsection
