<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:#c0392b;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:22px;}
.body{padding:32px;}
.body h2{margin-top:0;color:#c0392b;}
.info-box{background:#fdf2f2;border-left:4px solid #c0392b;padding:14px 16px;margin:16px 0;border-radius:4px;}
.info-box p{margin:4px 0;font-size:14px;}
.btn{display:inline-block;padding:12px 28px;background:#2c3e50;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;margin-top:16px;}
.footer{background:#f5f5f5;padding:16px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>🛍 EShopper — Order Cancelled</h1></div>
    <div class="body">
        <h2>Your Order #{{ $order->id }} has been cancelled</h2>
        <p>Hi {{ $order->user->name }}, we're confirming that your order has been successfully cancelled.</p>

        <div class="info-box">
            <p><strong>Order #:</strong> {{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p><strong>Total:</strong> ₹{{ number_format($order->total_price, 2) }}</p>
            @if($reason)
            <p><strong>Reason:</strong> {{ $reason }}</p>
            @endif
        </div>

        <p>If you paid online, a refund will be processed within 5–7 business days to your original payment method.</p>
        <p>If you have any questions, please contact our support team.</p>

        <a href="{{ route('products.index') }}" class="btn">Continue Shopping</a>
    </div>
    <div class="footer">© {{ date('Y') }} EShopper. All rights reserved.</div>
</div>
</body></html>
