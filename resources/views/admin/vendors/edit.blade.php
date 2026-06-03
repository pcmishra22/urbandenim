@extends('layouts.dashboard')

@section('title', 'Vendor Details')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-store"></i> Vendor Details</h2>
</div>

@if(!isset($vendor))
    <div class="alert alert-warning">Vendor not found.</div>
@else
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ $vendor->shop_name }}</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted" style="font-size:12px;">Approval Status</div>
                        <div class="fw-semibold">
                            @if($vendor->approval_status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($vendor->approval_status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </div>
                        @if($vendor->rejection_reason)
                            <div class="text-muted mt-2">{{ $vendor->rejection_reason }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted" style="font-size:12px;">Wallet Balance</div>
                        @php($wallet = $vendor->wallet->first())
                        <div class="fw-semibold">{{ $wallet?->balance ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-2 bg-light rounded">
                        <div class="text-muted" style="font-size:12px;">KYC</div>
                        <div>
                            {{ $vendor->kycDocuments->where('verification_status','approved')->count() }} approved /
                            {{ $vendor->kycDocuments->where('verification_status','pending')->count() }} pending /
                            {{ $vendor->kycDocuments->where('verification_status','rejected')->count() }} rejected
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h6>Settlement Reports</h6>
                    <div class="list-group">
                        @forelse($vendor->settlementReports->sortByDesc('period_end') as $sr)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div><strong>{{ $sr->period_start }} → {{ $sr->period_end }}</strong></div>
                                        <div class="text-muted" style="font-size:12px;">Net payout: {{ $sr->net_payout_amount }}</div>
                                    </div>
                                    <div>
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
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No settlement reports.</div>
                        @endforelse
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>KYC Documents</h6>
                    <div class="list-group">
                        @forelse($vendor->kycDocuments->sortByDesc('submitted_at') as $doc)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div><strong>{{ $doc->document_type }}</strong></div>
                                        <div class="text-muted" style="font-size:12px;">Submitted: {{ optional($doc->submitted_at)->format('Y-m-d') }}</div>
                                        <div class="text-muted" style="font-size:12px;">File: {{ $doc->file_path }}</div>
                                    </div>
                                    <div>
                                        @if($doc->verification_status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($doc->verification_status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </div>
                                </div>
                                @if($doc->rejection_reason)
                                    <div class="text-danger mt-2" style="font-size:12px;">{{ $doc->rejection_reason }}</div>
                                @endif
                                @if($doc->verification_status === 'pending')
                                    <div class="mt-2 d-flex gap-2">
                                        <form action="{{ route('admin.vendors.kyc.approve', $doc) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.vendors.kyc.reject', $doc) }}" method="POST" onsubmit="return confirm('Reject this KYC document?')">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" value="Invalid document" />
                                            <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No KYC documents.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif
@endsection

