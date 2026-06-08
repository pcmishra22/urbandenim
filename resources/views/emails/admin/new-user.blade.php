@extends('emails.layout', ['headerColor' => '#1e2a3a', 'headerSubtitle' => "Admin Alert"])

@section('content')
<h2>👤 New Customer Registered</h2>
    <p>A new customer has registered on EShopper.</p>

    <div class="info">
        <p><span class="label">Name</span>{{ $user->name }}</p>
        <p><span class="label">Email</span>{{ $user->email }}</p>
        <p><span class="label">Registered At</span>{{ $user->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div class="btn-wrap">
        <a href="{{ route('admin.dashboard') }}" class="btn">Go to Admin Panel</a>
    </div>
@endsection
