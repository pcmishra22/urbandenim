@extends('layouts.eshopper')
@section('title', 'Secure Checkout | Jeanzo India')
@section('meta_description', 'Complete your Jeanzo order securely. Enter your delivery address and payment details for fast, safe checkout with multiple payment options.')
@section('meta_robots', 'noindex, nofollow')
@section('canonical', route('checkout.index'))

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'checkout', 'breadcrumb' => 'Checkout', 'showCategories' => false])

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:20px;">
    <div class="row px-xl-5">

        {{-- ═══════════════════════════════════════════════
             LEFT: Identity Gateway + Address + Coupon
        ════════════════════════════════════════════════ --}}
        <div class="col-lg-8 mb-4 checkout-form-col">

            {{-- Flash alerts --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif
            @if(session('guest_message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('guest_message') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- ─────────────────────────────────────────────────────────
                 IDENTITY SECTION
                 • Logged-in user  → show "checking out as" badge only
                 • Guest           → show inline email gateway
            ──────────────────────────────────────────────────────────── --}}
            @auth
                {{-- Already authenticated: show who's checking out --}}
                <div class="alert alert-info mb-3" style="border-radius:12px;">
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <i class="fa fa-user-circle" style="font-size:1.05rem;"></i>
                        <div>
                            Checking out as <strong>{{ auth()->user()->email }}</strong>.
                            @if(auth()->user()->is_guest)
                                <span class="badge badge-secondary ml-1">Guest</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endauth
            @guest
                {{-- ── IDENTITY GATEWAY (inline, no separate page) ── --}}
                <div class="j-section mb-3" id="identity-gateway"
                     style="background:#fff;border:1px solid #ececec;border-radius:12px;">
                    <div class="j-section-title" style="color:#2b2b2b;">
                        <i class="fa fa-envelope mr-2" style="color:var(--j-primary);"></i>Enter your email to continue
                    </div>
                    <p class="text-muted small mb-3">
                        Ordered before? We'll pre-fill your saved address. No password required.
                    </p>

                    {{-- Email input --}}
                    <div class="input-group mb-2" id="email-input-row">
                        <input type="email" id="guest-email-input" class="form-control"
                               placeholder="you@example.com" autocomplete="email" autofocus>
                        <div class="input-group-append">
                            <button type="button" id="guest-email-btn"
                                    class="btn btn-primary px-4"
                                    style="border-radius:0 8px 8px 0;min-width:110px;">
                                <span id="email-btn-text">Continue</span>
                                <span id="email-btn-spinner"
                                      class="spinner-border spinner-border-sm d-none ml-1"
                                      role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Inline validation feedback --}}
                    <div id="guest-email-feedback" class="small mb-3"></div>

                    {{-- Divider --}}
                    <div class="d-flex align-items-center my-3" style="gap:10px;">
                        <hr style="flex:1;margin:0;border-color:#e0e0e0;">
                        <span class="text-muted small">or</span>
                        <hr style="flex:1;margin:0;border-color:#e0e0e0;">
                    </div>

                    {{-- Skip / Sign in --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                        <button type="button" id="skip-email-btn"
                                class="btn btn-outline-secondary btn-sm px-4"
                                style="border-radius:8px;min-width:180px;">
                            <span id="skip-btn-text">Continue without email</span>
                            <span id="skip-btn-spinner"
                                  class="spinner-border spinner-border-sm d-none ml-1"
                                  role="status" aria-hidden="true"></span>
                        </button>
                        <small class="text-muted">
                            Already have an account?
                            <a href="{{ route('customer.login') }}?redirect=checkout"
                               style="color:var(--j-primary);font-weight:600;">Sign in →</a>
                        </small>
                    </div>
                </div>
            @endguest

            {{-- ─────────────────────────────────────────────────────────
                 CHECKOUT FORM (hidden for guests until identity resolved)
            ──────────────────────────────────────────────────────────── --}}
            <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form"
                  @guest style="display:none;" @endguest>
            @csrf

            {{-- Hidden field: populated by JS after lookupEmail so the server
                 can re-establish the guest session if the cookie is lost --}}
            <input type="hidden" name="guest_email" id="guest-email-hidden">

            {{-- Saved Addresses (only for returning non-guest logged-in users) --}}
            @if($addresses->isNotEmpty())
            <div class="j-section mb-3">
                <div class="j-section-title">
                    <i class="fa fa-map-marker-alt mr-2" style="color:var(--j-primary);"></i>Deliver to a Saved Address
                </div>
                <div class="row">
                    @foreach($addresses as $address)
                    <div class="col-md-6 mb-2">
                        <div class="j-address-card saved-addr" style="cursor:pointer;"
                             data-full_name="{{ $address->full_name }}"
                             data-phone="{{ $address->phone }}"
                             data-street="{{ $address->street }}"
                             data-city="{{ $address->city }}"
                             data-state="{{ $address->state }}"
                             data-postal="{{ $address->postal_code }}"
                             data-country="{{ $address->country }}">
                            <div class="font-weight-700 mb-1">{{ $address->full_name }}</div>
                            <div class="text-muted small">{{ $address->street }}, {{ $address->city }}</div>
                            <div class="text-muted small">{{ $address->state }} {{ $address->postal_code }}</div>
                            <small style="color:var(--j-primary);" class="mt-1 d-block">Tap to use this address</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Shipping Details --}}
            <div class="j-section mb-3">
                <div class="j-section-title">
                    <i class="fa fa-home mr-2" style="color:var(--j-primary);"></i>Shipping Details
                </div>

                @if(isset($prefillAddress) && $prefillAddress)
                <div class="alert py-2 mb-3"
                     style="background:#f0faf4;border:1px solid #a5d6a7;border-radius:8px;font-size:.83rem;color:#1b5e20;">
                    <i class="fa fa-check-circle mr-1"></i>
                    <strong>Address pre-filled</strong> from your last order. You can edit any field below.
                </div>
                @endif

                <div class="row checkout-addr-row">
                        <input class="form-control @error('shipping_full_name') is-invalid @enderror"
                               type="text" name="shipping_full_name"
                               value="{{ old('shipping_full_name', $prefillAddress->full_name ?? auth()->user()->name ?? '') }}"
                               placeholder="Full Name">
                        @error('shipping_full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-600">Mobile No <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_phone') is-invalid @enderror"
                               type="text" name="shipping_phone"
                               value="{{ old('shipping_phone', $prefillAddress->phone ?? '') }}"
                               placeholder="+91 99999 99999">
                        @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 form-group">
                        <label class="font-weight-600">Street Address <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_street') is-invalid @enderror"
                               type="text" name="shipping_street"
                               value="{{ old('shipping_street', $prefillAddress->street ?? '') }}"
                               placeholder="House no. and Street name">
                        @error('shipping_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-600">City <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_city') is-invalid @enderror"
                               type="text" name="shipping_city"
                               value="{{ old('shipping_city', $prefillAddress->city ?? '') }}"
                               placeholder="City">
                        @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-600">State <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_state') is-invalid @enderror"
                               type="text" name="shipping_state"
                               value="{{ old('shipping_state', $prefillAddress->state ?? '') }}"
                               placeholder="State">
                        @error('shipping_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-600">PIN Code <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_postal_code') is-invalid @enderror"
                               type="text" name="shipping_postal_code"
                               value="{{ old('shipping_postal_code', $prefillAddress->postal_code ?? '') }}"
                               placeholder="PIN Code">
                        @error('shipping_postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-600">Country <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_country') is-invalid @enderror"
                               type="text" name="shipping_country"
                               value="{{ old('shipping_country', $prefillAddress->country ?? 'India') }}"
                               placeholder="Country">
                        @error('shipping_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 form-group mb-0">
                        <label class="font-weight-600">Order Notes
                            <small class="text-muted font-weight-normal">(optional)</small>
                        </label>
                        <textarea class="form-control" name="notes" rows="2"
                                  placeholder="Special delivery instructions">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Coupon --}}
            <div class="j-section mb-3">
                <div class="j-section-title">
                    <i class="fa fa-tag mr-2" style="color:var(--j-primary);"></i>Coupon Code
                    <small class="font-weight-normal text-muted">(optional)</small>
                </div>
                @if(session('coupon_code'))
                    <div class="alert py-2 mb-0 d-flex justify-content-between align-items-center"
                         style="background:var(--j-primary-lt);border:1px solid var(--j-primary);border-radius:8px;">
                        <span>
                            <i class="fa fa-check-circle mr-1" style="color:var(--j-primary);"></i>
                            <strong>{{ session('coupon_code') }}</strong> applied — saving ₹{{ number_format($discount,2) }}
                        </span>
                    </div>
                @else
                    <div class="d-flex gap-2" style="max-width:420px;">
                        <input class="form-control" type="text" id="coupon_input" name="coupon_code"
                               value="{{ old('coupon_code') }}" placeholder="Enter coupon code"
                               style="border-radius:8px;">
                        <button type="button" class="btn btn-outline-primary btn-sm px-4"
                                id="apply-coupon-btn" style="white-space:nowrap;">Apply</button>
                    </div>
                    <div id="coupon-msg" class="mt-2 small"></div>
                @endif
            </div>

            </form>
        </div>

        {{-- ═══════════════════════════════════════════════
             RIGHT: Summary + Payment
        ════════════════════════════════════════════════ --}}
        <div class="col-lg-4 mb-5 checkout-summary-col">

            {{-- Trust Badges --}}
            <div class="mb-3 p-3" style="background:#f8f9fa;border-radius:12px;border:1px solid #eee;">
                <div class="d-flex align-items-center mb-2" style="gap:8px;">
                    <i class="fa fa-lock" style="color:#27ae60;"></i>
                    <span style="font-size:.8rem;font-weight:700;color:#333;">Safe & Secure Checkout</span>
                </div>
                <div style="font-size:.75rem;color:#555;line-height:1.9;">
                    ✅ &nbsp;Cash on Delivery — No advance payment needed<br>
                    ✅ &nbsp;100% Secure Online Payment<br>
                    ✅ &nbsp;FREE Shipping on Every Order<br>
                    ✅ &nbsp;Easy 7-Day Returns<br>
                    ✅ &nbsp;Premium Quality Denim<br>
                    ✅ &nbsp;Made in India 🇮🇳
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="j-order-summary mb-3">
                <div class="summary-title">
                    <i class="fa fa-shopping-bag mr-2" style="color:var(--j-primary);"></i>Order Summary
                </div>
                @foreach($cartItems as $item)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2"
                     style="border-bottom:1px solid var(--j-border);">
                    <div style="font-size:.88rem;max-width:180px;">
                        {{ $item['product']->name }}
                        <span class="text-muted">×{{ $item['quantity'] }}</span>
                    </div>
                    <div class="font-weight-700" style="font-size:.88rem;">₹{{ number_format($item['subtotal'],2) }}</div>
                </div>
                @endforeach
                <div class="summary-row mt-3"><span>Subtotal</span><span>₹{{ number_format($subtotal,2) }}</span></div>
                @if($discount > 0)
                <div class="summary-row text-success">
                    <span>Discount @if($couponCode)<small>({{ $couponCode }})</small>@endif</span>
                    <span>- ₹{{ number_format($discount,2) }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>
                        @if($shipping==0)
                            <span class="text-success font-weight-700">FREE</span>
                        @else
                            ₹{{ number_format($shipping,2) }}
                        @endif
                    </span>
                </div>
                <div class="summary-total">
                    <span>Grand Total</span>
                    <span style="color:var(--j-primary);">₹{{ number_format($grandTotal,2) }}</span>
                </div>
            </div>

            <div class="j-order-summary mb-3">
                <div class="summary-title">
                    <i class="fa fa-credit-card mr-2" style="color:var(--j-primary);"></i>Payment Method
                </div>

                {{-- COD --}}
                <div class="mb-3" style="background:#e8f5e9;border:2px solid #27ae60;border-radius:12px;padding:14px 16px;">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="payment_method"
                               id="pay_cod" value="cod" form="checkout-form"
                               {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="pay_cod" style="cursor:pointer;">
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <i class="fa fa-money-bill-wave fa-lg" style="color:#27ae60;"></i>
                                <div>
                                    <strong style="font-size:.95rem;color:#1b5e20;">Cash on Delivery (COD)</strong>
                                    <span style="background:#27ae60;color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:8px;">RECOMMENDED</span>
                                    <div class="text-muted small mt-1">Pay in cash when your order arrives. No advance payment needed.</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- UPI --}}
                <div class="custom-control custom-radio mb-3"
                     style="padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;">
                    <input type="radio" class="custom-control-input" name="payment_method"
                           id="pay_upi" value="upi" form="checkout-form"
                           {{ old('payment_method') === 'upi' ? 'checked' : '' }}>
                    <label class="custom-control-label" for="pay_upi" style="cursor:pointer;">
                        <i class="fa fa-mobile-alt mr-2" style="color:var(--j-primary);"></i>
                        <strong>UPI / Net Banking</strong>
                        <small class="d-block text-muted" style="margin-left:22px;">Google Pay, PhonePe, BHIM, IMPS</small>
                    </label>
                </div>

                {{-- Card --}}
                <div class="custom-control custom-radio mb-3"
                     style="padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;">
                    <input type="radio" class="custom-control-input" name="payment_method"
                           id="pay_card" value="card" form="checkout-form"
                           {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                    <label class="custom-control-label" for="pay_card" style="cursor:pointer;">
                        <i class="fa fa-credit-card mr-2" style="color:var(--j-primary);"></i>
                        <strong>Credit / Debit Card</strong>
                        <small class="d-block text-muted" style="margin-left:22px;">Visa, Mastercard, Rupay — via PayU</small>
                    </label>
                </div>

                @error('payment_method')<small class="text-danger d-block mb-2">{{ $message }}</small>@enderror

                <div class="pt-2 mb-3" style="border-top:1px solid var(--j-border);">
                    <small class="text-muted">
                        <i class="fa fa-shield-alt text-success mr-1"></i>Online payments secured by <strong>PayU</strong>
                    </small>
                </div>

                <div id="pay-error" class="alert alert-danger small py-2 mb-2"
                     style="display:none;border-radius:8px;"></div>
                <div id="pay-spinner" class="text-center py-2 mb-2" style="display:none;">
                    <div class="spinner-border spinner-border-sm mr-2"
                         style="color:var(--j-primary);" role="status"></div>
                    <span id="pay-spinner-msg" class="small text-muted">Processing…</span>
                </div>

                {{-- Place Order button: disabled for guests until identity is confirmed --}}
                <button type="submit" form="checkout-form" id="place-order-btn"
                        class="btn btn-primary btn-block py-3 font-weight-bold"
                        style="border-radius:10px;font-size:1rem;"
                        @guest disabled @endguest>
                    <i id="btn-icon" class="fa fa-lock mr-2"></i><span id="btn-label">Place Order</span>
                </button>

                @guest
                <p class="text-center text-muted small mt-2 mb-0" id="place-order-hint">
                    Enter your email above to enable checkout
                </p>
                @endguest
            </div>

            {{-- Trust badges --}}
            <div class="j-section">
                <div class="d-flex align-items-center mb-3">
                    <div class="action-icon mr-3"><i class="fa fa-shield-alt" style="color:var(--j-primary);"></i></div>
                    <div>
                        <div class="font-weight-700" style="font-size:.9rem;">Secure Checkout</div>
                        <small class="text-muted">SSL encrypted &amp; safe</small>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="action-icon mr-3"><i class="fa fa-undo" style="color:var(--j-primary);"></i></div>
                    <div>
                        <div class="font-weight-700" style="font-size:.9rem;">Easy Returns</div>
                        <small class="text-muted">7-day return policy</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="action-icon mr-3"><i class="fa fa-headset" style="color:var(--j-primary);"></i></div>
                    <div>
                        <div class="font-weight-700" style="font-size:.9rem;">24/7 Support</div>
                        <small class="text-muted">We're here to help</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var CSRF          = '{{ csrf_token() }}';
    var STORE_PENDING = '{{ route("checkout.store-pending") }}';
    var CREATE_ORDER  = '{{ route("payment.create-order") }}';
    var COD_URL       = '{{ route("checkout.store") }}';
    var GRAND_TOTAL   = '{{ number_format($grandTotal, 2) }}';

    /* ═══════════════════════════════════════════════════════════════
       PAYMENT SECTION HELPERS
    ═══════════════════════════════════════════════════════════════ */

    function spin(msg) {
        $('#pay-error').hide();
        $('#pay-spinner').show().find('#pay-spinner-msg').text(msg || 'Processing…');
        $('#place-order-btn').prop('disabled', true);
    }
    function unspin() { $('#pay-spinner').hide(); $('#place-order-btn').prop('disabled', false); }
    function showErr(msg) {
        unspin();
        $('#pay-error').text(msg).show();
        $('html,body').animate({ scrollTop: $('#pay-error').offset().top - 120 }, 250);
    }

    function updateBtn() {
        var m = $('input[name=payment_method]:checked').val();
        if (m === 'cod') {
            $('#btn-label').text('Place Order — Pay on Delivery');
            $('#btn-icon').attr('class', 'fa fa-shopping-bag mr-2');
        } else if (m === 'upi' || m === 'card') {
            $('#btn-label').text('Proceed to Pay ₹' + GRAND_TOTAL);
            $('#btn-icon').attr('class', 'fa fa-lock mr-2');
        } else {
            $('#btn-label').text('Place Order');
            $('#btn-icon').attr('class', 'fa fa-lock mr-2');
        }
    }
    $('input[name=payment_method]').on('change', updateBtn);
    updateBtn();

    /* ── Saved address click ── */
    $(document).on('click', '.saved-addr', function () {
        $('.saved-addr').css({ 'border-color': 'var(--j-border)', 'background': '#fff' });
        $(this).css({ 'border-color': 'var(--j-primary)', 'background': 'var(--j-primary-lt)' });
        var d = $(this).data();
        $('[name=shipping_full_name]').val(d.full_name || '');
        $('[name=shipping_phone]').val(d.phone || '');
        $('[name=shipping_street]').val(d.street || '');
        $('[name=shipping_city]').val(d.city || '');
        $('[name=shipping_state]').val(d.state || '');
        $('[name=shipping_postal_code]').val(d.postal || '');
        $('[name=shipping_country]').val(d.country || '');
    });

    /* ── Field validation ── */
    function validateFields() {
        var checks = [
            ['shipping_full_name', 'Full Name'],
            ['shipping_phone', 'Mobile No'],
            ['shipping_street', 'Street Address'],
            ['shipping_city', 'City'],
            ['shipping_state', 'State'],
            ['shipping_postal_code', 'PIN Code'],
            ['shipping_country', 'Country']
        ];
        for (var i = 0; i < checks.length; i++) {
            if (!$('[name=' + checks[i][0] + ']').val().trim()) {
                showErr(checks[i][1] + ' is required.');
                $('[name=' + checks[i][0] + ']').focus();
                return false;
            }
        }
        return true;
    }

    /* ── Collect form fields ── */
    function formData(method) {
        return {
            shipping_full_name:   $('[name=shipping_full_name]').val(),
            shipping_phone:       $('[name=shipping_phone]').val(),
            shipping_street:      $('[name=shipping_street]').val(),
            shipping_city:        $('[name=shipping_city]').val(),
            shipping_state:       $('[name=shipping_state]').val(),
            shipping_postal_code: $('[name=shipping_postal_code]').val(),
            shipping_country:     $('[name=shipping_country]').val(),
            notes:                $('[name=notes]').val(),
            coupon_code:          $('[name=coupon_code]').val(),
            guest_email:          $('#guest-email-hidden').val(),
            payment_method:       method
        };
    }

    /* ── JSON POST helper ── */
    function postJson(url, data) {
        return fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(data)
        }).then(function (r) {
            return r.text().then(function (text) {
                var json = null;
                try { json = text ? JSON.parse(text) : {}; } catch (e) {}
                if (!r.ok) {
                    if (json && json.errors) {
                        var f = Object.values(json.errors)[0];
                        throw new Error(Array.isArray(f) ? f[0] : String(f));
                    }
                    var msg = (json && (json.error || json.message)) || ('Server error ' + r.status);
                    if (r.status === 401 || (typeof msg === 'string' && msg.toLowerCase().indexOf('unauthenticated') !== -1)) {
                        msg = 'Session expired. Please refresh the page and try again.';
                    }
                    throw new Error(msg);
                }
                return json || {};
            });
        });
    }

    /* ── PayU hidden-form redirect ── */
    function redirectToPayU(payuUrl, params) {
        var form = $('<form>', { method: 'POST', action: payuUrl, style: 'display:none;' });
        $.each(params, function (name, value) {
            form.append($('<input>', { type: 'hidden', name: name, value: value }));
        });
        $('body').append(form);
        form.submit();
    }

    /* ── COD normal form POST ── */
    function submitCod(data) {
        var form = $('<form>', { method: 'POST', action: COD_URL, style: 'display:none;' });
        form.append($('<input>', { type: 'hidden', name: '_token', value: CSRF }));
        $.each(data, function (name, value) {
            form.append($('<input>', { type: 'hidden', name: name, value: value || '' }));
        });
        $('body').append(form);
        form.submit();
    }

    /* ── Main submit handler ── */
    $('#checkout-form').on('submit', function (e) {
        e.preventDefault();
        var method = $('input[name=payment_method]:checked').val();
        if (!method)           { showErr('Please select a payment method.'); return; }
        if (!validateFields()) return;

        if (method === 'cod') {
            spin('Placing your order…');
            submitCod(formData('cod'));
            return;
        }

        spin('Saving your order…');
        postJson(STORE_PENDING, formData(method))
            .then(function (od) {
                if (!od.order_id) throw new Error('No order ID returned.');
                spin('Connecting to PayU…');
                return postJson(CREATE_ORDER, { order_id: od.order_id })
                    .then(function (pu) { return { od: od, pu: pu }; });
            })
            .then(function (res) {
                var pu = res.pu;
                if (!pu.payu_url || !pu.params) throw new Error('PayU configuration error. Please try again.');
                spin('Redirecting to PayU…');
                redirectToPayU(pu.payu_url, pu.params);
            })
            .catch(function (err) { showErr(err.message || 'Something went wrong. Please try again.'); });
    });

    /* ── Coupon apply ── */
    $('#apply-coupon-btn').on('click', function () {
        var code = $('#coupon_input').val().trim();
        if (!code) { $('#coupon-msg').html('<span class="text-danger">Please enter a coupon code.</span>'); return; }
        $.post('{{ route("coupon.apply") }}', { _token: CSRF, coupon_code: code, subtotal: {{ $subtotal }} }, function (res) {
            if (res.success) {
                $('#coupon-msg').html('<span class="text-success"><i class="fa fa-check mr-1"></i>' + res.message + '</span>');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                $('#coupon-msg').html('<span class="text-danger">' + res.message + '</span>');
            }
        }).fail(function () { $('#coupon-msg').html('<span class="text-danger">Error applying coupon.</span>'); });
    });

    /* ═══════════════════════════════════════════════════════════════
       IDENTITY GATEWAY (guests only)
       Runs only when the user is not authenticated.
       The auth block in Blade already hides #identity-gateway for logged-in users.
    ═══════════════════════════════════════════════════════════════ */
    @guest
    var $gateway      = $('#identity-gateway');
    var $emailInput   = $('#guest-email-input');
    var $emailBtn     = $('#guest-email-btn');
    var $emailBtnText = $('#email-btn-text');
    var $emailSpinner = $('#email-btn-spinner');
    var $emailFeedback = $('#guest-email-feedback');
    var $skipBtn      = $('#skip-email-btn');
    var $skipBtnText  = $('#skip-btn-text');
    var $skipSpinner  = $('#skip-btn-spinner');
    var $form         = $('#checkout-form');
    var $placeBtn     = $('#place-order-btn');
    var $placeHint    = $('#place-order-hint');

    /**
     * Called after identity is established (either via email or skip).
     * Hides the gateway, reveals the form, enables the Place Order button.
     */
    function revealCheckout(bannerMsg, bannerType) {
        $gateway.slideUp(250, function () {
            if (bannerMsg) {
                var cls = bannerType === 'success' ? 'alert-success' : 'alert-info';
                $form.prepend(
                    '<div class="alert ' + cls + ' alert-dismissible fade show mb-3">' +
                    bannerMsg +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
                );
            }
            $form.slideDown(250);
            $placeBtn.prop('disabled', false);
            $placeHint.hide();
            updateBtn(); // re-sync button label now that it's enabled
        });
    }

    /**
     * Pre-fill shipping fields from the address object returned by lookupEmail.
     */
    function prefillAddress(address) {
        if (!address) return;
        if (address.full_name)   $('[name=shipping_full_name]').val(address.full_name);
        if (address.phone)       $('[name=shipping_phone]').val(address.phone);
        if (address.street)      $('[name=shipping_street]').val(address.street);
        if (address.city)        $('[name=shipping_city]').val(address.city);
        if (address.state)       $('[name=shipping_state]').val(address.state);
        if (address.postal_code) $('[name=shipping_postal_code]').val(address.postal_code);
        if (address.country)     $('[name=shipping_country]').val(address.country);
    }

    /* ── "Continue" button (email lookup) ── */
    function doEmailLookup() {
        var email = $emailInput.val().trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $emailFeedback.html('<span class="text-danger"><i class="fa fa-exclamation-circle mr-1"></i>Please enter a valid email address.</span>');
            $emailInput.focus();
            return;
        }

        $emailFeedback.html('');
        $emailBtnText.text('Checking…');
        $emailSpinner.removeClass('d-none');
        $emailBtn.prop('disabled', true);
        $skipBtn.prop('disabled', true);

        $.ajax({
            url:    '{{ route("checkout.lookup-email") }}',
            method: 'POST',
            data:   { _token: CSRF, email: email },
            success: function (res) {
                // Populate the hidden guest_email field so store() can recover if session drops
                $('#guest-email-hidden').val(email);

                if (res.address) prefillAddress(res.address);

                var msg = res.is_existing
                    ? '<i class="fa fa-check-circle mr-1"></i>Welcome back! Your saved details have been pre-filled.'
                    : '<i class="fa fa-check-circle mr-1"></i>Got it — fill in your delivery details below.';
                revealCheckout(msg, 'success');
            },
            error: function (xhr) {
                var err = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                    ? (xhr.responseJSON.message || xhr.responseJSON.error)
                    : 'Could not verify email. Please try again.';
                $emailFeedback.html('<span class="text-danger"><i class="fa fa-exclamation-circle mr-1"></i>' + err + '</span>');
                $emailBtnText.text('Continue');
                $emailSpinner.addClass('d-none');
                $emailBtn.prop('disabled', false);
                $skipBtn.prop('disabled', false);
            }
        });
    }

    $emailBtn.on('click', doEmailLookup);
    $emailInput.on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); doEmailLookup(); }
    });

    /* ── "Continue without email" button (ghost account) ── */
    $skipBtn.on('click', function () {
        $skipBtnText.text('Please wait…');
        $skipSpinner.removeClass('d-none');
        $skipBtn.prop('disabled', true);
        $emailBtn.prop('disabled', true);

        $.ajax({
            url:    '{{ route("checkout.guest-skip") }}',
            method: 'POST',
            data:   { _token: CSRF },
            success: function () {
                revealCheckout(
                    '<i class="fa fa-info-circle mr-1"></i>Continuing as guest — fill in your delivery details below.',
                    'info'
                );
            },
            error: function () {
                // Even on error, reveal the form — the server-side safety net in store() will handle it
                revealCheckout(null, null);
            }
        });
    });
    @endguest

})(jQuery);
</script>
@endpush
