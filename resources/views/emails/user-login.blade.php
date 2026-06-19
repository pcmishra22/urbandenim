@extends('emails.layout', ['headerColor' => '#2c3e50', 'headerSubtitle' => 'Security Alert'])

@section('content')
<h2>New Login Detected 🔐</h2>
<p>Hi <strong>{{ $user->name }}</strong>, we noticed a new login to your Jeanzo account.</p>

<div class="info">
    <p><span class="label">Account</span>{{ $user->email }}</p>
    <p><span class="label">Login Time</span>{{ $loginAt }}</p>
    <p><span class="label">IP Address</span>{{ $ip }}</p>
</div>

<p style="font-size:14px;color:#555;">If this was you, no action is needed. If you did not log in, please secure your account immediately.</p>

<div class="btn-wrap">
    <a href="{{ route('contact') }}" class="btn">Contact Support</a>
</div>

<hr class="divider">
<p style="font-size:12px;color:#aaa;text-align:center;">
    This is an automated security notification from Jeanzo. Please do not reply to this email.
</p>
@endsection
