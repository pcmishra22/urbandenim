@extends('layouts.eshopper')

@section('content')
    @include('front.partials.page-banner', ['title' => 'Change Password', 'breadcrumb' => 'Change Password'])
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
