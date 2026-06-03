@extends('layouts.dashboard')

@section('title', 'Return & Refund Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-undo"></i> Return & Refund Management</h2>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-primary {{ $activeTab === 'returns' ? 'active' : '' }}">
            Return Requests
        </a>
        <a href="{{ route('admin.returns.index', ['type' => 'exchanges']) }}" class="btn btn-outline-primary {{ $activeTab === 'exchanges' ? 'active' : '' }}">
            Exchange Requests
        </a>
    </div>
</div>

@if(($activeTab ?? 'returns') === 'exchanges')
    <div class="card mt-3">
        <div class="card-header">
            <span>Exchange Requests {{ $exchanges->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Exchange #</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($exchanges as $exchange)
                    @php($ret = $exchange->returnRequest)
                    <tr>
                        <td><strong>#{{ $exchange->id }}</strong></td>
                        <td>#{{ $ret->order_id ?? '-' }}</td>
                        <td>{{ $ret->user->name ?? '-' }}</td>
                        <td>
                            @if($exchange->status === 'approved')
                                <span class="badge badge-processing">Approved</span>
                            @elseif($exchange->status === 'rejected')
                                <span class="badge badge-cancelled">Rejected</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($exchange->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $exchange->requested_at ? $exchange->requested_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.returns.show', $ret) }}">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No exchange requests found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $exchanges->links() }}
        </div>
    </div>
@else
    <div class="card mt-3">
        <div class="card-header">
            <span>Return Requests {{ $returns->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Return #</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td><strong>#{{ $return->id }}</strong></td>
                        <td>#{{ $return->order_id }}</td>
                        <td>{{ $return->user->name ?? '-' }}</td>
                        <td>
                            @php($s = $return->status)
                            @if($s === 'requested')
                                <span class="badge badge-pending">Requested</span>
                            @elseif($s === 'approved')
                                <span class="badge badge-processing">Approved</span>
                            @elseif($s === 'rejected')
                                <span class="badge badge-cancelled">Rejected</span>
                            @elseif($s === 'pickup_requested')
                                <span class="badge bg-info">Pickup Requested</span>
                            @elseif($s === 'pickup_received')
                                <span class="badge bg-secondary">Pickup Received</span>
                            @elseif($s === 'refund_completed')
                                <span class="badge badge-delivered">Refund Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                            @endif
                        </td>
                        <td>{{ $return->requested_at ? $return->requested_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.returns.show', $return) }}">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No return requests found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $returns->links() }}
        </div>
    </div>
@endif
@endsection

