@extends('emails.layout', ['headerColor' => '#2c3e50', 'headerSubtitle' => "Welcome aboard! 🎉"])

@section('content')
<h2>Welcome, {{ $user->name }}! 🎉</h2>
    <p>Thank you for joining <strong>Jeanzo</strong>. Your account is ready and you can start shopping right away.</p>

    <div class="info">
        <p><span class="label">Name</span>{{ $user->name }}</p>
        <p><span class="label">Email</span>{{ $user->email }}</p>
        <p><span class="label">Member Since</span>{{ $user->created_at->format('d M Y') }}</p>
    </div>

    <p>Here's what you can do with your account:</p>
    <ul style="margin:10px 0 16px 20px;font-size:14px;color:#555;line-height:2;">
        <li>Browse thousands of fashion products</li>
        <li>Track your orders in real-time</li>
        <li>Save items to your wishlist</li>
        <li>Manage multiple delivery addresses</li>
        <li>Write reviews for purchased products</li>
    </ul>

    <div class="btn-wrap">
        <a href="{{ url('/') }}" class="btn">Start Shopping</a>
    </div>

    <hr class="divider">
    <p style="font-size:12px;color:#aaa;text-align:center;">
        If you didn't create this account, please ignore this email or <a href="{{ route('contact') }}">contact support</a>.
    </p>
@endsection
