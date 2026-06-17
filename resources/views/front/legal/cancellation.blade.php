@extends('layouts.eshopper')
@section('title', 'Cancellation Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Cancellation Policy', 'breadcrumb' => 'Cancellation Policy', 'showCategories' => false])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                {-- Header --}
                <div class="card-header d-flex align-items-center py-4 px-4 border-0" style="background:#fff;border-bottom:2px solid #f0eded;">
                    <div style="width:46px;height:46px;border-radius:50%;background:var(--j-primary-lt,#fdf0ef);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa fa-times-circle" style="color:var(--j-primary,#D19C97);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="color:#1a1a1a;">Cancellation Policy</h4>
                        <small class="text-muted">Last updated: {{ date('d F Y') }}</small>
                    </div>
                </div>
                {-- Body --}
                <div class="card-body px-4 py-4" style="font-size:.93rem;line-height:1.75;color:#333;">

        <h6 class="font-weight-bold mt-2">1. Before Dispatch</h6>
        <p>You may cancel your order <strong>before it is dispatched</strong> by emailing <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> with your Order ID.</p>
        <ul>
            <li><strong>Prepaid (UPI / Card):</strong> Full refund to original payment method within 5–7 business days.</li>
            <li><strong>COD orders:</strong> Simply cancelled — no charge.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">2. After Dispatch</h6>
        <p>Once dispatched (you've received a shipping confirmation email), the order <strong>cannot be cancelled</strong>. You may initiate a return after delivery as per our <a href="{{ route('legal.refund') }}" class="text-primary">Return &amp; Refund Policy</a>.</p>

        <h6 class="font-weight-bold mt-4">3. Cancellation by Jeanzo</h6>
        <p>We may cancel orders if an item is out of stock, there is a pricing error, or the order appears fraudulent. A <strong>full refund</strong> is issued in all such cases within 5–7 business days.</p>

        <h6 class="font-weight-bold mt-4">4. How to Cancel</h6>
        <ol>
            <li>Email <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> with your <strong>Order ID</strong> and reason.</li>
            <li>Our team will confirm cancellation within <strong>2–4 hours</strong> on business days.</li>
            <li>Refund (if applicable) processed within 5–7 business days.</li>
        </ol>

        <h6 class="font-weight-bold mt-4">5. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
                </div>
            </div>

            {-- Navigation pills --}
            <div class="mt-4 d-flex flex-wrap" style="gap:8px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Cancellation</span>
            </div>

        </div>
    </div>
</div>

@endsection
