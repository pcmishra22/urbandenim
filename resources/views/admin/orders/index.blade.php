@extends('layouts.dashboard')

@section('title', 'Orders - Admin Listing')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-receipt"></i> Orders</h2>
</div>

<div class="card">
    <div class="card-header">
        <span>All Orders</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>
                            @php $s = $order->status; @endphp
                            @if($s === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($s === 'confirmed')
                                <span class="badge badge-processing">Confirmed</span>
                            @elseif($s === 'packed')
                                <span class="badge badge-processing">Packed</span>
                            @elseif($s === 'shipped')
                                <span class="badge badge-shipped">Shipped</span>
                            @elseif($s === 'delivered')
                                <span class="badge badge-delivered">Delivered</span>
                            @elseif($s === 'cancelled')
                                <span class="badge badge-cancelled">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ $s }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection

