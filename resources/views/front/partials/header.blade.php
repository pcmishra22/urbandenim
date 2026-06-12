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
    @stack('scripts')
</head>
<body>

<!-- ===== STICKY HEADER ===== -->
<div id="site-header" style="position:sticky;top:0;z-index:1050;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

    <div class="container-fluid px-xl-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-2 px-0">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="navbar-brand mr-4 text-decoration-none">
                <h1 class="m-0 display-5 font-weight-semi-bold" style="font-size:1.6rem;">
                    <span class="text-primary font-weight-bold border px-3 mr-1">J</span>eanzo
                </h1>
            </a>

            {{-- Mobile toggler --}}
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">

                {{-- Search bar --}}
                <form action="{{ route('products.index') }}" method="GET" class="mx-3 flex-grow-1" style="max-width:380px;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products…" value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>

                {{-- Nav links --}}
                <div class="navbar-nav mr-auto py-0">
                    <a href="{{ url('/') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="nav-item nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Shop</a>
                    <a href="{{ route('blog.index') }}" class="nav-item nav-link {{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
                    <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                </div>

                {{-- Right: cart + account --}}
                <div class="navbar-nav ml-auto py-0 align-items-center">
                    @auth
                    <a href="{{ route('wishlist.index') }}" class="nav-item nav-link px-2">
                        <i class="fas fa-heart text-primary"></i>
                    </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="nav-item nav-link px-2">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <span class="badge badge-pill badge-primary" id="cart-count" style="font-size:.65rem;">{{ $headerCartCount ?? 0 }}</span>
                    </a>
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
    </div>

</div>
<!-- ===== END STICKY HEADER ===== -->

<script>
window.addEventListener('scroll', function () {
    document.querySelectorAll('.navbar-vertical.show').forEach(function(el) {
        el.classList.remove('show');
    });
}, { passive: true });
</script>

