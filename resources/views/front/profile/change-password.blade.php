@extends('layouts.eshopper')
@section('title', 'Change Password | Jeanzo India')
@section('meta_description', 'Update your Jeanzo account password securely. Keep your account safe by using a strong, unique password.')
@section('meta_robots', 'noindex, nofollow')

@section('content')

<div class="container-fluid pb-5" style="background:#faf8f8;">
    <div class="row px-xl-5 pt-4">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="j-section" style="max-width:520px;">
                <div class="j-section-title"><i class="fa fa-lock mr-2" style="color:var(--j-primary);"></i>Change Your Password</div>

                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-600">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Enter current password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="At least 8 characters">
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-5">Update Password</button>
                        <a href="{{ route('profile.dashboard') }}" class="btn btn-outline-dark px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
