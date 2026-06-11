@extends('layouts.dashboard')

@section('title', 'Edit Banner')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-edit"></i> Edit Banner</h2>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Banner Type <span class="text-danger">*</span></label>
                    <select name="type" id="banner-type" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(old('type', $banner->type) === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                </div>
            </div>

            {{-- Page header specific --}}
            <div id="page-header-fields" style="{{ old('type', $banner->type) === 'page_header' ? '' : 'display:none;' }}">
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Page Header Banner</strong> — Set Page Key to target a specific page, or leave blank for global default.
                    <div class="mt-2">
                        <strong>Page keys:</strong>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach(['shop','product-detail','Shopping Cart','checkout','Order Confirmed','My Account','My Orders','Order Details','My Wishlist','My Addresses','Personal Info','Change Password','Contact Us','FAQs','Help Center','About','Blog'] as $key)
                                <code class="bg-light px-2 py-1 rounded small page-key-chip" style="cursor:pointer;">{{ $key }}</code>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Page Key <small class="text-muted">(which page)</small></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $banner->title) }}"
                               placeholder="e.g., shop  (leave blank = global)">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Custom Heading <small class="text-muted">(optional)</small></label>
                        <input type="text" class="form-control @error('heading') is-invalid @enderror"
                               name="heading" value="{{ old('heading', $banner->heading) }}"
                               placeholder="Leave blank to use page title">
                        @error('heading')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div id="normal-title-field" style="{{ old('type', $banner->type) !== 'page_header' ? '' : 'display:none;' }}">
                <div class="mb-3">
                    <label class="form-label">Title <small class="text-muted">(optional)</small></label>
                    <input type="text" class="form-control" name="title_plain" value="{{ old('title', $banner->title) }}" placeholder="e.g., Summer Sale">
                </div>
            </div>

            {{-- Current image --}}
            @if($banner->image_url)
            <div class="mb-3">
                <label class="form-label">Current Image</label><br>
                <img src="{{ $banner->image }}" alt="Current banner"
                     style="max-height:150px;border-radius:6px;border:1px solid #ddd;">
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Replace Image <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       id="image" name="image" accept="image/*">
                <div class="form-text">Recommended: <strong>1400 × 400px</strong> for page headers. Max 4MB.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div id="img-preview" style="display:none;" class="mt-2">
                    <img id="img-preview-src" src="" alt="Preview" style="max-height:150px;border-radius:6px;border:1px solid #ddd;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Link URL <small class="text-muted">(optional)</small></label>
                <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                       name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://...">
                @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" @selected(old('is_active', $banner->is_active ? '1' : '0') === '1' || old('is_active', $banner->is_active ? '1' : '0') === true)>Active</option>
                    <option value="0" @selected(old('is_active', $banner->is_active ? '1' : '0') === '0')>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Banner</button>
                <a href="{{ route('admin.banners.index', ['type' => $banner->type]) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect   = document.getElementById('banner-type');
    const phFields     = document.getElementById('page-header-fields');
    const normalFields = document.getElementById('normal-title-field');

    typeSelect.addEventListener('change', function() {
        const isPageHeader = this.value === 'page_header';
        phFields.style.display     = isPageHeader ? '' : 'none';
        normalFields.style.display = isPageHeader ? 'none' : '';
    });

    document.querySelectorAll('.page-key-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            document.getElementById('title').value = chip.textContent.trim();
        });
    });

    document.getElementById('image').addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('img-preview-src').src = e.target.result;
            document.getElementById('img-preview').style.display = '';
        };
        reader.readAsDataURL(this.files[0]);
    });
});
</script>
@endpush
