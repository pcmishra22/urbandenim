@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="h3 mb-4 text-gray-800">Edit Coupon: {{ $coupon->code }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code">Coupon Code</label>
                        <input type="text" class="form-control" value="{{ $coupon->code }}" disabled>
                        <small class="text-muted">Code cannot be changed once created.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type">Type</label>
                        <select name="type" class="form-control" required>
                            <option value="flat" {{ $coupon->type == 'flat' ? 'selected' : '' }}>Flat Discount</option>
                            <option value="percentage" {{ $coupon->type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="free_shipping" {{ $coupon->type == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="value">Value (Amount or %)</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="usage_limit">Usage Limit (Total uses)</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expires_at">Expiry Date</label>
                        <input type="date" name="expires_at" class="form-control" value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_id">Assign to Specific User</label>
                        <select name="user_id" class="form-control select2">
                            <option value="">Available to all users</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $coupon->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->email }} ({{ $user->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" {{ $coupon->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label" for="isActive">Active</label>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Coupon</button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection