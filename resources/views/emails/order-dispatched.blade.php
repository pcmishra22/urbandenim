@extends('emails.layout', ['headerColor' => '#1a6e3a', 'headerSubtitle' => "Your order is on its way! 🚚"])

@section('content')
<h2>Great news! Your order has been shipped 🚚</h2>
    <p>Hi {{ $order->user->name }}, your order <strong>#{{ $order->id }}</strong> has been packed and handed over to the courier.</p>

    {{-- Order progress --}}
    <ul class="steps" style="margin:24px 0;">
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Placed</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Confirmed</span></li>
        <li class="step done"><span class="step-dot">✓</span><span class="step-label">Packed</span></li>
        <li class="step active"><span class="step-dot">4</span><span class="step-label">Shipped</span></li>
        <li class="step pending"><span class="step-dot">5</span><span class="step-label">Delivered</span></li>
    </ul>

    <div class="alert alert-success">
        <p style="margin:0;font-size:14px;">📦 Your order is on its way! Expected delivery in <strong>3–7 business days</strong>.</p>
    </div>

    <div class="info">
        <p><span class="label">Order Number</span>#{{ $order->id }}</p>
        <p><span class="label">Order Date</span>{{ $order->created_at->format('d M Y') }}</p>
        <p><span class="label">Status</span><span class="badge badge-shipped">Shipped</span></p>
        @if($trackingNumber ?? false)
        <p><span class="label">Tracking Number</span><strong>{{ $trackingNumber }}</strong></p>
        @endif
        @if($courierName ?? false)
        <p><span class="label">Courier</span>{{ $courierName }}</p>
        @endif
    </div>

    <table>
        <thead><tr><th>Product</th><th style="width:60px;text-align:center;">Qty</th></tr></thead>
        <tbody>
            @foreach($order->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td style="text-align:center;">{{ $product->pivot->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($order->shipping_full_name)
    <div class="info">
        <p><span class="label">Delivering To</span>
            {{ $order->shipping_full_name }}<br>
            {{ $order->shipping_street }}, {{ $order->shipping_city }},<br>
            {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}
        </p>
    </div>
    @endif

    <div class="btn-wrap">
        <a href="{{ route('profile.order-details', $order->id) }}" class="btn">Track My Order</a>
    </div>
@endsection
