@extends('layouts.dashboard')

@section('title', 'Category Details')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-th"></i> {{ $category->name }}</h2>
    <div class="btn-group">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <span>Category Information</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Name:</strong> {{ $category->name }}
                </div>
                <div class="mb-3">
                    <strong>Slug:</strong> <code>{{ $category->slug }}</code>
                </div>
                <div class="mb-3">
                    <strong>Description:</strong> {{ $category->description ?? 'N/A' }}
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    @if($category->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Sort Order:</strong> {{ $category->sort_order ?? 0 }}
                </div>
                <div class="mb-3">
                    <strong>Created At:</strong> {{ $category->created_at->format('M d, Y H:i') }}
                </div>
                <div class="mb-3">
                    <strong>Updated At:</strong> {{ $category->updated_at->format('M d, Y H:i') }}
                </div>
            </div>
        </div>

        @if($category->children->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <span>Subcategories ({{ $category->children->count() }})</span>
                </div>
                <div class="list-group">
                    @foreach($category->children as $child)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $child->name }}</h6>
                                    <small class="text-muted">{{ $child->slug }}</small>
                                </div>
                                <div>
                                    <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <span>Statistics</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Products:</span>
                        <strong class="badge bg-primary">{{ $products->count() }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Subcategories:</span>
                        <strong class="badge bg-success">{{ $children->count() }}</strong>
                    </div>
                </div>
                @if($category->parent)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Parent Category:</span>
                            <a href="{{ route('admin.categories.show', $category->parent) }}" class="badge bg-info">
                                {{ $category->parent->name }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($products->count() > 0)
            <div class="card">
                <div class="card-header">
                    <span>Recent Products</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($products->take(5) as $product)
                        <a href="{{ route('admin.products.show', $product) }}" class="list-group-item list-group-item-action">
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-muted">₹{{ $product->price }}</small>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
