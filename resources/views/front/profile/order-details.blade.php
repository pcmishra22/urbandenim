@extends('layouts.eshopper')

@section('title', 'Order #{{ $order->id }} - EShopper')

@section('content')
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Order #{{ $order->id }}</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="{{ route('profile.orders') }}">My Orders</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Order #{{ $order->id }}</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-semi-bold m-0">Order Details</h5>
                    <a href="{{ route('profile.orders') }}" class="btn btn-outline-dark btn-sm">
                        <i class="fa fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>

                <!-- Order Summary -->
                <div class="bg-light p-4 mb-4 rounded">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Order Number</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Order Date</small>
                            <strong>{{ $order->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Total Amount</small>
                            <strong>₹{{ number_format($order->total_price, 2) }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Status</small>
                            @php
                                $badges = [
                                    'pending'    => 'warning',
                                    'processing' => 'info',
                                    'shipped'    => 'primary',
                                    'delivered'  => 'success',
                                    'cancelled'  => 'danger',
                                ];
                                $badge = $badges[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badge }} text-capitalize">{{ $order->status }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong>
                                @if($order->payment_method === 'cod') Cash on Delivery
                                @elseif($order->payment_method === 'upi') UPI / Net Banking
                                @else Credit / Debit Card
                                @endif
                            </strong>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Payment Status</small>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} text-capitalize">
                                {{ $order->payment_status ?? 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                @if($order->products && $order->products->count() > 0)
                <div class="mb-4">
                    <h6 class="font-weight-semi-bold mb-3">Items Ordered</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-secondary">
                                <tr>
                                    <th class="text-left">Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->products as $product)
                                <tr>
                                    <td class="text-left align-middle">
                                        <div class="d-flex align-items-center">
                                            @if($product->images && $product->images->isNotEmpty())
                                                <img src="{{ $product->images->first()->url }}"
                                                     alt="{{ $product->name }}"
                                                     style="width:45px;height:45px;object-fit:cover;border-radius:4px;margin-right:10px;">
                                            @else
                                                <img src="{{ asset('eshopper/img/product-1.jpg') }}"
                                                     alt="{{ $product->name }}"
                                                     style="width:45px;height:45px;object-fit:cover;border-radius:4px;margin-right:10px;">
                                            @endif
                                            {{ $product->name }}
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $product->pivot->quantity }}</td>
                                    <td class="align-middle">₹{{ number_format($product->pivot->price, 2) }}</td>
                                    <td class="align-middle">₹{{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-right">Subtotal</td>
                                    <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Shipping</td>
                                    <td>
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
                                        Discount @if($order->coupon_code)<small>({{ $order->coupon_code }})</small>@endif
                                    </td>
                                    <td class="text-success">- ₹{{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold">Total</td>
                                    <td class="font-weight-bold">₹{{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Shipping Address -->
                @if($order->shipping_full_name)
                <div class="bg-light p-4 mb-4 rounded">
                    <h6 class="font-weight-semi-bold mb-3">Shipping Address</h6>
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
                @endif

                <!-- Shipment Info -->
                @if($order->shipments && $order->shipments->count() > 0)
                <div class="bg-light p-4 rounded">
                    <h6 class="font-weight-semi-bold mb-3">Shipment Information</h6>
                    @foreach($order->shipments as $shipment)
                    <div class="mb-3">
                        <p class="mb-1"><strong>Courier:</strong> {{ $shipment->courier->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Tracking:</strong> {{ $shipment->tracking_number ?? 'Not available yet' }}</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="text-capitalize">{{ $shipment->status ?? 'Pending' }}</span></p>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection
