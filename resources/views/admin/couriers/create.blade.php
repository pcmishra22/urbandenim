@extends('layouts.dashboard')

@section('title', 'Add Courier - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-plus"></i> Add Courier</h2>
    <a href="{{ route('admin.couriers.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Courier Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.couriers.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" class="form-control" required>
                <div class="form-text">Unique short code (e.g., DHL, FEDEX)</div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label">Active</label>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection

