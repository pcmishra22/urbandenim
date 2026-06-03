@extends('layouts.dashboard')

@section('title', 'Inventory - Warehouse Inventory')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-warehouse"></i> Warehouse Inventory</h2>
</div>

<div class="card">
    <div class="card-header">
        <span>Warehouses</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Variants Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $warehouse)
                    <tr>
                        <td><strong>#{{ $warehouse->id }}</strong></td>
                        <td>{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->location ?? '-' }}</td>
                        <td>{{ $warehouse->variants->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No warehouses found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $warehouses->links() }}
    </div>
</div>
@endsection

