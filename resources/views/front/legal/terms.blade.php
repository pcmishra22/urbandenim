@extends('layouts.eshopper')
@section('title', 'Terms & Conditions — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Terms & Conditions', 'breadcrumb' => 'Terms & Conditions'])
</div></div>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-9">
    <div class="j-section" style="padding:36px 44px;">
        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom:1.5px solid var(--j-border);">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                <i class="fa fa-file-contract" style="color:var(--j-primary);font-size:1.2rem;"></i>
            </div>
            <div><h4 style="margin:0;font-weight:800;color:var(--j-dark);">Terms &amp; Conditions</h4>
            <small class="text-muted">Last updated: {{ date('d F Y') }}</small></div>
        </div>

        <p>Welcome to <strong>Jeanzo</strong>. By accessing our website <strong>jeanzo.in</strong> or placing an order, you agree to be bound by these Terms &amp; Conditions.</p>

        <h6 class="font-weight-bold mt-4">1. Eligibility</h6>
        <p>You must be at least 18 years of age to use this website. By using Jeanzo you confirm that you meet this requirement.</p>

        <h6 class="font-weight-bold mt-4">2. Products &amp; Pricing</h6>
        <p>All prices are in Indian Rupees (₹) and inclusive of applicable taxes unless stated otherwise. We reserve the right to correct pricing errors and cancel orders placed at an incorrect price, with a full refund issued in such cases.</p>

        <h6 class="font-weight-bold mt-4">3. Orders &amp; Acceptance</h6>
        <p>An order is confirmed only after you receive an order confirmation email. We may cancel orders due to stock unavailability, suspected fraud, or pricing errors and will issue a full refund.</p>

        <h6 class="font-weight-bold mt-4">4. Payment</h6>
        <p>We accept UPI, Credit/Debit Cards (via <strong>PayU</strong>), and Cash on Delivery. Card details are processed securely by PayU and never stored on our servers.</p>

        <h6 class="font-weight-bold mt-4">5. Delivery</h6>
        <p>We ship across India. Estimated delivery is 3–7 business days. Timelines are indicative and may vary due to courier delays or force majeure. See our <a href="{{ route('legal.shipping') }}" class="text-primary">Shipping Policy</a> for details.</p>

        <h6 class="font-weight-bold mt-4">6. Returns &amp; Refunds</h6>
        <p>We offer a 7-day return window from delivery for eligible items. See our <a href="{{ route('legal.refund') }}" class="text-primary">Return &amp; Refund Policy</a> for full details.</p>

        <h6 class="font-weight-bold mt-4">7. Intellectual Property</h6>
        <p>All content on this website is the property of Jeanzo and protected by applicable copyright laws. Reproduction without prior written consent is prohibited.</p>

        <h6 class="font-weight-bold mt-4">8. Limitation of Liability</h6>
        <p>To the fullest extent permitted by law, Jeanzo shall not be liable for indirect or consequential damages. Our total liability shall not exceed the amount paid for the specific order.</p>

        <h6 class="font-weight-bold mt-4">9. Governing Law</h6>
        <p>These Terms are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of courts in <strong>Chandigarh, India</strong>.</p>

        <h6 class="font-weight-bold mt-4">10. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; <strong>Address:</strong> Jeanzo, Chandigarh, India</p>
    </div>
    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
        <span class="btn btn-primary btn-sm" style="cursor:default;">Terms &amp; Conditions</span>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
    </div>
    </div></div>
</div>
@endsection
