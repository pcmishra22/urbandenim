@extends('layouts.eshopper')
@section('title', 'Shipping Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Shipping Policy', 'breadcrumb' => 'Shipping Policy'])
</div></div>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-9">

    <div class="row text-center mb-4">
        @foreach([['fa-truck','Pan-India Delivery','All states'],['fa-gift','Free Shipping','Orders above ₹500'],['fa-calendar-alt','3–7 Business Days','Standard delivery'],['fa-search-location','Order Tracking','Via email updates']] as $b)
        <div class="col-6 col-md-3 mb-3">
            <div class="j-section h-100 py-3 text-center" style="margin-bottom:0;">
                <i class="fa {{ $b[0] }} fa-lg mb-2 d-block" style="color:var(--j-primary);"></i>
                <div class="font-weight-bold" style="font-size:.85rem;">{{ $b[1] }}</div>
                <small class="text-muted">{{ $b[2] }}</small>
            </div>
        </div>
        @endforeach
    </div>

    <div class="j-section" style="padding:36px 44px;">
        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom:1.5px solid var(--j-border);">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                <i class="fa fa-truck" style="color:var(--j-primary);font-size:1.2rem;"></i>
            </div>
            <div><h4 style="margin:0;font-weight:800;color:var(--j-dark);">Shipping Policy</h4>
            <small class="text-muted">Last updated: {{ date('d F Y') }}</small></div>
        </div>

        <h6 class="font-weight-bold mt-3">1. Coverage</h6>
        <p>We deliver across <strong>India</strong> through reputed courier partners. International shipping is not available currently.</p>

        <h6 class="font-weight-bold mt-4">2. Shipping Charges</h6>
        <table class="table table-bordered mb-3" style="font-size:.9rem;">
            <thead style="background:var(--j-primary-lt);"><tr><th>Order Value</th><th>Shipping Charge</th></tr></thead>
            <tbody>
                <tr><td>Below ₹500</td><td>₹50</td></tr>
                <tr><td>₹500 and above</td><td><strong class="text-success">FREE</strong></td></tr>
            </tbody>
        </table>

        <h6 class="font-weight-bold mt-4">3. Processing Time</h6>
        <p>Orders are processed and dispatched within <strong>1–2 business days</strong> (Mon–Sat, excluding holidays) after payment confirmation.</p>

        <h6 class="font-weight-bold mt-4">4. Delivery Timeline</h6>
        <ul>
            <li><strong>Metro cities</strong> (Delhi, Mumbai, Bengaluru, etc.): 3–5 business days</li>
            <li><strong>Other cities &amp; towns:</strong> 5–7 business days</li>
            <li><strong>Remote areas:</strong> 7–10 business days</li>
        </ul>

        <h6 class="font-weight-bold mt-4">5. Tracking</h6>
        <p>A shipping confirmation email with a tracking number is sent once your order is dispatched. Track under <a href="{{ route('profile.orders') }}" class="text-primary">My Orders</a> after login.</p>

        <h6 class="font-weight-bold mt-4">6. Failed Delivery</h6>
        <p>Couriers make up to 3 delivery attempts. If undeliverable (wrong address, unavailable recipient), the package is returned to us and re-shipping charges apply.</p>

        <h6 class="font-weight-bold mt-4">7. Damaged / Lost Shipments</h6>
        <p>Contact us within <strong>48 hours</strong> of the expected delivery date at <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a>. We will raise a dispute and arrange a replacement or refund.</p>

        <h6 class="font-weight-bold mt-4">8. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
    </div>
    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Shipping Policy</span>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
    </div>
    </div></div>
</div>
@endsection
