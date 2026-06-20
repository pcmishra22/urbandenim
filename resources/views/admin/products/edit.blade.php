@extends('layouts.dashboard')

@section('title', 'Edit Product')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-edit"></i> Edit Product: {{ $product->name }}</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle"></i> Basic Information</h5>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $product->name) }}"
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
                               id="slug" name="slug" value="{{ old('slug', $product->slug) }}"
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
                               id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
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
                          placeholder="Brief product description...">{{ old('short_description', $product->short_description) }}</textarea>
                @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="model_info" class="form-label">Model Info <small class="text-muted">(shown on product page)</small></label>
                    <input type="text" class="form-control @error('model_info') is-invalid @enderror"
                           id="model_info" name="model_info"
                           placeholder="e.g. Model is 5'5&quot;, wearing size 30"
                           value="{{ old('model_info', $product->model_info) }}">
                    @error('model_info')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Helps buyers choose the right size. Example: <em>Model is 5'4", 58 kg, wearing size 28</em></small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fabric_info" class="form-label">Fabric Info <small class="text-muted">(shown on product page)</small></label>
                    <input type="text" class="form-control @error('fabric_info') is-invalid @enderror"
                           id="fabric_info" name="fabric_info"
                           placeholder="e.g. 98% Cotton, 2% Elastane — Medium Stretch"
                           value="{{ old('fabric_info', $product->fabric_info) }}">
                    @error('fabric_info')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Example: <em>99% Cotton, 1% Lycra — High Stretch</em></small>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="4"
                          placeholder="Full product description...">{{ old('description', $product->description) }}</textarea>
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
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
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
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>
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
                                   id="price" name="price" value="{{ old('price', $product->price) }}"
                                   placeholder="0.00" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sale_price" class="form-label">Sale Price (Jeanzo Display Price) <small class="text-muted">(auto-calculated or manual)</small></label>
                        @if($product->vendor && $product->vendor_sale_price)
                        <div class="alert alert-info py-2 mb-2" style="font-size:.82rem;">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Vendor set their price at ₹{{ number_format($product->vendor_sale_price, 2) }}</strong>
                            — Set courier charge + profit margin below to auto-calculate Jeanzo's display price.
                            Current Jeanzo price: <strong>₹{{ number_format($product->jeanzo_price, 2) }}</strong>
                        </div>
                        @endif
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                                   id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                                   placeholder="0.00">
                        </div>
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Fill cost + courier + margin below to auto-calculate ↓</small>
                    </div>
                </div>
            </div>

            <!-- Pricing Calculator -->
            <div class="card border-0 bg-light mb-3">
                <div class="card-body py-3">
                    <h6 class="mb-3" style="font-size:.88rem;font-weight:600;color:#555;">
                        <i class="fas fa-calculator mr-1"></i> Price Calculator (optional — fills Sale Price automatically)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label" style="font-size:.82rem;">Cost Price (₹)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm price-calc"
                                   id="cost_price" name="cost_price"
                                   value="{{ old('cost_price', $product->cost_price) }}"
                                   placeholder="Vendor cost">
                            <small class="text-muted" style="font-size:.72rem;">What you pay the vendor</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label" style="font-size:.82rem;">Courier Charge (₹)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm price-calc"
                                   id="courier_charge" name="courier_charge"
                                   value="{{ old('courier_charge', $product->courier_charge ?? 0) }}"
                                   placeholder="e.g. 60">
                            <small class="text-muted" style="font-size:.72rem;">Shipping cost per order</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label" style="font-size:.82rem;">Profit Margin (%)</label>
                            <input type="number" step="0.1" class="form-control form-control-sm price-calc"
                                   id="profit_margin" name="profit_margin"
                                   value="{{ old('profit_margin', $product->profit_margin ?? 0) }}"
                                   placeholder="e.g. 30">
                            <small class="text-muted" style="font-size:.72rem;">Jeanzo markup %</small>
                        </div>
                    </div>
                    <div class="mt-2" style="font-size:.82rem;color:#27ae60;font-weight:600;" id="calc-preview"></div>
                </div>
            </div>

            <script>
            (function(){
                var vendorBasePrice = {{ $product->vendor_sale_price ?? 'null' }};
                function recalc(){
                    var cost    = parseFloat(document.getElementById('cost_price').value) || 0;
                    var courier = parseFloat(document.getElementById('courier_charge').value) || 0;
                    var margin  = parseFloat(document.getElementById('profit_margin').value) || 0;
                    // Use vendor_sale_price as base if set, otherwise use cost_price
                    var base = vendorBasePrice ? (vendorBasePrice + courier) : (cost + courier);
                    if(base <= 0) { document.getElementById('calc-preview').textContent=''; return; }
                    var final = (base * (1 + margin/100)).toFixed(2);
                    document.getElementById('sale_price').value = final;
                    var baseLabel = vendorBasePrice
                        ? '₹'+vendorBasePrice+' vendor + ₹'+courier+' courier'
                        : '₹'+cost+' cost + ₹'+courier+' courier';
                    document.getElementById('calc-preview').textContent =
                        '✓ Jeanzo display price = ('+baseLabel+') × '+(1+margin/100).toFixed(2)+' = ₹'+final;
                }
                document.querySelectorAll('.price-calc').forEach(function(el){
                    el.addEventListener('input', recalc);
                });
                recalc();
            })();
            </script>

            <div class="row">
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

            <!-- Product Images -->
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <h5 class="mb-0"><i class="fas fa-images"></i> Product Images</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-generate-ai"
                        onclick="openGenerateModal()">
                    <i class="fas fa-magic"></i> Generate with AI
                </button>
            </div>

            {{-- ── AI Generate Modal ───────────────────────────────────── --}}
            <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="generateModalLabel">
                                <i class="fas fa-magic text-primary"></i> Generate AI Product Images
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">
                                This will use <strong>Hugging Face FLUX.1</strong> to generate 5 product images
                                (Front, Back, Left, Right, Detail) based on this product's details and save them automatically.
                            </p>

                            {{-- Product info preview --}}
                            <div class="alert alert-light border small mb-3">
                                <div><strong>Product:</strong> {{ $product->name }}</div>
                                @if($product->color_family)<div><strong>Color:</strong> {{ ucfirst($product->color_family) }}</div>@endif
                                @if($product->gender)<div><strong>Gender:</strong> {{ ucfirst($product->gender) }}</div>@endif
                                @if($product->category)<div><strong>Category:</strong> {{ $product->category->name }}</div>@endif
                            </div>

                            {{-- Progress area (hidden until generation starts) --}}
                            <div id="gen-progress" style="display:none;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span id="gen-status-text" class="small fw-semibold">Starting generation…</span>
                                </div>
                                <div class="progress mb-3" style="height:6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         id="gen-progress-bar" role="progressbar" style="width:0%"></div>
                                </div>
                                <div id="gen-preview-row" class="d-flex flex-wrap gap-2"></div>
                            </div>

                            {{-- Error area --}}
                            <div id="gen-error" class="alert alert-danger small" style="display:none;"></div>

                            {{-- Success area --}}
                            <div id="gen-success" class="alert alert-success small" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" id="btn-modal-close">Cancel</button>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-start-generate" onclick="startGeneration()">
                                <i class="fas fa-magic"></i> Generate 5 Images
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $viewSlots = [
                    0 => ['label' => 'Front View',   'icon' => 'fa-street-view',  'hint' => 'Main front-facing photo'],
                    1 => ['label' => 'Back View',    'icon' => 'fa-undo',         'hint' => 'Back of the product'],
                    2 => ['label' => 'Left Side',    'icon' => 'fa-arrow-left',   'hint' => 'Left side angle'],
                    3 => ['label' => 'Right Side',   'icon' => 'fa-arrow-right',  'hint' => 'Right side angle'],
                    4 => ['label' => 'Detail / Flat','icon' => 'fa-search-plus',  'hint' => 'Close-up or flat lay'],
                ];
                $existingImages = $product->images->sortBy('sort_order')->values();
            @endphp

            <div class="row g-3 mb-3" id="image-slots">
                @foreach($viewSlots as $slotIndex => $slot)
                @php $existingImg = $existingImages->get($slotIndex); @endphp
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="image-slot card h-100 text-center p-2"
                         style="border:2px dashed #dee2e6;border-radius:10px;cursor:pointer;transition:border-color .2s;"
                         onclick="document.getElementById('slot-input-{{ $slotIndex }}').click()"
                         id="slot-{{ $slotIndex }}">

                        <div class="slot-preview mb-2" style="height:120px;display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:8px;background:#f8f9fa;">
                            @if($existingImg)
                                <img src="{{ $existingImg->url }}"
                                     id="slot-img-{{ $slotIndex }}"
                                     style="width:100%;height:100%;object-fit:cover;border-radius:8px;"
                                     alt="{{ $slot['label'] }}">
                            @else
                                <div id="slot-placeholder-{{ $slotIndex }}" style="padding:10px;">
                                    <i class="fas {{ $slot['icon'] }} fa-2x text-muted d-block mb-1"></i>
                                    <div class="small text-muted" style="font-size:.7rem;">Click to upload</div>
                                </div>
                                <img id="slot-img-{{ $slotIndex }}"
                                     style="display:none;width:100%;height:100%;object-fit:cover;border-radius:8px;"
                                     alt="{{ $slot['label'] }}">
                            @endif
                        </div>

                        <div class="fw-semibold" style="font-size:.75rem;">{{ $slot['label'] }}</div>
                        <div class="text-muted" style="font-size:.65rem;">{{ $slot['hint'] }}</div>

                        <input type="file"
                               id="slot-input-{{ $slotIndex }}"
                               name="images[]"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               data-slot="{{ $slotIndex }}"
                               style="display:none;">

                        @if($existingImg)
                        <div class="mt-2" onclick="event.stopPropagation()">
                            <label class="d-flex align-items-center justify-content-center gap-1 small text-danger mb-0" style="cursor:pointer;">
                                <input type="checkbox" name="delete_images[]" value="{{ $existingImg->id }}"
                                       id="del-{{ $slotIndex }}"
                                       onchange="toggleDeleteSlot({{ $slotIndex }}, this.checked)">
                                <span>Delete</span>
                            </label>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($existingImages->count() > 5)
                <div class="col-12">
                    <p class="text-muted small mb-2"><i class="fas fa-info-circle"></i> Additional images:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($existingImages->slice(5) as $extraImg)
                        <div class="text-center">
                            <img src="{{ $extraImg->url }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                            <div class="mt-1">
                                <label class="small text-danger d-flex align-items-center gap-1" style="cursor:pointer;">
                                    <input type="checkbox" name="delete_images[]" value="{{ $extraImg->id }}"> Delete
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="form-text text-muted mb-3">
                <i class="fas fa-info-circle"></i>
                Click any slot to upload that view. Max 2MB per image. Accepted: JPG, PNG, WEBP.
            </div>

            @error('images.*')
                <div class="alert alert-danger py-2 small">{{ $message }}</div>
            @enderror

            <!-- Status -->
            <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cog"></i> Status</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star"></i> Featured Product
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active))>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-eye"></i> Active/Visible
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
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
    // Live preview when a slot file is chosen
    document.querySelectorAll('input[data-slot]').forEach(function (input) {
        input.addEventListener('change', function () {
            const slot        = this.dataset.slot;
            const file        = this.files[0];
            if (!file) return;
            const imgEl       = document.getElementById('slot-img-' + slot);
            const placeholder = document.getElementById('slot-placeholder-' + slot);
            const slotCard    = document.getElementById('slot-' + slot);
            const reader      = new FileReader();
            reader.onload = function (e) {
                imgEl.src           = e.target.result;
                imgEl.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
                slotCard.style.borderColor = '#0d6efd';
                slotCard.style.borderStyle = 'solid';
            };
            reader.readAsDataURL(file);
        });
    });
});

