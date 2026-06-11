<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Jeanzo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a3c34 0%, #27ae60 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .verify-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .email-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #27ae60, #1a9b55);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .btn-verify { background: #27ae60; border-color: #27ae60; color: #fff; }
        .btn-verify:hover { background: #219a52; border-color: #219a52; color: #fff; }
        .divider { border-top: 1px solid #f0f0f0; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="verify-card card">
                    <div class="card-body p-5 text-center">

                        <div class="email-icon">
                            <i class="fas fa-envelope-open-text fa-2x text-white"></i>
                        </div>

                        <h2 class="fw-bold mb-2">Check Your Email</h2>
                        <p class="text-muted mb-1">
                            We sent a verification link to
                            @auth <strong>{{ Auth::user()->email }}</strong> @endauth
                        </p>
                        <p class="text-muted small mb-0">Click the link in the email to verify your account. The link expires in 60 minutes.</p>

                        {{-- Success alerts --}}
                        @if(session('status') === 'verification-link-sent')
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-check-circle me-1"></i>
                                New verification link sent! Please check your inbox.
                            </div>
                        @endif
                        @if(session('status') === 'already-verified')
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Your email is already verified.
                            </div>
                        @endif

                        <div class="divider"></div>

                        {{-- PRIMARY: Go to Dashboard without verifying --}}
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-verify btn-lg w-100 mb-3">
                            <i class="fas fa-home me-2"></i> Continue to Dashboard
                        </a>

                        {{-- Resend verification --}}
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 mb-3">
                                <i class="fas fa-redo me-2"></i> Resend Verification Email
                            </button>
                        </form>

                        <div class="divider"></div>

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted text-decoration-none w-100">
                                <i class="fas fa-sign-out-alt me-1"></i> Sign Out &amp; Back to Login
                            </button>
                        </form>

                    </div>
                </div>
                <p class="text-center text-white mt-3 small opacity-75">
                    Didn't receive the email? Check your spam folder or resend above.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
