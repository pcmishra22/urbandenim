@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Shipment Management</h3>
            <a href="{{ route('admin.shipments.create') }}" class="btn btn-success">Add New Shipment</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Courier</th>
                            <th>Tracking Number</th>
                            <th>Status</th>
                            <th>Shipped At</th>
                            <th>Delivered At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr>
                                <td>{{ $shipment->id }}</td>
                                <td>{{ $shipment->order->id ?? 'N/A' }}</td>
                                <td>{{ $shipment->order->user->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->courier->name ?? 'N/A' }}</td>
                                <td>{{ $shipment->tracking_id ?? 'N/A' }}</td>
                                <td>{{ ucfirst($shipment->status) }}</td>
                                <td>{{ $shipment->shipped_at ? \Carbon\Carbon::parse($shipment->shipped_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $shipment->delivered_at ? \Carbon\Carbon::parse($shipment->delivered_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shipment?');" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No shipments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $shipments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection