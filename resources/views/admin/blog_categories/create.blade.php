@extends('layouts.dashboard')

@section('title', 'Create Blog Category')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-folder-plus"></i> Create Blog Category</h2>
    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('admin.blog.categories.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (optional)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                <div class="form-text">If empty, slug will be generated from name.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Image URL (optional)</label>
                <input type="text" name="image_url" class="form-control" value="{{ old('image_url') }}">
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label">Active</label>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection

