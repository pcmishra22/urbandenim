<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Jeanzo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('eshopper/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('eshopper/css/style.css') }}" rel="stylesheet">
    @stack('styles')

    <style>
    /* ============================================================
       JEANZO HEADER — fully responsive
       ============================================================ */

    *, *::before, *::after { box-sizing: border-box; }
    html, body { overflow-x: hidden; max-width: 100%; }

    /* Promo bar */
    #promo-bar {
        background: #1a1a1a; color: #fff;
        font-size: .72rem; text-align: center;
        padding: 8px 16px; letter-spacing: .5px;
        line-height: 1.5;
    }
    #promo-bar a { color: #D19C97; text-decoration: none; font-weight: 600; }

    /* Sticky header */
    #site-header {
        position: sticky; top: 0; z-index: 1050;
        background: #fff; box-shadow: 0 1px 0 #e8e8e8;
    }

    /* ── Desktop header top row ── */
    .header-top {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        padding: 14px 0 12px;
        gap: 20px;
    }
    .header-logo { justify-self: start; }
    .header-logo a { text-decoration: none; }
    .header-logo h1 {
        font-size: 1.65rem; font-weight: 800; margin: 0; color: #1a1a1a;
        letter-spacing: -0.5px;
    }
    .header-logo h1 .logo-box {
        display: inline-block; background: #1a1a1a; color: #fff;
        padding: 2px 10px; margin-right: 3px;
    }
    .header-search { width: 100%; max-width: 480px; justify-self: center; }
    .header-search .form-control {
        border: 1.5px solid #e0e0e0; border-right: none;
        border-radius: 0 !important; font-size: .85rem;
        padding: 8px 14px; height: auto;
    }
    .header-search .form-control:focus { border-color: #1a1a1a; box-shadow: none; }
    .header-search .btn-search {
        background: #1a1a1a; color: #fff; border: none;
        padding: 0 18px; border-radius: 0; cursor: pointer;
        font-size: .88rem; transition: background .2s; height: 100%;
    }
    .header-search .btn-search:hover { background: #D19C97; }
    .header-icons { display: flex; align-items: center; gap: 20px; justify-self: end; }
    .header-icon-btn {
        background: none; border: none; padding: 0;
        font-size: 1.1rem; color: #1a1a1a; cursor: pointer;
        position: relative; line-height: 1; text-decoration: none;
        transition: color .15s;
    }
    .header-icon-btn:hover { color: #D19C97; }
    .header-icon-btn .badge {
        position: absolute; top: -7px; right: -9px;
        background: #D19C97; color: #fff;
        font-size: .58rem; border-radius: 10px;
        padding: 1px 5px; line-height: 1.4; font-weight: 700;
    }

    /* Mobile hamburger — hidden on desktop */
    .mobile-menu-btn {
        display: none; background: none; border: none;
        font-size: 1.4rem; color: #1a1a1a; cursor: pointer;
        padding: 4px; line-height: 1;
    }

    /* ── Desktop nav row ── */
    #header-nav-row { border-top: 1.5px solid #e8e8e8; }
    .header-nav {
        display: flex; align-items: center;
        justify-content: center; gap: 0;
        position: relative;
    }
    .header-nav > a,
    .mega-trigger > a.nav-top-link {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 12px 20px;
        font-size: .82rem; font-weight: 600; color: #1a1a1a;
        text-decoration: none; white-space: nowrap;
        letter-spacing: .3px; text-transform: uppercase;
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
    }
    .header-nav > a:hover,
    .mega-trigger > a.nav-top-link:hover,
    .header-nav > a.active,
    .mega-trigger.hovered > a.nav-top-link {
        color: #D19C97; border-bottom-color: #D19C97;
    }
    .nav-top-link .fa-chevron-down { font-size: .55rem; opacity: .45; transition: transform .2s; }
    .mega-trigger.hovered > a.nav-top-link .fa-chevron-down { transform: rotate(180deg); }
    .mega-trigger { position: static; }
    .mega-panel {
        display: none;
        position: absolute; left: -40px; right: -40px; top: 100%;
        background: #fff; border-top: 2px solid #D19C97;
        box-shadow: 0 12px 40px rgba(0,0,0,.12);
        z-index: 2000; padding: 28px 40px 24px;
    }
    .mega-trigger.hovered .mega-panel { display: flex; gap: 0; }
    .mega-column { flex: 1; min-width: 130px; padding: 0 20px 0 0; }
    .mega-column:last-child { padding-right: 0; }
    .mega-column-title {
        font-size: .72rem; font-weight: 700; color: #1a1a1a;
        letter-spacing: 1px; text-transform: uppercase;
        margin-bottom: 12px; padding-bottom: 8px;
        border-bottom: 1.5px solid #f0f0f0;
    }
    .mega-column ul { list-style: none; padding: 0; margin: 0; }
    .mega-column ul li { margin-bottom: 7px; }
    .mega-column ul li a {
        font-size: .82rem; color: #555; text-decoration: none;
        transition: color .13s; display: block; line-height: 1.4;
    }
    .mega-column ul li a:hover { color: #D19C97; }
    .mega-view-all-btn {
        display: inline-block; font-size: .75rem; font-weight: 700;
        color: #D19C97; text-decoration: none;
        border-bottom: 1px solid #D19C97; padding-bottom: 1px; margin-top: 16px;
    }
    .mega-view-all-btn:hover { color: #b8807a; border-color: #b8807a; text-decoration: none; }

    /* ── Mobile drawer ── */
    #mobile-drawer {
        display: none;
        position: fixed; inset: 0; z-index: 2000;
    }
    #mobile-drawer.open { display: flex; }
    #drawer-overlay {
        position: absolute; inset: 0;
        background: rgba(0,0,0,.5);
    }
    #drawer-panel {
        position: absolute; top: 0; left: 0; bottom: 0;
        width: 300px; max-width: 85vw;
        background: #fff; overflow-y: auto;
        display: flex; flex-direction: column;
        transform: translateX(-100%);
        transition: transform .28s ease;
    }
    #mobile-drawer.open #drawer-panel { transform: translateX(0); }
    #drawer-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #f0f0f0;
        background: #1a1a1a; color: #fff;
    }
    #drawer-header .drawer-logo { font-size: 1.2rem; font-weight: 800; }
    #drawer-header .drawer-logo .logo-box {
        background: #fff; color: #1a1a1a;
        padding: 1px 7px; margin-right: 3px;
    }
    #drawer-close {
        background: none; border: none; color: #fff;
        font-size: 1.3rem; cursor: pointer; padding: 0; line-height: 1;
    }
    /* Mobile search inside drawer */
    .drawer-search {
        padding: 14px 16px; border-bottom: 1px solid #f0f0f0;
    }
    .drawer-search form { display: flex; }
    .drawer-search input {
        flex: 1; border: 1.5px solid #e0e0e0; border-right: none;
        padding: 9px 14px; font-size: .85rem; outline: none;
        border-radius: 0;
    }
    .drawer-search button {
        background: #1a1a1a; color: #fff; border: none;
        padding: 0 16px; font-size: .88rem; cursor: pointer;
        border-radius: 0;
    }
    /* Nav links */
    .drawer-nav { flex: 1; padding: 8px 0; }
    .drawer-nav a,
    .drawer-nav-section > .drawer-section-toggle {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 20px;
        font-size: .88rem; font-weight: 600; color: #1a1a1a;
        text-decoration: none; text-transform: uppercase;
        letter-spacing: .4px; border-bottom: 1px solid #f5f5f5;
        background: none; border-left: none; border-right: none; border-top: none;
        width: 100%; cursor: pointer; transition: color .15s;
    }
    .drawer-nav a:hover,
    .drawer-nav-section > .drawer-section-toggle:hover { color: #D19C97; }
    .drawer-nav a.active,
    .drawer-nav-section.expanded > .drawer-section-toggle { color: #D19C97; }
    .drawer-section-toggle .toggle-icon { font-size: .65rem; transition: transform .2s; }
    .drawer-nav-section.expanded .toggle-icon { transform: rotate(180deg); }
    .drawer-submenu {
        display: none;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
    }
    .drawer-nav-section.expanded .drawer-submenu { display: block; }
    .drawer-submenu a {
        padding: 11px 20px 11px 34px;
        font-size: .83rem; font-weight: 400;
        color: #555; text-transform: none; letter-spacing: 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .drawer-submenu a:last-child { border-bottom: none; }
    .drawer-submenu a:hover { color: #D19C97; background: #fff; }
    /* Footer icons row in drawer */
    .drawer-footer {
        padding: 16px 20px; border-top: 1px solid #f0f0f0;
        display: flex; gap: 16px; align-items: center;
        flex-wrap: wrap;
    }
    .drawer-footer a {
        font-size: .83rem; color: #333; text-decoration: none;
        display: flex; align-items: center; gap: 7px;
    }
    .drawer-footer a i { color: #D19C97; }

    /* ── Tablet / Mobile breakpoints ── */
    @media (max-width: 991px) {
        .header-top {
            grid-template-columns: auto auto 1fr;
            padding: 10px 0;
        }
        .mobile-menu-btn { display: block; }
        .header-search { display: none; }   /* search moves inside drawer */
        #header-nav-row { display: none; }  /* desktop nav hidden */
        .header-icons { gap: 14px; }
        .header-logo h1 { font-size: 1.35rem; }
    }
    @media (max-width: 480px) {
        #promo-bar { font-size: .68rem; padding: 7px 12px; }
        .header-logo h1 { font-size: 1.15rem; }
        .header-icon-btn { font-size: 1rem; }
    }
    </style>
</head>
<body>

<!-- PROMO BAR -->
<div id="promo-bar">
    🎉 Free shipping on orders above ₹999 &nbsp;|&nbsp; Code <strong>FIRST10</strong> for 10% off &nbsp;|&nbsp; <a href="{{ route('products.index') }}">Shop Now →</a>
</div>

<!-- STICKY HEADER -->
<div id="site-header">
<div class="container-fluid px-xl-5 px-3">

    <!-- Row 1: Hamburger | Logo | Search | Icons -->
    <div class="header-top">
        <!-- Hamburger (mobile only) -->
        <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open menu">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Logo -->
        <div class="header-logo">
            <a href="{{ url('/') }}">
                <h1><span class="logo-box">J</span>eanzo</h1>
            </a>
        </div>

        <!-- Search (desktop only) -->
        <div class="header-search">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search jeans, fits, styles…" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Icons -->
        <div class="header-icons">
            @auth
            <a href="{{ route('wishlist.index') }}" class="header-icon-btn" title="Wishlist">
                <i class="fas fa-heart"></i>
            </a>
            @endauth
            <a href="{{ route('cart.index') }}" class="header-icon-btn" title="Cart">
                <i class="fas fa-shopping-bag"></i>
                @if(($headerCartCount ?? 0) > 0)
                <span class="badge">{{ $headerCartCount }}</span>
                @endif
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
                    <div class="dropdown-menu dropdown-menu-right rounded-0 m-0" style="min-width:180px;border:1px solid #eee;">
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

    <!-- Row 2: Desktop Nav -->
    <div id="header-nav-row">
    <div style="position:relative;">
    <div class="header-nav" id="main-nav">
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">All Jeans</a>

        @php
            $mensCat = \App\Models\Category::where('slug', 'mens-jeans')->first();
            $menSubcats = $mensCat
                ? \App\Models\Category::where('parent_id', $mensCat->id)->where('is_active', true)
                    ->whereHas('products', fn($p) => $p->where('is_active', true))->orderBy('name')->get()
                : collect();
        @endphp
        <div class="mega-trigger" id="mega-men">
            <a href="{{ $mensCat ? route('products.index', ['category' => $mensCat->id]) : route('products.index') }}"
               class="nav-top-link {{ request('category') == ($mensCat->id ?? null) ? 'active' : '' }}">
                Men <i class="fas fa-chevron-down"></i>
            </a>
            @if($menSubcats->isNotEmpty())
            <div class="mega-panel">
                <div class="mega-column">
                    <div class="mega-column-title">Shop By Fit</div>
                    <ul>
                        @foreach($menSubcats->take(8) as $sub)
                        <li><a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a></li>
                        @endforeach
                    </ul>
                    @if($mensCat)<a class="mega-view-all-btn" href="{{ route('products.index', ['category' => $mensCat->id]) }}">View All Men's →</a>@endif
                </div>
            </div>
            @endif
        </div>

        @php
            $womenParentCats = \App\Models\Category::whereIn('slug', ['womens-jeans','womens-denim'])->where('is_active', true)->get();
            $womensCat = $womenParentCats->firstWhere('slug','womens-jeans') ?? $womenParentCats->first();
            $womenSubcats = $womenParentCats->isNotEmpty()
                ? \App\Models\Category::whereIn('parent_id', $womenParentCats->pluck('id'))
                    ->where('is_active', true)
                    ->whereHas('products', fn($p) => $p->where('is_active', true))
                    ->orderBy('name')->get()->unique('id')->values()
                : collect();
        @endphp
        <div class="mega-trigger" id="mega-women">
            <a href="{{ $womensCat ? route('products.index', ['category' => $womensCat->id]) : route('products.index') }}"
               class="nav-top-link {{ request('category') == ($womensCat->id ?? null) ? 'active' : '' }}">
                Women <i class="fas fa-chevron-down"></i>
            </a>
            @if($womenSubcats->isNotEmpty())
            <div class="mega-panel">
                <div class="mega-column">
                    <div class="mega-column-title">Shop By Fit</div>
                    <ul>
                        @foreach($womenSubcats->take(8) as $sub)
                        <li><a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a></li>
                        @endforeach
                    </ul>
                    @if($womensCat)<a class="mega-view-all-btn" href="{{ route('products.index', ['category' => $womensCat->id]) }}">View All Women's →</a>@endif
                </div>
            </div>
            @endif
        </div>

        <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
    </div>
    </div>
    </div>

</div>
</div>
<!-- END HEADER -->

<!-- MOBILE DRAWER -->
<div id="mobile-drawer" role="dialog" aria-modal="true" aria-label="Navigation menu">
    <div id="drawer-overlay"></div>
    <div id="drawer-panel">
        <!-- Drawer header -->
        <div id="drawer-header">
            <span class="drawer-logo"><span class="logo-box">J</span>eanzo</span>
            <button id="drawer-close" aria-label="Close menu"><i class="fas fa-times"></i></button>
        </div>

        <!-- Search inside drawer -->
        <div class="drawer-search">
            <form action="{{ route('products.index') }}" method="GET">
                <input type="text" name="search" placeholder="Search jeans, fits…" value="{{ request('search') }}">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <!-- Nav links -->
        <nav class="drawer-nav">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">All Jeans</a>

            @if($menSubcats->isNotEmpty())
            <div class="drawer-nav-section" id="drawer-men">
                <button class="drawer-section-toggle">
                    Men's <i class="fas fa-chevron-down toggle-icon"></i>
                </button>
                <div class="drawer-submenu">
                    <a href="{{ $mensCat ? route('products.index', ['category' => $mensCat->id]) : route('products.index') }}">All Men's Jeans</a>
                    @foreach($menSubcats as $sub)
                    <a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a>
                    @endforeach
                </div>
            </div>
            @else
            <a href="{{ $mensCat ? route('products.index', ['category' => $mensCat->id]) : route('products.index') }}">Men's</a>
            @endif

            @if($womenSubcats->isNotEmpty())
            <div class="drawer-nav-section" id="drawer-women">
                <button class="drawer-section-toggle">
                    Women's <i class="fas fa-chevron-down toggle-icon"></i>
                </button>
                <div class="drawer-submenu">
                    <a href="{{ $womensCat ? route('products.index', ['category' => $womensCat->id]) : route('products.index') }}">All Women's Jeans</a>
                    @foreach($womenSubcats as $sub)
                    <a href="{{ route('products.index', ['category' => $sub->id]) }}">{{ $sub->name }}</a>
                    @endforeach
                </div>
            </div>
            @else
            <a href="{{ $womensCat ? route('products.index', ['category' => $womensCat->id]) : route('products.index') }}">Women's</a>
            @endif

            <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
        </nav>

        <!-- Footer links -->
        <div class="drawer-footer">
            @auth
            <a href="{{ route('profile.dashboard') }}"><i class="fas fa-user"></i> My Account</a>
            <a href="{{ route('profile.orders') }}"><i class="fas fa-box"></i> My Orders</a>
            <a href="{{ route('wishlist.index') }}"><i class="fas fa-heart"></i> Wishlist</a>
            @else
            <a href="{{ route('customer.login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="{{ route('customer.register') }}"><i class="fas fa-user-plus"></i> Register</a>
            @endauth
        </div>
    </div>
</div>

<script>
(function () {
    var btn     = document.getElementById('mobile-menu-btn');
    var drawer  = document.getElementById('mobile-drawer');
    var overlay = document.getElementById('drawer-overlay');
    var closeBtn= document.getElementById('drawer-close');

    function openDrawer()  { drawer.classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeDrawer() { drawer.classList.remove('open'); document.body.style.overflow = ''; }

    if (btn)     btn.addEventListener('click', openDrawer);
    if (closeBtn)closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    // Accordion for mobile nav sections
    document.querySelectorAll('.drawer-section-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var section = toggle.closest('.drawer-nav-section');
            var isOpen  = section.classList.contains('expanded');
            document.querySelectorAll('.drawer-nav-section').forEach(function(s){ s.classList.remove('expanded'); });
            if (!isOpen) section.classList.add('expanded');
        });
    });

    // Desktop mega menus
    document.querySelectorAll('.mega-trigger').forEach(function (trigger) {
        var timer;
        trigger.addEventListener('mouseenter', function () {
            clearTimeout(timer);
            document.querySelectorAll('.mega-trigger').forEach(function(t){ t.classList.remove('hovered'); });
            trigger.classList.add('hovered');
        });
        trigger.addEventListener('mouseleave', function () {
            timer = setTimeout(function () { trigger.classList.remove('hovered'); }, 150);
        });
    });
    window.addEventListener('scroll', function () {
        document.querySelectorAll('.mega-trigger.hovered').forEach(function(t){ t.classList.remove('hovered'); });
    }, { passive: true });
})();
</script>
