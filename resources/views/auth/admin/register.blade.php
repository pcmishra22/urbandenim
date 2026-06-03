@extends('layouts.app')

@section('title', 'Create Admin Account')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-user-shield" style="font-size: 3rem; color: var(--admin-color);"></i>
        </div>
        
        <h2>Create Admin</h2>
        <p class="text-muted">Create a new admin account</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.register.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Admin name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@email.com">
                @error('email')
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

            <button type="submit" class="btn btn-primary mb-3" style="background: linear-gradient(135deg, var(--admin-color) 0%, #c0392b 100%); border: none;">
                <i class="fas fa-user-shield"></i> Create Admin
            </button>
        </form>

        <div class="divider">
            <span>Go back</span>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger w-100">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
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
