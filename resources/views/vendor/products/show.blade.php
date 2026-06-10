@extends('layouts.vendor')

@section('title', 'Product Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-box"></i> {{ $product->name }}</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('vendor.products.edit', $product) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i> Product Information</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="180">Name</th><td>{{ $product->name }}</td></tr>
                    <tr><th>SKU</th><td>{{ $product->sku ?? '—' }}</td></tr>
                    <tr><th>Slug</th><td><code>{{ $product->slug }}</code></td></tr>
                    <tr><th>Category</th><td>{{ $product->category->name ?? '—' }}</td></tr>
                    <tr><th>Brand</th><td>{{ $product->brand->name ?? '—' }}</td></tr>
                    <tr><th>Price</th><td><strong>₹{{ number_format($product->price, 2) }}</strong></td></tr>
                    <tr><th>Sale Price</th><td>{{ $product->sale_price ? '₹'.number_format($product->sale_price, 2) : '—' }}</td></tr>
                    <tr><th>Gender</th><td>{{ ucfirst($product->gender ?? '—') }}</td></tr>
                    <tr><th>Age Group</th><td>{{ ucfirst($product->age_group ?? '—') }}</td></tr>
                    <tr><th>Color</th><td>{{ ucfirst($product->color_family ?? '—') }}</td></tr>
                    <tr><th>Status</th><td>
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured)
                            <span class="badge bg-warning text-dark ms-1"><i class="fas fa-star"></i> Featured</span>
                        @endif
                    </td></tr>
                    <tr><th>Short Description</th><td>{{ $product->short_description ?? '—' }}</td></tr>
                    <tr><th>Description</th><td>{{ $product->description ?? '—' }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Variants --}}
        @if($product->variants->count())
        <div class="card">
            <div class="card-header"><i class="fas fa-layer-group me-2"></i> Variants ({{ $product->variants->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Size</th><th>Color</th><th>Stock</th><th>Price</th><th>SKU</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($product->variants as $variant)
                        <tr>
                            <td>{{ $variant->waist_size }}</td>
                            <td>{{ $variant->color ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $variant->quantity > 0 ? 'bg-success' : 'bg-danger' }}">{{ $variant->quantity }}</span>
                            </td>
                            <td>{{ $variant->price ? '₹'.number_format($variant->price, 2) : '—' }}</td>
                            <td><code>{{ $variant->sku ?? '—' }}</code></td>
                            <td><span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $variant->is_active ? 'Active' : 'Inactive' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Images --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-images me-2"></i> Images ({{ $product->images->count() }})</div>
            <div class="card-body">
                @if($product->images->count())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $img)
                        <img src="{{ $img->url }}" alt="Product image"
                             style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-2"><i class="fas fa-image"></i> No images uploaded.</p>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-header"><i class="fas fa-cogs me-2"></i> Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('vendor.products.edit', $product) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Product
                </a>
                <a href="{{ route('vendor.inventory.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-cubes"></i> Manage Inventory
                </a>
                <form action="{{ route('vendor.products.destroy', $product) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this product permanently?')">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
