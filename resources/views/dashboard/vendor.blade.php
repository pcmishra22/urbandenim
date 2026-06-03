@extends('layouts.dashboard')

@section('title', 'Vendor Dashboard')




@section('content')
<h1 class="page-title"><i class="fas fa-store"></i> Vendor Dashboard</h1>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Total Products</h6>
            <div class="value">{{ $total_products }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>In Stock</h6>
            <div class="value" style="color: #27ae60;">{{ $total_products - $low_stock - $out_of_stock }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Low Stock</h6>
            <div class="value" style="color: #f39c12;">{{ $low_stock }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>Out of Stock</h6>
            <div class="value" style="color: #e74c3c;">{{ $out_of_stock }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-box"></i> Inventory Management
        <a href="#" class="btn btn-sm btn-primary float-end">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $status = $product->quantity === 0 
                            ? 'out_of_stock' 
                            : ($product->quantity < 5 ? 'low_stock' : 'in_stock');
                        $status_badge = $status === 'out_of_stock' 
                            ? 'danger' 
                            : ($status === 'low_stock' ? 'warning' : 'success');
                        $status_text = $status === 'out_of_stock' 
                            ? 'Out of Stock' 
                            : ($status === 'low_stock' ? 'Low Stock' : 'In Stock');
                    @endphp
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>
                            <span class="badge bg-{{ $status_badge }}">
                                {{ $status_text }}
                            </span>
                        </td>
                        <td>${{ number_format($product->price * $product->quantity, 2) }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox"></i> No products. Create your first product!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-info-circle"></i> Inventory Summary
        </div>
        <div style="padding: 20px;">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Total Inventory Value:</strong></p>
                    <h4 style="color: var(--secondary-color);">${{ number_format($total_value, 2) }}</h4>
                </div>
                <div class="col-md-6">
                    <p><strong>Average Product Price:</strong></p>
                    <h4 style="color: var(--secondary-color);">
                        ${{ $total_products > 0 ? number_format($products->sum('price') / $total_products, 2) : '0.00' }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
