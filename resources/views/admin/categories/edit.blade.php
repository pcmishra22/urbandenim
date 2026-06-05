@extends('layouts.dashboard')

@section('title', 'Edit Category')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-edit"></i> Edit Category: {{ $category->name }}</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $category->name) }}"
                               placeholder="e.g., Designer Jeans" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                               placeholder="e.g., designer-jeans">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3"
                          placeholder="Category description...">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category <small class="text-muted">(optional)</small></label>
                        <select class="form-select @error('parent_id') is-invalid @enderror"
                                id="parent_id" name="parent_id">
                            <option value="">-- No Parent (Main Category) --</option>
                            @foreach($parentCategories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order"
                               value="{{ old('sort_order', $category->sort_order ?? 0) }}" placeholder="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" @selected(old('is_active', $category->is_active) == 1)>Active</option>
                            <option value="0" @selected(old('is_active', $category->is_active) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Category Image -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-image"></i> Category Image</h5>

            <div class="mb-3">
                @if($category->image_url)
                    <label class="form-label">Current Image</label>
                    <div class="mb-2 d-flex align-items-center gap-3">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                             id="current-image"
                             style="width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                <label class="form-check-label text-danger" for="remove_image">
                                    Remove current image
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Or upload a new image below to replace it.</small>
                        </div>
                    </div>
                @else
                    <div class="mb-2">
                        <img src="{{ asset('storage/default.jpeg') }}" alt="Default"
                             style="width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;opacity:.5;">
                        <small class="text-muted d-block mt-1">No image set — using default.</small>
                    </div>
                @endif

                <label for="image" class="form-label">Upload New Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <div class="form-text">Accepted: JPEG, PNG, JPG, GIF, WEBP. Max 2MB.
                    Saved to <code>storage/categories/{{ $category->id }}/images/</code></div>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div id="image-preview" class="mt-2"></div>
            </div>

            <!-- SEO -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-search"></i> SEO</h5>

            <div class="mb-3">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                       id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"
                       placeholder="SEO Title (defaults to Name)">
                @error('meta_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                          id="meta_description" name="meta_description" rows="2"
                          placeholder="SEO Description...">{{ old('meta_description', $category->meta_description) }}</textarea>
                @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="canonical_url" class="form-label">Canonical URL</label>
                <input type="url" class="form-control @error('canonical_url') is-invalid @enderror"
                       id="canonical_url" name="canonical_url" value="{{ old('canonical_url', $category->canonical_url) }}"
                       placeholder="https://example.com/custom-url">
                @error('canonical_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Image preview for new upload
    document.getElementById('image').addEventListener('change', function () {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        if (this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:120px;height:120px;object-fit:cover;border-radius:6px;border:2px dashed #0d6efd;';
                img.title = 'New image preview';
                preview.appendChild(img);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Grey out current image when "remove" is checked
    const removeCheckbox = document.getElementById('remove_image');
    const currentImg = document.getElementById('current-image');
    if (removeCheckbox && currentImg) {
        removeCheckbox.addEventListener('change', function () {
            currentImg.style.opacity = this.checked ? '0.3' : '1';
        });
    }
});
</script>
@endsection
