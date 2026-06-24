@extends('layouts.eshopper')
@section('title', 'Continue to Checkout | Jeanzo India')
@section('meta_description', 'Enter your email to continue to checkout or continue without email for a fast guest order.')
@section('meta_robots', 'noindex, nofollow')
@section('canonical', route('checkout.index'))

@section('content')
@include('front.partials.design-system')
@include('front.partials.page-banner', ['title' => 'checkout', 'breadcrumb' => 'Checkout', 'showCategories' => false])

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:20px;">
    <div class="row px-xl-5 justify-content-center">
        <div class="col-lg-6 mb-4">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="j-section mb-4" style="background:#fff;border:1px solid #ececec;border-radius:12px;">
                <div class="j-section-title" style="color:#2b2b2b;">
                    <i class="fa fa-envelope mr-2" style="color:var(--j-primary);"></i>Enter your email to continue
                </div>
                <p class="text-muted small mb-4">Use the same email you used before and we will pre-fill your saved shipping details. No password required.</p>

                <form method="POST" action="{{ route('checkout.identify') }}">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-600">Email address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="you@example.com" autofocus required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block py-3 font-weight-bold" style="border-radius:10px;font-size:1rem;">
                        Continue with email
                    </button>
                </form>

                <div class="text-center my-3 text-muted">or</div>

                <form method="POST" action="{{ route('checkout.identify') }}">
                    @csrf
                    <input type="hidden" name="continue_without_email" value="1">
                    <button type="submit" class="btn btn-outline-secondary btn-block py-3 font-weight-bold" style="border-radius:10px;font-size:1rem;">
                        Continue without email
                    </button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Already have an account?
                        <a href="{{ route('customer.login') }}?redirect=checkout" style="color:var(--j-primary);font-weight:600;">Sign in instead →</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
