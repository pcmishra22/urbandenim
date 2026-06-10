<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping Label — Order #{{ $order->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; background: #fff; }
        .page { width: 740px; margin: 20px auto; border: 3px solid #111; padding: 0; }

        /* Header bar */
        .label-header { background: #111; color: #fff; padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; }
        .label-header .brand { font-size: 18px; font-weight: 900; letter-spacing: 1px; }
        .label-header .order-no { font-size: 20px; font-weight: 900; font-family: monospace; }

        /* Two-column section */
        .section-row { display: flex; border-bottom: 2px solid #111; }
        .section-box { flex: 1; padding: 12px 14px; }
        .section-box + .section-box { border-left: 2px solid #111; }
        .section-label { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #555; margin-bottom: 6px; }
        .section-value { font-size: 13px; font-weight: 700; line-height: 1.5; }
        .section-value.muted { font-weight: 400; color: #333; }

        /* Items table */
        .items-section { padding: 12px 14px; border-bottom: 2px solid #111; }
        .items-section table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .items-section th { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; font-weight: 700; border-bottom: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        .items-section td { padding: 5px 6px; border-bottom: 1px dashed #e5e5e5; font-size: 11px; }
        .items-section td:last-child { text-align: right; }

        /* Footer */
        .label-footer { padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; background: #f7f7f7; }
        .barcode { font-family: monospace; font-size: 22px; letter-spacing: 4px; }
        .status-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; background: #e9f2ff; color: #0b5ed7; font-weight: 900; font-size: 13px; border: 1px solid #0b5ed7; }
        .print-btn { display: none; }
        @media print { .print-btn { display: none !important; } }
    </style>
</head>
<body>
@php
    $shipment = $order->shipments ? $order->shipments->first() : null;
    $courier  = $shipment?->courier ?? null;
@endphp

<div style="text-align:center; margin: 10px 0;">
    <button onclick="window.print()" class="print-btn"
        style="display:inline-block; padding:8px 20px; background:#0b5ed7; color:#fff; border:none; border-radius:4px; font-size:13px; cursor:pointer; margin-bottom:10px;">
        🖨 Print Label
    </button>
</div>

<div class="page">

    <!-- Header -->
    <div class="label-header">
        <div class="brand">📦 Jeanzo</div>
        <div style="text-align:center;">
            <div style="font-size:10px; letter-spacing:1px; opacity:.7;">SHIPPING LABEL</div>
            <div class="order-no">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div style="text-align:right; font-size:11px;">
            <div>{{ $order->created_at?->format('d M Y') }}</div>
            <div style="margin-top:4px;"><span class="status-badge">{{ strtoupper($order->status) }}</span></div>
        </div>
    </div>

    <!-- Ship To / Ship From -->
    <div class="section-row">
        <div class="section-box">
            <div class="section-label">📍 Ship To</div>
            <div class="section-value">{{ $order->shipping_full_name ?? $order->user?->name ?? '—' }}</div>
            <div class="section-value muted">
                @if($order->shipping_phone) 📞 {{ $order->shipping_phone }}<br>@endif
                @if($order->shipping_street) {{ $order->shipping_street }}<br>@endif
                @if($order->shipping_city)
                    {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif
                    @if($order->shipping_postal_code) — {{ $order->shipping_postal_code }}@endif
                    <br>
                @endif
                {{ $order->shipping_country ?? 'India' }}
            </div>
        </div>
        <div class="section-box">
            <div class="section-label">🏬 Ship From</div>
            <div class="section-value">Jeanzo Store</div>
            <div class="section-value muted">
                {{ config('app.address', '123, Main Market') }}<br>
                {{ config('app.city', 'Ludhiana') }}, Punjab — 141001<br>
                India<br>
                📞 {{ config('app.phone', '+91-9999999999') }}
            </div>
        </div>
    </div>

    <!-- Customer / Payment -->
    <div class="section-row">
        <div class="section-box">
            <div class="section-label">👤 Customer</div>
            <div class="section-value">{{ $order->user?->name ?? '—' }}</div>
            <div class="section-value muted">{{ $order->user?->email ?? '—' }}</div>
        </div>
        <div class="section-box">
            <div class="section-label">💳 Payment</div>
            <div class="section-value">
                @if($order->payment_method === 'cod') Cash on Delivery
                @elseif($order->payment_method === 'upi') UPI / Net Banking
                @else Credit / Debit Card
                @endif
            </div>
            <div class="section-value muted">
                Status: <strong>{{ ucfirst($order->payment_status ?? 'pending') }}</strong>
            </div>
        </div>
        <div class="section-box">
            <div class="section-label">🚚 Courier</div>
            <div class="section-value">{{ $courier?->name ?? 'Not assigned' }}</div>
            @if($shipment?->tracking_number)
            <div class="section-value muted">Tracking: <strong style="font-family:monospace;">{{ $shipment->tracking_number }}</strong></div>
            @endif
        </div>
    </div>

    <!-- Items -->
    <div class="items-section">
        <div class="section-label">📦 Items Ordered</div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td style="font-family:monospace; color:#555;">{{ $product->sku ?? '—' }}</td>
                    <td style="text-align:center;">{{ $product->pivot->quantity }}</td>
                    <td>₹{{ number_format($product->pivot->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals + Barcode -->
    <div class="label-footer">
        <div>
            <div class="barcode">||| {{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }} |||</div>
            <div style="font-size:10px; color:#555; text-align:center; margin-top:2px; letter-spacing:2px;">ORDER ID</div>
        </div>
        <div style="text-align:right; font-size:12px; line-height:1.8;">
            @if($order->subtotal)
            <div>Subtotal: ₹{{ number_format($order->subtotal, 2) }}</div>
            @endif
            @if($order->shipping_cost !== null)
            <div>Shipping: @if($order->shipping_cost == 0)<span style="color:green;">Free</span>@else ₹{{ number_format($order->shipping_cost, 2) }}@endif</div>
            @endif
            @if($order->discount_amount > 0)
            <div style="color:green;">Discount: - ₹{{ number_format($order->discount_amount, 2) }}</div>
            @endif
            <div style="font-size:15px; font-weight:900; border-top:2px solid #111; margin-top:4px; padding-top:4px;">
                TOTAL: ₹{{ number_format($order->total_price, 2) }}
            </div>
        </div>
    </div>

</div>

<script>window.onload = function() { window.print(); }</script>
</body>
</html>
