@extends('layouts.vendor')
@section('title', 'Return Request #' . $return->id)

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-undo"></i> Return Request #{{ $return->id }}</h2>
    <a href="{{ route('vendor.returns.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2">{{ session('error') }}</div>
@endif

<div class="row">

    {{-- Left: Return Details --}}
    <div class="col-md-8">

        {{-- Status timeline --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">Return Status</h6>
                @php
                    $steps = [
                        'requested'         => ['label'=>'Requested','icon'=>'fa-paper-plane'],
                        'approved'          => ['label'=>'Acknowledged','icon'=>'fa-check'],
                        'pickup_requested'  => ['label'=>'Pickup Arranged','icon'=>'fa-truck'],
                        'pickup_received'   => ['label'=>'Item Received','icon'=>'fa-box'],
                        'refund_wallet_queued' => ['label'=>'Refund Initiated','icon'=>'fa-rupee-sign'],
                        'refund_completed'  => ['label'=>'Refund Done','icon'=>'fa-check-circle'],
                    ];
                    $stepKeys = array_keys($steps);
                    $currentIdx = array_search($return->status, $stepKeys) ?: 0;
                @endphp
                <div class="d-flex align-items-center" style="overflow-x:auto;gap:0;padding-bottom:4px;">
                    @foreach($steps as $key => $step)
                    @php $idx = array_search($key,$stepKeys); $done = $idx <= $currentIdx; @endphp
                    <div class="text-center" style="flex:1;min-width:80px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $done ? '#27ae60' : '#e0e0e0' }};color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 6px;">
                            <i class="fas {{ $step['icon'] }}" style="font-size:.75rem;"></i>
                        </div>
                        <div style="font-size:.68rem;color:{{ $done ? '#27ae60' : '#aaa' }};font-weight:{{ $done?'700':'400' }};line-height:1.3;">{{ $step['label'] }}</div>
                    </div>
                    @if(!$loop->last)
                    <div style="flex:1;height:2px;background:{{ $idx < $currentIdx ? '#27ae60' : '#e0e0e0' }};min-width:20px;margin-bottom:24px;"></div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Return info --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i> Return Details</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><th style="width:140px;">Return #</th><td>#{{ $return->id }}</td></tr>
                    <tr><th>Order #</th><td><a href="{{ route('vendor.orders.show', $return->order_id) }}">#{{ $return->order_id }}</a></td></tr>
                    <tr><th>Type</th><td><span class="badge badge-{{ $return->type==='exchange'?'info':'primary' }}">{{ ucfirst($return->type ?? 'return') }}</span></td></tr>
                    <tr><th>Reason</th><td>{{ $return->reason }}</td></tr>
                    @if($return->description)
                    <tr><th>Description</th><td>{{ $return->description }}</td></tr>
                    @endif
                    <tr><th>Refund Amount</th><td><strong style="color:#27ae60;">₹{{ number_format($return->refund_amount ?? 0, 2) }}</strong></td></tr>
                    <tr><th>Requested On</th><td>{{ $return->created_at->format('d M Y, h:i A') }}</td></tr>
                    @if($return->vendor_note)
                    <tr><th>Your Note</th><td style="color:#555;">{{ $return->vendor_note }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Ordered items --}}
        @if($return->order->products->count())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header"><i class="fas fa-box me-2"></i> Items in Order</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($return->order->products as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>₹{{ number_format($product->pivot->price, 2) }}</td>
                            <td><strong>₹{{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- Right: Customer info + Action panel --}}
    <div class="col-md-4">

        {{-- Customer --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header"><i class="fas fa-user me-2"></i> Customer</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $return->user->name ?? 'N/A' }}</strong></p>
                <p class="mb-1 text-muted small">{{ $return->user->email ?? '—' }}</p>
                @if($return->order->shipping_phone)
                <p class="mb-0 text-muted small"><i class="fas fa-phone mr-1"></i>{{ $return->order->shipping_phone }}</p>
                @endif
                @if($return->order->shipping_street)
                <hr>
                <p class="mb-0 text-muted small">
                    {{ $return->order->shipping_street }},<br>
                    {{ $return->order->shipping_city }}, {{ $return->order->shipping_state }}<br>
                    {{ $return->order->shipping_postal_code }}
                </p>
                @endif
            </div>
        </div>

        {{-- Action Panel --}}
        @if(!in_array($return->status, ['refund_completed', 'rejected']))
        <div class="card border-0 shadow-sm mb-3" style="border:2px solid #D19C97!important;">
            <div class="card-header" style="background:#fff4f4;color:#D19C97;font-weight:700;">
                <i class="fas fa-bolt me-1"></i> Take Action
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.returns.update', $return->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-600" style="font-size:.88rem;">Update Status</label>
                        <select class="form-control" name="vendor_status" required>
                            <option value="">— Select action —</option>
                            @if(in_array($return->status, ['requested']))
                            <option value="acknowledged">✅ Acknowledge Return</option>
                            @endif
                            @if(in_array($return->status, ['requested','approved']))
                            <option value="pickup_arranged">🚚 Pickup Arranged</option>
                            @endif
                            @if(in_array($return->status, ['pickup_requested']))
                            <option value="received">📦 Item Received</option>
                            @endif
                            @if(in_array($return->status, ['pickup_received','approved']))
                            <option value="refund_initiated">💰 Initiate Refund</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-600" style="font-size:.88rem;">Note to Customer <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea class="form-control" name="vendor_note" rows="2"
                            placeholder="e.g. Pickup will be arranged by Blue Dart within 24hrs">{{ $return->vendor_note }}</textarea>
                        <small class="text-muted">This note is sent to the customer via email.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane mr-1"></i>Update & Notify Customer
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center py-3">
                <i class="fas fa-check-circle fa-2x mb-2" style="color:#27ae60;"></i>
                <p class="text-muted small mb-0">This return has been fully processed.</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
