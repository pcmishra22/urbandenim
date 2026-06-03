@extends('layouts.app')

@section('title', 'Vendor Registration')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-user-tie" style="font-size: 3rem; color: var(--vendor-color);"></i>
        </div>
        
        <h2>Become a Vendor</h2>
        <p class="text-muted">Register your store and start selling</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('vendor.register.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Your full name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="vendor@email.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="shop_name" class="form-label">Shop Name</label>
                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required placeholder="Your shop name">
                @error('shop_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Min. 6 characters">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password">
            </div>

            <button type="submit" class="btn btn-primary mb-3" style="background: linear-gradient(135deg, var(--vendor-color) 0%, #8e44ad 100%); border: none;">
                <i class="fas fa-user-tie"></i> Create Vendor Account
            </button>
        </form>

        <div class="divider">
            <span>Already have an account?</span>
        </div>

        <a href="{{ route('vendor.login') }}" class="btn btn-outline-primary w-100" style="color: var(--vendor-color); border-color: var(--vendor-color);">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </a>
    </div>
</div>

<style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--vendor-color) 0%, #8e44ad 100%);
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
