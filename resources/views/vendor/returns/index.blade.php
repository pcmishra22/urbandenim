@extends('layouts.vendor')
@section('title', 'Return Requests')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-undo"></i> Return Requests</h2>
    @if($pendingCount > 0)
    <span class="badge badge-warning" style="font-size:.85rem;padding:6px 14px;">
        {{ $pendingCount }} pending action{{ $pendingCount > 1 ? 's' : '' }}
    </span>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
@endif

@if($returns->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-undo fa-3x mb-3" style="color:#ddd;"></i>
        <p>No return requests yet.</p>
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>Return #</th>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $return)
                <tr>
                    <td><strong>#{{ $return->id }}</strong></td>
                    <td><a href="{{ route('vendor.orders.show', $return->order_id) }}">#{{ $return->order_id }}</a></td>
                    <td>{{ $return->user->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ $return->type === 'exchange' ? 'info' : 'primary' }}">
                            {{ ucfirst($return->type ?? 'return') }}
                        </span>
                    </td>
                    <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $return->reason }}</td>
                    <td>₹{{ number_format($return->refund_amount ?? 0, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $return->status_color }}">{{ $return->status_label }}</span>
                    </td>
                    <td>{{ $return->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('vendor.returns.show', $return->id) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $returns->links() }}</div>
@endif
@endsection