function toggleDeleteSlot(slot, checked) {
    const card = document.getElementById('slot-' + slot);
    if (checked) {
        card.style.opacity     = '0.4';
        card.style.borderColor = '#dc3545';
    } else {
        card.style.opacity     = '1';
        card.style.borderColor = '#dee2e6';
    }
}

// ── AI Image Generation ─────────────────────────────────────────
function openGenerateModal() {
    // Reset state
    document.getElementById('gen-progress').style.display  = 'none';
    document.getElementById('gen-error').style.display     = 'none';
    document.getElementById('gen-success').style.display   = 'none';
    document.getElementById('gen-preview-row').innerHTML   = '';
    document.getElementById('gen-progress-bar').style.width = '0%';
    document.getElementById('gen-status-text').textContent  = 'Starting generation…';
    document.getElementById('btn-start-generate').disabled  = false;
    document.getElementById('btn-start-generate').innerHTML = '<i class="fas fa-magic"></i> Generate 5 Images';
    document.getElementById('btn-modal-close').textContent  = 'Cancel';

    const modal = new bootstrap.Modal(document.getElementById('generateModal'));
    modal.show();
}

function startGeneration() {
    const btn      = document.getElementById('btn-start-generate');
    const progress = document.getElementById('gen-progress');
    const errorBox = document.getElementById('gen-error');
    const successBox = document.getElementById('gen-success');
    const statusText = document.getElementById('gen-status-text');
    const progressBar = document.getElementById('gen-progress-bar');
    const previewRow = document.getElementById('gen-preview-row');

    btn.disabled    = true;
    btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span> Generating…';
    progress.style.display  = 'block';
    errorBox.style.display  = 'none';
    successBox.style.display = 'none';
    previewRow.innerHTML    = '';

    // Animate progress bar while waiting (indeterminate feel)
    let fakeProgress = 5;
    const ticker = setInterval(() => {
        if (fakeProgress < 85) {
            fakeProgress += Math.random() * 3;
            progressBar.style.width = fakeProgress + '%';
        }
    }, 800);

    const views = ['Front View', 'Back View', 'Left Side', 'Right Side', 'Detail / Flat'];
    statusText.textContent = 'Sending request to Hugging Face… (this takes 30–90 seconds)';

    fetch('{{ route("admin.products.generate-images", $product) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept':       'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(res => res.json())
    .then(data => {
        clearInterval(ticker);
        progressBar.style.width = '100%';
        progressBar.classList.remove('progress-bar-animated');

        if (data.success) {
            statusText.textContent = data.message;

            // Show generated image thumbnails
            (data.images || []).forEach(img => {
                const wrap = document.createElement('div');
                wrap.className = 'text-center';
                wrap.innerHTML = `
                    <img src="${img.url}" style="width:80px;height:100px;object-fit:cover;border-radius:6px;border:2px solid #0d6efd;">
                    <div class="small text-muted mt-1" style="font-size:.65rem;">${img.label}</div>`;
                previewRow.appendChild(wrap);
            });

            if (data.errors && data.errors.length) {
                errorBox.style.display  = 'block';
                errorBox.innerHTML      = '<strong>Some views failed:</strong><br>' + data.errors.join('<br>');
            }

            successBox.style.display   = 'block';
            successBox.textContent     = '✓ ' + data.message + ' Reload the page to see them in the image slots.';
            btn.innerHTML              = '<i class="fas fa-check"></i> Done';
            document.getElementById('btn-modal-close').textContent = 'Close & Reload';
            document.getElementById('btn-modal-close').onclick     = () => window.location.reload();
        } else {
            statusText.textContent    = 'Generation failed.';
            errorBox.style.display   = 'block';
            errorBox.innerHTML        = data.message + (data.errors ? '<br>' + data.errors.join('<br>') : '');
            btn.disabled              = false;
            btn.innerHTML             = '<i class="fas fa-redo"></i> Retry';
        }
    })
    .catch(err => {
        clearInterval(ticker);
        statusText.textContent   = 'Network error.';
        errorBox.style.display  = 'block';
        errorBox.textContent     = 'Request failed: ' + err.message;
        btn.disabled             = false;
        btn.innerHTML            = '<i class="fas fa-redo"></i> Retry';
    });
}
</script>
@endsection
