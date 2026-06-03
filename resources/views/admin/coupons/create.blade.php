@extends('layouts.dashboard')

@section('title', 'Create Coupon')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-plus"></i> Create Coupon</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Coupon Type <span class="text-danger">*</span></label>
                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="flat" @selected(old('type')==='flat')>Flat</option>
                    <option value="percentage" @selected(old('type')==='percentage')>Percentage</option>
                    <option value="free_shipping" @selected(old('type')==='free_shipping')>Free Shipping</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="value" class="form-label">Value
                    <span class="text-muted">(Flat amount for flat, % for percentage)</span>
                </label>
                <input type="number" step="0.01" id="value" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}">
                @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="usage_limit" class="form-label">Usage Limit</label>
                    <input type="number" id="usage_limit" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" value="{{ old('usage_limit') }}" min="1">
                    @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="expires_at" class="form-label">Expiry Date</label>
                    <input type="date" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at')?->format('Y-m-d') ?? old('expires_at') }}">
                    @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">User-specific Coupon</label>
                <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string)old('user_id') === (string)$user->id)>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active')==='1' || old('is_active')===true)>
                <label class="form-check-label" for="is_active">Active</label>
                @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Coupon
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>
@endsection

