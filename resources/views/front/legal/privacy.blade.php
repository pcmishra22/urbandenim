@extends('layouts.eshopper')
@section('title', 'Privacy Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Privacy Policy', 'breadcrumb' => 'Privacy Policy', 'showCategories' => false])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
                {{-- Header --}}
                <div class="card-header d-flex align-items-center py-4 px-4 border-0" style="background:#fff;border-bottom:2px solid #f0eded;">
                    <div style="width:46px;height:46px;border-radius:50%;background:var(--j-primary-lt,#fdf0ef);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                        <i class="fa fa-shield-alt" style="color:var(--j-primary,#D19C97);font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="color:#1a1a1a;">Privacy Policy</h4>
                        <small class="text-muted">Last updated: {{ date('d F Y') }}</small>
                    </div>
                </div>
                {{-- Body --}}
                <div class="card-body px-4 py-4" style="font-size:.93rem;line-height:1.75;color:#333;">

        <p>At <strong>Jeanzo</strong>, your privacy matters. This policy explains what data we collect and how we use it when you visit <strong>jeanzo.in</strong>.</p>

        <h6 class="font-weight-bold mt-4">1. Information We Collect</h6>
        <ul>
            <li><strong>Personal:</strong> Name, email, phone, and delivery address when you register or place an order.</li>
            <li><strong>Payment:</strong> Processed securely by PayU — we never store card numbers or banking credentials.</li>
            <li><strong>Usage:</strong> Pages visited, browser type, IP address via cookies and analytics tools.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">2. How We Use Your Data</h6>
        <ul>
            <li>To process and fulfil your orders and send confirmations.</li>
            <li>To communicate about orders, returns, and support queries.</li>
            <li>To send promotional emails (only if opted in; unsubscribe any time).</li>
            <li>To improve our website and prevent fraud.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">3. Sharing Your Data</h6>
        <p>We do not sell your data. We share it only with logistics partners (for delivery), PayU (for payments), and law enforcement if required by law.</p>

        <h6 class="font-weight-bold mt-4">4. Cookies</h6>
        <p>We use cookies to maintain sessions and analyse traffic. You can disable cookies in browser settings, though some features may not work.</p>

        <h6 class="font-weight-bold mt-4">5. Your Rights</h6>
        <p>You may request access to, correction of, or deletion of your data by emailing <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a>.</p>

        <h6 class="font-weight-bold mt-4">6. Security</h6>
        <p>We use SSL encryption to protect your data. No internet transmission is 100% secure.</p>

        <h6 class="font-weight-bold mt-4">7. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a></p>
                </div>
            </div>

            {{-- Navigation pills --}}
            <div class="mt-4 d-flex flex-wrap" style="gap:8px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Privacy Policy</span>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
            </div>

        </div>
    </div>
</div>

@endsection
