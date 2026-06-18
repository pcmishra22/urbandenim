@extends('layouts.eshopper')
@section('title', 'Create a Jeanzo Account | India')
@section('meta_description', 'Create a free Jeanzo account to enjoy faster checkout, order tracking, wishlist and exclusive member offers on premium denim jeans.')
@section('meta_robots', 'noindex, follow')

@section('content')
    <div class="container" style="padding-top: 30px;">
        <div class="row justify-content-center">

            <div class="col-lg-6 col-md-8">
                <div class="auth-theme-card shadow-sm">
                    <div class="auth-theme-header">
                        <div class="auth-theme-title">Register</div>

                    </div>

                    <div class="auth-theme-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('customer.register.submit') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3">Create Account</button>
                        </form>

                        <div class="mt-3 text-center">
                            <span class="text-muted">Already have an account?</span>
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary w-100 mt-2">Sign In</a>
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
            background: #D19C97;

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


