<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Urban Denim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; height: 100vh; }
        .card { border: none; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4">Verify Your Email</h2>
                        <p class="text-muted">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
                        
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success mt-3">
                                A new verification link has been sent to the email address you provided during registration.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.resend') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg px-5">Resend Verification Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>