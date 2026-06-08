@extends('layouts.dashboard')

@section('title', 'Vendor Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-user-tie"></i> Vendor Management</h2>
    <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Vendor
    </a>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Vendors ({{ $vendors->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Vendor</th>
                <th>Approval</th>
                <th>KYC</th>
                <th>Commission</th>
                <th>Wallet</th>
                <th>Settlement</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($vendors as $vendor)
                @php
                    $wallet           = $vendor->wallet->first();
                    $kycApproved      = $vendor->kycDocuments->where('verification_status', 'approved')->count();
                    $kycRejected      = $vendor->kycDocuments->where('verification_status', 'rejected')->count();
                    $kycPending       = $vendor->kycDocuments->where('verification_status', 'pending')->count();
                    $commissionRule   = $vendor->commissionRule->first();
                    $latestSettlement = $vendor->settlementReports->sortByDesc('period_end')->first();
                @endphp
                <tr>
                    <td>
                        <div><strong>#{{ $vendor->id }}</strong></div>
                        <div>{{ $vendor->shop_name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $vendor->user?->email ?? '—' }}</div>
                        @if($vendor->vendor_code)
                            <div class="text-muted" style="font-size:11px;">Code: {{ $vendor->vendor_code }}</div>
                        @endif
                    </td>

                    <td>
                        @if($vendor->approval_status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($vendor->approval_status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                        <div class="mt-1">
                            <span class="badge {{ $vendor->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if($vendor->rejection_reason)
                            <div class="text-muted mt-1" style="font-size:11px;">{{ $vendor->rejection_reason }}</div>
                        @endif
                    </td>

                    <td>
                        <div><span class="badge bg-success">{{ $kycApproved }} ok</span></div>
                        <div><span class="badge bg-warning text-dark">{{ $kycPending }} pending</span></div>
                        <div><span class="badge bg-danger">{{ $kycRejected }} rejected</span></div>
                    </td>

                    <td>
                        @if($commissionRule)
                            <div>Rate: {{ $commissionRule->commission_rate }}%</div>
                            <div>Flat: {{ $commissionRule->commission_flat ?? 0 }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $commissionRule->payout_frequency }}</div>
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </td>

                    <td>
                        @if($wallet)
                            <div><strong>{{ number_format($wallet->balance, 2) }}</strong></div>
                            <div class="text-muted" style="font-size:11px;">{{ $wallet->updated_at?->format('Y-m-d') }}</div>
                        @else
                            <span class="text-muted">No wallet</span>
                        @endif
                    </td>

                    <td>
                        @if($latestSettlement)
                            @if($latestSettlement->status === 'paid')
                                <span class="badge bg-primary">Paid</span>
                            @elseif($latestSettlement->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($latestSettlement->status === 'submitted')
                                <span class="badge bg-info text-dark">Submitted</span>
                            @elseif($latestSettlement->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                            <div class="text-muted" style="font-size:11px;">
                                {{ $latestSettlement->period_start }} → {{ $latestSettlement->period_end }}
                            </div>
                        @else
                            <span class="text-muted">No reports</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex flex-column gap-1" style="min-width:110px;">

                            {{-- Edit --}}
                            <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            {{-- Approve / Reject (pending only) --}}
                            @if($vendor->approval_status === 'pending')
                                <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.vendors.reject', $vendor) }}" method="POST"
                                      onsubmit="return confirm('Reject this vendor?')">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="KYC/Docs not verified" />
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            {{-- Quick KYC approve if pending docs --}}
                            @if($vendor->kycDocuments->where('verification_status','pending')->isNotEmpty())
                                @php($pendingKyc = $vendor->kycDocuments->where('verification_status','pending')->first())
                                <form action="{{ route('admin.vendors.kyc.approve', $pendingKyc) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fas fa-user-check"></i> KYC
                                    </button>
                                </form>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($vendor->shop_name) }} and their account? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fas fa-user-tie fa-2x mb-2 d-block"></i>
                        No vendors yet.
                        <a href="{{ route('admin.vendors.create') }}">Add the first one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $vendors->links() }}
</div>
@endsection
