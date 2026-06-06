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
.btn{display:inline-block;padding:10px 24px;background:#1e2a3a;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;}
.footer{background:#f5f5f5;padding:14px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>👤 New Customer Registered</h1></div>
    <div class="body">
        <p>A new customer has registered on EShopper.</p>
        <div class="info-box">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Registered:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn">Go to Admin Panel</a>
    </div>
    <div class="footer">EShopper Admin Notification</div>
</div>
</body></html>
