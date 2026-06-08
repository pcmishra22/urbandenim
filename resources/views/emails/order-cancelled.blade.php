@extends('emails.layout', ['headerColor' => '#c0392b', 'headerSubtitle' => "Order Cancellation"])

@section('content')
<h2>Your Order #{{ $order->id }} has been cancelled</h2>
    <p>Hi {{ $order->user->name }}, we're confirming that your order has been successfully cancelled.</p>

    <div class="info">
        <p><span class="label">Order Number</span>#{{ $order->id }}</p>
        <p><span class="label">Order Date</span>{{ $order->created_at->format('d M Y') }}</p>
        <p><span class="label">Total</span>₹{{ number_format($order->total_price, 2) }}</p>
        <p><span class="label">Payment Method</span>
            @if($order->payment_method==='cod') Cash on Delivery
            @elseif($order->payment_method==='upi') UPI / Net Banking
            @else Credit / Debit Card @endif
        </p>
        @if($reason ?? false)
        <p><span class="label">Reason</span>{{ $reason }}</p>
        @endif
        <p><span class="label">Status</span><span class="badge badge-cancelled">Cancelled</span></p>
    </div>

    @if(($order->payment_status ?? '') === 'paid')
    <div class="alert alert-info">
        <p style="margin:0;font-size:14px;">💳 Since you paid online, a full refund of <strong>₹{{ number_format($order->total_price,2) }}</strong> will be processed to your original payment method within <strong>5–7 business days</strong>.</p>
    </div>
    @endif

    <p>If you have any questions, please don't hesitate to <a href="{{ route('contact') }}">contact our support team</a>.</p>

    <div class="btn-wrap">
        <a href="{{ route('products.index') }}" class="btn">Continue Shopping</a>
    </div>
@endsection
