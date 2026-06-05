@extends('layouts.dashboard')

@section('title', 'Create Product')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-plus"></i> Add New Product</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle"></i> Basic Information</h5>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name') }}"
                       placeholder="e.g., Blue Slim Fit Jeans" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug') }}"
                               placeholder="Auto-generated if empty">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                               id="sku" name="sku" value="{{ old('sku') }}"
                               placeholder="e.g., BLUE-SLIM-001">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <textarea class="form-control @error('short_description') is-invalid @enderror"
                          id="short_description" name="short_description" rows="2"
                          placeholder="Brief product description...">{{ old('short_description') }}</textarea>
                @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="4"
                          placeholder="Full product description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Categorization -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-folder"></i> Categorization</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->parent ? '→ ' : '' }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id" name="brand_id">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-tag"></i> Pricing</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price') }}"
                                   placeholder="0.00" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Sale Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                   id="sale_price" name="sale_price" value="{{ old('sale_price') }}"
                                   placeholder="0.00">
                        </div>
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Attributes -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-sliders-h"></i> Attributes</h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">-- Select --</option>
                            <option value="men"    @selected(old('gender') == 'men')>Men</option>
                            <option value="women"  @selected(old('gender') == 'women')>Women</option>
                            <option value="boys"   @selected(old('gender') == 'boys')>Boys</option>
                            <option value="girls"  @selected(old('gender') == 'girls')>Girls</option>
                            <option value="unisex" @selected(old('gender') == 'unisex')>Unisex</option>
                        </select>
                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="age_group" class="form-label">Age Group</label>
                        <select class="form-select @error('age_group') is-invalid @enderror" id="age_group" name="age_group">
                            <option value="">-- Select --</option>
                            <option value="kids"  @selected(old('age_group') == 'kids')>Kids</option>
                            <option value="teen"  @selected(old('age_group') == 'teen')>Teen</option>
                            <option value="adult" @selected(old('age_group') == 'adult')>Adult</option>
                        </select>
                        @error('age_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="color_family" class="form-label">Color Family</label>
                        <select class="form-select @error('color_family') is-invalid @enderror" id="color_family" name="color_family">
                            <option value="">-- Select --</option>
                            <option value="black" @selected(old('color_family') == 'black')>Black</option>
                            <option value="blue"  @selected(old('color_family') == 'blue')>Blue</option>
                            <option value="white" @selected(old('color_family') == 'white')>White</option>
                            <option value="navy"  @selected(old('color_family') == 'navy')>Navy</option>
                            <option value="gray"  @selected(old('color_family') == 'gray')>Gray</option>
                            <option value="other" @selected(old('color_family') == 'other')>Other</option>
                        </select>
                        @error('color_family') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-images"></i> Product Images</h5>

            <div class="mb-3">
                <label for="images" class="form-label">Upload Images</label>
                <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                       id="images" name="images[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp">
                <div class="form-text">You can select multiple images. Max 2MB each. Accepted: JPG, PNG, WEBP.<br>
                    Images will be saved to <code>storage/products/{id}/images/</code> — the folder is created automatically.</div>
                @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Preview selected images before upload -->
            <div id="image-preview" class="d-flex flex-wrap gap-2 mb-3"></div>

            <!-- Variant Management -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-layer-group"></i> Product Variants</h5>
            <div id="variant-wrapper"></div>
            <button type="button" class="btn btn-outline-primary mb-4" id="add-variant-btn">
                <i class="fas fa-plus"></i> Add Variant
            </button>

            <!-- Status -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cog"></i> Status</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured'))>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star"></i> Featured Product
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-eye"></i> Active/Visible
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Image preview
    document.getElementById('images').addEventListener('change', function () {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position:relative;display:inline-block;';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ddd;';
                wrapper.appendChild(img);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    });

    // Slug auto-generation from name
    document.getElementById('name').addEventListener('input', function () {
        const slugField = document.getElementById('slug');
        if (!slugField.dataset.manual) {
            slugField.value = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim().replace(/\s+/g, '-');
        }
    });
    document.getElementById('slug').addEventListener('input', function () {
        this.dataset.manual = '1';
    });

    // Variants
    let variantIndex = 0;
    document.getElementById('add-variant-btn').addEventListener('click', function () {
        const html = `
        <div class="card mb-3 variant-item">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Size</label>
                        <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="26">
                    </div>
                    <div class="col-md-2">
                        <label>Color</label>
                        <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Black">
                    </div>
                    <div class="col-md-2">
                        <label>Stock</label>
                        <input type="number" name="variants[${variantIndex}][stock]" class="form-control" value="0">
                    </div>
                    <div class="col-md-2">
                        <label>Price</label>
                        <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>SKU</label>
                        <input type="text" name="variants[${variantIndex}][sku]" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-variant">✕</button>
                    </div>
                </div>
            </div>
        </div>`;
        document.getElementById('variant-wrapper').insertAdjacentHTML('beforeend', html);
        variantIndex++;
    });
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-item').remove();
        }
    });
});
</script>
@endsection
