@extends('layouts.dashboard')

@section('title', 'Edit Delivery Charge Tier - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-edit"></i> Edit Delivery Charge Tier</h2>
    <a href="{{ route('admin.delivery_charges.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Tier Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.delivery_charges.update', $charge) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Shipping Rule</label>
                    <select class="form-select" name="shipping_rule_id" required>
                        @foreach($rules as $rule)
                            <option value="{{ $rule->id }}" {{ (string)$charge->shipping_rule_id === (string)$rule->id ? 'selected' : '' }}>
                                {{ $rule->country }} / {{ $rule->region ?? '-' }}{{ $rule->service_level ? ' (' . $rule->service_level . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight From</label>
                    <input type="number" step="0.01" class="form-control" name="weight_from" value="{{ old('weight_from', $charge->weight_from) }}" min="0" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Weight To</label>
                    <input type="number" step="0.01" class="form-control" name="weight_to" value="{{ old('weight_to', $charge->weight_to) }}" min="0" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Charge Amount</label>
                    <input type="number" step="0.01" class="form-control" name="charge_amount" value="{{ old('charge_amount', $charge->charge_amount) }}" min="0" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" class="form-control" name="currency" value="{{ old('currency', $charge->currency) }}" maxlength="3">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Active</label>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $charge->is_active) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection

