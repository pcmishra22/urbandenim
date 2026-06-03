@extends('layouts.dashboard')

@section('title', 'Couriers - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-truck"></i> Couriers</h2>
    <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Courier
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span>All Couriers ({{ $couriers->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($couriers as $courier)
                    <tr>
                        <td><strong>#{{ $courier->id }}</strong></td>
                        <td>{{ $courier->name }}</td>
                        <td><code>{{ $courier->code }}</code></td>
                        <td>
                            @if($courier->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this courier?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No couriers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $couriers->links() }}
    </div>
</div>
@endsection

