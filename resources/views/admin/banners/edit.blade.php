@extends('layouts.dashboard')

@section('title', 'Edit Banner')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-edit"></i> Edit Banner</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(old('type', $banner->type) === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0" step="1">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title (Optional)</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}" placeholder="e.g., Summer Sale">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image_url" class="form-label">Image URL <span class="text-danger">*</span></label>
                <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url', $banner->image_url) }}" placeholder="https://example.com/banner.jpg" required>
                @error('image_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="link_url" class="form-label">Link URL (Optional)</label>
                <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://example.com/some-page">
                @error('link_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1" @selected((string) old('is_active', $banner->is_active) === '1')>Active</option>
                        <option value="0" @selected((string) old('is_active', $banner->is_active) === '0')>Inactive</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Banner
                </button>
                <a href="{{ route('admin.banners.index', ['type' => $banner->type]) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

