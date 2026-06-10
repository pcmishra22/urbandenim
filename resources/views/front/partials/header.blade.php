<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
<title>@yield('title', 'Jeanzo')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="{{ asset('eshopper/img/favicon.ico') }}" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('eshopper/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('eshopper/css/style.css') }}" rel="stylesheet">
    @stack('styles')
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FKT9P78GTQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-FKT9P78GTQ');
</script>
    @stack('scripts')
</head>
<body>
    <!-- Topbar Start -->
    <div class="container-fluid">
        <div class="row bg-secondary py-2 px-xl-5">
            <div class="col-lg-6 d-none d-lg-block">
                <div class="d-inline-flex align-items-center">
                    <a class="text-dark" href="{{ route('faq') }}">FAQs</a>
                    <span class="text-muted px-2">|</span>
                    <a class="text-dark" href="{{ route('help') }}">Help</a>
                    <span class="text-muted px-2">|</span>
                    <a class="text-dark" href="{{ route('contact') }}">Support</a>
                </div>
            </div>
            <div class="col-lg-6 text-center text-lg-right">
                <div class="d-inline-flex align-items-center">
                    @php $ss = \App\Models\SiteSetting::all_settings(); @endphp
                    @if(!empty($ss['facebook_url']))<a class="text-dark px-2" href="{{ $ss['facebook_url'] }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>@endif
                    @if(!empty($ss['twitter_url']))<a class="text-dark px-2" href="{{ $ss['twitter_url'] }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>@endif
                    @if(!empty($ss['linkedin_url']))<a class="text-dark px-2" href="{{ $ss['linkedin_url'] }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if(!empty($ss['instagram_url']))<a class="text-dark px-2" href="{{ $ss['instagram_url'] }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>@endif
                    @if(!empty($ss['youtube_url']))<a class="text-dark pl-2" href="{{ $ss['youtube_url'] }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>@endif
                </div>
            </div>
        </div>
        <div class="row align-items-center py-3 px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="m-0 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border px-3 mr-1">J</span>eanzo</h1>
                </a>
            </div>
            <div class="col-lg-6 col-6 text-left">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search for products" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text bg-transparent text-primary border-left-0" style="cursor:pointer;border:1px solid #ced4da;">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 col-6 text-right">
                @auth
                <a href="{{ route('wishlist.index') }}" class="btn border">
                    <i class="fas fa-heart text-primary"></i>
                    <span class="badge">{{ $headerWishlistCount ?? 0 }}</span>
                </a>
                @endauth
                <a href="{{ route('cart.index') }}" class="btn border">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    <span class="badge" id="cart-count">{{ $headerCartCount ?? 0 }}</span>
                </a>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid">
        <div class="row border-top px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
                   data-toggle="collapse" href="#navbar-vertical" style="height: 65px; margin-top: -1px; padding: 0 30px;">
                    <h6 class="m-0">Categories</h6>
                    <i class="fa fa-angle-down text-dark"></i>
                </a>
                <nav class="collapse{{ request()->is('/') ? ' show' : '' }} position-absolute navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 bg-light" id="navbar-vertical" style="width:calc(100% - 30px);z-index:999;">
                    <div class="navbar-nav w-100 overflow-hidden" style="height:410px">
                        @php $navCategories = \App\Models\Category::where('is_active', true)->take(10)->get(); @endphp
                        @forelse($navCategories as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->id]) }}" class="nav-item nav-link">{{ $cat->name }}</a>
                        @empty
                            <a href="{{ route('products.index') }}" class="nav-item nav-link">All Products</a>
                        @endforelse
                    </div>
                </nav>
            </div>
            <div class="col-lg-9">
                <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                    <a href="{{ url('/') }}" class="text-decoration-none d-block d-lg-none">
                        <h1 class="m-0 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border px-3 mr-1">J</span>eanzo</h1>
                    </a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav mr-auto py-0">
                            <a href="{{ url('/') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                            <a href="{{ route('products.index') }}" class="nav-item nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Shop</a>
                            <a href="{{ route('blog.index') }}" class="nav-item nav-link {{ request()->is('blog*') ? 'active' : '' }}">Blog</a>

                            <a href="{{ route('cart.index') }}" class="nav-item nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">Cart</a>
                            <a href="{{ route('checkout.index') }}" class="nav-item nav-link {{ request()->routeIs('checkout.*') ? 'active' : '' }}">Checkout</a>
                              @auth
                            <a href="{{ route('profile.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">My Account</a>
                            <a href="{{ route('profile.orders') }}" class="nav-item nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">My Orders</a>
                            @endauth
                            <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                        </div>
                        <div class="navbar-nav ml-auto py-0">
                            @guest
                                <a href="{{ route('customer.login') }}" class="nav-item nav-link">Login</a>
                                <a href="{{ route('customer.register') }}" class="nav-item nav-link">Register</a>
                            @else
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-user-circle mr-1"></i>{{ auth()->user()->name }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right rounded-0 m-0">
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
                </nav>
                @yield('navbar-extra')
            </div>
        </div>
    </div>
    <!-- Navbar End -->
