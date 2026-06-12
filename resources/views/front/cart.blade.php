@extends('layouts.eshopper')
@section('title', 'Shopping Cart - Jeanzo')

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'Shopping Cart', 'breadcrumb' => 'Cart', 'showCategories' => false])

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:20px;">
    <div class="row px-xl-5">

        @if(session('success'))
        <div class="col-12 mb-3">
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        </div>
        @endif

        @if(empty($cartItems))
        <div class="col-12 text-center py-5">
            <div style="width:96px;height:96px;border-radius:50%;background:var(--j-primary-lt);display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <i class="fa fa-shopping-cart fa-2x" style="color:var(--j-primary);"></i>
            </div>
            <h4 class="font-weight-bold mb-2">Your cart is empty</h4>
            <p class="text-muted mb-4">Looks like you haven't added anything yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary px-5 py-3">Start Shopping</a>
        </div>
        @else

        {{-- Cart Items --}}
        <div class="col-lg-8 mb-5">

            {{-- Header row --}}
            <div class="d-none d-md-flex j-section mb-2 py-2" style="background:#fff;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--j-muted);">
                <div style="flex:3;">Product</div>
                <div style="flex:1;text-align:center;">Price</div>
                <div style="flex:1;text-align:center;">Qty</div>
                <div style="flex:1;text-align:center;">Total</div>
                <div style="flex:.5;text-align:center;">Remove</div>
            </div>

            @foreach($cartItems as $key => $item)
            @php $product = $item['product']; $price = $product->sale_price ?? $product->price; @endphp
            <div class="j-cart-item d-flex align-items-center gap-3 flex-wrap flex-md-nowrap mb-2">
                {{-- Image --}}
                <div style="flex-shrink:0;">
                    <img src="{{ $product->images && $product->images->isNotEmpty() ? $product->images->first()->url : asset('eshopper/img/product-1.jpg') }}"
                         alt="{{ $product->name }}"
                         style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid var(--j-border);">
                </div>
                {{-- Name --}}
                <div style="flex:2;min-width:140px;">
                    <div class="font-weight-700" style="font-size:.92rem;line-height:1.3;">{{ $product->name }}</div>
                    @if(isset($item['variant']))
                        <small class="text-muted">{{ $item['variant'] }}</small>
                    @endif
                </div>
                {{-- Price --}}
                <div style="flex:1;text-align:center;" class="font-weight-700" style="color:var(--j-primary);">
                    ₹{{ number_format($price, 2) }}
                </div>
                {{-- Qty --}}
                <div style="flex:1;" class="d-flex justify-content-center">
                    <div class="input-group quantity" style="width:110px;">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-primary btn-minus" type="button"><i class="fa fa-minus"></i></button>
                        </div>
                        <input type="text" class="form-control form-control-sm bg-white text-center qty-input"
                               value="{{ $item['quantity'] }}" data-product="{{ $product->id }}" min="1"
                               style="border-color:var(--j-border);">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-primary btn-plus" type="button"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                {{-- Row total --}}
                <div style="flex:1;text-align:center;" class="font-weight-700 row-total" data-price="{{ $price }}">
                    ₹{{ number_format($price * $item['quantity'], 2) }}
                </div>
                {{-- Remove --}}
                <div style="flex:.5;" class="d-flex justify-content-center">
                    <form method="POST" action="{{ route('cart.remove') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:6px;" title="Remove">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark px-4">
                    <i class="fa fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4 mb-5">

            {{-- Coupon --}}
            <div class="j-order-summary mb-3">
                <div class="summary-title"><i class="fa fa-tag mr-2" style="color:var(--j-primary);"></i>Coupon Code</div>
                @if(session('coupon_code'))
                    <div class="alert alert-success py-2 mb-2 d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-check-circle mr-1"></i><strong>{{ session('coupon_code') }}</strong> applied!</span>
                        <form method="POST" action="{{ route('coupon.remove') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">Remove</button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('coupon.apply') }}" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="coupon_code" class="form-control form-control-sm" placeholder="Enter coupon code" style="border-radius:8px;">
                        <button type="submit" class="btn btn-primary btn-sm px-3" style="white-space:nowrap;">Apply</button>
                    </form>
                    @if(session('coupon_error'))
                        <small class="text-danger mt-1 d-block">{{ session('coupon_error') }}</small>
                    @endif
                @endif
            </div>

            {{-- Summary --}}
            <div class="j-order-summary">
                <div class="summary-title"><i class="fa fa-receipt mr-2" style="color:var(--j-primary);"></i>Order Summary</div>

                <div class="summary-row"><span>Subtotal</span><span id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span></div>
                @if(session('coupon_discount'))
                <div class="summary-row text-success"><span>Discount</span><span>- ₹{{ number_format(session('coupon_discount'), 2) }}</span></div>
                @endif
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>
                        @if($subtotal >= 500)
                            <span class="text-success font-weight-bold">FREE</span>
                        @else
                            ₹50.00
                        @endif
                    </span>
                </div>
                @if($subtotal < 500)
                <div class="mb-2">
                    <div class="progress" style="height:4px;border-radius:2px;">
                        <div class="progress-bar" style="width:{{ min(100,($subtotal/500)*100) }}%;background:var(--j-primary);"></div>
                    </div>
                    <small class="text-muted">Add ₹{{ number_format(500-$subtotal,2) }} more for free shipping</small>
                </div>
                @endif

                <div class="summary-total">
                    <span>Total</span>
                    <span id="cart-total" style="color:var(--j-primary);">₹{{ number_format($subtotal - (session('coupon_discount',0)) + ($subtotal >= 500 ? 0 : 50), 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block mt-4 py-3 font-weight-bold" style="border-radius:10px;font-size:1rem;">
                    <i class="fa fa-lock mr-2"></i>Proceed to Checkout
                </a>
            </div>
        </div>

        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCartQty(productId, qty) {
    $.post('{{ route("cart.update") }}', { _token: '{{ csrf_token() }}', product_id: productId, quantity: qty }, function(data) {
        if (data.success) { $('#cart-count').text(data.cart_count); location.reload(); }
    });
}
$(document).on('click', '.btn-plus', function() {
    var $i = $(this).closest('.quantity').find('.qty-input'), v = parseInt($i.val())||1;
    $i.val(v+1); updateCartQty($i.data('product'), v+1);
});
$(document).on('click', '.btn-minus', function() {
    var $i = $(this).closest('.quantity').find('.qty-input'), v = parseInt($i.val())||1;
    if(v>1){ $i.val(v-1); updateCartQty($i.data('product'), v-1); }
});
</script>
@endpush
