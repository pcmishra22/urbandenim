@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--admin-color);"></i>
        </div>
        
        <h2>Admin Login</h2>
        <p class="text-muted">Access the admin control panel</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@email.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Enter your password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mb-3" style="background: linear-gradient(135deg, var(--admin-color) 0%, #c0392b 100%); border: none;">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <hr>

        <div style="text-align: center; font-size: 0.9rem; color: #999;">
            <p>Other roles:</p>
            <a href="{{ route('customer.login') }}" class="btn btn-sm btn-outline-primary me-2">
                <i class="fas fa-shopping-cart"></i> Customer Login
            </a>
            <a href="{{ route('vendor.login') }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-store"></i> Vendor Login
            </a>
        </div>
    </div>
</div>

<style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--admin-color) 0%, #c0392b 100%);
    }

    .auth-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        padding: 40px;
        width: 100%;
        max-width: 420px;
    }
</style>
@endsection
