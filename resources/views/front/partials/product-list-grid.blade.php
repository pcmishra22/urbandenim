@forelse($products as $product)
    @include('front.partials.product-card-grid', ['product' => $product])
@empty
    <div class="col-12 text-center py-5" data-empty-products>
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No products found</h5>
        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Clear Filters</a>
    </div>
@endforelse

