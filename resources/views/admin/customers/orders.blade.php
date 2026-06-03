@extends('layouts.dashboard')

@section('title', 'Customer Orders')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Orders - {{ $customer->name }}</h1>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.index') }}">Back</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $order->products->count() }} items</span>
                                </td>
                                <td>${{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $orders->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>
@endsection

