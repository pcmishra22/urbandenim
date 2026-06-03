<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Urban Denim - CozaStore Theme')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('themes/cozastore/assets/images/icons/favicon.png') }}"/>

    <!-- CozaStore Theme Assets (copied assets should live under public/themes/cozastore/assets) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/MagnificPopup/magnific-popup.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/cozastore/assets/css/main.css') }}">

    @stack('styles')
</head>
<body class="animsition">
    <header>
        @include('themes.cozastore.partials.header')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('themes.cozastore.partials.footer')
    </footer>

    <!-- Back to top -->
    <div class="btn-back-to-top" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <i class="zmdi zmdi-chevron-up"></i>
        </span>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('themes/cozastore/assets/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/select2/select2.min.js') }}"></script>

    <script>
        $(".js-select2").each(function(){
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        })
    </script>

    <script src="{{ asset('themes/cozastore/assets/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/slick/slick.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/js/slick-custom.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/parallax100/parallax100.js') }}"></script>

    <script>
        if (typeof $ !== 'undefined' && $('.parallax100').length) {
            $('.parallax100').parallax100();
        }
    </script>

    <script src="{{ asset('themes/cozastore/assets/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
    <script>
        if (typeof $ !== 'undefined') {
            $('.gallery-lb').each(function() {
                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: { enabled: true },
                    mainClass: 'mfp-fade'
                });
            });
        }
    </script>

    <script src="{{ asset('themes/cozastore/assets/vendor/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('themes/cozastore/assets/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

    <script>
        if (typeof $ !== 'undefined') {
            $('.js-pscroll').each(function(){
                $(this).css('position','relative');
                $(this).css('overflow','hidden');
                var ps = new PerfectScrollbar(this, {
                    wheelSpeed: 1,
                    scrollingThreshold: 1000,
                    wheelPropagation: false,
                });
                $(window).on('resize', function(){
                    ps.update();
                })
            });
        }
    </script>

    <script src="{{ asset('themes/cozastore/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>

