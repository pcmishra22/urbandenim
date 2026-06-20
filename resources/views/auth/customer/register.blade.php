@extends('layouts.eshopper')
@section('title', 'Create a Jeanzo Account | India')
@section('meta_description', 'Create a free Jeanzo account to enjoy faster checkout, order tracking, wishlist and exclusive member offers on premium denim jeans.')
@section('meta_robots', 'noindex, follow')

@section('content')
    <div class="container" style="padding-top:30px;padding-bottom:40px;">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">

                {{-- Guest checkout nudge --}}
                <div class="text-center mb-3" style="font-size:.88rem;color:#555;">
                    Just want to buy? <a href="{{ route('checkout.index') }}" style="color:var(--j-primary);font-weight:600;">Checkout as guest — no account needed →</a>
                </div>

                <div class="auth-theme-card shadow-sm">
                    <div class="auth-theme-header">
                        <div class="auth-theme-title">Create Account</div>
                        <div class="auth-theme-subtitle">Save your address, track orders & get member offers</div>
                    </div>

                    <div class="auth-theme-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                @foreach ($errors->all() as $error)
                                    <div><i class="fa fa-exclamation-circle mr-1"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        @php($captchaQuestion = \App\Services\SimpleCaptcha::generate())

                        <form action="{{ route('customer.register.submit') }}" method="POST" autocomplete="off">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-600">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="e.g. Priya Sharma" required autocomplete="name">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-600">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       placeholder="your@email.com" required autocomplete="email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-600">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="Min. 8 characters" required autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                                onclick="togglePassword()" tabindex="-1">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-600">Confirm Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Re-enter password" required autocomplete="new-password">
                            </div>

                            {{-- Captcha — at bottom so it doesn't intimidate --}}
                            <div class="mb-4 p-3" style="background:#f8f9fa;border-radius:10px;border:1px dashed #ddd;">
                                <label class="form-label fw-600 mb-1" style="font-size:.88rem;">
                                    <i class="fa fa-robot mr-1" style="color:#888;"></i>Quick check: What is
                                    <strong style="color:var(--j-primary);">{{ $captchaQuestion }}</strong>?
                                </label>
                                <input type="text" inputmode="numeric"
                                       class="form-control @error('captcha_answer') is-invalid @enderror"
                                       name="captcha_answer" placeholder="Enter answer" required
                                       style="max-width:160px;">
                                @error('captcha_answer')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3" style="font-size:1rem;font-weight:600;">
                                <i class="fa fa-user-plus mr-2"></i>Create My Account
                            </button>

                        </form>

                        <div class="mt-3 text-center">
                            <span class="text-muted small">Already have an account?</span><br>
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary w-100 mt-2">Sign In</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .auth-theme-card{border-radius:14px;border:2px solid #D19C97;background:#fff;overflow:hidden}
        .auth-theme-header{background:#D19C97;padding:18px 20px;text-align:center}
        .auth-theme-title{color:#fff;font-weight:700;font-size:1.35rem;letter-spacing:.2px}
        .auth-theme-subtitle{color:rgba(255,255,255,.85);font-size:.8rem;margin-top:4px}
        .auth-theme-body{background:#fff}
        .fw-600{font-weight:600}
    </style>

    <script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>

@endsection
