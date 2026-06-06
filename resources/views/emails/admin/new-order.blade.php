<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;}
.header{background:#1e2a3a;padding:24px 32px;}
.header h1{color:#fff;margin:0;font-size:20px;}
.body{padding:28px;}
.info-box{background:#f8f9fa;border-radius:6px;padding:14px 16px;margin:12px 0;}
.info-box p{margin:4px 0;font-size:14px;}
table{width:100%;border-collapse:collapse;font-size:14px;margin:12px 0;}
th{background:#1e2a3a;color:#fff;padding:8px 10px;text-align:left;}
td{padding:7px 10px;border-bottom:1px solid #eee;}
.btn{display:inline-block;padding:10px 24px;background:#1e2a3a;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;}
.footer{background:#f5f5f5;padding:14px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>🛒 New Order #{{ $order->id }} — ₹{{ number_format($order->total_price,2) }}</h1></div>
    <div class="body">
        <div class="info-box">
            <p><strong>Customer:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
            <p><strong>Payment:</strong>
                @if($order->payment_method==='cod') Cash on Delivery
                @elseif($order->payment_method==='upi') UPI / Net Banking
                @else Card @endif — <strong>{{ ucfirst($order->payment_status ?? 'pending') }}</strong>
            </p>
            <p><strong>Ship To:</strong> {{ $order->shipping_full_name }}, {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
        </div>
        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>
            <tbody>
                @foreach($order->products as $p)
                <tr><td>{{ $p->name }}</td><td>{{ $p->pivot->quantity }}</td><td>₹{{ number_format($p->pivot->price,2) }}</td></tr>
                @endforeach
                <tr><td colspan="2" style="text-align:right;font-weight:bold">Total</td>
                    <td style="font-weight:bold">₹{{ number_format($order->total_price,2) }}</td></tr>
            </tbody>
        </table>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">View Order in Admin</a>
    </div>
    <div class="footer">EShopper Admin Notification</div>
</div>
</body></html>
