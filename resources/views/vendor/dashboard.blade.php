@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-chart-bar"></i> My Store Dashboard</h2>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

{{-- Access Scope Banner --}}
<div class="access-denied-banner d-flex align-items-center gap-3 mb-4">
    <i class="fas fa-store fa-2x opacity-75"></i>
    <div>
        <div class="fw-bold fs-6">{{ $vendor->shop_name }}</div>
        <div style="font-size:0.85rem;opacity:0.85;">You are managing your own store. You can only view and edit your products, orders, and inventory.</div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>My Products</h6>
            <div class="value">{{ $total_products }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>Active Products</h6>
            <div class="value" style="color:#27ae60;">{{ $active_products }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>Low Stock</h6>
            <div class="value" style="color:#f39c12;">{{ $low_stock }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>My Orders</h6>
            <div class="value" style="color:#3498db;">{{ $total_orders }}</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <h6>Out of Stock</h6>
            <div class="value" style="color:#e74c3c;">{{ $out_of_stock }}</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <h6>Pending Orders</h6>
            <div class="value" style="color:#e67e22;">{{ $pending_orders }}</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <h6>Total Inventory Value</h6>
            <div class="value" style="font-size:1.4rem;">₹{{ number_format($total_value, 0) }}</div>
        </div>
    </div>
</div>

{{-- Recent Products --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-box me-2"></i> My Recent Products</span>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-sm btn-outline-success">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_products as $product)
                @php $stock = $product->variants->sum('quantity'); @endphp
                <tr>
                    <td><strong>{{ $product->name }}</strong><br><small class="text-muted">SKU: {{ $product->sku ?? '—' }}</small></td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                    <td>
                        <span class="badge {{ $stock == 0 ? 'bg-danger' : ($stock < 5 ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $stock }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('vendor.products.edit', $product) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <a href="{{ route('vendor.products.show', $product) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                        No products yet. <a href="{{ route('vendor.products.create') }}">Add your first product!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row mt-2">
    <div class="col-md-4">
        <a href="{{ route('vendor.products.create') }}" class="card text-decoration-none" style="transition:.2s;">
            <div class="card-body text-center py-4">
                <i class="fas fa-plus-circle fa-2x mb-2" style="color:#27ae60;"></i>
                <div class="fw-600 text-dark">Add New Product</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('vendor.orders.index') }}" class="card text-decoration-none">
            <div class="card-body text-center py-4">
                <i class="fas fa-receipt fa-2x mb-2" style="color:#3498db;"></i>
                <div class="fw-600 text-dark">View My Orders</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('vendor.inventory.alerts') }}" class="card text-decoration-none">
            <div class="card-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2" style="color:#e67e22;"></i>
                <div class="fw-600 text-dark">Low Stock Alerts</div>
                @if($low_stock > 0)
                    <span class="badge bg-warning text-dark mt-1">{{ $low_stock }} items</span>
                @endif
            </div>
        </a>
    </div>
</div>
@endsection
