@extends('layouts.dashboard')

@section('title', 'Shipments - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-truck-fast"></i> Shipments / Tracking</h2>
</div>

<div class="card">
    <div class="card-header">
        <span>All Shipments ({{ $shipments->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order</th>
                    <th>Courier</th>
                    <th>Tracking ID</th>
                    <th>Status</th>
                    <th>Shipped</th>
                    <th>Delivered</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shipments as $shipment)
                    <tr>
                        <td><strong>#{{ $shipment->id }}</strong></td>
                        <td>
                            #{{ $shipment->order->id }}<br/>
                            <span class="text-muted" style="font-size:0.85rem;">
                                {{ $shipment->order->user->name ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $shipment->courier->name ?? '-' }}</td>
                        <td><code>{{ $shipment->tracking_id ?? '-' }}</code></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $shipment->status)) }}</td>
                        <td>{{ $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Update
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No shipments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $shipments->links() }}
    </div>
</div>
@endsection

