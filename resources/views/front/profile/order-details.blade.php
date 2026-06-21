@extends('layouts.eshopper')
@section('title', 'Order #' . $order->id . ' — Details | Jeanzo India')
@section('meta_description', 'View the details, items and tracking information for your Jeanzo order #' . $order->id . '.')
@section('meta_robots', 'noindex, nofollow')

@section('content')

<div class="container-fluid pb-5" style="background:#faf8f8;">
    <div class="row px-xl-5 pt-4">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
            @endif

            @php
                $statusClass = ['pending'=>'j-badge-pending','processing'=>'j-badge-processing','shipped'=>'j-badge-shipped','delivered'=>'j-badge-delivered','cancelled'=>'j-badge-cancelled'][$order->status] ?? 'j-badge-pending';
                $payClass = $order->payment_status === 'paid' ? 'j-badge-paid' : 'j-badge-awaiting';
                $cancellable = in_array($order->status, ['pending','processing']);
                $returnable  = $order->status === 'delivered' && $order->updated_at->diffInDays(now()) <= 7;
                $existingReturn = \App\Models\ReturnRequest::where('order_id',$order->id)
                    ->where('user_id',auth()->id())
                    ->whereNotIn('status',['rejected'])
                    ->first();
            @endphp

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-weight-bold m-0">Order #{{ $order->id }}</h5>
                <a href="{{ route('profile.orders') }}" class="btn btn-outline-dark btn-sm px-4">
                    <i class="fa fa-arrow-left mr-1"></i>Back
                </a>
            </div>

            {{-- Order summary strip --}}
            <div class="j-section mb-3" style="background:var(--j-primary-lt);border-color:var(--j-primary);">
                <div class="row">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">ORDER #</div>
                        <strong>#{{ $order->id }}</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">DATE</div>
                        <strong>{{ $order->created_at->format('d M Y') }}</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">TOTAL</div>
                        <strong>₹{{ number_format($order->total_price, 2) }}</strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">STATUS</div>
                        <span class="j-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">PAYMENT METHOD</div>
                        <strong>
                            @if($order->payment_method==='cod') Cash on Delivery
                            @elseif($order->payment_method==='upi') UPI / Net Banking
                            @else Card @endif
                        </strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:.75rem;">PAYMENT STATUS</div>
                        <span class="j-badge {{ $payClass }}">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            @if($order->products && $order->products->count())
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-box mr-2" style="color:var(--j-primary);"></i>Items Ordered</div>
                @foreach($order->products as $product)
                <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    @if($product->images && $product->images->isNotEmpty())
                        <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}"
                             style="width:60px;height:60px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                    @else
                        <div style="width:60px;height:60px;border-radius:8px;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa fa-box" style="color:var(--j-primary);"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="font-weight-600">{{ $product->name }}</div>
                        <small class="text-muted">Qty: {{ $product->pivot->quantity }} &times; ₹{{ number_format($product->pivot->price, 2) }}</small>
                    </div>
                    <div class="font-weight-bold" style="color:var(--j-primary);">
                        ₹{{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}
                    </div>
                </div>
                @endforeach

                {{-- Totals --}}
                <div class="mt-3 pt-3 border-top">
                    @if($order->subtotal)
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Shipping</span>
                        <span>@if(!$order->shipping_cost)<span class="text-success">Free</span>@else ₹{{ number_format($order->shipping_cost,2) }}@endif</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between text-success small mb-1">
                        <span>Discount @if($order->coupon_code)<span class="text-muted">({{ $order->coupon_code }})</span>@endif</span>
                        <span>- ₹{{ number_format($order->discount_amount,2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between font-weight-bold mt-2 pt-2 border-top">
                        <span>Grand Total</span><span style="color:var(--j-primary);">₹{{ number_format($order->total_price,2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Shipping Address --}}
            @if($order->shipping_full_name)
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-map-marker-alt mr-2" style="color:var(--j-primary);"></i>Shipping Address</div>
                <p class="mb-1 font-weight-600">{{ $order->shipping_full_name }}</p>
                <p class="mb-1 text-muted small">{{ $order->shipping_street }}</p>
                <p class="mb-1 text-muted small">{{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}</p>
                <p class="mb-1 text-muted small">{{ $order->shipping_country }}</p>
                <p class="mb-0 text-muted small"><i class="fa fa-phone mr-1"></i>{{ $order->shipping_phone }}</p>
                @if($order->notes)<p class="mb-0 mt-2 text-muted small"><i class="fa fa-sticky-note mr-1"></i>{{ $order->notes }}</p>@endif
            </div>
            @endif

            {{-- Tracking --}}
            @if($order->shipments && $order->shipments->count())
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-truck mr-2" style="color:var(--j-primary);"></i>Shipment Tracking</div>
                @foreach($order->shipments as $shipment)
                <div class="d-flex gap-4 flex-wrap">
                    <div><div class="text-muted small">Courier</div><strong>{{ $shipment->courier->name ?? 'N/A' }}</strong></div>
                    <div><div class="text-muted small">Tracking #</div><strong>{{ $shipment->tracking_number ?? 'Not assigned yet' }}</strong></div>
                    <div><div class="text-muted small">Status</div><strong>{{ ucfirst($shipment->status ?? 'Pending') }}</strong></div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Cancel --}}
            @if($cancellable)
            <div class="j-section" style="border-color:#f5c6cb;background:#fff8f8;">
                <div class="j-section-title" style="color:#842029;border-bottom-color:#f5c6cb;">
                    <i class="fa fa-times-circle mr-2"></i>Cancel This Order
                </div>
                <p class="text-muted small mb-3">You can cancel this order while it's <strong>{{ $order->status }}</strong>. Once shipped, cancellation is no longer possible.</p>
                <button class="btn btn-outline-danger btn-sm px-4" data-toggle="modal" data-target="#cancelModal">Cancel Order</button>
            </div>

            <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
                <div class="modal-dialog"><div class="modal-content" style="border-radius:12px;overflow:hidden;">
                    <div class="modal-header" style="background:var(--j-primary);color:#fff;">
                        <h5 class="modal-title">Cancel Order #{{ $order->id }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('profile.cancel-order', $order->id) }}">
                        @csrf @method('PATCH')
                        <div class="modal-body">
                            <p>Are you sure you want to cancel this order? This cannot be undone.</p>
                            <select class="form-control" name="reason">
                                <option value="">-- Select a reason --</option>
                                <option>Changed my mind</option>
                                <option>Ordered by mistake</option>
                                <option>Found a better price elsewhere</option>
                                <option>Delivery time is too long</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Keep Order</button>
                            <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                        </div>
                    </form>
                </div></div>
            </div>
            @endif

            {{-- Return Order --}}
            @if($returnable && !$existingReturn)
            <div class="j-section mt-3" style="border-color:#cce5ff;background:#f0f7ff;">
                <div class="j-section-title" style="color:#004085;border-bottom-color:#cce5ff;">
                    <i class="fa fa-undo mr-2"></i>Return or Exchange This Order
                </div>
                <p class="text-muted small mb-3">
                    Not happy with your order? You can return or exchange within
                    <strong>7 days</strong> of delivery.
                    Window closes <strong>{{ $order->updated_at->addDays(7)->format('d M Y') }}</strong>.
                </p>
                <a href="{{ route('profile.return.create', $order->id) }}"
                   class="btn btn-primary btn-sm px-4">
                    <i class="fa fa-undo mr-1"></i>Request Return / Exchange
                </a>
            </div>
            @elseif($existingReturn)
            <div class="j-section mt-3" style="border-color:#d4edda;background:#f0faf4;">
                <div class="j-section-title" style="color:#155724;border-bottom-color:#d4edda;">
                    <i class="fa fa-check-circle mr-2"></i>Return Request Submitted
                </div>
                <div class="row">
                    <div class="col-sm-4 mb-2">
                        <div class="text-muted small">Request #</div>
                        <strong>#{{ $existingReturn->id }}</strong>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <div class="text-muted small">Type</div>
                        <strong>{{ ucfirst($existingReturn->type ?? 'Return') }}</strong>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <div class="text-muted small">Status</div>
                        <span class="badge badge-{{ $existingReturn->status_color }}">{{ $existingReturn->status_label }}</span>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="text-muted small">Reason</div>
                        <strong>{{ $existingReturn->reason }}</strong>
                        @if($existingReturn->description)
                        <p class="text-muted small mt-1 mb-0">{{ $existingReturn->description }}</p>
                        @endif
                    </div>
                    @if($existingReturn->vendor_note)
                    <div class="col-12">
                        <div style="background:#e8f5e9;border-left:3px solid #27ae60;padding:8px 12px;border-radius:0 8px 8px 0;font-size:.83rem;">
                            <strong>Seller note:</strong> {{ $existingReturn->vendor_note }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
