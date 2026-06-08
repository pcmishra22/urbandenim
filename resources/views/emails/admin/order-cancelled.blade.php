@extends('emails.layout', ['headerColor' => '#c0392b', 'headerSubtitle' => "Order Cancelled Alert"])

@section('content')
<h2>❌ Order #{{ $order->id }} Cancelled by Customer</h2>

    <div class="info">
        <p><span class="label">Order</span>#{{ $order->id }}</p>
        <p><span class="label">Customer</span>{{ $order->user->name }} ({{ $order->user->email }})</p>
        <p><span class="label">Order Total</span>₹{{ number_format($order->total_price, 2) }}</p>
        <p><span class="label">Payment</span>{{ strtoupper($order->payment_method ?? 'N/A') }} — {{ ucfirst($order->payment_status ?? 'pending') }}</p>
        @if($reason ?? false)<p><span class="label">Reason</span>{{ $reason }}</p>@endif
        <p><span class="label">Cancelled At</span>{{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="btn-wrap">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">View Order</a>
    </div>
@endsection
