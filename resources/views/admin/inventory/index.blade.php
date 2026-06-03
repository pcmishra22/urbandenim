@extends('layouts.dashboard')

@section('title', 'Inventory - Stock Tracking')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-cubes"></i> Stock Tracking (Variants)</h2>
</div>

<div class="card">
    <div class="card-header">
        <span>All Variant Stock</span>
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
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $variant)
                    <tr>
                        <td><strong>#{{ $variant->id }}</strong></td>
                        <td>{{ $variant->product->name ?? '-' }}</td>
                        <td><code>{{ $variant->sku }}</code></td>
                        <td>{{ $variant->color }} / {{ $variant->size }}</td>
                        <td>
                            @if((int)$variant->stock <= 10)
                                <span class="badge bg-danger">{{ $variant->stock }}</span>
                            @else
                                <span class="badge bg-success">{{ $variant->stock }}</span>
                            @endif
                        </td>
                        <td>
                            @if($variant->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No inventory found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $inventory->links() }}
    </div>
</div>
@endsection

