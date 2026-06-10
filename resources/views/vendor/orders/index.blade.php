@extends('layouts.vendor')

@section('title', 'My Orders')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-receipt"></i> My Orders</h2>
</div>

<div class="alert alert-info py-2 mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Showing only orders that contain <strong>your products</strong>. You cannot see orders for other vendors.
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <input type="number" name="search" class="form-control" placeholder="Order #ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100"><i class="fas fa-search"></i> Filter</button>
            </div>
            @if(request()->hasAny(['search','status']))
            <div class="col-md-1">
                <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-danger w-100"><i class="fas fa-times"></i></a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">My Orders ({{ $orders->total() }})</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $statusColors = [
                        'pending'   => 'warning text-dark',
                        'confirmed' => 'info',
                        'packed'    => 'primary',
                        'shipped'   => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    ];
                    $color = $statusColors[$order->status] ?? 'secondary';
                @endphp
                <tr>
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>{{ $order->user->name ?? 'Guest' }}<br><small class="text-muted">{{ $order->user->email ?? '' }}</small></td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td><strong>₹{{ number_format($order->total_price, 2) }}</strong></td>
                    <td><span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span></td>
                    <td>
                        <a href="{{ route('vendor.orders.show', $order) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                        No orders found for your products yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $orders->links() }}
</div>
@endsection
