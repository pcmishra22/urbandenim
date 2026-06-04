@extends('layouts.dashboard')

@section('title', 'Create Banner')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-plus"></i> Add New Banner</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="" disabled @selected(!$type)>Select Type</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected($type === $t || old('type') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" step="1">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title (Optional)</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       id="title" name="title" value="{{ old('title') }}" placeholder="e.g., Summer Sale">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       id="image" name="image" accept="image/*" required>
                <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max 4MB.</div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="link_url" class="form-label">Link URL (Optional)</label>
                <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                       id="link_url" name="link_url" value="{{ old('link_url') }}"
                       placeholder="https://example.com/some-page">
                @error('link_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active') === '1' || old('is_active') === null)>Active</option>
                        <option value="0" @selected(old('is_active') === '0')>Inactive</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Banner
                </button>
                <a href="{{ route('admin.banners.index', ['type' => $type]) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
