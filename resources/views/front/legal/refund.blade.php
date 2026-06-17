@extends('layouts.eshopper')
@section('title', 'Return &amp; Refund Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Return & Refund Policy', 'breadcrumb' => 'Return & Refund Policy', 'showCategories' => false])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                {-- Header --}
                <div class="card-header d-flex align-items-center py-4 px-4 border-0" style="background:#fff;border-bottom:2px solid #f0eded;">
                    <div style="width:46px;height:46px;border-radius:50%;background:var(--j-primary-lt,#fdf0ef);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa fa-undo" style="color:var(--j-primary,#D19C97);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="color:#1a1a1a;">Return &amp; Refund Policy</h4>
                        <small class="text-muted">Last updated: {{ date('d F Y') }}</small>
                    </div>
                </div>
                {-- Body --}
                <div class="card-body px-4 py-4" style="font-size:.93rem;line-height:1.75;color:#333;">

        <h6 class="font-weight-bold mt-2">1. Return Window</h6>
        <p>You may initiate a return within <strong>7 days</strong> of the delivery date. Returns will not be accepted after this period.</p>

        <h6 class="font-weight-bold mt-4">2. Eligibility</h6>
        <p>Items must be: unused &amp; unwashed, in original packaging with all tags attached, and accompanied by the original invoice.</p>
        <p><strong>Non-returnable:</strong> Worn/washed items, items without tags, items marked "Final Sale", innerwear and accessories.</p>

        <h6 class="font-weight-bold mt-4">3. How to Return</h6>
        <ol>
            <li>Email <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> with your <strong>Order ID</strong> and reason within 7 days.</li>
            <li>We confirm eligibility within 1–2 business days and send return instructions.</li>
            <li>Pack the item securely and ship it to the address provided. Return shipping is your responsibility unless the item is defective.</li>
            <li>Refund is processed after we receive and inspect the item.</li>
        </ol>

        <h6 class="font-weight-bold mt-4">4. Defective or Wrong Items</h6>
        <p>Email us within <strong>48 hours</strong> of delivery with photos. We will arrange free pickup and issue a full refund or replacement.</p>

        <h6 class="font-weight-bold mt-4">5. Refund Timeline</h6>
        <ul>
            <li><strong>Online (UPI / Card):</strong> Back to original payment method within 5–7 business days.</li>
            <li><strong>COD orders:</strong> Bank transfer (NEFT) within 5–7 business days. Share bank details when requesting the return.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">6. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a> &nbsp;|&nbsp; Mon–Sat, 10am–6pm IST</p>
                </div>
            </div>

            {-- Navigation pills --}
            <div class="mt-4 d-flex flex-wrap" style="gap:8px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <a href="{{ route('legal.privacy') }}" class="btn btn-outline-secondary btn-sm">Privacy Policy</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Return &amp; Refund</span>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
            </div>

        </div>
    </div>
</div>

@endsection
