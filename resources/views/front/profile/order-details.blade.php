@extends('layouts.eshopper')

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
                    <a href="{{ route('profile.orders') }}" class="btn btn-outline-dark btn-sm"><i class="fa fa-arrow-left mr-2"></i>Back to Orders</a>
                </div>

                <!-- Order Summary -->
                <div class="bg-light p-4 mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">Order Number</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">Order Date</small>
                            <strong>{{ $order->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">Total Amount</small>
                            <strong>${{ number_format($order->total_price, 2) }}</strong>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="text-muted d-block">Status</small>
                            @php
                                $badges = [
                                    'pending'   => 'warning',
                                    'confirmed' => 'info',
                                    'packed'    => 'secondary',
                                    'shipped'   => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                ];
                                $badge = $badges[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badge }} text-capitalize">{{ $order->status }}</span>
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
                                            <td class="text-left">{{ $product->name }}</td>
                                            <td>{{ $product->pivot->quantity }}</td>
                                            <td>${{ number_format($product->pivot->price, 2) }}</td>
                                            <td>${{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td class="font-weight-bold">${{ number_format($order->total_price, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Shipment Info -->
                @if($order->shipments && $order->shipments->count() > 0)
                    <div class="bg-light p-4">
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
