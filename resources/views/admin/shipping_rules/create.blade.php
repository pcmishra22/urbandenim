@extends('layouts.dashboard')

@section('title', 'Add Shipping Rule - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-plus"></i> Add Shipping Rule</h2>
    <a href="{{ route('admin.shipping_rules.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Shipping Rule Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.shipping_rules.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Country (2 letters)</label>
                    <input type="text" name="country" value="{{ old('country') }}" class="form-control" maxlength="2" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Region / Zone</label>
                    <input type="text" name="region" value="{{ old('region') }}" class="form-control" placeholder="e.g., CA, NY, North">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Service Level</label>
                    <input type="text" name="service_level" value="{{ old('service_level') }}" class="form-control" placeholder="e.g., Standard, Express">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Active</label>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Base Days</label>
                    <input type="number" name="base_days" value="{{ old('base_days') }}" class="form-control" min="0" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Extra Days</label>
                    <input type="number" name="extra_days" value="{{ old('extra_days') }}" class="form-control" min="0" required>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection

