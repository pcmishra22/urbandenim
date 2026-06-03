@extends('layouts.dashboard')

@section('title', 'Add Delivery Charge Tier - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-plus"></i> Add Delivery Charge Tier</h2>
    <a href="{{ route('admin.delivery_charges.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Tier Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.delivery_charges.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Shipping Rule</label>
                    <select class="form-select" name="shipping_rule_id" required>
                        <option value="" disabled {{ old('shipping_rule_id') ? '' : 'selected' }}>Select rule...</option>
                        @foreach($rules as $rule)
                            <option value="{{ $rule->id }}" {{ (string)old('shipping_rule_id') === (string)$rule->id ? 'selected' : '' }}>
                                {{ $rule->country }} / {{ $rule->region ?? '-' }}{{ $rule->service_level ? ' (' . $rule->service_level . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight From</label>
                    <input type="number" step="0.01" class="form-control" name="weight_from" value="{{ old('weight_from') }}" min="0" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight To</label>
                    <input type="number" step="0.01" class="form-control" name="weight_to" value="{{ old('weight_to') }}" min="0" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Charge Amount</label>
                    <input type="number" step="0.01" class="form-control" name="charge_amount" value="{{ old('charge_amount') }}" min="0" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" class="form-control" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Active</label>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection

