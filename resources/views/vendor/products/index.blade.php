@extends('layouts.vendor')

@section('title', 'My Products')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-box"></i> My Products</h2>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

{{-- Scoping notice --}}
<div class="alert alert-info alert-dismissible fade show py-2 mb-3">
    <i class="fas fa-info-circle me-1"></i>
    Showing only products belonging to <strong>{{ $vendor->shop_name }}</strong>. You cannot view or edit other vendors' products.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- All Categories --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- All Status --</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100"><i class="fas fa-search"></i> Filter</button>
            </div>
            @if(request()->hasAny(['search','category_id','status']))
            <div class="col-md-1">
                <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-danger w-100"><i class="fas fa-times"></i></a>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Products Table --}}
<div class="card">
    <div class="card-header">
        <span>My Products ({{ $products->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Sale Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php $stock = $product->variants->sum('quantity'); @endphp
                <tr>
                    <td><strong>#{{ $product->id }}</strong></td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <small class="text-muted">SKU: {{ $product->sku ?? '—' }}</small>
                    </td>
                    <td>
                        @if($product->category)
                            <span class="badge bg-info">{{ $product->category->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><strong>₹{{ number_format($product->price, 2) }}</strong></td>
                    <td>
                        @if($product->sale_price)
                            <strong class="text-success">₹{{ number_format($product->sale_price, 2) }}</strong>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ $stock }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('vendor.products.show', $product) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('vendor.products.edit', $product) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('vendor.products.destroy', $product) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this product?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                        No products found. <a href="{{ route('vendor.products.create') }}">Create your first product!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endsection
