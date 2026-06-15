<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Jeanzo')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="{{ asset('eshopper/img/favicon.ico') }}" rel="icon">

    @hasSection('meta_description')<meta name="description" content="@yield('meta_description')">@endif
    @hasSection('meta_robots')<meta name="robots" content="@yield('meta_robots')">@else<meta name="robots" content="index, follow">@endif
    @hasSection('canonical')<link rel="canonical" href="@yield('canonical')">@endif
    <meta property="og:site_name" content="Jeanzo">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'Jeanzo'))">
    <meta property="og:description" content="@yield('og_description', '')">
    @hasSection('og_image')<meta property="og:image" content="@yield('og_image')">@endif
    <meta name="twitter:card" content="summary_large_image">
    @stack('json_ld')

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('eshopper/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('eshopper/css/style.css') }}" rel="stylesheet">
    @stack('styles')
    @stack('scripts')

    <style>
    /* ================================================
       JEANZO TOP BAR + MEGA MENU  (Pepe Jeans style)
       ================================================ */

    /* -- Top promo bar -- */
    #promo-bar {
        background: #2d2d2d;
        color: #fff;
        font-size: .75rem;
        text-align: center;
        padding: 7px 0;
        letter-spacing: .4px;
    }
    #promo-bar a { color: #D19C97; text-decoration: none; font-weight: 600; }

    /* -- Header wrapper -- */
    #site-header {
        position: sticky;
        top: 0;
        z-index: 1050;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }

    /* -- Top row: logo + search + icons -- */
    .header-top {
        display: flex;
        align-items: center;
        padding: 12px 0 8px;
        gap: 16px;
    }
    .header-logo { flex-shrink: 0; }
    .header-search { flex: 1; max-width: 460px; margin: 0 auto; }
    .header-search .input-group-text,
    .header-search .form-control { border-radius: 0 !important; }
    .header-icons { display: flex; align-items: center; gap: 18px; flex-shrink: 0; }
    .header-icon-btn {
        background: none; border: none; padding: 0;
        font-size: 1.15rem; color: #2d2d2d; cursor: pointer;
        position: relative; line-height: 1;
        text-decoration: none;
    }
    .header-icon-btn:hover { color: #D19C97; }
    .header-icon-btn .badge {
        position: absolute; top: -6px; right: -8px;
        background: #D19C97; color: #fff;
        font-size: .6rem; border-radius: 10px;
        padding: 1px 5px; line-height: 1.4;
    }

    /* -- Nav bar (bottom row of header) -- */
    .header-nav {
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 0;
    }
    .header-nav > a,
    .mega-trigger > a.nav-top-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 11px 18px;
        font-size: .85rem;
        font-weight: 500;
        color: #2d2d2d;
        text-decoration: none;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
    }
    .header-nav > a:hover,
    .mega-trigger > a.nav-top-link:hover,
    .header-nav > a.active,
    .mega-trigger.hovered > a.nav-top-link {
        color: #D19C97;
        border-bottom-color: #D19C97;
    }
    .nav-top-link .fa-chevron-down { font-size: .6rem; opacity: .5; transition: transform .2s; }
    .mega-trigger.hovered > a.nav-top-link .fa-chevron-down { transform: rotate(180deg); }

    /* -- Mega dropdown -- */
    .mega-trigger { position: static; }
    .mega-panel {
        display: none;
        position: absolute;
        left: 0; right: 0;
        top: 100%;
        background: #fff;
        border-top: 2px solid #D19C97;
        box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        z-index: 2000;
        padding: 32px 40px 28px;
    }
/* Open via hover intent (JS adds .hovered) OR direct hover (pure CSS) */
.mega-trigger.hovered .mega-panel,
.mega-trigger:hover .mega-panel { 
    display: flex; 
    gap: 0; 
}

