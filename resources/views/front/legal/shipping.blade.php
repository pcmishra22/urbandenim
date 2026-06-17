@extends('layouts.eshopper')
@section('title', 'Shipping Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Shipping Policy', 'breadcrumb' => 'Shipping Policy', 'showCategories' => false])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                {-- Header --}
                <div class="card-header d-flex align-items-center py-4 px-4 border-0" style="background:#fff;border-bottom:2px solid #f0eded;">
                    <div style="width:46px;height:46px;border-radius:50%;background:var(--j-primary-lt,#fdf0ef);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa fa-truck" style="color:var(--j-primary,#D19C97);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="color:#1a1a1a;">Shipping Policy</h4>
                        <small class="text-muted">Last updated: {{ date('d F Y') }}</small>
                    </div>
                </div>
                {-- Body --}
                <div class="card-body px-4 py-4" style="font-size:.93rem;line-height:1.75;color:#333;">

        <h6 class="font-weight-bold mt-2">1. Coverage</h6>
        <p>We deliver across <strong>India</strong> through reputed courier partners. International shipping is not available currently.</p>

        <h6 class="font-weight-bold mt-4">2. Shipping Charges</h6>
        <table class="table table-bordered mb-3" style="font-size:.9rem;">
            <thead style="background:#f8f8f8;"><tr><th>Order Value</th><th>Shipping Charge</th></tr></thead>
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

        <h6 class="font-weight-bold mt-4">5. Order Tracking</h6>
        <p>A shipping confirmation email with a tracking number is sent once your order is dispatched. You can also track under <a href="{{ route('profile.orders') }}" class="text-primary">My Orders</a>.</p>

        <h6 class="font-weight-bold mt-4">6. Failed Delivery</h6>
        <p>Couriers attempt delivery up to 3 times. If undeliverable, the package returns to us and re-shipping charges will apply.</p>

        <h6 class="font-weight-bold mt-4">7. Damaged / Lost Shipments</h6>
        <p>Contact us within <strong>48 hours</strong> of the expected delivery date at <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a>.</p>

        <h6 class="font-weight-bold mt-4">8. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
                </div>
            </div>

            {-- Navigation pills --}
            <div class="mt-4 d-flex flex-wrap" style="gap:8px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Shipping Policy</span>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
            </div>

        </div>
    </div>
</div>

@endsection
