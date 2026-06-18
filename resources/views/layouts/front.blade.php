<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'Urban Denim - CozaStore')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO defaults (page can override using @section('meta_description') etc.) --}}
    @php
        $metaDescription = trim(strip_tags(@yield('meta_description', '')));
        $canonical        = trim(@yield('canonical', url()->current()));
        $ogType           = trim(@yield('og_type', 'website'));
        $ogTitle          = trim(@yield('og_title', @yield('title', config('app.name', 'Urban Denim'))));
        $ogDescription    = trim(strip_tags(@yield('og_description', $metaDescription)));
        $ogImage          = trim(@yield('og_image', asset('eshopper/img/og-default.jpg')));
        $twitterImage     = $ogImage;
        $currentUrl       = url()->current();
    @endphp

    @if($metaDescription !== '')
        <meta name="description" content="{{ $metaDescription }}">
    @else
        <meta name="description" content="{{ config('app.name', 'Urban Denim') }} - Premium denim for men & women.">
    @endif

    <link rel="canonical" href="{{ $canonical }}">

    {{-- OpenGraph --}}
    <meta property="og:site_name" content="{{ config('app.name', 'Urban Denim') }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $twitterImage }}">

    <!-- CozaStore Theme Assets -->
    <link rel="icon" type="image/png" href="{{ asset('frontend/images/icons/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/MagnificPopup/magnific-popup.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">

