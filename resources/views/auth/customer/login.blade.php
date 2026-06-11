@extends('layouts.eshopper')

@section('title', 'Customer Login')

@section('content')
    <div class="container" style="padding-top: 30px;">
        <div class="row justify-content-center">

            <div class="col-lg-6 col-md-8">
                <div class="auth-theme-card shadow-sm">
                    <div class="auth-theme-header">
                        <div class="auth-theme-title">Login</div>
                    </div>

                    <div class="auth-theme-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form action="{{ route('customer.login.submit') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3 text-end">
                                <a href="{{ route('customer.password.request') }}" class="small text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3">Sign In</button>
                        </form>

                        <div class="mt-3 text-center">
                            <span class="text-muted">Don’t have an account?</span>
                            <a href="{{ route('customer.register') }}" class="btn btn-outline-primary w-100 mt-2">Create Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .auth-theme-card {
            border-radius: 14px;
            border: 2px solid #D19C97; /* match Create Account button background */

            background: #ffffff;
            overflow: hidden;
        }

        .auth-theme-header {
            background: #D19C97; /* match Create Account button background */
            padding: 18px 18px;
        }

        .auth-theme-title {
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            font-size: 1.35rem;
            letter-spacing: 0.2px;
        }

        .auth-theme-body {
            background: #ffffff;
        }
    </style>
@endsection


