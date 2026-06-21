<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{padding:28px 32px;text-align:center;}
.header h1{margin:0;font-size:1.4rem;}
.body{padding:28px 32px;}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:.9rem;}
.info-row:last-child{border-bottom:none;}
.label{color:#888;}
.value{font-weight:600;color:#333;}
.note-box{background:#f0faf4;border-left:4px solid #27ae60;border-radius:0 8px 8px 0;padding:12px 16px;margin:16px 0;font-size:.88rem;color:#333;}
.action-btn{display:inline-block;background:#D19C97;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:.92rem;margin-top:16px;}
.footer{background:#faf8f8;padding:16px 32px;text-align:center;font-size:.78rem;color:#aaa;}
@php
$isApproved = in_array($returnRequest->status, ['approved','pickup_requested','pickup_received','refund_wallet_queued','refund_completed']);
$isRejected = $returnRequest->status === 'rejected';
@endphp
</style>
</head>
<body>
<div class="wrap">
    <div class="header" style="background:{{ $isRejected ? '#fff5f5' : ($isApproved ? '#f0faf4' : '#faf8f8') }};">
        <div style="font-size:2.5rem;">{{ $isRejected ? '❌' : ($isApproved ? '✅' : '🔄') }}</div>
        <h1 style="color:{{ $isRejected ? '#c0392b' : ($isApproved ? '#1b5e20' : '#333') }};">
            Return Request {{ $returnRequest->status_label }}
        </h1>
        <p style="color:#666;margin:6px 0 0;font-size:.88rem;">Return Request #{{ $returnRequest->id }} · Order #{{ $returnRequest->order_id }}</p>
    </div>
    <div class="body">
        <p style="color:#444;font-size:.92rem;">Hi <strong>{{ $returnRequest->user->name ?? 'Customer' }}</strong>,</p>

        @if($isApproved)
        <p style="color:#444;font-size:.88rem;line-height:1.7;">
            Great news! Your return request has been <strong style="color:#27ae60;">{{ strtolower($returnRequest->status_label) }}</strong>.
            @if($returnRequest->status === 'pickup_requested')
            Our team will arrange a reverse pickup from your address soon.
            @elseif($returnRequest->status === 'refund_completed')
            Your refund has been processed. Please allow 5–7 business days for it to reflect.
            @else
            We will keep you updated on the next steps via email.
            @endif
        </p>
        @elseif($isRejected)
        <p style="color:#444;font-size:.88rem;line-height:1.7;">
            Unfortunately, your return request has been <strong style="color:#c0392b;">rejected</strong>.
            If you believe this is an error, please contact our support team at
            <a href="mailto:support@jeanzo.in">support@jeanzo.in</a>.
        </p>
        @else
        <p style="color:#444;font-size:.88rem;line-height:1.7;">
            Your return request status has been updated to <strong>{{ $returnRequest->status_label }}</strong>.
        </p>
        @endif

        <div class="info-row"><span class="label">Return Request #</span><span class="value">#{{ $returnRequest->id }}</span></div>
        <div class="info-row"><span class="label">Order #</span><span class="value">#{{ $returnRequest->order_id }}</span></div>
        <div class="info-row"><span class="label">Status</span><span class="value">{{ $returnRequest->status_label }}</span></div>
        @if($returnRequest->refund_amount)
        <div class="info-row"><span class="label">Refund Amount</span><span class="value" style="color:#27ae60;">₹{{ number_format($returnRequest->refund_amount, 2) }}</span></div>
        @endif

        @if($returnRequest->vendor_note)
        <div class="note-box">
            <strong>Note from seller:</strong><br>{{ $returnRequest->vendor_note }}
        </div>
        @endif

        <a href="{{ url('/profile/orders/' . $returnRequest->order_id) }}" class="action-btn">View Order Details →</a>
    </div>
    <div class="footer">Jeanzo · support@jeanzo.in · Questions? Reply to this email</div>
</div>
</body>
</html>
