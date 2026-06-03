<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - Order #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .container { width: 100%; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
        .title { font-size: 20px; font-weight: 700; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background: #f5f5f5; text-align: left; }
        .totals { width: 340px; float: right; margin-top: 12px; }
        .totals table td { border: none; padding: 6px 0; }
        .totals .row { display: flex; justify-content: space-between; }
        .totals .label { color: #333; }
        .totals .value { font-weight: 700; }
        .footer { clear: both; margin-top: 30px; color: #666; font-size: 11px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; background: #e9f2ff; color: #0b5ed7; font-weight: 700; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; }
        .box h4 { margin: 0 0 8px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <div class="title">Invoice</div>
            <div class="muted">Order #{{ $order->id }}</div>
            <div class="muted">Issued: {{ $order->created_at?->format('Y-m-d') }}</div>
        </div>
        <div style="text-align:right;">
            <div class="badge">{{ $order->status }}</div>
            <div class="muted" style="margin-top:8px;">Total: {{ number_format((float)$order->total_price, 2) }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <h4>Bill To</h4>
            <div><strong>{{ $order->user->name ?? '-' }}</strong></div>
            <div class="muted">{{ $order->user->email ?? '' }}</div>
        </div>
        <div class="box">
            <h4>Order Summary</h4>
            <div class="muted">Items: {{ $order->products->count() }}</div>
            <div class="muted">Placed: {{ $order->created_at?->format('Y-m-d') }}</div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width:52%;">Product</th>
            <th style="width:12%;">Qty</th>
            <th style="width:18%;">Price</th>
            <th style="width:18%;">Line Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->products as $product)
            @php
                $qty = (int)($product->pivot->quantity ?? 0);
                $price = (float)($product->pivot->price ?? 0);
                $lineTotal = $qty * $price;
            @endphp
            <tr>
                <td>
                    <strong>{{ $product->title ?? $product->name ?? '-' }}</strong>
                </td>
                <td>{{ $qty }}</td>
                <td>{{ number_format($price, 2) }}</td>
                <td>{{ number_format($lineTotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @php
        $subtotal = (float)$order->products->sum(function($product) {
            $qty = (int)($product->pivot->quantity ?? 0);
            $price = (float)($product->pivot->price ?? 0);
            return $qty * $price;
        });
        $grandTotal = (float)$order->total_price;
    @endphp

    <div class="totals">
        <table>
            <tbody>
            <tr>
                <td class="label">Subtotal</td>
                <td class="value" style="text-align:right; font-weight:700;">{{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Grand Total</td>
                <td class="value" style="text-align:right; font-weight:700;">{{ number_format($grandTotal, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Thank you for shopping with us.
    </div>
</div>
</body>
</html>

