@extends('emails.layout', ['headerColor' => '#1e2a3a', 'headerSubtitle' => "New Order Received"])

@section('content')
<h2>🛒 New Order #{{ $order->id }} — ₹{{ number_format($order->total_price,2) }}</h2>

    <div class="info">
        <p><span class="label">Customer</span>{{ $order->user->name }} ({{ $order->user->email }})</p>
        <p><span class="label">Order Date</span>{{ $order->created_at->format('d M Y, h:i A') }}</p>
        <p><span class="label">Payment</span>
            @if($order->payment_method==='cod') Cash on Delivery
            @elseif($order->payment_method==='upi') UPI / Net Banking
            @else Card @endif
            — <span class="badge badge-{{ $order->payment_status === 'paid' ? 'paid' : 'pending_payment' }}">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
        </p>
        <p><span class="label">Ship To</span>{{ $order->shipping_full_name }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
    </div>

    <table>
        <thead><tr><th>Product</th><th style="width:60px;text-align:center;">Qty</th><th style="width:100px;text-align:right;">Price</th></tr></thead>
        <tbody>
            @foreach($order->products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td style="text-align:center;">{{ $p->pivot->quantity }}</td>
                <td style="text-align:right;">₹{{ number_format($p->pivot->price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="2" style="text-align:right;">Grand Total</td><td style="text-align:right;">₹{{ number_format($order->total_price,2) }}</td></tr>
        </tfoot>
    </table>

    <div class="btn-wrap">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">View Order in Admin</a>
    </div>
@endsection
