@extends('layouts.dashboard')

@section('title', 'Product Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-box"></i> {{ $product->name }}</h2>
    <div class="btn-group">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Product Information -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Product Information</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Product Name:</strong>
                        <p>{{ $product->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>SKU:</strong>
                        <p><code>{{ $product->sku ?? 'N/A' }}</code></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Slug:</strong>
                        <p><code>{{ $product->slug }}</code></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            @if($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                            @if($product->is_featured)
                                <span class="badge bg-warning"><i class="fas fa-star"></i> Featured</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Short Description:</strong>
                    <p>{{ $product->short_description ?? 'N/A' }}</p>
                </div>

                <div class="mb-3">
                    <strong>Description:</strong>
                    <p>{{ $product->description ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Categorization -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Categorization</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Category:</strong>
                        @if($product->category)
                            <p><span class="badge bg-info">{{ $product->category->name }}</span></p>
                        @else
                            <p><span class="text-muted">Not assigned</span></p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Brand:</strong>
                        @if($product->brand)
                            <p><span class="badge bg-secondary">{{ $product->brand->name }}</span></p>
                        @else
                            <p><span class="text-muted">Not assigned</span></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Pricing</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Regular Price:</strong>
                        <p class="h5 text-primary">₹{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Sale Price:</strong>
                        @if($product->sale_price)
                            <p class="h5 text-success">₹{{ number_format($product->sale_price, 2) }}</p>
                            <small class="text-muted">Discount: ₹{{ number_format($product->price - $product->sale_price, 2) }}</small>
                        @else
                            <p class="text-muted">Not set</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attributes -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Attributes</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Gender:</strong>
                        <p>{{ $product->gender ? ucfirst($product->gender) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Age Group:</strong>
                        <p>{{ $product->age_group ? ucfirst($product->age_group) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Fit Type:</strong>
                        <p>{{ $product->fit_type ? ucfirst($product->fit_type) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Color Family:</strong>
                        <p>{{ $product->color_family ? ucfirst($product->color_family) : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card">
            <div class="card-header">
                <span>Metadata</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Created:</strong>
                        <p>{{ $product->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Last Updated:</strong>
                        <p>{{ $product->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <span>Statistics</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Variants:</span>
                        <strong class="badge bg-primary">{{ $variants->count() }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Stock:</span>
                        @php $totalStock = $variants->sum('quantity'); @endphp
                        <strong class="badge @if($totalStock > 0) bg-success @else bg-danger @endif">
                            {{ $totalStock }}
                        </strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Images:</span>
                        <strong class="badge bg-info">{{ $images->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants -->
        @if($variants->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <span>Variants ({{ $variants->count() }})</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($variants as $variant)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        Size: <strong>{{ $variant->waist_size }}</strong> | 
                                        Color: <strong>{{ $variant->color }}</strong>
                                    </h6>
                                    <small class="text-muted">SKU: {{ $variant->sku }}</small>
                                    <br>
                                    <small class="text-muted">Price: ₹{{ $variant->price }}</small>
                                </div>
                                <span class="badge @if($variant->quantity > 0) bg-success @else bg-danger @endif">
                                    {{ $variant->quantity }} in stock
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Images -->
        @if($images->count() > 0)
            <div class="card">
                <div class="card-header">
                    <span>Images ({{ $images->count() }})</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($images as $image)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Image {{ $image->sort_order }}</small>
                                    <br>
                                    <strong>{{ basename($image->image) }}</strong>
                                </div>
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
