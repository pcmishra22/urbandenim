<header>
    <div class="container-menu-desktop">
        <div class="wrap-menu-desktop">
            <nav class="limiter-menu-desktop container">
                <!-- Logo desktop -->
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('themes/cozastore/assets/images/icons/logo-01.png') }}" alt="URBAN DENIM">
                </a>

                <!-- Menu desktop -->
                <div class="menu-desktop">
                    <ul class="main-menu">
                        <li class="active-menu"><a href="{{ url('/') }}">Home</a></li>
                        <li>
                            <a href="#">Shop</a>
                            <ul class="sub-menu">
                                <li><a href="{{ url('/category/men') }}">Men Jeans</a></li>
                                <li><a href="{{ url('/category/women') }}">Women Jeans</a></li>
                                <li><a href="{{ url('/category/kids') }}">Kids Jeans</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                    </ul>
                </div>

                <!-- Icon header -->
                <div class="wrap-icon-header flex-w flex-r-m">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="0">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>

                    <a href="{{ url('/wishlist') }}" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a>
                    
                    @auth
                        <a href="{{ url('/profile') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
                            <i class="zmdi zmdi-account"></i>
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="cl2 hov-cl1 trans-04 p-l-22 p-r-11 stext-101">
                            Login
                        </a>
                    @endauth
                </div>
            </nav>
        </div>
    </div>

    <!-- Modal Search and Mobile Menu would go here -->
</header>