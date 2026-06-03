@extends('layouts.dashboard')

@section('title', 'Coupons Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-ticket-alt"></i> Coupons Management</h2>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Coupon
    </a>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>All Coupons ({{ $coupons->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Free Shipping</th>
                    <th>Usage</th>
                    <th>Expiry</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($coupons as $coupon)
                    @php
                        $typeLabel = match($coupon->type) {
                            'flat' => 'Flat',
                            'percentage' => 'Percentage',
                            'free_shipping' => 'Free Shipping',
                            default => $coupon->type,
                        };
                        $valueLabel = $coupon->type === 'percentage'
                            ? ($coupon->value . '%')
                            : ($coupon->type === 'flat' ? ($coupon->value ?? 0) : '-');
                    @endphp
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>{{ $typeLabel }}</td>
                        <td>{{ $valueLabel }}</td>
                        <td>
                            @if($coupon->type === 'free_shipping')
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div>Limit: {{ $coupon->usage_limit ?? '∞' }}</div>
                            <div class="text-muted" style="font-size:12px;">Used: {{ $coupon->used_count ?? 0 }}</div>
                        </td>
                        <td>{{ optional($coupon->expires_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            {{ $coupon->user?->name ?? ($coupon->user_id ? ('User #'.$coupon->user_id) : 'All users') }}
                        </td>
                        <td>
                            @if($coupon->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete coupon {{ $coupon->code }}?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No coupons found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $coupons->links() }}
</div>
@endsection

