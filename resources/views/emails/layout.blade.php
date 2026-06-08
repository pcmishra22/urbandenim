<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EShopper</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:Arial,Helvetica,sans-serif;background:#f0f2f5;color:#333;-webkit-font-smoothing:antialiased;}
  .outer{max-width:620px;margin:32px auto;padding:0 16px 40px;}
  .card{background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);}
  .hdr{padding:28px 36px;text-align:center;}
  .hdr .logo{font-size:24px;font-weight:900;color:#fff;letter-spacing:1px;}
  .hdr .logo span{background:rgba(255,255,255,.25);padding:2px 10px;border-radius:4px;margin-right:4px;}
  .hdr .subtitle{color:rgba(255,255,255,.85);font-size:13px;margin-top:6px;}
  .body{padding:36px;}
  .body h2{font-size:20px;color:#2c3e50;margin-bottom:12px;}
  .body p{font-size:14px;line-height:1.7;color:#555;margin-bottom:12px;}
  .badge{display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;}
  .badge-pending{background:#fff3cd;color:#856404;}
  .badge-confirmed{background:#cfe2ff;color:#084298;}
  .badge-packed{background:#e2d9f3;color:#432874;}
  .badge-shipped{background:#d1ecf1;color:#0c5460;}
  .badge-delivered{background:#d4edda;color:#155724;}
  .badge-cancelled{background:#f8d7da;color:#721c24;}
  .badge-paid{background:#d4edda;color:#155724;}
  .badge-pending_payment{background:#fff3cd;color:#856404;}
  .badge-awaiting_payment{background:#fff3cd;color:#856404;}
  .info{background:#f8f9fa;border-radius:8px;padding:18px 20px;margin:18px 0;}
  .info p{margin:5px 0;font-size:14px;color:#444;}
  .label{color:#888;font-size:11px;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:2px;margin-top:8px;}
  table{width:100%;border-collapse:collapse;margin:18px 0;font-size:14px;}
  table th{background:#2c3e50;color:#fff;padding:10px 12px;text-align:left;font-size:13px;}
  table td{padding:9px 12px;border-bottom:1px solid #f0f0f0;color:#444;}
  table tfoot td{font-weight:700;border-top:2px solid #2c3e50;background:#f8f9fa;border-bottom:none;}
  .btn-wrap{text-align:center;margin:24px 0 8px;}
  .btn{display:inline-block;padding:13px 32px;background:#2c3e50;color:#fff !important;text-decoration:none;border-radius:6px;font-weight:700;font-size:14px;letter-spacing:.3px;}
  .divider{border:none;border-top:1px solid #eee;margin:20px 0;}
  .alert{border-radius:8px;padding:16px 20px;margin:18px 0;font-size:14px;}
  .alert-success{background:#d4edda;border-left:4px solid #28a745;}
  .alert-info{background:#d1ecf1;border-left:4px solid #17a2b8;}
  .alert-warning{background:#fff3cd;border-left:4px solid #ffc107;}
  .alert-danger{background:#f8d7da;border-left:4px solid #dc3545;}
  /* Order progress steps */
  .steps{display:table;width:100%;table-layout:fixed;margin:20px 0;padding:0;list-style:none;}
  .step{display:table-cell;text-align:center;position:relative;}
  .step:not(:last-child)::after{content:'';position:absolute;top:15px;left:50%;width:100%;height:2px;background:#dee2e6;}
  .step.done::after,.step.active::after{background:#2c3e50;}
  .step-dot{width:30px;height:30px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;position:relative;z-index:1;line-height:1;}
  .step.done .step-dot{background:#2c3e50;color:#fff;}
  .step.active .step-dot{background:#2c3e50;color:#fff;box-shadow:0 0 0 3px rgba(44,62,80,.2);}
  .step.pending .step-dot{background:#dee2e6;color:#888;}
  .step-label{font-size:10px;color:#888;margin-top:5px;display:block;}
  .step.active .step-label,.step.done .step-label{color:#2c3e50;font-weight:700;}
  .footer{text-align:center;padding:18px 36px;font-size:12px;color:#aaa;background:#f8f9fa;border-top:1px solid #eee;line-height:1.8;}
  .footer a{color:#888;text-decoration:none;}
</style>
</head>
<body>
<div class="outer">
  <div class="card">
    <div class="hdr" style="background:{{ $headerColor ?? '#2c3e50' }};">
      <div class="logo"><span>E</span>Shopper</div>
      <div class="subtitle">{{ $headerSubtitle ?? 'Your Fashion Destination' }}</div>
    </div>
    <div class="body">
      @yield('content')
    </div>
    <div class="footer">
      © {{ date('Y') }} EShopper. All rights reserved.<br>
      <a href="{{ url('/') }}">Visit Store</a> &nbsp;·&nbsp;
      <a href="{{ route('contact') }}">Contact Support</a>
    </div>
  </div>
</div>
</body>
</html>
