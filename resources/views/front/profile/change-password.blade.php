@extends('layouts.eshopper')

@section('content')
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Change Password</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="{{ route('profile.dashboard') }}">My Account</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Change Password</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="bg-light p-4">
                    <h5 class="font-weight-semi-bold mb-4">Change Your Password</h5>
                    <form method="POST" action="{{ route('profile.update-password') }}">
                        @csrf
                        <div class="form-group">
                            <label>Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Enter current password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="Enter new password (min. 8 characters)">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Update Password</button>
                        <a href="{{ route('profile.dashboard') }}" class="btn btn-outline-dark ml-2 px-4">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
