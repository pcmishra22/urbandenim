@extends('layouts.eshopper')

@section('title', 'Shopping Cart - EShopper')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Shopping Cart</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Shopping Cart</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Cart Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">

            @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
            @endif

            @if(empty($cartItems))
            <div class="col-12 text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Your cart is empty</h4>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-3 px-5">Continue Shopping</a>
            </div>
            @else

            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-bordered text-center mb-0">
                    <thead class="bg-secondary text-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @foreach($cartItems as $key => $item)
                        @php $product = $item['product']; $price = $product->sale_price ?? $product->price; @endphp
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center justify-content-center">
                                    @if($product->images && $product->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('eshopper/img/product-1.jpg') }}" alt="" style="width: 50px;">
                                    @endif
                                    <span class="ml-2">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="align-middle">₹{{ number_format($price, 2) }}</td>
                            <td class="align-middle">
                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-primary btn-minus" type="button"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm bg-secondary text-center qty-input"
                                           value="{{ $item['quantity'] }}"
                                           data-product="{{ $product->id }}"
                                           min="1">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-primary btn-plus" type="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle row-total" data-price="{{ $price }}">₹{{ number_format($price * $item['quantity'], 2) }}</td>
                            <td class="align-middle">
                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-times"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-lg-4">
                <!-- Coupon -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Coupon Code</h4>
                    </div>
                    <div class="card-body">
                        <p>Enter a coupon code to get a discount on your order.</p>
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon-input" placeholder="Coupon code">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cart Summary -->
                <div class="card border-secondary mb-5">
                    <div class="card-header bg-secondary border-0">
                        <h4 class="font-weight-semi-bold m-0">Cart Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 pt-1">
                            <h6 class="font-weight-medium">Subtotal</h6>
                            <h6 class="font-weight-medium" id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">
                                @if($subtotal >= 500)
                                    <span class="text-success">Free</span>
                                @else
                                    ₹50.00
                                @endif
                            </h6>
                        </div>
                        @if($subtotal < 500)
                        <small class="text-muted">Free shipping on orders above ₹500</small>
                        @endif
                    </div>
                    <div class="card-footer border-secondary bg-transparent">
                        <div class="d-flex justify-content-between mt-2">
                            <h5 class="font-weight-bold">Total</h5>
                            <h5 class="font-weight-bold" id="cart-total">₹{{ number_format($subtotal + ($subtotal >= 500 ? 0 : 50), 2) }}</h5>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-block btn-primary my-3 py-3 font-weight-bold">
                            Proceed To Checkout
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-block btn-outline-dark py-3">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
    <!-- Cart End -->

@endsection

@push('scripts')
<script>
function updateCartQty(productId, qty) {
    $.post('{{ route("cart.update") }}', {
        _token: '{{ csrf_token() }}',
        product_id: productId,
        quantity: qty
    }, function(data) {
        if (data.success) {
            $('#cart-count').text(data.cart_count);
            // Reload to recalculate totals accurately
            location.reload();
        }
    });
}

$(document).on('click', '.btn-plus', function() {
    var $input = $(this).closest('.quantity').find('.qty-input');
    var val = parseInt($input.val()) || 1;
    var newVal = val + 1;
    $input.val(newVal);
    updateCartQty($input.data('product'), newVal);
});

$(document).on('click', '.btn-minus', function() {
    var $input = $(this).closest('.quantity').find('.qty-input');
    var val = parseInt($input.val()) || 1;
    if (val > 1) {
        var newVal = val - 1;
        $input.val(newVal);
        updateCartQty($input.data('product'), newVal);
    }
});
</script>
@endpush
