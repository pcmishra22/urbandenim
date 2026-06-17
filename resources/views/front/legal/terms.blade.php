@extends('layouts.eshopper')
@section('title', 'Terms &amp; Conditions — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Terms & Conditions', 'breadcrumb' => 'Terms & Conditions', 'showCategories' => false])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
              {{-- Header --}}
                <div class="card-header d-flex align-items-center py-4 px-4 border-0" style="background:#fff;border-bottom:2px solid #f0eded;">
                    <div style="width:46px;height:46px;border-radius:50%;background:var(--j-primary-lt,#fdf0ef);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa fa-file-contract" style="color:var(--j-primary,#D19C97);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="color:#1a1a1a;">Terms &amp; Conditions</h4>
                        <small class="text-muted">Last updated: {{ date('d F Y') }}</small>
                    </div>
                </div>
                {{-- Body --}}
                <div class="card-body px-4 py-4" style="font-size:.93rem;line-height:1.75;color:#333;">

        <p>Welcome to <strong>Jeanzo</strong>. By accessing our website <strong>jeanzo.in</strong> or placing an order, you agree to these Terms &amp; Conditions.</p>

        <h6 class="font-weight-bold mt-4">1. Eligibility</h6>
        <p>You must be at least 18 years of age to use this website.</p>

        <h6 class="font-weight-bold mt-4">2. Products &amp; Pricing</h6>
        <p>All prices are in Indian Rupees (₹) and inclusive of applicable taxes. We reserve the right to correct pricing errors and cancel orders placed at an incorrect price, issuing a full refund in such cases.</p>

        <h6 class="font-weight-bold mt-4">3. Orders &amp; Acceptance</h6>
        <p>An order is confirmed only after you receive an order confirmation email. We may cancel orders due to stock unavailability, suspected fraud, or pricing errors, with a full refund.</p>

        <h6 class="font-weight-bold mt-4">4. Payment</h6>
        <p>We accept UPI, Credit/Debit Cards (via <strong>PayU</strong>), and Cash on Delivery. Card details are processed securely by PayU and never stored on our servers.</p>

        <h6 class="font-weight-bold mt-4">5. Delivery</h6>
        <p>We ship across India. Estimated delivery is 3–7 business days. See our <a href="{{ route('legal.shipping') }}" class="text-primary">Shipping Policy</a> for details.</p>

        <h6 class="font-weight-bold mt-4">6. Returns &amp; Refunds</h6>
        <p>We offer a 7-day return window from delivery for eligible items. See our <a href="{{ route('legal.refund') }}" class="text-primary">Return &amp; Refund Policy</a>.</p>

        <h6 class="font-weight-bold mt-4">7. Intellectual Property</h6>
        <p>All content on this website is the property of Jeanzo and protected by copyright laws. Reproduction without prior written consent is prohibited.</p>

        <h6 class="font-weight-bold mt-4">8. Limitation of Liability</h6>
        <p>Jeanzo shall not be liable for indirect or consequential damages. Our total liability shall not exceed the amount paid for the specific order.</p>

        <h6 class="font-weight-bold mt-4">9. Governing Law</h6>
        <p>These Terms are governed by the laws of India. Disputes are subject to exclusive jurisdiction of courts in <strong>Chandigarh, India</strong>.</p>

        <h6 class="font-weight-bold mt-4">10. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; <strong>Address:</strong> Jeanzo, Chandigarh, India</p>
                </div>
            </div>

           {{-- Navigation pills --}}
            <div class="mt-4 d-flex flex-wrap" style="gap:8px;">
        <span class="btn btn-primary btn-sm" style="cursor:default;">Terms &amp; Conditions</span>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
            </div>

        </div>
    </div>
</div>

@endsection
