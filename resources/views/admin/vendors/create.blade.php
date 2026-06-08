@extends('layouts.dashboard')

@section('title', 'Create Vendor')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-plus"></i> Create Vendor</h2>
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Vendors
    </a>
</div>

<div class="card mt-3">
    <div class="card-header">Vendor Details</div>
    <div class="card-body">
        <form action="{{ route('admin.vendors.store') }}" method="POST">
            @csrf

            <h6 class="text-muted mb-3">User Account</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Temporary Password <span class="text-danger">*</span></label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Minimum 8 characters. Ask the vendor to change this on first login.</div>
            </div>

            <hr>
            <h6 class="text-muted mb-3">Vendor Profile</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="shop_name">Shop Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('shop_name') is-invalid @enderror"
                           id="shop_name" name="shop_name"
                           value="{{ old('shop_name') }}" required>
                    @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="vendor_code">Vendor Code <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text"
                           class="form-control @error('vendor_code') is-invalid @enderror"
                           id="vendor_code" name="vendor_code"
                           value="{{ old('vendor_code') }}"
                           placeholder="e.g. VND-001">
                    @error('vendor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Leave blank to assign later. Must be unique.</div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Vendor
                </button>
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
