@extends('layouts.vendor')

@section('title', 'My Inventory')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-cubes"></i> My Stock Tracking</h2>
    <a href="{{ route('vendor.inventory.alerts') }}" class="btn btn-outline-warning">
        <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
    </a>
</div>

<div class="alert alert-info py-2 mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Showing only inventory for <strong>{{ $vendor->shop_name }}</strong>'s products.
</div>

{{-- Adjust Stock Form --}}
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-edit me-2"></i> Adjust Stock</div>
    <div class="card-body">
        <form method="POST" action="{{ route('vendor.inventory.adjust') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Variant ID</label>
                <input type="number" name="variant_id" class="form-control" placeholder="Variant ID (from table below)" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Adjustment</label>
                <input type="number" name="adjustment" class="form-control" placeholder="+10 or -5" required>
                <div class="form-text">Use negative to reduce.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Reason</label>
                <input type="text" name="reason" class="form-control" placeholder="e.g., New stock received" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Adjust</button>
            </div>
        </form>
    </div>
</div>

{{-- Inventory Table --}}
<div class="card">
    <div class="card-header">Inventory ({{ $inventory->total() }} variants)</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Variant ID</th>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>SKU</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $variant)
                <tr>
                    <td><strong>#{{ $variant->id }}</strong></td>
                    <td>{{ $variant->product->name ?? '—' }}</td>
                    <td>{{ $variant->waist_size }}</td>
                    <td>{{ $variant->color ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $variant->quantity <= 0 ? 'bg-danger' : ($variant->quantity <= 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $variant->quantity }}
                        </span>
                    </td>
                    <td>{{ $variant->price ? '₹'.number_format($variant->price, 2) : '—' }}</td>
                    <td><code>{{ $variant->sku ?? '—' }}</code></td>
                    <td><span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No variants found. Add products with variants first.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $inventory->links() }}
</div>
@endsection
