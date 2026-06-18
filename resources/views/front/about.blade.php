@extends('layouts.eshopper')
@section('title', 'About Us — Jeanzo | Premium Denim Brand India')
@section('meta_description', 'Learn about Jeanzo — India\'s premium denim brand for men and women. Quality jeans crafted for every fit and occasion. Fast delivery, easy returns.')
@section('canonical', route('about'))
@section('og_title', 'About Jeanzo — Premium Denim Brand India')
@section('og_description', 'India\'s premium denim brand for men and women. Quality jeans crafted for every fit and occasion.')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">About Us</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">About Us</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- About Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5 align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('eshopper/img/about.jpg') }}" class="img-fluid w-100" alt="About Jeanzo"
                     onerror="this.src='https://via.placeholder.com/600x400?text=About+Us'">
            </div>
            <div class="col-lg-6">
                <h2 class="font-weight-semi-bold mb-4">Welcome to Jeanzo</h2>
                <p class="mb-4">We are a passionate team of people who believe in delivering the best online shopping experience. Founded with a vision to make quality products accessible to everyone, Jeanzo has grown to become a trusted destination for fashion, electronics, and everyday essentials.</p>
                <p class="mb-4">Our platform brings together top brands and independent sellers, ensuring you always find what you're looking for — at the best price, with fast delivery and hassle-free returns.</p>
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="border text-center p-3">
                            <h4 class="text-primary font-weight-bold mb-1">10K+</h4>
                            <p class="m-0">Happy Customers</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border text-center p-3">
                            <h4 class="text-primary font-weight-bold mb-1">500+</h4>
                            <p class="m-0">Products</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border text-center p-3">
                            <h4 class="text-primary font-weight-bold mb-1">50+</h4>
                            <p class="m-0">Brands</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border text-center p-3">
                            <h4 class="text-primary font-weight-bold mb-1">24/7</h4>
                            <p class="m-0">Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Us -->
        <div class="row px-xl-5 pb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="section-title px-5"><span class="px-2">Why Choose Jeanzo?</span></h2>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="border text-center p-4">
                    <i class="fa fa-check fa-2x text-primary mb-3"></i>
                    <h5 class="font-weight-semi-bold mb-2">Quality Products</h5>
                    <p class="m-0 text-muted">Every product is carefully vetted to meet our quality standards before being listed.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="border text-center p-4">
                    <i class="fa fa-shipping-fast fa-2x text-primary mb-3"></i>
                    <h5 class="font-weight-semi-bold mb-2">Fast Shipping</h5>
                    <p class="m-0 text-muted">We partner with leading couriers to ensure your order reaches you quickly.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="border text-center p-4">
                    <i class="fas fa-exchange-alt fa-2x text-primary mb-3"></i>
                    <h5 class="font-weight-semi-bold mb-2">Easy Returns</h5>
                    <p class="m-0 text-muted">Not satisfied? Return within 14 days, no questions asked.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="border text-center p-4">
                    <i class="fa fa-headset fa-2x text-primary mb-3"></i>
                    <h5 class="font-weight-semi-bold mb-2">24/7 Support</h5>
                    <p class="m-0 text-muted">Our customer care team is always here to help you with any questions.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->
@endsection
