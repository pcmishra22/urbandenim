@extends('layouts.dashboard')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-receipt"></i> Order #{{ $order->id }}</h2>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('admin.orders.invoice', $order) }}">
            <i class="fas fa-file-invoice"></i> Invoice
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.orders.shippingLabel', $order) }}" target="_blank">
            <i class="fas fa-print"></i> Shipping Label
        </a>
        <a class="btn btn-outline-dark" href="{{ route('admin.orders.index') }}">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row mt-3">
    {{-- Left: Order details + items --}}
    <div class="col-md-7">

        {{-- Summary --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-info-circle me-2"></i>Order Summary</span>
                @php
                    $badgeMap = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                    $badge = $badgeMap[$order->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $badge }} text-capitalize fs-6">{{ $order->status }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6"><p class="mb-2"><strong>Order #:</strong> {{ $order->id }}</p></div>
                    <div class="col-6"><p class="mb-2"><strong>Date:</strong> {{ $order->created_at?->format('d M Y, h:i A') }}</p></div>
                    <div class="col-6"><p class="mb-2"><strong>Customer:</strong> {{ $order->user?->name ?? '—' }}</p></div>
                    <div class="col-6"><p class="mb-2"><strong>Email:</strong> {{ $order->user?->email ?? '—' }}</p></div>
                    <div class="col-6"><p class="mb-2"><strong>Payment:</strong>
                        @if($order->payment_method==='cod') Cash on Delivery
                        @elseif($order->payment_method==='upi') UPI / Net Banking
                        @else Card @endif
                    </p></div>
                    <div class="col-6"><p class="mb-2"><strong>Payment Status:</strong>
                        <span class="badge {{ $order->payment_status==='paid' ? 'bg-success' : 'bg-warning' }} text-capitalize">{{ $order->payment_status ?? 'pending' }}</span>
                    </p></div>
                    @if($order->notes)
                    <div class="col-12"><p class="mb-0"><strong>Notes:</strong> {{ $order->notes }}</p></div>
                    @endif

                    {{-- Mark as Paid: only for UPI orders awaiting payment --}}
                    @if($order->payment_method === 'upi' && $order->payment_status !== 'paid')
                    <div class="col-12 mt-2">
                        <div class="alert alert-warning py-2 mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-clock"></i>
                            <span>
                                <strong>UPI Payment Pending —</strong>
                                Check your PhonePe / Google Pay app for a payment of
                                <strong>₹{{ number_format($order->total_price, 2) }}</strong>
                                from <strong>{{ $order->shipping_full_name }}</strong>
                                ({{ $order->shipping_phone }}).
                                Once confirmed, click the button below.
                            </span>
                        </div>
                        <form method="POST" action="{{ route('admin.orders.markPaid', $order) }}"
                              onsubmit="return confirm('Have you verified the UPI payment of ₹{{ number_format($order->total_price, 2) }} in your PhonePe/GPay app?\n\nThis will mark the order as paid and send a confirmation email to the customer.');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-1"></i> Mark as Paid — Payment Verified
                            </button>
                        </form>
                    </div>
                    @endif
            </div>
        </div>

        {{-- Items --}}
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-box me-2"></i>Items Ordered</div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->products as $product)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->images && $product->images->isNotEmpty())
                                        <img src="{{ $product->images->first()->url }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                    @endif
                                    <span>{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="text-center align-middle">{{ $product->pivot->quantity }}</td>
                            <td class="text-end align-middle">₹{{ number_format($product->pivot->price, 2) }}</td>
                            <td class="text-end align-middle">₹{{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        @if($order->subtotal)
                        <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">₹{{ number_format($order->subtotal,2) }}</td></tr>
                        @endif
                        <tr><td colspan="3" class="text-end">Shipping</td>
                            <td class="text-end">@if(!$order->shipping_cost)<span class="text-success">Free</span>@else ₹{{ number_format($order->shipping_cost,2) }}@endif</td></tr>
                        @if($order->discount_amount > 0)
                        <tr><td colspan="3" class="text-end text-success">Discount @if($order->coupon_code)<small>({{ $order->coupon_code }})</small>@endif</td>
                            <td class="text-end text-success">- ₹{{ number_format($order->discount_amount,2) }}</td></tr>
                        @endif
                        <tr><td colspan="3" class="text-end fw-bold">Grand Total</td>
                            <td class="text-end fw-bold">₹{{ number_format($order->total_price,2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Shipping Address --}}
        @if($order->shipping_full_name)
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->shipping_full_name }}</strong> &nbsp; 📞 {{ $order->shipping_phone }}</p>
                <p class="mb-1">{{ $order->shipping_street }}</p>
                <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}, {{ $order->shipping_country }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- Right: Status + Shipments --}}
    <div class="col-md-5">

        {{-- Update Status --}}
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-sync me-2"></i>Update Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update To</label>
                        <select name="status" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync me-1"></i>Update Status</button>
                </form>
            </div>
        </div>

        {{-- Shipments --}}
        @if($order->shipments && $order->shipments->count() > 0)
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-truck me-2"></i>Shipment Info</div>
            <div class="card-body">
                @foreach($order->shipments as $shipment)
                <p class="mb-1"><strong>Courier:</strong> {{ $shipment->courier?->name ?? '—' }}</p>
                <p class="mb-1"><strong>Tracking #:</strong> <code>{{ $shipment->tracking_number ?? '—' }}</code></p>
                <p class="mb-1"><strong>Shipped:</strong> {{ $shipment->shipped_at?->format('d M Y') ?? '—' }}</p>
                <p class="mb-0"><strong>Status:</strong> {{ ucfirst($shipment->status ?? '—') }}</p>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
