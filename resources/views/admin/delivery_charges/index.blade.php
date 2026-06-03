@extends('layouts.dashboard')

@section('title', 'Delivery Charges - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-dollar-sign"></i> Delivery Charges</h2>
    <a href="{{ route('admin.delivery_charges.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Delivery Charge Tier
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span>All Delivery Charge Tiers ({{ $charges->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shipping Rule</th>
                    <th>Weight Range</th>
                    <th>Charge</th>
                    <th>Status</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($charges as $charge)
                    <tr>
                        <td><strong>#{{ $charge->id }}</strong></td>
                        <td>
                            @php
                                $r = $charge->shippingRule;
                            @endphp
                            {{ $r ? ($r->country . ' / ' . ($r->region ?? '-') ) : '-' }}
                            @if($r && $r->service_level)
                                <div class="text-muted" style="font-size: 0.85rem;">{{ $r->service_level }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $charge->weight_from }} - {{ $charge->weight_to }}
                        </td>
                        <td>
                            {{ $charge->currency }} {{ $charge->charge_amount }}
                        </td>
                        <td>
                            @if($charge->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.delivery_charges.edit', $charge) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.delivery_charges.destroy', $charge) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tier?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No delivery charges found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $charges->links() }}
    </div>
</div>
@endsection

