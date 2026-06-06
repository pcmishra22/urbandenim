<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:#2c3e50;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:22px;}
.body{padding:32px;}
.body h2{margin-top:0;color:#2c3e50;}
.info-box{background:#f8f9fa;border-radius:6px;padding:16px;margin:16px 0;}
.info-box p{margin:4px 0;font-size:14px;}
table{width:100%;border-collapse:collapse;margin:16px 0;font-size:14px;}
th{background:#2c3e50;color:#fff;padding:8px 12px;text-align:left;}
td{padding:8px 12px;border-bottom:1px solid #eee;}
.total-row td{font-weight:bold;font-size:15px;border-top:2px solid #2c3e50;}
.btn{display:inline-block;padding:12px 28px;background:#2c3e50;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;margin-top:16px;}
.footer{background:#f5f5f5;padding:16px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>🛍 EShopper — Order Confirmed</h1></div>
    <div class="body">
        <h2>Thank you for your order, {{ $order->user->name }}!</h2>
        <p>Your order <strong>#{{ $order->id }}</strong> has been placed successfully and is being processed.</p>

        <div class="info-box">
            <p><strong>Order #:</strong> {{ $order->id }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
            <p><strong>Payment:</strong>
                @if($order->payment_method === 'cod') Cash on Delivery
                @elseif($order->payment_method === 'upi') UPI / Net Banking
                @else Credit / Debit Card @endif
            </p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        </div>

        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>
            <tbody>
                @foreach($order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>₹{{ number_format($product->pivot->price, 2) }}</td>
                </tr>
                @endforeach
                <tr><td colspan="2" style="text-align:right">Shipping</td>
                    <td>{{ $order->shipping_cost == 0 ? 'Free' : '₹'.number_format($order->shipping_cost,2) }}</td></tr>
                @if($order->discount_amount > 0)
                <tr><td colspan="2" style="text-align:right;color:green">Discount</td>
                    <td style="color:green">- ₹{{ number_format($order->discount_amount,2) }}</td></tr>
                @endif
                <tr class="total-row"><td colspan="2" style="text-align:right">Total</td>
                    <td>₹{{ number_format($order->total_price,2) }}</td></tr>
            </tbody>
        </table>

        <div class="info-box">
            <p><strong>Shipping To:</strong></p>
            <p>{{ $order->shipping_full_name }}, {{ $order->shipping_phone }}</p>
            <p>{{ $order->shipping_street }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_postal_code }}</p>
        </div>

        <a href="{{ route('profile.order-details', $order->id) }}" class="btn">View Order Details</a>
    </div>
    <div class="footer">© {{ date('Y') }} EShopper. All rights reserved.</div>
</div>
</body></html>
