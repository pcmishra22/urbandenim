<div class="mb-3">
    <label class="form-label">Product Images</label>
    <input type="file" name="images[]" multiple class="form-control">
    <small class="text-muted">
        Allowed: JPG, PNG, WEBP | Max: 2MB each
    </small>
</div>

@if(isset($product) && $product->images->count())
<div class="row mt-4">
    @foreach($product->images as $image)
        <div class="col-md-3 mb-3">
            <div class="card">
                <img src="{{ asset('storage/' . $image->image) }}"
                     class="card-img-top"
                     style="height:200px; object-fit:cover;">

                <div class="card-body text-center">
                    <form method="POST"
                          action="{{ route('admin.products.images.delete', $image->id) }}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
