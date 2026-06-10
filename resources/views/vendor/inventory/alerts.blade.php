@extends('layouts.vendor')

@section('title', 'Low Stock Alerts')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-exclamation-triangle text-warning"></i> Low Stock Alerts</h2>
    <a href="{{ route('vendor.inventory.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
</div>

<div class="alert alert-warning py-2 mb-3">
    <i class="fas fa-exclamation-triangle me-1"></i>
    Showing your product variants with stock <strong>&le; {{ $threshold }}</strong>.
    <form method="GET" class="d-inline ms-3">
        <label>Threshold: <input type="number" name="threshold" value="{{ $threshold }}" min="1" max="100" class="form-control d-inline" style="width:70px;"></label>
        <button type="submit" class="btn btn-sm btn-warning ms-1">Apply</button>
    </form>
</div>

<div class="card">
    <div class="card-header">Low Stock Items ({{ $items->total() }})</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Variant ID</th>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Current Stock</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $variant)
                <tr>
                    <td><strong>#{{ $variant->id }}</strong></td>
                    <td>
                        <a href="{{ route('vendor.products.edit', $variant->product_id) }}" class="text-decoration-none">
                            {{ $variant->product->name ?? '—' }}
                        </a>
                    </td>
                    <td>{{ $variant->waist_size }}</td>
                    <td>{{ $variant->color ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $variant->quantity <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $variant->quantity <= 0 ? 'OUT OF STOCK' : $variant->quantity . ' left' }}
                        </span>
                    </td>
                    <td>{{ $variant->price ? '₹'.number_format($variant->price, 2) : '—' }}</td>
                    <td>
                        <a href="{{ route('vendor.inventory.index') }}#adjust" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Restock (ID: #{{ $variant->id }})
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-success py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                        No low stock items! All variants have stock above {{ $threshold }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $items->links() }}
</div>
@endsection
