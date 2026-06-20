@extends('layouts.eshopper')
@section('title', 'Become a Supplier — Sell on Jeanzo')
@section('meta_description', 'Join Jeanzo as a supplier. List your denim products, reach thousands of customers across India, and grow your business.')

@section('content')
<div style="background:linear-gradient(135deg,#f8f4f4 0%,#fff 100%);min-height:100vh;padding:40px 0 60px;">
<div class="container">
<div class="row align-items-start justify-content-between">

    {{-- Left: Benefits --}}
    <div class="col-lg-5 d-none d-lg-block" style="padding-top:30px;">
        <div style="font-size:.82rem;font-weight:600;color:var(--j-primary);text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">
            <i class="fas fa-store mr-1"></i> Jeanzo Supplier Program
        </div>
        <h2 style="font-size:2rem;font-weight:800;color:#1a1a1a;line-height:1.25;margin-bottom:16px;">
            Sell Your Denim<br>to Customers<br><span style="color:var(--j-primary);">Across India</span>
        </h2>
        <p style="color:#555;line-height:1.7;margin-bottom:28px;">
            Join Jeanzo's supplier network and get your products in front of thousands of jeans buyers.
            Simple onboarding, transparent pricing, fast settlements.
        </p>
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach([
                ['fas fa-box-open','List products in minutes','Upload photos, set your price, go live instantly.'],
                ['fas fa-rupee-sign','Transparent pricing','You set your price. Jeanzo adds courier + margin. No hidden fees.'],
                ['fas fa-chart-line','Track orders & earnings','Real-time dashboard for sales, inventory, and payouts.'],
                ['fas fa-star','Build your reputation','Customers rate and review your service. Great sellers get featured.'],
            ] as [$icon,$title,$desc])
            <div style="display:flex;gap:14px;align-items:flex-start;">
                <div style="width:40px;height:40px;border-radius:10px;background:#fff4f4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="{{ $icon }}" style="color:var(--j-primary);font-size:1rem;"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.92rem;color:#222;">{{ $title }}</div>
                    <div style="font-size:.82rem;color:#888;margin-top:2px;">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 pt-3" style="border-top:1px solid #eee;font-size:.82rem;color:#888;">
            Already a supplier? <a href="{{ route('vendor.login') }}" style="color:var(--j-primary);font-weight:600;">Sign in to your dashboard →</a>
        </div>
    </div>

    {{-- Right: Registration form --}}
    <div class="col-lg-6 col-md-8 col-sm-12">
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 40px rgba(0,0,0,.08);overflow:hidden;">
            <div style="background:var(--j-primary);padding:24px 28px;">
                <div style="font-size:1.2rem;font-weight:700;color:#fff;">Create Supplier Account</div>
                <div style="font-size:.82rem;color:rgba(255,255,255,.85);margin-top:4px;">Free to join. Start selling in minutes.</div>
            </div>
            <div style="padding:28px;">

                @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    @foreach ($errors->all() as $error)
                        <div style="font-size:.85rem;"><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('vendor.register.submit') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" style="font-weight:600;font-size:.88rem;">Your Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" style="font-weight:600;font-size:.88rem;">Shop / Brand Name</label>
                            <input type="text" class="form-control @error('shop_name') is-invalid @enderror"
                                   name="shop_name" value="{{ old('shop_name') }}" placeholder="e.g. DenimCraft India" required>
                            @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:.88rem;">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" placeholder="supplier@email.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;font-size:.88rem;">Phone Number</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" style="font-weight:600;font-size:.88rem;">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" placeholder="Min. 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" style="font-weight:600;font-size:.88rem;">Confirm Password</label>
                            <input type="password" class="form-control"
                                   name="password_confirmation" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="terms" required>
                            <label class="custom-control-label" for="terms" style="font-size:.82rem;color:#555;">
                                I agree to Jeanzo's
                                <a href="{{ route('legal.terms') }}" target="_blank" style="color:var(--j-primary);">Supplier Terms</a>
                                and
                                <a href="{{ route('legal.privacy') }}" target="_blank" style="color:var(--j-primary);">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block py-3" style="font-size:1rem;font-weight:700;border-radius:10px;">
                        <i class="fas fa-store mr-2"></i>Create Supplier Account — It's Free
                    </button>
                </form>

                <div class="text-center mt-3" style="font-size:.82rem;color:#888;">
                    Already have an account?
                    <a href="{{ route('vendor.login') }}" style="color:var(--j-primary);font-weight:600;">Sign in →</a>
                </div>

            </div>
        </div>
    </div>

</div>
</div>
</div>
@endsection
