<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;}
.wrap{max-width:600px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);}
.header{background:#2c3e50;padding:28px 32px;text-align:center;}
.header h1{color:#fff;margin:0;font-size:22px;letter-spacing:1px;}
.body{padding:32px;}
.body h2{margin-top:0;color:#2c3e50;}
.btn{display:inline-block;padding:12px 28px;background:#2c3e50;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;margin-top:16px;}
.footer{background:#f5f5f5;padding:16px 32px;text-align:center;font-size:12px;color:#888;}
</style></head>
<body>
<div class="wrap">
    <div class="header"><h1>🛍 EShopper</h1></div>
    <div class="body">
        <h2>Welcome, {{ $user->name }}! 🎉</h2>
        <p>Thank you for registering at <strong>EShopper</strong>. Your account is ready to use.</p>
        <p>You can now browse our collection, place orders, track your deliveries, and manage your wishlist.</p>
        <a href="{{ url('/') }}" class="btn">Start Shopping</a>
        <hr style="margin:24px 0;border:none;border-top:1px solid #eee;">
        <p style="font-size:13px;color:#666;margin:0;">
            If you didn't create this account, please ignore this email.
        </p>
    </div>
    <div class="footer">© {{ date('Y') }} EShopper. All rights reserved.</div>
</div>
</body></html>
