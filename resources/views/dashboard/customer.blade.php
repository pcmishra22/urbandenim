@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')

@section('content')
<h1 class="page-title"><i class="fas fa-home"></i> Welcome, {{ Auth::user()->name }}!</h1>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>Total Orders</h6>
            <div class="value">{{ $orders->total() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6>Pending Orders</h6>
            <div class="value">{{ \App\Models\Order::where('user_id', Auth::id())->where('status', 'pending')->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6>Total Spent</h6>
            <div class="value">${{ number_format(\App\Models\Order::where('user_id', Auth::id())->sum('total_price'), 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-shopping-bag"></i> Recent Orders
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $order->products->count() }} items
                            </span>
                        </td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($order->status) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                View Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox"></i> No orders yet. Start shopping!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $orders->links('pagination::bootstrap-4') }}
</div>

<div style="margin-top: 30px;">
    <a href="{{ route('customer.login') }}" class="btn btn-outline-secondary">
        <i class="fas fa-plus"></i> Browse Products
    </a>
</div>
@endsection
