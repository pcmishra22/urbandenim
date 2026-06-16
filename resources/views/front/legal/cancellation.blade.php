@extends('layouts.eshopper')
@section('title', 'Cancellation Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Cancellation Policy', 'breadcrumb' => 'Cancellation Policy'])
</div></div>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-9">
    <div class="j-section" style="padding:36px 44px;">
        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom:1.5px solid var(--j-border);">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                <i class="fa fa-times-circle" style="color:var(--j-primary);font-size:1.2rem;"></i>
            </div>
            <div><h4 style="margin:0;font-weight:800;color:var(--j-dark);">Cancellation Policy</h4>
            <small class="text-muted">Last updated: {{ date('d F Y') }}</small></div>
        </div>

        <h6 class="font-weight-bold mt-3">1. Before Dispatch</h6>
        <p>You may cancel your order <strong>before it is dispatched</strong> by contacting us at <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> or calling +91-XXXXXXXXXX. If the order is cancelled before dispatch:</p>
        <ul>
            <li><strong>Prepaid orders (UPI/Card):</strong> Full refund to the original payment method within 5–7 business days.</li>
            <li><strong>COD orders:</strong> No charge — the order is simply cancelled.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">2. After Dispatch</h6>
        <p>Once an order has been dispatched (you've received a shipping confirmation email), it <strong>cannot be cancelled</strong>. You may initiate a return after delivery as per our <a href="{{ route('legal.refund') }}" class="text-primary">Return &amp; Refund Policy</a>.</p>

        <h6 class="font-weight-bold mt-4">3. Cancellation by Jeanzo</h6>
        <p>We reserve the right to cancel orders in the following situations:</p>
        <ul>
            <li>Item is out of stock or discontinued after order placement.</li>
            <li>Pricing errors or technical issues at the time of ordering.</li>
            <li>Suspected fraudulent or invalid orders.</li>
            <li>Inability to deliver to the provided address.</li>
        </ul>
        <p>In all such cases, a <strong>full refund</strong> is issued within 5–7 business days.</p>

        <h6 class="font-weight-bold mt-4">4. How to Cancel</h6>
        <ol>
            <li>Email us at <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> with your <strong>Order ID</strong> and reason.</li>
            <li>Our team will confirm cancellation status within <strong>2–4 hours</strong> on business days.</li>
            <li>Refund (if applicable) will be processed within 5–7 business days.</li>
        </ol>

        <h6 class="font-weight-bold mt-4">5. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
    </div>
    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Cancellation</span>
    </div>
    </div></div>
</div>
@endsection
