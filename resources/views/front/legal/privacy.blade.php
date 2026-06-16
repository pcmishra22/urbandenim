@extends('layouts.eshopper')
@section('title', 'Privacy Policy — Jeanzo')
@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Privacy Policy', 'breadcrumb' => 'Privacy Policy'])
</div></div>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-9">
    <div class="j-section" style="padding:36px 44px;">
        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom:1.5px solid var(--j-border);">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:14px;">
                <i class="fa fa-shield-alt" style="color:var(--j-primary);font-size:1.2rem;"></i>
            </div>
            <div><h4 style="margin:0;font-weight:800;color:var(--j-dark);">Privacy Policy</h4>
            <small class="text-muted">Last updated: {{ date('d F Y') }}</small></div>
        </div>

        <p>At <strong>Jeanzo</strong>, your privacy matters. This policy explains what data we collect and how we use it when you use <strong>jeanzo.in</strong>.</p>

        <h6 class="font-weight-bold mt-4">1. Information We Collect</h6>
        <ul>
            <li><strong>Personal:</strong> Name, email, phone, and delivery address when you register or place an order.</li>
            <li><strong>Payment:</strong> Processed securely by PayU — we never store card numbers or banking credentials.</li>
            <li><strong>Usage:</strong> Pages visited, browser type, IP address, device info via cookies and analytics.</li>
            <li><strong>Communications:</strong> Messages sent via contact forms or email.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">2. How We Use Your Data</h6>
        <ul>
            <li>To process and fulfil your orders.</li>
            <li>To communicate about orders, returns, and support.</li>
            <li>To send promotional emails (only if opted in; unsubscribe any time).</li>
            <li>To improve our website and prevent fraud.</li>
            <li>To comply with legal obligations.</li>
        </ul>

        <h6 class="font-weight-bold mt-4">3. Sharing Your Data</h6>
        <p>We do not sell your data. We share it only with: logistics partners (for delivery), PayU (for payment processing), and law enforcement if required by law.</p>

        <h6 class="font-weight-bold mt-4">4. Cookies</h6>
        <p>We use cookies to maintain sessions, remember your cart, and analyse traffic. You can disable cookies in browser settings; some features may not function correctly.</p>

        <h6 class="font-weight-bold mt-4">5. Your Rights</h6>
        <p>You may request access to, correction of, or deletion of your personal data by emailing <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a>.</p>

        <h6 class="font-weight-bold mt-4">6. Security</h6>
        <p>We use industry-standard SSL encryption. No method of internet transmission is 100% secure; we cannot guarantee absolute security.</p>

        <h6 class="font-weight-bold mt-4">7. Contact</h6>
        <p><strong>Email:</strong> <a href="mailto:support@jeanzo.in" class="text-primary">support@jeanzo.in</a></p>
    </div>
    <div class="mt-4 d-flex flex-wrap" style="gap:10px;">
        <a href="{{ route('legal.terms') }}" class="btn btn-outline-secondary btn-sm">Terms &amp; Conditions</a>
        <span class="btn btn-primary btn-sm" style="cursor:default;">Privacy Policy</span>
        <a href="{{ route('legal.refund') }}" class="btn btn-outline-secondary btn-sm">Return &amp; Refund</a>
        <a href="{{ route('legal.shipping') }}" class="btn btn-outline-secondary btn-sm">Shipping Policy</a>
        <a href="{{ route('legal.cancellation') }}" class="btn btn-outline-secondary btn-sm">Cancellation</a>
    </div>
    </div></div>
</div>
@endsection
