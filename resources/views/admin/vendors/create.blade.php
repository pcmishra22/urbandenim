@extends('layouts.dashboard')

@section('title', 'Create Vendor')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-plus"></i> Create Vendor</h2>
</div>

<div class="alert alert-info">
    Admin-created vendors are supported for back-office onboarding.
    This will create a vendor user and vendor profile in <code>vendors</code> table, with <code>approval_status=pending</code>.
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.vendors.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="name">Vendor User Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Vendor Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Temporary Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="shop_name">Shop Name</label>
                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" id="shop_name" name="shop_name" value="{{ old('shop_name') }}" required>
                @error('shop_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="vendor_code">Vendor Code (Optional)</label>
                <input type="text" class="form-control @error('vendor_code') is-invalid @enderror" id="vendor_code" name="vendor_code" value="{{ old('vendor_code') }}">
                @error('vendor_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Vendor (Pending Approval)
            </button>

            <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
        </form>
    </div>
</div>

<a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Vendors
</a>
@endsection

