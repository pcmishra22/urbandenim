@extends('layouts.dashboard')

@section('title', 'Edit Vendor — ' . $vendor->shop_name)

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-store"></i> {{ $vendor->shop_name }}</h2>
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Vendors
    </a>
</div>

{{-- ── Status Bar ── --}}
<div class="row mt-3">
    <div class="col-6 col-md-3 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">Approval</div>
                @if($vendor->approval_status === 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($vendor->approval_status === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                @else
                    <span class="badge bg-warning text-dark">Pending</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">Status</div>
                <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">Wallet</div>
                @php($wallet = $vendor->wallet->first())
                <div class="fw-bold">{{ number_format($wallet?->balance ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card h-100 text-center">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">KYC</div>
                <span class="badge bg-success">{{ $vendor->kycDocuments->where('verification_status','approved')->count() }}</span>
                <span class="badge bg-warning text-dark">{{ $vendor->kycDocuments->where('verification_status','pending')->count() }}</span>
                <span class="badge bg-danger">{{ $vendor->kycDocuments->where('verification_status','rejected')->count() }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Form ── --}}
<div class="card">
    <div class="card-header">Edit Profile</div>
    <div class="card-body">
        <form action="{{ route('admin.vendors.update', $vendor) }}" method="POST">
            @csrf
            @method('PUT')

            <h6 class="text-muted mb-3">User Account</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name"
                           value="{{ old('name', $vendor->user?->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ old('email', $vendor->user?->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr>
            <h6 class="text-muted mb-3">Vendor Profile</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="shop_name">Shop Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('shop_name') is-invalid @enderror"
                           id="shop_name" name="shop_name"
                           value="{{ old('shop_name', $vendor->shop_name) }}" required>
                    @error('shop_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="vendor_code">Vendor Code</label>
                    <input type="text"
                           class="form-control @error('vendor_code') is-invalid @enderror"
                           id="vendor_code" name="vendor_code"
                           value="{{ old('vendor_code', $vendor->vendor_code) }}">
                    @error('vendor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                       value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (vendor can log in and list products)</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- ── Approve / Reject ── --}}
@if($vendor->approval_status === 'pending')
<div class="card mt-2">
    <div class="card-header">Approval Decision</div>
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
        <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Approve Vendor
            </button>
        </form>
        <form action="{{ route('admin.vendors.reject', $vendor) }}" method="POST"
              onsubmit="return confirm('Reject this vendor?')"
              class="d-flex gap-2 align-items-center">
            @csrf
            <input type="text" class="form-control" name="rejection_reason"
                   placeholder="Rejection reason (optional)" style="width:260px;">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-times"></i> Reject
            </button>
        </form>
    </div>
</div>
@endif

{{-- ── KYC Documents ── --}}
<div class="card mt-2">
    <div class="card-header">KYC Documents</div>
    <div class="card-body p-0">
        @if($vendor->kycDocuments->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>File</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($vendor->kycDocuments->sortByDesc('submitted_at') as $doc)
                        <tr>
                            <td>{{ $doc->document_type }}</td>
                            <td><span class="text-muted" style="font-size:12px;">{{ $doc->file_path }}</span></td>
                            <td>{{ $doc->submitted_at?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                @if($doc->verification_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($doc->verification_status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @if($doc->rejection_reason)
                                        <div class="text-danger" style="font-size:11px;">{{ $doc->rejection_reason }}</div>
                                    @endif
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->verification_status === 'pending')
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('admin.vendors.kyc.approve', $doc) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.vendors.kyc.reject', $doc) }}" method="POST"
                                              onsubmit="return confirm('Reject this KYC document?')">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" value="Invalid document" />
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">No KYC documents submitted yet.</div>
        @endif
    </div>
</div>

{{-- ── Settlement Reports ── --}}
<div class="card mt-2">
    <div class="card-header">Settlement Reports</div>
    <div class="card-body p-0">
        @if($vendor->settlementReports->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Gross</th>
                            <th>Commission</th>
                            <th>Net Payout</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($vendor->settlementReports->sortByDesc('period_end') as $sr)
                        <tr>
                            <td>{{ $sr->period_start }} → {{ $sr->period_end }}</td>
                            <td>{{ number_format($sr->gross_amount, 2) }}</td>
                            <td>{{ number_format($sr->commission_amount, 2) }}</td>
                            <td><strong>{{ number_format($sr->net_payout_amount, 2) }}</strong></td>
                            <td>
                                @if($sr->status === 'paid')
                                    <span class="badge bg-primary">Paid</span>
                                @elseif($sr->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($sr->status === 'submitted')
                                    <span class="badge bg-info text-dark">Submitted</span>
                                @elseif($sr->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($sr->status === 'submitted')
                                    <form action="{{ route('admin.vendors.settlements.approve', $sr) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">No settlement reports yet.</div>
        @endif
    </div>
</div>

{{-- ── Danger Zone ── --}}
<div class="card border-danger mt-2 mb-5">
    <div class="card-header text-danger">Danger Zone</div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Permanently deletes this vendor and their user account. This cannot be undone.
        </p>
        <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST"
              onsubmit="return confirm('Delete {{ addslashes($vendor->shop_name) }} permanently? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Delete Vendor
            </button>
        </form>
    </div>
</div>
@endsection
