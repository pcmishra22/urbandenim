@extends('layouts.eshopper')
@section('title', 'Personal Info - Jeanzo')

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

            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-user mr-2" style="color:var(--j-primary);"></i>Edit Personal Information</div>

                <form method="POST" action="{{ route('profile.update-personal-info') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-600">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $user->first_name ?? '') }}" placeholder="John">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-600">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $user->last_name ?? '') }}" placeholder="Doe">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-600">Email Address</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background:#f5f5f5;">
                            <small class="text-muted">Email cannot be changed here.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-600">Phone Number</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone ?? '') }}" placeholder="+91 99999 99999">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-600">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                        <a href="{{ route('profile.dashboard') }}" class="btn btn-outline-dark px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
