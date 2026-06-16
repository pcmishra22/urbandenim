@extends('layouts.eshopper')
@section('title', 'Return & Refund Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Return & Refund Policy', 'breadcrumb' => 'Return & Refund Policy'])
</div></div>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-9">

    {{-- Quick badges --}}
    <div class="row text-center mb-4">
        @foreach([['fa-undo','7-Day Returns','From delivery'],['fa-rupee-sign','Full Refund','For eligible items'],['fa-clock','5–7 Business Days','Refund timeline'],['fa-headset','Easy Process','Email to start']] as $b)
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
                <i class="fa fa-undo" style="color:var(--j-primary);font-size:1.2rem;"></i>
            </div>
            <div><h4 style="margin:0;font-weight:800;color:var(--j-dark);">Return &amp; Refund Policy</h4>
            <small class="text-muted">Last updated: {{ date('d F Y') }}</small></div>
        </div>

        <h6 class="font-weight-bold mt-3">1. Return Window</h6>
        <p>You may initiate a return within <strong>7 days</strong> of the delivery date. Returns will not be accepted after this period.</p>

        <h6 class="font-weight-bold mt-4">2. Eligibility</h6>
        <p>To be eligible, items must be: unused &amp; unwashed, in original packaging with tags attached, accompanied by the original invoice.</p>
        <p><strong>Non-returnable:</strong> Worn or washed items, items without tags, items marked "Final Sale", innerwear and accessories (hygiene reasons).</p>

        <h6 class="font-weight-bold mt-4">3. How to Return</h6>
        <ol>
            <li>Email <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> with your <strong>Order ID</strong> and reason within 7 days.</li>
            <li>We confirm eligibility within 1–2 business days and send return instructions.</li>
            <li>Pack securely and ship to the address provided. Return shipping is the customer's responsibility unless the item is defective or incorrect.</li>
            <li>Refund is processed after we receive and inspect the item.</li>
        </ol>

        <h6 class="font-weight-bold mt-4">4. Defective or Wrong Items</h6>
        <p>Email us within <strong>48 hours</strong> of delivery with photos. We'll arrange free pickup and issue a full refund or replacement.</p>

        <h6 class="font-weight-bold mt-4">5. Refund Timeline</h6>
        <ul>
            <li><strong>Online (UPI/Card):</strong> Back to original payment method within 5–7 business days.</li>
            <li><strong>COD orders:</strong> Bank transfer (NEFT) within 5–7 business days — please share bank details when requesting the return.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">6. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
    </div>
    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Return &amp; Refund</span>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
    </div>
    </div></div>
</div>
@endsection
