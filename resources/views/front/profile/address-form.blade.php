@extends('layouts.eshopper')

@section('content')
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">{{ isset($address) ? 'Edit Address' : 'Add Address' }}</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="{{ route('profile.addresses') }}">Addresses</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">{{ isset($address) ? 'Edit' : 'Add' }}</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">
                <div class="bg-light p-4">
                    <h5 class="font-weight-semi-bold mb-4">{{ isset($address) ? 'Edit Address' : 'Add New Address' }}</h5>

                    @if(isset($address))
                        <form method="POST" action="{{ route('profile.address.update', $address->id) }}">
                    @else
                        <form method="POST" action="{{ route('profile.address.store') }}">
                    @endif
                        @csrf

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Address Type <span class="text-danger">*</span></label>
                                <select name="address_type" class="custom-select @error('address_type') is-invalid @enderror">
                                    <option value="">Select type</option>
                                    <option value="billing" {{ old('address_type', $address->address_type ?? '') === 'billing' ? 'selected' : '' }}>Billing</option>
                                    <option value="shipping" {{ old('address_type', $address->address_type ?? '') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                                </select>
                                @error('address_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                       value="{{ old('full_name', $address->full_name ?? '') }}" placeholder="Full name">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $address->phone ?? '') }}" placeholder="+91 99999 99999">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Street Address <span class="text-danger">*</span></label>
                                <input type="text" name="street" class="form-control @error('street') is-invalid @enderror"
                                       value="{{ old('street', $address->street ?? '') }}" placeholder="123 Street Name">
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $address->city ?? '') }}" placeholder="City">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                                       value="{{ old('state', $address->state ?? '') }}" placeholder="State">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Postal Code <span class="text-danger">*</span></label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                       value="{{ old('postal_code', $address->postal_code ?? '') }}" placeholder="Postal Code">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                       value="{{ old('country', $address->country ?? 'India') }}" placeholder="Country">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1"
                                           {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_default">Set as default address</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">{{ isset($address) ? 'Update Address' : 'Save Address' }}</button>
                        <a href="{{ route('profile.addresses') }}" class="btn btn-outline-dark ml-2 px-4">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
