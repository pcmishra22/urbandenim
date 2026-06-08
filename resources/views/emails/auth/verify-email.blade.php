@extends('emails.layout', ['headerColor' => '#2c3e50', 'headerSubtitle' => "Please verify your email address"])

@section('content')
<h2>Verify Your Email Address</h2>
    <p>Hi {{ $user->name }}, thanks for registering! Please verify your email address by clicking the button below.</p>

    <div class="alert alert-info">
        <p style="margin:0;font-size:14px;">This verification link will expire in <strong>60 minutes</strong>.</p>
    </div>

    <div class="btn-wrap">
        <a href="{{ $verificationUrl }}" class="btn">✓ Verify Email Address</a>
    </div>

    <hr class="divider">
    <p style="font-size:13px;color:#888;">If the button doesn't work, copy and paste this link into your browser:</p>
    <p style="font-size:12px;word-break:break-all;color:#aaa;">{{ $verificationUrl }}</p>
    <hr class="divider">
    <p style="font-size:12px;color:#aaa;text-align:center;">
        If you did not create an account, no further action is required.
    </p>
@endsection
