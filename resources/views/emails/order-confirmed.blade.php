@extends('emails.layout', ['headerColor' => '#2c3e50', 'headerSubtitle' => "Order Confirmed ✓"])

@section('content')
<h2>Thank you for your order, {{ $order->user->name }}! 🎉</h2>
    <p>Your order <strong>#{{ $order->id }}</strong> has been placed and is now being processed.</p>

    {{-- Order progress --}}
    <ul class="steps" style="margin:24px 0;">
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Placed</span></li>
        <li class="step active"><span class="step-dot">2</span><span class="step-label">Confirmed</span></li>
        <li class="step pending"><span class="step-dot">3</span><span class="step-label">Packed</span></li>
        <li class="step pending"><span class="step-dot">4</span><span class="step-label">Shipped</span></li>
        <li class="step pending"><span class="step-dot">5</span><span class="step-label">Delivered</span></li>
    </ul>

    <div class="info">
        <p><span class="label">Order Number</span>#{{ $order->id }}</p>
        <p><span class="label">Date</span>{{ $order->created_at->format('d M Y, h:i A') }}</p>
        <p><span class="label">Payment Method</span>
            @if($order->payment_method==='cod') Cash on Delivery
            @elseif($order->payment_method==='upi') UPI / Net Banking
            @else Credit / Debit Card @endif
        </p>
        <p><span class="label">Payment Status</span>
            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'paid' : 'pending_payment' }}">
                {{ ucfirst($order->payment_status ?? 'pending') }}
            </span>
        </p>
        <p><span class="label">Order Status</span>
            <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        </p>
    </div>

    <table>
        <thead><tr><th>Product</th><th style="width:60px;text-align:center;">Qty</th><th style="width:100px;text-align:right;">Price</th></tr></thead>
        <tbody>
            @foreach($order->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td style="text-align:center;">{{ $product->pivot->quantity }}</td>
                <td style="text-align:right;">₹{{ number_format($product->pivot->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="2" style="text-align:right;">Subtotal</td><td style="text-align:right;">₹{{ number_format($order->subtotal ?? $order->total_price, 2) }}</td></tr>
            <tr><td colspan="2" style="text-align:right;">Shipping</td>
                <td style="text-align:right;">{{ ($order->shipping_cost ?? 0) == 0 ? 'Free' : '₹'.number_format($order->shipping_cost,2) }}</td></tr>
            @if(($order->discount_amount ?? 0) > 0)
            <tr><td colspan="2" style="text-align:right;color:green;">Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</td>
                <td style="text-align:right;color:green;">- ₹{{ number_format($order->discount_amount,2) }}</td></tr>
            @endif
            <tr><td colspan="2" style="text-align:right;">Grand Total</td><td style="text-align:right;">₹{{ number_format($order->total_price,2) }}</td></tr>
        </tfoot>
    </table>

    @if($order->shipping_full_name)
    <div class="info">
        <p><span class="label">Shipping Address</span>
            {{ $order->shipping_full_name }}<br>
            {{ $order->shipping_street }}, {{ $order->shipping_city }},<br>
            {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}<br>
            📞 {{ $order->shipping_phone }}
        </p>
    </div>
    @endif

    <div class="btn-wrap">
        <a href="{{ route('profile.order-details', $order->id) }}" class="btn">View Order Details</a>
    </div>
@endsection
