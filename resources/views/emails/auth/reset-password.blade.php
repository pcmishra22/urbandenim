@extends('emails.layout', ['headerColor' => '#2c3e50', 'headerSubtitle' => "Password Reset Request"])

@section('content')
<h2>Reset Your Password</h2>
    <p>Hi {{ $user->name }}, we received a request to reset your EShopper account password.</p>

    <div class="alert alert-warning">
        <p style="margin:0;font-size:14px;">This link will expire in <strong>60 minutes</strong>. If you did not request a password reset, no action is needed.</p>
    </div>

    <div class="btn-wrap">
        <a href="{{ $resetUrl }}" class="btn">Reset Password</a>
    </div>

    <hr class="divider">
    <p style="font-size:13px;color:#888;">If the button doesn't work, copy and paste this URL into your browser:</p>
    <p style="font-size:12px;word-break:break-all;color:#aaa;">{{ $resetUrl }}</p>
    <hr class="divider">
    <p style="font-size:12px;color:#aaa;text-align:center;">
        If you did not request a password reset, please <a href="{{ route('contact') }}">contact support</a> immediately.
    </p>
@endsection
