@extends('layouts.dashboard')

@section('title', 'Shipping Rules - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-route"></i> Shipping Rules</h2>
    <a href="{{ route('admin.shipping_rules.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Shipping Rule
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span>All Shipping Rules ({{ $rules->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>Region</th>
                    <th>Service Level</th>
                    <th>Base Days</th>
                    <th>Extra Days</th>
                    <th>Status</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                    <tr>
                        <td><strong>#{{ $rule->id }}</strong></td>
                        <td>{{ $rule->country }}</td>
                        <td>{{ $rule->region ?? '-' }}</td>
                        <td>{{ $rule->service_level ?? '-' }}</td>
                        <td>{{ $rule->base_days }}</td>
                        <td>{{ $rule->extra_days }}</td>
                        <td>
                            @if($rule->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.shipping_rules.edit', $rule) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.shipping_rules.destroy', $rule) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this shipping rule?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No shipping rules found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $rules->links() }}
    </div>
</div>
@endsection

