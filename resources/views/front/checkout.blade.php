@extends('layouts.eshopper')

@section('title', 'Checkout - Jeanzo')

@section('content')

    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Checkout</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Checkout</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5">
        <div class="row px-xl-5">

            {{-- ── Left: address form ──────────────────────────── --}}
            <div class="col-lg-8">

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

                <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf

                <h4 class="font-weight-semi-bold mb-4">Billing / Shipping Address</h4>

                @if($addresses->isNotEmpty())
                <div class="mb-4">
                    <label class="font-weight-medium mb-2">Use a saved address:</label>
                    <div class="row">
                        @foreach($addresses as $address)
                        <div class="col-md-6 mb-2">
                            <div class="border p-3 saved-addr" style="cursor:pointer;border-radius:4px;"
                                 data-full_name="{{ $address->full_name }}"
                                 data-phone="{{ $address->phone }}"
                                 data-street="{{ $address->street }}"
                                 data-city="{{ $address->city }}"
                                 data-state="{{ $address->state }}"
                                 data-postal="{{ $address->postal_code }}"
                                 data-country="{{ $address->country }}">
                                <strong>{{ $address->full_name }}</strong>
                                <p class="mb-0 small text-muted">{{ $address->street }}, {{ $address->city }}</p>
                                <p class="mb-0 small text-muted">{{ $address->state }} {{ $address->postal_code }}</p>
                                <small class="text-primary">Click to use</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_full_name') is-invalid @enderror"
                               type="text" name="shipping_full_name"
                               value="{{ old('shipping_full_name', auth()->user()->name ?? '') }}"
                               placeholder="Full Name">
                        @error('shipping_full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Mobile No <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_phone') is-invalid @enderror"
                               type="text" name="shipping_phone"
                               value="{{ old('shipping_phone') }}" placeholder="+91 99999 99999">
                        @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Street Address <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_street') is-invalid @enderror"
                               type="text" name="shipping_street"
                               value="{{ old('shipping_street') }}" placeholder="House no. and Street name">
                        @error('shipping_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>City <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_city') is-invalid @enderror"
                               type="text" name="shipping_city"
                               value="{{ old('shipping_city') }}" placeholder="City">
                        @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>State <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_state') is-invalid @enderror"
                               type="text" name="shipping_state"
                               value="{{ old('shipping_state') }}" placeholder="State">
                        @error('shipping_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>ZIP / PIN Code <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_postal_code') is-invalid @enderror"
                               type="text" name="shipping_postal_code"
                               value="{{ old('shipping_postal_code') }}" placeholder="PIN Code">
                        @error('shipping_postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Country <span class="text-danger">*</span></label>
                        <input class="form-control @error('shipping_country') is-invalid @enderror"
                               type="text" name="shipping_country"
                               value="{{ old('shipping_country', 'India') }}" placeholder="Country">
                        @error('shipping_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 form-group">
                        <label>Order Notes <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control" name="notes" rows="3"
                                  placeholder="Special delivery instructions">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="mb-4">
                    <h4 class="font-weight-semi-bold mb-3">
                        Coupon Code <small class="text-muted font-weight-normal">(optional)</small>
                    </h4>
                    <div class="input-group" style="max-width:400px;">
                        <input class="form-control" type="text" id="coupon_input" name="coupon_code"
                               value="{{ old('coupon_code', session('coupon_code')) }}"
                               placeholder="Enter coupon code">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary" id="apply-coupon-btn">Apply</button>
                        </div>
                    </div>
                    <div id="coupon-msg" class="mt-2 small"></div>
                </div>

                </form>
            </div>

            {{-- ── Right: summary + payment ────────────────────── --}}
            <div class="col-lg-4">

                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Order Total</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="font-weight-medium mb-3">Products</h5>
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <p class="mb-0 text-truncate" style="max-width:200px;">
                                {{ $item['product']->name }}
                                <small class="text-muted">×{{ $item['quantity'] }}</small>
                            </p>
                            <p class="mb-0">₹{{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                        @endforeach
                        <hr class="mt-0">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium">₹{{ number_format($subtotal, 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">
                                @if($shipping == 0)<span class="text-success">Free</span>
                                @else ₹{{ number_format($shipping, 2) }}@endif
                            </h6>
                        </div>
                        @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="font-weight-medium text-success">
                                Discount @if($couponCode)<small>({{ $couponCode }})</small>@endif
                            </h6>
                            <h6 class="font-weight-medium text-success">- ₹{{ number_format($discount, 2) }}</h6>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <div class="d-flex justify-content-between mt-2">
                            <h5 class="font-weight-bold">Grand Total</h5>
                            <h5 class="font-weight-bold">₹{{ number_format($grandTotal, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Payment Method</h4>
                    </div>
                    <div class="card-body">

                        {{-- COD temporarily disabled — courier company criteria not yet met
                        <div class="form-group mb-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="payment_method"
                                       id="pay_cod" value="cod" form="checkout-form"
                                       {{ old('payment_method','cod') === 'cod' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pay_cod">
                                    <i class="fa fa-money-bill-wave text-success mr-2"></i>
                                    <strong>Cash on Delivery</strong>
                                    <small class="d-block text-muted">Pay when your order arrives</small>
                                </label>
                            </div>
                        </div>
                        --}}

                        <div class="form-group mb-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="payment_method"
                                       id="pay_upi" value="upi" form="checkout-form"
                                       {{ old('payment_method', 'upi') === 'upi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pay_upi">
                                    <i class="fa fa-mobile-alt text-primary mr-2"></i>
                                    <strong>UPI / QR Code Payment</strong>
                                    <small class="d-block text-muted">Google Pay, PhonePe, BHIM, Paytm & all UPI apps</small>
                                </label>
                            </div>

                            {{-- UPI QR Scanner Panel --}}
                            <div id="upi-qr-panel" class="mt-3 p-3 border rounded" style="background:#f9f9f9;">

                                {{-- QR Code Image --}}
                                <div class="text-center mb-3">
                                    <img src="{{ asset('storage/qr-payment.jpeg') }}"
                                         alt="Scan to Pay - Prakash Chandra Mishra"
                                         class="img-fluid border"
                                         style="max-width:220px; border-radius:8px;">
                                </div>

                                {{-- Payee Name --}}
                                <div class="text-center mb-3">
                                    <span class="badge badge-success px-3 py-2" style="font-size:14px;">
                                        <i class="fa fa-user mr-1"></i> Prakash Chandra Mishra
                                    </span>
                                </div>

                                {{-- Step-by-step instructions --}}
                                <div class="mb-3">
                                    <p class="font-weight-bold mb-2" style="font-size:14px;">
                                        <i class="fa fa-info-circle text-primary mr-1"></i>
                                        How to pay in 3 easy steps:
                                    </p>
                                    <ol class="pl-3 mb-0" style="font-size:13px; line-height:2;">
                                        <li>Open <strong>Google Pay, PhonePe, Paytm</strong> or any UPI app on your phone.</li>
                                        <li>Tap <strong>"Scan QR"</strong> and scan the code above — the amount will auto-fill.</li>
                                        <li>Confirm the payment &amp; click <strong>"Place Order"</strong> below once done.</li>
                                    </ol>
                                </div>

                                {{-- Amount to pay --}}
                                <div class="d-flex align-items-center justify-content-between p-2 rounded mb-3"
                                     style="background:#fff3cd; border:1px solid #ffc107;">
                                    <span style="font-size:13px;" class="text-dark">
                                        <i class="fa fa-rupee-sign mr-1"></i> Amount to pay:
                                    </span>
                                    <strong class="text-dark" style="font-size:16px;">
                                        ₹{{ number_format($grandTotal, 2) }}
                                    </strong>
                                </div>

                                {{-- Important note --}}
                                <div class="alert alert-info py-2 mb-0" style="font-size:12px;">
                                    <i class="fa fa-exclamation-circle mr-1"></i>
                                    <strong>Important:</strong> After completing your UPI payment, click
                                    <strong>"Place Order"</strong> below. Your order will be confirmed once
                                    we verify the payment (usually within a few minutes).
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="payment_method"
                                       id="pay_card" value="card" form="checkout-form"
                                       {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pay_card">
                                    <i class="fa fa-credit-card text-info mr-2"></i>
                                    <strong>Credit / Debit Card</strong>
                                    <small class="d-block text-muted">Visa, Mastercard, Rupay — via Razorpay</small>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror

                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fa fa-shield-alt text-success mr-1"></i>
                                Online payments secured by <strong>Razorpay</strong>
                            </small>
                        </div>
                    </div>

                    <div class="card-footer border-secondary bg-transparent">
                        {{-- error box (hidden by default) --}}
                        <div id="pay-error" class="alert alert-danger small py-2 mb-2" style="display:none;"></div>

                        {{-- spinner (hidden by default — NO !important so JS can show it) --}}
                        <div id="pay-spinner" class="text-center py-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                            <span id="pay-spinner-msg" class="small text-muted">Processing…</span>
                        </div>

                        <button type="submit" form="checkout-form" id="place-order-btn"
                                class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                            <i class="fa fa-lock mr-2"></i><span id="btn-label">Place Order</span>
                        </button>
                    </div>
                </div>

                <div class="border p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-shield-alt text-primary mr-3 fa-lg"></i>
                        <div><h6 class="mb-0">Secure Checkout</h6><small class="text-muted">SSL encrypted</small></div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-undo text-primary mr-3 fa-lg"></i>
                        <div><h6 class="mb-0">Easy Returns</h6><small class="text-muted">14-day return policy</small></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fa fa-headset text-primary mr-3 fa-lg"></i>
                        <div><h6 class="mb-0">24/7 Support</h6><small class="text-muted">We're here to help</small></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- Load Razorpay SDK unconditionally — it's a tiny script and always needed --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
(function ($) {

    /* ── Config injected from server ─────────────────────── */
    var RZP_KEY          = '{{ config("services.razorpay.key", "") }}';
    var CSRF             = '{{ csrf_token() }}';
    var STORE_PENDING    = '{{ route("checkout.store-pending") }}';
    var CREATE_RZP_ORDER = '{{ route("payment.create-order") }}';
    var VERIFY_URL       = '{{ route("payment.verify") }}';
    var GRAND_TOTAL      = '{{ number_format($grandTotal, 2) }}';

    /* ── UI helpers ──────────────────────────────────────── */
    function spin(msg) {
        $('#pay-error').hide();
        $('#pay-spinner').show();
        $('#pay-spinner-msg').text(msg || 'Processing…');
        $('#place-order-btn').prop('disabled', true);
    }
    function unspin() {
        $('#pay-spinner').hide();
        $('#place-order-btn').prop('disabled', false);
    }
    function showErr(msg) {
        unspin();
        $('#pay-error').text(msg).show();
        $('html,body').animate({ scrollTop: $('#pay-error').offset().top - 120 }, 250);
    }

    /* ── Payment-method label update + QR panel toggle ───── */
    function updatePaymentUI(method) {
        /* Button label */
        $('#btn-label').text(
            method === 'upi' ? 'Place Order — I\'ve Paid via UPI' : 'Proceed to Pay ₹' + GRAND_TOTAL
        );
        /* Show QR panel only for UPI */
        $('#upi-qr-panel').toggle(method === 'upi');
    }

    $('input[name=payment_method]').on('change', function () {
        updatePaymentUI(this.value);
    });

    /* Set initial state on page load */
    updatePaymentUI($('input[name=payment_method]:checked').val() || 'upi');

    /* ── Saved address autofill ──────────────────────────── */
    $(document).on('click', '.saved-addr', function () {
        $('.saved-addr').removeClass('border-primary');
        $(this).addClass('border-primary');
        var d = $(this).data();
        $('[name=shipping_full_name]').val(d.full_name || '');
        $('[name=shipping_phone]').val(d.phone || '');
        $('[name=shipping_street]').val(d.street || '');
        $('[name=shipping_city]').val(d.city || '');
        $('[name=shipping_state]').val(d.state || '');
        $('[name=shipping_postal_code]').val(d.postal || '');
        $('[name=shipping_country]').val(d.country || '');
    });

    /* ── Field validation helper ─────────────────────────── */
    function validateFields() {
        var checks = [
            ['shipping_full_name',   'Full Name'],
            ['shipping_phone',       'Mobile No'],
            ['shipping_street',      'Street Address'],
            ['shipping_city',        'City'],
            ['shipping_state',       'State'],
            ['shipping_postal_code', 'PIN Code'],
            ['shipping_country',     'Country'],
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

    /* ── Collect all form fields as object ───────────────── */
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
            payment_method:       method,
        };
    }

    /* ── JSON POST helper ────────────────────────────────── */
    function postJson(url, data) {
        return fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',   // forces JSON errors from Laravel
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify(data),
        }).then(function (r) {
            return r.text().then(function (text) {
                var json = null;
                try { json = text ? JSON.parse(text) : {}; } catch (e) {}

                if (!r.ok) {
                    /* Laravel 422 validation → { errors: { field: ['msg'] } } */
                    if (json && json.errors) {
                        var first = Object.values(json.errors)[0];
                        throw new Error(Array.isArray(first) ? first[0] : String(first));
                    }
                    throw new Error(
                        (json && (json.error || json.message)) ||
                        ('Server error ' + r.status + (text ? ': ' + text.substring(0, 200) : ''))
                    );
                }
                return json || {};
            });
        });
    }

    /* ── Submit handler ──────────────────────────────────── */
    $('#checkout-form').on('submit', function (e) {
        var method = $('input[name=payment_method]:checked').val();

        /* UPI — validate then let normal form POST proceed to checkout.store */
        if (method === 'upi') {
            if (!validateFields()) { e.preventDefault(); }
            return;
        }

        /* Card — intercept and run Razorpay flow */
        e.preventDefault();

        if (!validateFields()) return;

        /* Razorpay key check (catches misconfigured .env at runtime) */
        if (!RZP_KEY) {
            showErr('Online payment is not configured on this server. Please use Cash on Delivery.');
            return;
        }

        /* ── Step 1: save order to DB ─────────────────────── */
        spin('Saving your order…');

        postJson(STORE_PENDING, formData(method))
        .then(function (od) {
            if (!od.order_id) throw new Error('No order ID returned from server.');
            spin('Connecting to payment gateway…');

            /* ── Step 2: create Razorpay order ──────────────── */
            return postJson(CREATE_RZP_ORDER, { order_id: od.order_id })
                   .then(function (rzp) { return { od: od, rzp: rzp }; });
        })
        .then(function (res) {
            unspin();
            var od  = res.od;
            var rzp = res.rzp;

            if (!rzp.razorpay_order_id) throw new Error('Razorpay order ID missing in response.');

            /* ── Step 3: open Razorpay modal ─────────────────── */

            var options = {
                key:         rzp.key,
                amount:      rzp.amount,
                currency:    rzp.currency || 'INR',
name:        rzp.name || 'Jeanzo',
                description: rzp.description || 'Order #' + od.order_id,
                order_id:    rzp.razorpay_order_id,
                prefill:     rzp.prefill || {},
                theme:       { color: '#2c3e50' },

                /* ── Step 4: on success, POST to verify ─────── */
                handler: function (response) {
                    spin('Verifying payment…');
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = VERIFY_URL;
                    var fields = {
                        '_token':              CSRF,
                        'razorpay_order_id':   response.razorpay_order_id,
                        'razorpay_payment_id': response.razorpay_payment_id,
                        'razorpay_signature':  response.razorpay_signature,
                        'order_id':            od.order_id,
                    };
                    Object.keys(fields).forEach(function (k) {
                        var inp = document.createElement('input');
                        inp.type = 'hidden'; inp.name = k; inp.value = fields[k];
                        form.appendChild(inp);
                    });
                    document.body.appendChild(form);
                    form.submit();
                },

                modal: {
                    ondismiss: function () {
                        unspin();
                        showErr(
                            'Payment cancelled. Your order #' + od.order_id +
                            ' is saved. You can complete payment from My Orders, or switch to Cash on Delivery.'
                        );
                    }
                }
            };

            var rzpObj = new Razorpay(options);
            rzpObj.on('payment.failed', function (resp) {
                unspin();
                showErr('Payment failed: ' + (resp.error.description || 'Unknown error') + '. Please try again.');
            });
            rzpObj.open();
        })
        .catch(function (err) {
            showErr(err.message || 'Something went wrong. Please try again or use Cash on Delivery.');
            console.error('Razorpay flow error:', err);
        });
    });

    /* ── Coupon AJAX ─────────────────────────────────────── */
    $('#apply-coupon-btn').on('click', function () {
        var code = $('#coupon_input').val().trim();
        if (!code) {
            $('#coupon-msg').html('<span class="text-danger">Please enter a coupon code.</span>');
            return;
        }
        $.post('{{ route("coupon.apply") }}', {
            _token: CSRF, coupon_code: code, subtotal: {{ $subtotal }}
        }, function (res) {
            if (res.success) {
                $('#coupon-msg').html('<span class="text-success"><i class="fa fa-check mr-1"></i>' + res.message + '</span>');
                setTimeout(function () { location.reload(); }, 800);
            } else {
                $('#coupon-msg').html('<span class="text-danger">' + res.message + '</span>');
            }
        }).fail(function () {
            $('#coupon-msg').html('<span class="text-danger">Error applying coupon.</span>');
        });
    });

})(jQuery);
</script>
@endpush
