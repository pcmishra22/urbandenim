@extends('layouts.cozastore')

@section('title', 'Urban Denim - Homepage')

@section('content')
    <!-- Slider / Hero Banner -->
    <section class="section-slide">
        <div class="wrap-slick1">
            <div class="slick1">
                <div class="item-slick1" style="background-image: url({{ asset('themes/cozastore/assets/images/slide-01.jpg') }});">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                                <span class="ltext-101 cl2 respon2">Women Collection 2024</span>
                            </div>
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                                <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">NEW ARRIVALS</h2>
                            </div>
                            <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                                <a href="{{ url('/shop') }}" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner / Featured Categories -->
    <div class="sec-banner bg0 p-t-80 p-b-50">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xl-4 p-b-30 m-lr-auto">
                    <!-- Block1 -->
                    <div class="block1 wrap-pic-w">
                        <img src="{{ asset('themes/cozastore/assets/images/banner-01.jpg') }}" alt="IMG-BANNER">
                        <a href="{{ url('/category/women') }}" class="block1-txt ab-t-l s-full flex-col-l-m p-l-38 p-t-11 p-b-30 trans-05">
                            <div class="block1-txt-child1 flex-col-l">
                                <span class="block1-name ltext-102 trans-04 p-b-8">Women</span>
                                <span class="block1-info stext-102 trans-04">Spring 2024</span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Add more blocks for Men and Kids here -->
            </div>
        </div>
    </div>

    <!-- Product Section placeholder -->
@endsection