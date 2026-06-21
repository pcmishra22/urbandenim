<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:20px;}
.wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:#D19C97;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:1.4rem;}
.header p{color:rgba(255,255,255,.9);margin:6px 0 0;font-size:.9rem;}
.body{padding:28px 32px;}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:.9rem;}
.info-row:last-child{border-bottom:none;}
.label{color:#888;}
.value{font-weight:600;color:#333;}
.badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:700;background:#fff3cd;color:#856404;}
.reason-box{background:#faf8f8;border-left:4px solid #D19C97;border-radius:0 8px 8px 0;padding:12px 16px;margin:16px 0;font-size:.88rem;color:#555;}
.action-btn{display:inline-block;background:#D19C97;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:.92rem;margin-top:16px;}
.footer{background:#faf8f8;padding:16px 32px;text-align:center;font-size:.78rem;color:#aaa;}
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>📦 Return Request Received</h1>
        <p>{{ $recipientType === 'vendor' ? 'A customer has requested a return for your product' : 'A customer has submitted a return request' }}</p>
    </div>
    <div class="body">
        <div class="info-row"><span class="label">Return Request #</span><span class="value">#{{ $returnRequest->id }}</span></div>
        <div class="info-row"><span class="label">Order #</span><span class="value">#{{ $returnRequest->order_id }}</span></div>
        <div class="info-row"><span class="label">Customer</span><span class="value">{{ $returnRequest->user->name ?? 'N/A' }} ({{ $returnRequest->user->email ?? '' }})</span></div>
        <div class="info-row"><span class="label">Return Type</span><span class="value">{{ ucfirst($returnRequest->type ?? 'return') }}</span></div>
        <div class="info-row"><span class="label">Status</span><span class="value"><span class="badge">Requested</span></span></div>
        <div class="info-row"><span class="label">Requested On</span><span class="value">{{ $returnRequest->created_at->format('d M Y, h:i A') }}</span></div>

        @if($returnRequest->reason)
        <div class="reason-box">
            <strong>Reason:</strong> {{ $returnRequest->reason }}<br>
            @if($returnRequest->description)
            <span style="margin-top:4px;display:block;">{{ $returnRequest->description }}</span>
            @endif
        </div>
        @endif

        @if($returnRequest->refund_amount)
        <div class="info-row"><span class="label">Refund Amount</span><span class="value" style="color:#27ae60;">₹{{ number_format($returnRequest->refund_amount, 2) }}</span></div>
        @endif

        @if($recipientType === 'vendor')
        <p style="font-size:.88rem;color:#555;margin-top:16px;">Please log in to your Jeanzo vendor dashboard to review this return request and initiate the return/refund process.</p>
        <a href="{{ url('/vendor/dashboard') }}" class="action-btn">View in Vendor Dashboard →</a>
        @else
        <p style="font-size:.88rem;color:#555;margin-top:16px;">Please review this return request in the admin panel and take appropriate action.</p>
        <a href="{{ url('/admin/returns/' . $returnRequest->id) }}" class="action-btn">Review in Admin Panel →</a>
        @endif
    </div>
    <div class="footer">Jeanzo · support@jeanzo.in · This is an automated notification</div>
</div>
</body>
</html>
