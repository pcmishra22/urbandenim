@extends('layouts.dashboard')

@section('title', 'Inventory - Low Stock Alerts')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-triangle-exclamation"></i> Low Stock Alerts</h2>
    <form method="GET" action="{{ route('admin.inventory.alerts') }}" class="d-flex gap-2">
        <input type="number" name="threshold" value="{{ $threshold }}" class="form-control" style="width: 140px;" min="0">
        <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span>Showing variants with stock ≤ {{ $threshold }}</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Variant ID</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Variant</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $variant)
                    <tr>
                        <td><strong>#{{ $variant->id }}</strong></td>
                        <td>{{ $variant->product->name ?? '-' }}</td>
                        <td><code>{{ $variant->sku }}</code></td>
                        <td>{{ $variant->color }} / {{ $variant->size }}</td>
                        <td><span class="badge bg-danger">{{ $variant->stock }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No low-stock items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection

