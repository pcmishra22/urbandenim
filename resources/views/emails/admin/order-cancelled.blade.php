<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;}
.header{background:#c0392b;padding:24px 32px;}
.header h1{color:#fff;margin:0;font-size:20px;}
.body{padding:28px;}
.info-box{background:#fdf2f2;border-left:4px solid #c0392b;padding:14px 16px;margin:12px 0;border-radius:4px;}
.info-box p{margin:4px 0;font-size:14px;}
.btn{display:inline-block;padding:10px 24px;background:#1e2a3a;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;}
.footer{background:#f5f5f5;padding:14px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>❌ Order #{{ $order->id }} Cancelled by Customer</h1></div>
    <div class="body">
        <p>A customer has cancelled their order.</p>
        <div class="info-box">
            <p><strong>Order #:</strong> {{ $order->id }}</p>
            <p><strong>Customer:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
            <p><strong>Order Total:</strong> ₹{{ number_format($order->total_price, 2) }}</p>
            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'N/A') }}</p>
            @if($reason)<p><strong>Reason Given:</strong> {{ $reason }}</p>@endif
            <p><strong>Cancelled At:</strong> {{ now()->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">View Order</a>
    </div>
    <div class="footer">EShopper Admin Notification</div>
</div>
</body></html>
