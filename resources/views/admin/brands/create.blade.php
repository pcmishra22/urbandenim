@extends('layouts.dashboard')

@section('title', 'Create Brand')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-plus"></i> Add New Brand</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" 
                       placeholder="e.g., Levi's" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Brand Logo (Optional)</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                       id="logo" name="logo" accept="image/*">
                <div class="form-text">Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Max 2MB.</div>
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Brand Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4"
                          placeholder="Brand description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr />
            <h5 class="mb-3">Brand SEO</h5>

            <div class="mb-3">
                <label for="seo_title" class="form-label">SEO Title</label>
                <input type="text" class="form-control @error('seo_title') is-invalid @enderror" 
                       id="seo_title" name="seo_title" value="{{ old('seo_title') }}"
                       placeholder="e.g., Levi's Jeans | Official Brand" />
                @error('seo_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="seo_description" class="form-label">SEO Description</label>
                <textarea class="form-control @error('seo_description') is-invalid @enderror" 
                          id="seo_description" name="seo_description" rows="3"
                          placeholder="SEO description...">{{ old('seo_description') }}</textarea>
                @error('seo_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="seo_keywords" class="form-label">SEO Keywords</label>
                <textarea class="form-control @error('seo_keywords') is-invalid @enderror" 
                          id="seo_keywords" name="seo_keywords" rows="2"
                          placeholder="jeans, denim, ...">{{ old('seo_keywords') }}</textarea>
                @error('seo_keywords')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active') == '1')>Active</option>
                        <option value="0" @selected(old('is_active') == '0')>Inactive</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Featured Brand</label>
                    <select name="is_featured" class="form-select @error('is_featured') is-invalid @enderror">
                        <option value="1" @selected(old('is_featured') == '1')>Yes</option>
                        <option value="0" @selected(old('is_featured') == '0')>No</option>
                    </select>
                    @error('is_featured')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Brand
                </button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

