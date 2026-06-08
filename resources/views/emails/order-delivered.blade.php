@extends('emails.layout', ['headerColor' => '#155724', 'headerSubtitle' => "Order Delivered! Hope you love it 💚"])

@section('content')
<h2>Your order has been delivered! 🎉</h2>
    <p>Hi {{ $order->user->name }}, we hope you received your order <strong>#{{ $order->id }}</strong> in perfect condition!</p>

    {{-- Order progress --}}
    <ul class="steps" style="margin:24px 0;">
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Placed</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Confirmed</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Packed</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Shipped</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Delivered</span></li>
    </ul>

    <div class="alert alert-success">
        <p style="margin:0;font-size:14px;">✅ Your order has been delivered. Enjoy your purchase!</p>
    </div>

    <div class="info">
        <p><span class="label">Order Number</span>#{{ $order->id }}</p>
        <p><span class="label">Ordered On</span>{{ $order->created_at->format('d M Y') }}</p>
        <p><span class="label">Delivered On</span>{{ now()->format('d M Y') }}</p>
        <p><span class="label">Total Paid</span>₹{{ number_format($order->total_price, 2) }}</p>
    </div>

    <table>
        <thead><tr><th>Product</th><th style="width:60px;text-align:center;">Qty</th><th style="width:100px;text-align:right;">Price</th></tr></thead>
        <tbody>
            @foreach($order->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td style="text-align:center;">{{ $product->pivot->quantity }}</td>
                <td style="text-align:right;">₹{{ number_format($product->pivot->price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="alert alert-info" style="margin-top:20px;">
        <p style="margin:0;font-size:14px;">⭐ <strong>Loved your purchase?</strong> Share your experience by leaving a review. Your feedback helps other shoppers!</p>
    </div>

    <div class="btn-wrap">
        <a href="{{ route('profile.order-details', $order->id) }}" class="btn">Write a Review</a>
    </div>

    <hr class="divider">
    <p style="font-size:13px;color:#888;text-align:center;">
        Not happy with your order? Our <strong>7-day return policy</strong> has you covered.
        <a href="{{ route('contact') }}">Contact support</a> to initiate a return.
    </p>
@endsection
