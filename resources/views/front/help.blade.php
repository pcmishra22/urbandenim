@extends('layouts.eshopper')
@section('title', 'Help Center — Jeanzo India')
@section('meta_description', 'Find answers to common questions about orders, sizing, delivery, returns and payment at Jeanzo. Our help centre is here for you.')
@section('canonical', route('help'))
@section('og_title', 'Help Center — Jeanzo')
@section('og_description', 'Find answers to common questions about orders, sizing, delivery, returns and payment at Jeanzo.')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Help Center', 'breadcrumb' => 'Help', 'showCategories' => false])

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:28px;">

    <div class="text-center mb-5 pt-2">
        <h2 class="font-weight-bold" style="color:#2d2d2d;">How can we help you?</h2>
        <div class="mx-auto" style="width:50px;height:3px;background:var(--j-primary);border-radius:2px;margin-top:8px;"></div>
    </div>

    <div class="row px-xl-5">
        {{-- Help Categories --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">Shipping & Delivery</h5>
                <p class="text-muted mb-3">Track your order, check delivery timelines and shipping costs.</p>
                <ul class="list-unstyled text-left small">
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>FREE shipping on all orders — no minimum required</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Standard delivery: 3–7 business days</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Express delivery available at checkout</li>
                    <li><i class="fa fa-check text-success mr-2"></i>Track via order details page</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-undo fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">Returns & Refunds</h5>
                <p class="text-muted mb-3">Easy 7-day return policy. Get full refund for eligible items.</p>
                <ul class="list-unstyled text-left small">
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>7-day hassle-free return window</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Refund within 5–7 business days</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Items must be unused with tags</li>
                    <li><i class="fa fa-check text-success mr-2"></i>Contact support to initiate return</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-credit-card fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">Payments</h5>
                <p class="text-muted mb-3">Secure payments via multiple methods. All transactions encrypted.</p>
                <ul class="list-unstyled text-left small">
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Cash on Delivery</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>UPI / Net Banking</li>
                    <li class="mb-1"><i class="fa fa-check text-success mr-2"></i>Credit & Debit Cards</li>
                    <li><i class="fa fa-check text-success mr-2"></i>SSL encrypted transactions</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-user-circle fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">My Account</h5>
                <p class="text-muted mb-3">Manage your profile, orders, addresses and wishlist.</p>
                <a href="{{ route('profile.dashboard') }}" class="btn btn-outline-primary btn-sm mt-2">Go to My Account</a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-question-circle fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">FAQs</h5>
                <p class="text-muted mb-3">Browse our frequently asked questions for quick answers.</p>
                <a href="{{ route('faq') }}" class="btn btn-outline-primary btn-sm mt-2">View FAQs</a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="border rounded p-4 text-center h-100">
                <i class="fa fa-headset fa-3x text-primary mb-3"></i>
                <h5 class="font-weight-bold">Contact Support</h5>
                <p class="text-muted mb-3">Can't find what you need? Get in touch with our team.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary btn-sm mt-2">Contact Us</a>
            </div>
        </div>
    </div>
</div>
@endsection
