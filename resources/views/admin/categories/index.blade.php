@extends('layouts.dashboard')

@section('title', 'Categories Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-th"></i> Categories Management</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Category
    </a>
</div>

<!-- Search & Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search categories...">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header">
        <span>All Categories ({{ $categories->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Subcategories</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong>#{{ $category->id }}</strong></td>
                        <td>
                            <img src="{{ $category->image }}" alt="{{ $category->name }}"
                                 style="height:40px; width:40px; object-fit:cover; border-radius:4px;">
                        </td>
                        <td>{{ $category->name }}</td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>
                            @if($category->parent_id)
                                <span class="badge bg-info">{{ $category->parent->name }}</span>
                            @else
                                <span class="badge bg-secondary">Main</span>
                            @endif
                        </td>
                        <td>
                            @if($category->children->count() > 0)
                                <span class="badge bg-success">{{ $category->children->count() }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php $productCount = $category->products()->count(); @endphp
                            <span class="badge bg-primary">{{ $productCount }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Show Subcategories -->
                    @foreach($category->children as $subcategory)
                        <tr style="background-color: #f5f5f5;">
                            <td><strong>#{{ $subcategory->id }}</strong></td>
                            <td>
                                <img src="{{ $subcategory->image }}" alt="{{ $subcategory->name }}"
                                     style="height:40px; width:40px; object-fit:cover; border-radius:4px;">
                            </td>
                            <td style="padding-left: 40px;">
                                <i class="fas fa-arrow-right text-muted"></i> {{ $subcategory->name }}
                            </td>
                            <td><code>{{ $subcategory->slug }}</code></td>
                            <td><span class="badge bg-info">{{ $category->name }}</span></td>
                            <td><span class="text-muted">-</span></td>
                            <td>
                                @php $subProductCount = $subcategory->products()->count(); @endphp
                                <span class="badge bg-primary">{{ $subProductCount }}</span>
                            </td>
                            <td>
                                @if($subcategory->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.edit', $subcategory) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $subcategory) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No categories found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $categories->links() }}
</div>
@endsection
