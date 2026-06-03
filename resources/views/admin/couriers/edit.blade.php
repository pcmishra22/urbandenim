@extends('layouts.dashboard')

@section('title', 'Edit Courier - Admin')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-edit"></i> Edit Courier</h2>
    <a href="{{ route('admin.couriers.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">Courier Details</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.couriers.update', $courier) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $courier->name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" value="{{ old('code', $courier->code) }}" class="form-control" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $courier->is_active) ? 'checked' : '' }}>
                <label class="form-check-label">Active</label>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection

