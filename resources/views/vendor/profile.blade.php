@extends('layouts.vendor')

@section('title', 'My Profile')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-user-cog"></i> My Profile</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-store me-2"></i> Shop & Account Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('vendor.profile.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-muted">(read only)</span></label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="shop_name" class="form-label">Shop Name</label>
                        <input type="text" name="shop_name" id="shop_name" class="form-control @error('shop_name') is-invalid @enderror"
                               value="{{ old('shop_name', $vendor->shop_name) }}" required>
                        @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr>
                    <h6 class="mb-3">Change Password <small class="text-muted">(leave blank to keep current)</small></h6>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i> Account Info</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Role</th><td><span class="badge bg-success">Vendor</span></td></tr>
                    <tr><th>Shop Name</th><td>{{ $vendor->shop_name }}</td></tr>
                    <tr><th>Vendor Code</th><td><code>{{ $vendor->vendor_code ?? '—' }}</code></td></tr>
                    <tr><th>Approval Status</th><td>
                        <span class="badge {{ $vendor->approval_status === 'approved' ? 'bg-success' : ($vendor->approval_status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($vendor->approval_status ?? 'pending') }}
                        </span>
                    </td></tr>
                    <tr><th>Account Active</th><td>
                        <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td></tr>
                    <tr><th>Member Since</th><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                </table>

                <div class="alert alert-light border mt-3 mb-0">
                    <small class="text-muted">
                        <i class="fas fa-lock me-1"></i>
                        As a vendor, you can only manage <strong>your own products, orders, and inventory</strong>.
                        For account issues, contact the platform administrator.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
