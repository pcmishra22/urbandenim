@extends('layouts.eshopper')

@section('title', 'Checkout - EShopper')

@section('content')

    <!-- Page Header Start -->
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
    <!-- Page Header End -->

    <!-- Checkout Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">

            <div class="col-lg-8">

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                @endif

                <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                @csrf

                <div class="mb-4">
                    <h4 class="font-weight-semi-bold mb-4">Billing Address</h4>

                    {{-- Saved addresses --}}
                    @auth
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
                    @endauth

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
                                   value="{{ old('shipping_phone') }}"
                                   placeholder="+91 99999 99999">
                            @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Street Address <span class="text-danger">*</span></label>
                            <input class="form-control @error('shipping_street') is-invalid @enderror"
                                   type="text" name="shipping_street"
                                   value="{{ old('shipping_street') }}"
                                   placeholder="House no. and Street name">
                            @error('shipping_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>City <span class="text-danger">*</span></label>
                            <input class="form-control @error('shipping_city') is-invalid @enderror"
                                   type="text" name="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   placeholder="City">
                            @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>State <span class="text-danger">*</span></label>
                            <input class="form-control @error('shipping_state') is-invalid @enderror"
                                   type="text" name="shipping_state"
                                   value="{{ old('shipping_state') }}"
                                   placeholder="State">
                            @error('shipping_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>ZIP / PIN Code <span class="text-danger">*</span></label>
                            <input class="form-control @error('shipping_postal_code') is-invalid @enderror"
                                   type="text" name="shipping_postal_code"
                                   value="{{ old('shipping_postal_code') }}"
                                   placeholder="PIN Code">
                            @error('shipping_postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Country <span class="text-danger">*</span></label>
                            <input class="form-control @error('shipping_country') is-invalid @enderror"
                                   type="text" name="shipping_country"
                                   value="{{ old('shipping_country', 'India') }}"
                                   placeholder="Country">
                            @error('shipping_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 form-group">
                            <label>Order Notes <small class="text-muted">(optional)</small></label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Notes about your order, e.g. special delivery instructions">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="mb-4">
                    <h4 class="font-weight-semi-bold mb-3">Coupon Code <small class="text-muted font-weight-normal">(optional)</small></h4>
                    <div class="input-group" style="max-width:400px;">
                        <input class="form-control" type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter coupon code">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary">Apply</button>
                        </div>
                    </div>
                </div>

                </form>{{-- form ends, submit button is in sidebar --}}
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <!-- Order Total -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Order Total</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="font-weight-medium mb-3">Products</h5>
                        @php $shipping = $subtotal >= 500 ? 0 : 50; @endphp
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
                        <div class="d-flex justify-content-between mb-3 pt-1">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium">₹{{ number_format($subtotal, 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">
                                @if($shipping == 0)<span class="text-success">Free</span>
                                @else ₹{{ number_format($shipping, 2) }}
                                @endif
                            </h6>
                        </div>
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <div class="d-flex justify-content-between mt-2">
                            <h5 class="font-weight-bold">Total</h5>
                            <h5 class="font-weight-bold">₹{{ number_format($subtotal + $shipping, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="payment_method" id="pay_cod" value="cod"
                                       form="checkout-form" {{ old('payment_method','cod') === 'cod' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pay_cod">
                                    <i class="fa fa-money-bill-wave text-success mr-1"></i> Cash on Delivery
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="payment_method" id="pay_upi" value="upi"
                                       form="checkout-form" {{ old('payment_method') === 'upi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pay_upi">
                                    <i class="fa fa-mobile-alt text-primary mr-1"></i> UPI / Net Banking
                                </label>
                            </div>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="payment_method" id="pay_card" value="card"
                                   form="checkout-form" {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="pay_card">
                                <i class="fa fa-credit-card text-info mr-1"></i> Credit / Debit Card
                            </label>
                        </div>
                        @error('payment_method')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <button type="submit" form="checkout-form"
                                class="btn btn-lg btn-block btn-primary font-weight-bold my-3 py-3">
                            <i class="fa fa-lock mr-2"></i>Place Order
                        </button>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="border p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-shield-alt text-primary mr-3 fa-lg"></i>
                        <div>
                            <h6 class="mb-0">Secure Checkout</h6>
                            <small class="text-muted">Your information is protected</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa fa-undo text-primary mr-3 fa-lg"></i>
                        <div>
                            <h6 class="mb-0">Easy Returns</h6>
                            <small class="text-muted">14-day return policy</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fa fa-headset text-primary mr-3 fa-lg"></i>
                        <div>
                            <h6 class="mb-0">24/7 Support</h6>
                            <small class="text-muted">We're here to help</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Checkout End -->

@endsection

@push('scripts')
<script>
$('.saved-addr').on('click', function() {
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
</script>
@endpush
