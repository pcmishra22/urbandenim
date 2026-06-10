@extends('layouts.vendor')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-receipt"></i> Order #{{ $order->id }}</h2>
    <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="alert alert-info py-2 mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Showing only <strong>your products</strong> from this order. The full order may contain products from other vendors.
</div>

<div class="row">
    <div class="col-md-8">
        {{-- Your products in this order --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-box me-2"></i> Your Products in This Order</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->pivot->product_variant_id)
                                    <br><small class="text-muted">Variant #{{ $product->pivot->product_variant_id }}</small>
                                @endif
                            </td>
                            <td><code>{{ $product->sku ?? '—' }}</code></td>
                            <td>{{ $product->pivot->quantity ?? 1 }}</td>
                            <td>₹{{ number_format($product->pivot->price ?? $product->price, 2) }}</td>
                            <td><strong>₹{{ number_format(($product->pivot->price ?? $product->price) * ($product->pivot->quantity ?? 1), 2) }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Order Info --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i> Order Info</div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th>Order #</th><td>#{{ $order->id }}</td></tr>
                    <tr><th>Date</th><td>{{ $order->created_at->format('d M Y H:i') }}</td></tr>
                    <tr><th>Status</th><td>
                        @php
                            $statusColors = ['pending'=>'warning text-dark','confirmed'=>'info','packed'=>'primary','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $color = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                    </td></tr>
                    <tr><th>Order Total</th><td><strong>₹{{ number_format($order->total_price, 2) }}</strong></td></tr>
                </table>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-user me-2"></i> Customer</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->user->name ?? 'Guest' }}</strong></p>
                <p class="mb-1 text-muted">{{ $order->user->email ?? '—' }}</p>
                @if($order->shipping_address)
                    <hr>
                    <p class="mb-0 text-muted small">{{ $order->shipping_address }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
