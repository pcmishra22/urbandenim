<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping Label - Order #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .page { width: 100%; max-width: 720px; margin: 0 auto; border: 2px solid #222; padding: 14px; }
        .row { display: flex; justify-content: space-between; gap: 12px; }
        .box { border: 1px solid #ddd; padding: 10px; flex: 1; }
        .title { font-size: 18px; font-weight: 800; margin-bottom: 4px; }
        .muted { color: #666; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        h4 { margin: 0 0 8px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 6px 0; border-bottom: 1px dashed #e5e5e5; }
        .badge { display:inline-block; padding: 4px 8px; border-radius: 10px; background: #e9f2ff; color:#0b5ed7; font-weight:800; }
        .footer { margin-top: 12px; color: #666; font-size: 11px; }
    </style>
</head>
<body>
@php
    $shipment = $order->shipments->first();
    $courier = $shipment?->courier;
@endphp

<div class="page">
    <div class="row">
        <div>
            <div class="title">Shipping Label</div>
            <div class="muted">Order #{{ $order->id }}</div>
            <div class="muted">Created: {{ $order->created_at?->format('Y-m-d') }}</div>
        </div>
        <div style="text-align:right;">
            <div class="badge">{{ $shipment?->status ?? $order->status }}</div>
            <div class="muted" style="margin-top:8px;">Courier: {{ $courier?->name ?? '-' }}</div>
        </div>
    </div>

    <div class="row" style="margin-top: 12px;">
        <div class="box">
            <h4>Ship To</h4>
            <div><strong>{{ $order->user->name ?? '-' }}</strong></div>
            <div class="muted">{{ $order->user->email ?? '' }}</div>
            <div class="muted" style="margin-top:8px;">(Address data not yet connected)</div>
        </div>
        <div class="box">
            <h4>Shipment Details</h4>
            <table>
                <tr><td><strong>Tracking ID:</strong> <span class="mono">{{ $shipment?->tracking_id ?? '-' }}</span></td></tr>
                <tr><td><strong>Shipped At:</strong> {{ $shipment?->shipped_at?->format('Y-m-d') ?? '-' }}</td></tr>
                <tr><td><strong>Delivered At:</strong> {{ $shipment?->delivered_at?->format('Y-m-d') ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Barcode rendering is pending integration with actual label/PDF tooling.
    </div>
</div>
</body>
</html>

