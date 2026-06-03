@extends('layouts.dashboard')

@section('title', 'Inventory - Stock History')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-history"></i> Stock History</h2>
</div>

<div class="card">
    <div class="card-header">
        <span>Inventory Logs</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Date</th>
                    <th>Variant</th>
                    <th>Old</th>
                    <th>New</th>
                    <th>Adjustment</th>
                    <th>Reason</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><strong>#{{ $log->id }}</strong></td>
                        <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            {{ $log->variant->product->name ?? '-' }}<br>
                            <small class="text-muted">{{ $log->variant->sku ?? '' }}</small>
                        </td>
                        <td>{{ $log->old_stock }}</td>
                        <td>{{ $log->new_stock }}</td>
                        <td>
                            @php
                                $adj = (int)$log->adjustment;
                            @endphp
                            @if($adj >= 0)
                                <span class="badge bg-success">+{{ $adj }}</span>
                            @else
                                <span class="badge bg-danger">{{ $adj }}</span>
                            @endif
                        </td>
                        <td>{{ $log->reason }}</td>
                        <td>{{ $log->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No inventory history found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection

