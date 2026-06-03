@extends('layouts.dashboard')

@section('title', 'Products Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-box"></i> Products Management</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">-- All Categories --</option>
                    @foreach(\App\Models\Category::all() as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="brand_id" class="form-select">
                    <option value="">-- All Brands --</option>
                    @foreach(\App\Models\Brand::all() as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="fit_type" class="form-select">
                    <option value="">-- All Fit Types --</option>
                    @foreach(['Slim','Regular','Relaxed','Oversized'] as $fit)
                        <option value="{{ $fit }}" @selected(request('fit_type') === $fit)>{{ $fit }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" name="color" class="form-control" placeholder="Color (family)" value="{{ request('color') }}">
            </div>

            <div class="col-md-2">
                <input type="number" step="0.01" name="price_min" class="form-control" placeholder="Min price" value="{{ request('price_min') }}">
            </div>

            <div class="col-md-2">
                <input type="number" step="0.01" name="price_max" class="form-control" placeholder="Max price" value="{{ request('price_max') }}">
            </div>

            <div class="col-md-2">
                <input type="text" name="size" class="form-control" placeholder="Size (variant)" value="{{ request('size') }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <span>All Products ({{ $products->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Sale Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><strong>#{{ $product->id }}</strong></td>
                        <td>
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <br>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-info">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($product->brand)
                                <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>₹{{ number_format($product->price, 2) }}</strong>
                        </td>
                        <td>
                            @if($product->sale_price)
                                <strong class="text-success">₹{{ number_format($product->sale_price, 2) }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php $stock = $product->variants()->sum('quantity'); @endphp
                            <span class="badge @if($stock > 0) bg-success @else bg-danger @endif">
                                {{ $stock }}
                            </span>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No products found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endsection
