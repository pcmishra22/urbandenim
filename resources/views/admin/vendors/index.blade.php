@extends('layouts.dashboard')

@section('title', 'Vendor Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-store"></i> Vendor Management</h2>
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
                    $wallet = $vendor->wallet->first();
                    $kycApproved = $vendor->kycDocuments->where('verification_status', 'approved')->count();
                    $kycRejected = $vendor->kycDocuments->where('verification_status', 'rejected')->count();
                    $kycPending = $vendor->kycDocuments->where('verification_status', 'pending')->count();
                    $commissionRule = $vendor->commissionRule->first();
                    $latestSettlement = $vendor->settlementReports->sortByDesc('period_end')->first();
                @endphp
                <tr>
                    <td>
                        <div><strong>#{{ $vendor->id }}</strong></div>
                        <div>{{ $vendor->shop_name }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ $vendor->user?->email ?? '' }}</div>
                    </td>

                    <td>
                        @if($vendor->approval_status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($vendor->approval_status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                        @if($vendor->rejection_reason)
                            <div class="text-muted" style="font-size: 12px;">{{ $vendor->rejection_reason }}</div>
                        @endif
                    </td>

                    <td>
                        <div>
                            <span class="badge bg-success">Approved: {{ $kycApproved }}</span>
                            <span class="badge bg-warning text-dark">Pending: {{ $kycPending }}</span>
                            <span class="badge bg-danger">Rejected: {{ $kycRejected }}</span>
                        </div>
                        @if($vendor->kycDocuments->isNotEmpty())
                            <div class="text-muted" style="font-size: 12px; margin-top: 6px;">
                                Latest: {{ $vendor->kycDocuments->sortByDesc('submitted_at')->first()->document_type ?? '-' }}
                            </div>
                        @endif
                    </td>

                    <td>
                        @if($commissionRule)
                            <div>Rate: {{ $commissionRule->commission_rate }}</div>
                            <div>Flat: {{ $commissionRule->commission_flat ?? 0 }}</div>
                            <div class="text-muted" style="font-size: 12px;">Frequency: {{ $commissionRule->payout_frequency }}</div>
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </td>

                    <td>
                        @if($wallet)
                            <div><strong>{{ $wallet->balance }}</strong></div>
                            <div class="text-muted" style="font-size: 12px;">Wallet updated: {{ $wallet->updated_at?->format('Y-m-d') }}</div>
                        @else
                            <span class="text-muted">No wallet</span>
                        @endif
                    </td>

                    <td>
                        @if($latestSettlement)
                            <div>
                                @if($latestSettlement->status === 'paid')
                                    <span class="badge bg-primary">Paid</span>
                                @elseif($latestSettlement->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($latestSettlement->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($latestSettlement->status === 'submitted')
                                    <span class="badge bg-info text-dark">Submitted</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </div>
                            <div class="text-muted" style="font-size: 12px;">{{ $latestSettlement->period_start }} → {{ $latestSettlement->period_end }}</div>
                        @else
                            <span class="text-muted">No reports</span>
                        @endif
                    </td>

                    <td>
                        <div class="btn-group-vertical" style="gap: 6px;">
                            @if($vendor->approval_status === 'pending')
                                <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.vendors.reject', $vendor) }}" method="POST" onsubmit="return confirm('Reject this vendor?')">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" value="KYC/Docs not verified" />
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            @if($vendor->kycDocuments->where('verification_status','pending')->isNotEmpty())
                                @php($pendingKyc = $vendor->kycDocuments->where('verification_status','pending')->first())
                                <form action="{{ route('admin.vendors.kyc.approve', $pendingKyc) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-check"></i> Approve KYC
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- If you want more detailed multi-document KYC actions per vendor, expand this row into nested tables. --}}
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox"></i> No vendors found
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

