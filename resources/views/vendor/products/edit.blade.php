@extends('layouts.vendor')

@section('title', 'Edit Product')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-edit"></i> Edit: {{ $product->name }}</h2>
    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('vendor.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle"></i> Basic Information</h5>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                               id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                               id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <textarea class="form-control" id="short_description" name="short_description" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Full Description</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
            </div>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-folder"></i> Categorization</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                    {{ $category->parent ? '→ ' : '' }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-tag"></i> Pricing</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        </div>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Sale Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control"
                                   id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-sliders-h"></i> Attributes</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">-- Select --</option>
                            <option value="men"    @selected(old('gender', $product->gender) == 'men')>Men</option>
                            <option value="women"  @selected(old('gender', $product->gender) == 'women')>Women</option>
                            <option value="boys"   @selected(old('gender', $product->gender) == 'boys')>Boys</option>
                            <option value="girls"  @selected(old('gender', $product->gender) == 'girls')>Girls</option>
                            <option value="unisex" @selected(old('gender', $product->gender) == 'unisex')>Unisex</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="age_group" class="form-label">Age Group</label>
                        <select class="form-select" id="age_group" name="age_group">
                            <option value="">-- Select --</option>
                            <option value="kids"  @selected(old('age_group', $product->age_group) == 'kids')>Kids</option>
                            <option value="teen"  @selected(old('age_group', $product->age_group) == 'teen')>Teen</option>
                            <option value="adult" @selected(old('age_group', $product->age_group) == 'adult')>Adult</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="color_family" class="form-label">Color Family</label>
                        <select class="form-select" id="color_family" name="color_family">
                            <option value="">-- Select --</option>
                            <option value="black" @selected(old('color_family', $product->color_family) == 'black')>Black</option>
                            <option value="blue"  @selected(old('color_family', $product->color_family) == 'blue')>Blue</option>
                            <option value="white" @selected(old('color_family', $product->color_family) == 'white')>White</option>
                            <option value="navy"  @selected(old('color_family', $product->color_family) == 'navy')>Navy</option>
                            <option value="gray"  @selected(old('color_family', $product->color_family) == 'gray')>Gray</option>
                            <option value="other" @selected(old('color_family', $product->color_family) == 'other')>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-images"></i> Product Images</h5>

            @if($product->images->isNotEmpty())
            <div class="mb-3">
                <label class="form-label">Current Images</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($product->images as $img)
                    <div class="text-center" style="position:relative;">
                        <img src="{{ $img->url }}" alt="Product image"
                             style="width:100px;height:100px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                        <div class="mt-1">
                            <label class="d-flex align-items-center justify-content-center gap-1 small text-danger" style="cursor:pointer;">
                                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}">
                                Delete
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="form-text text-muted">Check the box under an image to delete it on save.</div>
            </div>
            @endif

            <div class="mb-3">
                <label for="images" class="form-label">Add More Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp">
                <div class="form-text">Max 2MB each. JPG, PNG, WEBP.</div>
            </div>
            <div id="image-preview" class="d-flex flex-wrap gap-2 mb-3"></div>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-layer-group"></i> Product Variants</h5>
            <div id="variant-wrapper">
                @foreach($product->variants as $vi => $variant)
                <div class="card mb-3 variant-item">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3"><label>Size</label><input type="text" name="variants[{{ $vi }}][size]" class="form-control" value="{{ $variant->waist_size }}"></div>
                            <div class="col-md-2"><label>Color</label><input type="text" name="variants[{{ $vi }}][color]" class="form-control" value="{{ $variant->color }}"></div>
                            <div class="col-md-2"><label>Stock</label><input type="number" name="variants[{{ $vi }}][stock]" class="form-control" value="{{ $variant->quantity }}"></div>
                            <div class="col-md-2"><label>Price</label><input type="number" step="0.01" name="variants[{{ $vi }}][price]" class="form-control" value="{{ $variant->price }}"></div>
                            <div class="col-md-2"><label>SKU</label><input type="text" name="variants[{{ $vi }}][sku]" class="form-control" value="{{ $variant->sku }}"></div>
                            <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger remove-variant">✕</button></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary mb-4" id="add-variant-btn">
                <i class="fas fa-plus"></i> Add Variant
            </button>

            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cog"></i> Status</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
                        <label class="form-check-label" for="is_featured"><i class="fas fa-star"></i> Featured Product</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                        <label class="form-check-label" for="is_active"><i class="fas fa-eye"></i> Active / Visible</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Product</button>
                <a href="{{ route('vendor.products.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('images').addEventListener('change', function () {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:100px;height:100px;object-fit:cover;border-radius:6px;border:2px dashed #27ae60;';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    let variantIndex = {{ $product->variants->count() }};
    document.getElementById('add-variant-btn').addEventListener('click', function () {
        const html = `
        <div class="card mb-3 variant-item">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><label>Size</label><input type="text" name="variants[${variantIndex}][size]" class="form-control"></div>
                    <div class="col-md-2"><label>Color</label><input type="text" name="variants[${variantIndex}][color]" class="form-control"></div>
                    <div class="col-md-2"><label>Stock</label><input type="number" name="variants[${variantIndex}][stock]" class="form-control" value="0"></div>
                    <div class="col-md-2"><label>Price</label><input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control"></div>
                    <div class="col-md-2"><label>SKU</label><input type="text" name="variants[${variantIndex}][sku]" class="form-control"></div>
                    <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger remove-variant">✕</button></div>
                </div>
            </div>
        </div>`;
        document.getElementById('variant-wrapper').insertAdjacentHTML('beforeend', html);
        variantIndex++;
    });
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) e.target.closest('.variant-item').remove();
    });
});
</script>
@endsection
