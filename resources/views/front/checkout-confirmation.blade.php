@extends('layouts.eshopper')

@section('title', 'Order Confirmed - Jeanzo')

@section('content')

    <!-- Page Header -->
    @include('front.partials.page-banner', ['title' => 'Order Confirmed', 'breadcrumb' => 'Order Confirmation'])
</div>
    </div>

    <div class="container-fluid pt-3 pb-5">
        <div class="row px-xl-5 justify-content-center">
            <div class="col-lg-8">

                <!-- Success Banner -->
                <div class="text-center py-5 mb-4 border rounded" style="background:#f8fff8;">
                    <div class="mb-3" style="font-size:64px; color:#28a745;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h3 class="font-weight-bold mb-2">Thank you for your order!</h3>
                    <p class="text-muted mb-1">
                        Your order <strong>#{{ $order->id }}</strong> has been placed successfully.
                    </p>
                    <p class="text-muted mb-0">
                        @if($order->payment_method === 'cod')
                            You'll pay <strong>₹{{ number_format($order->total_price, 2) }}</strong> on delivery.
                        @else
                            Payment of <strong>₹{{ number_format($order->total_price, 2) }}</strong> received.
                        @endif
                    </p>
                </div>

                <!-- Order Info Row -->
                <div class="row mb-4 text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">Order Number</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">Order Date</small>
                            <strong>{{ $order->created_at->format('d M Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">Payment</small>
                            <strong class="text-capitalize">
                                @if($order->payment_method === 'cod') Cash on Delivery
                                @elseif($order->payment_method === 'upi') UPI / Net Banking
                                @else Credit / Debit Card
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block mb-1">Status</small>
                            <span class="badge badge-warning text-capitalize">{{ $order->status }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Ordered -->
                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary border-0">
                        <h5 class="font-weight-semi-bold m-0">Items Ordered</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center" style="width:80px;">Qty</th>
                                    <th class="text-right" style="width:120px;">Price</th>
                                    <th class="text-right" style="width:120px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->products as $product)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            @if($product->images && $product->images->isNotEmpty())
                                                <img src="{{ $product->images->first()->url }}"
                                                     alt="{{ $product->name }}"
                                                     style="width:50px;height:50px;object-fit:cover;border-radius:4px;margin-right:12px;">
                                            @else
                                                <img src="{{ asset('eshopper/img/product-1.jpg') }}"
                                                     alt="{{ $product->name }}"
                                                     style="width:50px;height:50px;object-fit:cover;border-radius:4px;margin-right:12px;">
                                            @endif
                                            <span>{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">{{ $product->pivot->quantity }}</td>
                                    <td class="text-right align-middle">₹{{ number_format($product->pivot->price, 2) }}</td>
                                    <td class="text-right align-middle">₹{{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-right">Subtotal</td>
                                    <td class="text-right">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Shipping</td>
                                    <td class="text-right">
                                        @if($order->shipping_cost == 0)
                                            <span class="text-success">Free</span>
                                        @else
                                            ₹{{ number_format($order->shipping_cost, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-right text-success">
                                        Discount
                                        @if($order->coupon_code)
                                            <small>({{ $order->coupon_code }})</small>
                                        @endif
                                    </td>
                                    <td class="text-right text-success">- ₹{{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold">Total</td>
                                    <td class="text-right font-weight-bold">₹{{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary border-0">
                        <h5 class="font-weight-semi-bold m-0">Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->shipping_full_name }}</strong></p>
                        <p class="mb-1">{{ $order->shipping_street }}</p>
                        <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}</p>
                        <p class="mb-1">{{ $order->shipping_country }}</p>
                        <p class="mb-0"><i class="fa fa-phone mr-1 text-muted"></i>{{ $order->shipping_phone }}</p>
                        @if($order->notes)
                            <hr>
                            <p class="mb-0 text-muted"><i class="fa fa-sticky-note mr-1"></i>{{ $order->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark px-4 py-3">
                        <i class="fa fa-shopping-bag mr-2"></i>Continue Shopping
                    </a>
                    <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-primary px-4 py-3">
                        <i class="fa fa-list mr-2"></i>View Order Details
                    </a>
                </div>

            </div>
        </div>
    </div>

@endsection
