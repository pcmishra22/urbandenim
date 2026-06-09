@extends('layouts.eshopper')

@section('title', 'Order #' . $order->id . ' - Jeanzo')

@section('content')

    @include('front.partials.page-banner', ['title' => 'Order Details', 'breadcrumb' => 'Order #' . $order->id])

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-semi-bold m-0">Order #{{ $order->id }}</h5>
                    <a href="{{ route('profile.orders') }}" class="btn btn-outline-dark btn-sm">
                        <i class="fa fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>

                {{-- Order Status Summary --}}
                @php
                    $badgeMap = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                    $badge = $badgeMap[$order->status] ?? 'secondary';
                    $cancellable = in_array($order->status, ['pending','processing']);
                @endphp

                <div class="bg-light rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Order #</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Date</small>
                            <strong>{{ $order->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Total</small>
                            <strong>₹{{ number_format($order->total_price, 2) }}</strong>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge badge-{{ $badge }} text-capitalize">{{ $order->status }}</span>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Payment</small>
                            <strong>
                                @if($order->payment_method==='cod') Cash on Delivery
                                @elseif($order->payment_method==='upi') UPI / Net Banking
                                @else Card @endif
                            </strong>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <small class="text-muted d-block">Payment Status</small>
                            <span class="badge {{ ($order->payment_status==='paid') ? 'badge-success' : 'badge-warning' }} text-capitalize">
                                {{ $order->payment_status ?? 'pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
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
                                                <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
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
                                @if($order->subtotal)
                                <tr><td colspan="3" class="text-right">Subtotal</td><td>₹{{ number_format($order->subtotal,2) }}</td></tr>
                                @endif
                                <tr><td colspan="3" class="text-right">Shipping</td>
                                    <td>@if(!$order->shipping_cost)<span class="text-success">Free</span>@else ₹{{ number_format($order->shipping_cost,2) }}@endif</td></tr>
                                @if($order->discount_amount > 0)
                                <tr><td colspan="3" class="text-right text-success">Discount @if($order->coupon_code)<small>({{ $order->coupon_code }})</small>@endif</td>
                                    <td class="text-success">- ₹{{ number_format($order->discount_amount,2) }}</td></tr>
                                @endif
                                <tr><td colspan="3" class="text-right font-weight-bold">Total</td>
                                    <td class="font-weight-bold">₹{{ number_format($order->total_price,2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Shipping Address --}}
                @if($order->shipping_full_name)
                <div class="bg-light rounded p-4 mb-4">
                    <h6 class="font-weight-semi-bold mb-3">Shipping Address</h6>
                    <p class="mb-1"><strong>{{ $order->shipping_full_name }}</strong></p>
                    <p class="mb-1">{{ $order->shipping_street }}</p>
                    <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}</p>
                    <p class="mb-1">{{ $order->shipping_country }}</p>
                    <p class="mb-0"><i class="fa fa-phone mr-1 text-muted"></i>{{ $order->shipping_phone }}</p>
                    @if($order->notes)<hr><p class="mb-0 text-muted"><i class="fa fa-sticky-note mr-1"></i>{{ $order->notes }}</p>@endif
                </div>
                @endif

                {{-- Shipment Tracking --}}
                @if($order->shipments && $order->shipments->count() > 0)
                <div class="bg-light rounded p-4 mb-4">
                    <h6 class="font-weight-semi-bold mb-3">Shipment Tracking</h6>
                    @foreach($order->shipments as $shipment)
                    <p class="mb-1"><strong>Courier:</strong> {{ $shipment->courier->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Tracking #:</strong> {{ $shipment->tracking_number ?? 'Not available yet' }}</p>
                    <p class="mb-0"><strong>Status:</strong> {{ ucfirst($shipment->status ?? 'Pending') }}</p>
                    @endforeach
                </div>
                @endif

                {{-- Cancel Order --}}
                @if($cancellable)
                <div class="border border-danger rounded p-4 mb-4">
                    <h6 class="font-weight-semi-bold text-danger mb-2"><i class="fa fa-times-circle mr-1"></i>Cancel This Order</h6>
                    <p class="text-muted small mb-3">You can cancel this order while it's still <strong>{{ $order->status }}</strong>. Once shipped, cancellation is no longer possible.</p>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#cancelModal">
                        Cancel Order
                    </button>
                </div>

                <!-- Cancel Modal -->
                <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Cancel Order #{{ $order->id }}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="POST" action="{{ route('profile.cancel-order', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel this order? This cannot be undone.</p>
                                    <div class="form-group">
                                        <label for="reason">Reason <small class="text-muted">(optional)</small></label>
                                        <select class="form-control" id="reason" name="reason">
                                            <option value="">-- Select a reason --</option>
                                            <option>Changed my mind</option>
                                            <option>Ordered by mistake</option>
                                            <option>Found a better price elsewhere</option>
                                            <option>Delivery time is too long</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Keep Order</button>
                                    <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

@endsection
