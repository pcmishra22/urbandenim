@extends('layouts.dashboard')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-receipt"></i> Order #{{ $order->id }}</h2>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('admin.orders.invoice', $order) }}">
            <i class="fas fa-file-invoice"></i> Invoice
        </a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.orders.shippingLabel', $order) }}">
            <i class="fas fa-map"></i> Shipping Label
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Order Details</div>
            <div class="card-body">
                <p><strong>Customer:</strong> {{ $order->user->name ?? '-' }}</p>
                <p><strong>Total:</strong> {{ $order->total_price }}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">{{ $order->status }}</span></p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Items</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->products as $product)
                                <tr>
                                    <td>{{ $product->name ?? '-' }}</td>
                                    <td>{{ $product->pivot->quantity ?? '-' }}</td>
                                    <td>{{ $product->pivot->price ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Order Status Flow</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Current status</label>
                        <input type="text" class="form-control" value="{{ $order->status }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update status</label>
                        <select name="status" class="form-select" required>
                            <option value="confirmed">Confirmed</option>
                            <option value="packed">Packed</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync"></i> Update
                    </button>
                </form>

                <div class="mt-3 text-muted" style="font-size: 0.9rem;">
                    Flow enforced: Pending → Confirmed → Packed → Shipped → Delivered
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

