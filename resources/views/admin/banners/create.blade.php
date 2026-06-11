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
                    <select name="type" id="banner-type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="" disabled @selected(!$type)>Select Type</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected($type === $t || old('type') === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                </div>
            </div>

            {{-- PAGE HEADER specific fields --}}
            <div id="page-header-fields" style="display:none;">
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Page Header Banner</strong> — Set the <strong>Page Key</strong> to match a specific page, or leave blank for a global default shown on all pages.
                    <div class="mt-2">
                        <strong>Available page keys:</strong>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach(['shop','product-detail','Shopping Cart','checkout','Order Confirmed','My Account','My Orders','Order Details','My Wishlist','My Addresses','Personal Info','Change Password','Contact Us','FAQs','Help Center','About','Blog'] as $key)
                                <code class="bg-light px-2 py-1 rounded small cursor-pointer page-key-chip" style="cursor:pointer;" title="Click to use">{{ $key }}</code>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Page Key <small class="text-muted">(which page to show this on)</small></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="e.g., shop  or  Shopping Cart  (leave blank = global)">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="heading" class="form-label">Custom Heading <small class="text-muted">(optional override)</small></label>
                        <input type="text" class="form-control @error('heading') is-invalid @enderror"
                               id="heading" name="heading" value="{{ old('heading') }}"
                               placeholder="e.g., Our Collection — leave blank to use page title">
                        @error('heading')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Non-page-header title field --}}
            <div id="normal-title-field">
                <div class="mb-3">
                    <label for="title_plain" class="form-label">Title <small class="text-muted">(optional)</small></label>
                    <input type="text" class="form-control" id="title_plain" placeholder="e.g., Summer Sale">
                    <div class="form-text text-muted">Not used for page_header type — switch type to see page header fields.</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('image') is-invalid @enderror"
                       id="image" name="image" accept="image/*" required>
                <div class="form-text">Recommended size: <strong>1400 × 400px</strong> for page headers. JPEG, PNG, WEBP. Max 4MB.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div id="img-preview" class="mt-2" style="display:none;">
                    <img id="img-preview-src" src="" alt="Preview" style="max-height:180px;border-radius:6px;border:1px solid #ddd;">
                </div>
            </div>

            <div class="mb-3">
                <label for="link_url" class="form-label">Link URL <small class="text-muted">(optional)</small></label>
                <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                       id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://example.com/sale">
                @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" @selected(old('is_active') !== '0')>Active</option>
                    <option value="0" @selected(old('is_active') === '0')>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Banner</button>
                <a href="{{ route('admin.banners.index', ['type' => $type]) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
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
    const titleInput   = document.getElementById('title');

    function toggleFields() {
        const isPageHeader = typeSelect.value === 'page_header';
        phFields.style.display     = isPageHeader ? '' : 'none';
        normalFields.style.display = isPageHeader ? 'none' : '';
    }
    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // init

    // Click chip to fill page key
    document.querySelectorAll('.page-key-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            titleInput.value = chip.textContent.trim();
            titleInput.focus();
        });
    });

    // Image preview
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('img-preview-src').src = e.target.result;
            document.getElementById('img-preview').style.display = '';
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