/* Prevent hovering the panel from closing the menu immediately on fast mouse moves */
.mega-panel {
        pointer-events: auto;
        visibility: visible;
        opacity: 1;
    }

    /* -- Mega columns -- */
    .mega-column { flex: 1; min-width: 140px; padding: 0 20px 0 0; }
    .mega-column:last-child { padding-right: 0; }
    .mega-column-title {
        font-size: .78rem;
        font-weight: 700;
        color: #2d2d2d;
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1.5px solid #f0f0f0;
    }
    .mega-column ul { list-style: none; padding: 0; margin: 0; }
    .mega-column ul li { margin-bottom: 8px; }
    .mega-column ul li a {
        font-size: .84rem;
        color: #555;
        text-decoration: none;
        transition: color .13s;
        display: block;
        line-height: 1.4;
    }
    .mega-column ul li a:hover { color: #D19C97; }

    /* View all button */
    .mega-view-all-wrap { margin-top: 20px; border-top: 1px solid #f0f0f0; padding-top: 16px; }
    .mega-view-all-btn {
        display: inline-block;
        font-size: .78rem;
        font-weight: 700;
        color: #D19C97;
        text-decoration: none;
        letter-spacing: .3px;
        border-bottom: 1px solid #D19C97;
        padding-bottom: 1px;
    }
    .mega-view-all-btn:hover { color: #b8807a; border-color: #b8807a; }

    /* Mobile nav */
    @media (max-width: 991px) {
        .header-nav { display: none; }
        .mega-panel { position: static; box-shadow: none; border: none; padding: 10px 16px; }
        .mega-trigger.hovered .mega-panel { flex-wrap: wrap; }
        .mega-column { min-width: 45%; flex: none; margin-bottom: 16px; }
    }
    </style>
</head>
<body>

<!-- ===== PROMO BAR ===== -->
<div id="promo-bar">
    🎉 Free shipping on orders above ₹999 &nbsp;|&nbsp; <a href="{{ route('products.index') }}">Shop Now</a>
</div>

<!-- ===== STICKY HEADER ===== -->
<div id="site-header">
<div class="container-fluid px-xl-5">

    {{-- TOP ROW --}}
    <div class="header-top">

        {{-- Logo --}}
        <div class="header-logo">
            <a href="{{ url('/') }}" class="text-decoration-none">
                <h1 class="m-0 font-weight-semi-bold" style="font-size:1.6rem;line-height:1;">
                    <span class="font-weight-bold border px-3 mr-1" style="color:#D19C97;border-color:#D19C97!important;">J</span>eanzo
                </h1>
            </a>
        </div>

        {{-- Search --}}
        <div class="header-search">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search for jeans, fits, styles…" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-sm px-3"
                                style="background:#D19C97;color:#fff;border:none;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Icons --}}
        <div class="header-icons">
            @auth
            <a href="{{ route('wishlist.index') }}" class="header-icon-btn" title="Wishlist">
                <i class="fas fa-heart"></i>
            </a>
            @endauth
            <a href="{{ route('cart.index') }}" class="header-icon-btn" title="Cart">
                <i class="fas fa-shopping-bag"></i>
                <span class="badge">{{ $headerCartCount ?? 0 }}</span>
            </a>
            @guest
                <a href="{{ route('customer.login') }}" class="header-icon-btn" title="Login">
                    <i class="fas fa-user"></i>
                </a>
            @else
                <div class="dropdown">
                    <a href="#" class="header-icon-btn dropdown-toggle" data-toggle="dropdown" title="{{ auth()->user()->name }}">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right rounded-0 m-0" style="min-width:180px;">
                        <div class="px-3 py-2 border-bottom">
                            <small class="text-muted">Signed in as</small>
                            <div class="font-weight-600" style="font-size:.85rem;">{{ auth()->user()->name }}</div>
                        </div>
                        <a href="{{ route('profile.dashboard') }}" class="dropdown-item">My Account</a>
                        <a href="{{ route('profile.orders') }}" class="dropdown-item">My Orders</a>
                        <a href="{{ route('wishlist.index') }}" class="dropdown-item">Wishlist</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>

    </div>

    {{-- NAV ROW — full width, positioned relative for mega panel --}}
    <div style="position:relative;">
    <div class="header-nav" id="main-nav">

        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Shop</a>

        {{-- MEN --}}
        @php
            $mensCat = \App\Models\Category::where('slug', 'mens-jeans')->first();
            // DB is flat: subcategories (Slim Fit, Skinny Fit…) are direct children of Men's Jeans
            // Only show subcategories that actually have active products
            $menSubcats = $mensCat
                ? \App\Models\Category::where('parent_id', $mensCat->id)
                    ->where('is_active', true)
                    ->whereHas('products', fn($p) => $p->where('is_active', true))
                    ->orderBy('name')
                    ->get()
                : collect();
            // Commented out: Clothing / Innerwear / Accessories — jeans-only store
        @endphp
        <div class="mega-trigger" id="mega-men">
            <a href="{{ $mensCat ? route('products.index', ['category' => $mensCat->id]) : route('products.index') }}"
               class="nav-top-link {{ request('category') == ($mensCat->id ?? null) ? 'active' : '' }}">
                Men <i class="fas fa-chevron-down"></i>
            </a>
            @if($menSubcats->isNotEmpty())
            <div class="mega-panel">
                <div class="mega-column">
                    <div class="mega-column-title">Jeans</div>
                    <ul>
                        @foreach($menSubcats as $sub)
                        <li><a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                {{-- <div class="mega-column"><div class="mega-column-title">Clothing</div>...</div> --}}
                {{-- <div class="mega-column"><div class="mega-column-title">Innerwear</div>...</div> --}}
                {{-- <div class="mega-column"><div class="mega-column-title">Accessories</div>...</div> --}}
                <div class="mega-view-all-wrap" style="align-self:flex-end;margin-left:auto;flex-shrink:0;padding-left:24px;border-top:none;">
                    <a class="mega-view-all-btn" href="{{ route('products.index', ['category' => $mensCat->id]) }}">
                        View All Men's →
                    </a>
                </div>
            </div>
            @endif
        </div>

        {{-- WOMEN --}}
        @php
            $womensCat = \App\Models\Category::whereIn('slug', ['womens-jeans', 'womens-denim'])->first();
            // DB is flat: subcategories are direct children of Women's Jeans
            // Only show subcategories that actually have active products
            $womenSubcats = $womensCat
                ? \App\Models\Category::where('parent_id', $womensCat->id)
                    ->where('is_active', true)
                    ->whereHas('products', fn($p) => $p->where('is_active', true))
                    ->orderBy('name')
                    ->get()
                : collect();
            // Commented out: Clothing / Innerwear / Accessories — jeans-only store
        @endphp
        <div class="mega-trigger" id="mega-women">
            <a href="{{ $womensCat ? route('products.index', ['category' => $womensCat->id]) : route('products.index') }}"
               class="nav-top-link {{ request('category') == ($womensCat->id ?? null) ? 'active' : '' }}">
                Women <i class="fas fa-chevron-down"></i>
            </a>
            @if($womenSubcats->isNotEmpty())
            <div class="mega-panel">
                <div class="mega-column">
                    <div class="mega-column-title">Jeans</div>
                    <ul>
                        @foreach($womenSubcats as $sub)
                        <li><a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                {{-- <div class="mega-column"><div class="mega-column-title">Clothing</div>...</div> --}}
                {{-- <div class="mega-column"><div class="mega-column-title">Innerwear</div>...</div> --}}
                {{-- <div class="mega-column"><div class="mega-column-title">Accessories</div>...</div> --}}
                <div class="mega-view-all-wrap" style="align-self:flex-end;margin-left:auto;flex-shrink:0;padding-left:24px;border-top:none;">
                    <a class="mega-view-all-btn" href="{{ route('products.index', ['category' => $womensCat->id]) }}">
                        View All Women's →
                    </a>
                </div>
            </div>
            @endif
        </div>

        <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>

    </div>
    </div>{{-- /relative wrapper --}}

</div>
</div>
<!-- ===== END STICKY HEADER ===== -->

<script>
// Hover intent for mega menus
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mega-trigger').forEach(function (trigger) {
        var timer;
        trigger.addEventListener('mouseenter', function () {
            clearTimeout(timer);
            // Close all others
            document.querySelectorAll('.mega-trigger').forEach(function(t){ t.classList.remove('hovered'); });
            trigger.classList.add('hovered');
        });
        trigger.addEventListener('mouseleave', function () {
            timer = setTimeout(function () { trigger.classList.remove('hovered'); }, 120);
        });
    });

    // Close on scroll
    window.addEventListener('scroll', function () {
        document.querySelectorAll('.mega-trigger.hovered').forEach(function(t){ t.classList.remove('hovered'); });
    }, { passive: true });
});
</script>
