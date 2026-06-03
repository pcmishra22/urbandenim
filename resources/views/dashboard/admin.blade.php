@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')



@section('content')
<h1 class="page-title"><i class="fas fa-chart-line"></i> Admin Dashboard</h1>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Users</h6>
            <div class="value">{{ $stats['total_users'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Products</h6>
            <div class="value">{{ $stats['total_products'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Orders</h6>
            <div class="value">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Revenue</h6>
            <div class="value">${{ number_format($stats['total_revenue'], 0) }}</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Pending Orders</h6>
            <div class="value" style="color: #f39c12;">{{ $stats['pending_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Shipped Orders</h6>
            <div class="value" style="color: #3498db;">{{ $stats['shipped_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Delivered Orders</h6>
            <div class="value" style="color: #27ae60;">{{ $stats['delivered_orders'] }}</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-receipt"></i> Recent Orders
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>${{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No orders</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users"></i> Recent Users
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge badge-{{ strtolower($user->role) }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No users</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i> Orders by Status
    </div>
    <div style="padding: 20px;">
        <div class="row">
            @foreach($stats['orders_by_status'] as $item)
                <div class="col-md-4">
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 10px;">
                        <h5 style="color: var(--primary-color); margin-bottom: 10px;">{{ ucfirst($item->status) }}</h5>
                        <div style="font-size: 1.5rem; font-weight: bold; color: var(--secondary-color);">{{ $item->count }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
