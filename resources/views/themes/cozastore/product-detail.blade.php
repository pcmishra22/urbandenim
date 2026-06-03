@extends('themes.cozastore.layouts.theme')

@section('title', 'Product Detail')

@section('content')
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="/" class="stext-109 cl8 hov-cl1 trans-04">Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">Product Detail</span>
        </div>
    </div>

    <!-- HTML-first product-detail section (minimal placeholder for now) -->
    <div class="bg0 p-t-23 p-b-140">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5 p-b-30">
                    <img class="w-100" src="{{ asset('themes/cozastore/assets/images/product-detail-01.jpg') }}" alt="IMG-PRODUCT">
                </div>
                <div class="col-md-6 col-lg-5 p-b-30">
                    <h4 class="mtext-105 cl2 p-b-14">Lightweight Jacket</h4>
                    <span class="mtext-106 cl2">$58.79</span>
                    <p class="stext-102 cl3 p-t-23">Nulla eget sem vitae eros pharetra viverra. Nam vitae luctus ligula. Mauris consequat ornare feugiat.</p>
                    <div class="p-t-33">
                        <button class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04" type="button">Add to cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

