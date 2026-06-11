@extends('layouts.eshopper')

@section('content')
    @include('front.partials.page-banner', ['title' => 'Personal Info', 'breadcrumb' => 'Personal Info'])
</div>
    </div>
    <!-- Page Header End -->

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            <!-- Sidebar -->
            @include('front.partials.profile-sidebar')

            <!-- Main Content -->
            <div class="col-lg-9 mb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                    </div>
                @endif

                <div class="bg-light p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <h5 class="font-weight-semi-bold mb-0">Edit Personal Information</h5>
                        <span class="text-muted small">Update your profile details anytime.</span>
                    </div>

                    <form method="POST" action="{{ route('profile.update-personal-info') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $user->first_name ?? '') }}" placeholder="John" autocomplete="given-name">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $user->last_name ?? '') }}" placeholder="Doe" autocomplete="family-name">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control bg-white" value="{{ $user->email }}" disabled>
                                <small class="text-muted">Email cannot be changed here.</small>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone ?? '') }}" placeholder="+91 99999 99999" autocomplete="tel">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                       value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}" autocomplete="bday">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 d-flex align-items-center flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            <a href="{{ route('profile.dashboard') }}" class="btn btn-outline-dark px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

