<div class="col-lg-3 col-md-6 col-sm-12 pb-1">
    <div class="card product-item border-0 mb-4">
        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            @if($product->images && $product->images->isNotEmpty())
                <img class="img-fluid w-100" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
            @else
                <img class="img-fluid w-100" src="{{ asset('eshopper/img/product-1.jpg') }}" alt="{{ $product->name }}">
            @endif
        </div>
        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
            <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
            <div class="d-flex justify-content-center">
                <h6>₹{{ number_format($product->sale_price ?? $product->price, 2) }}</h6>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <h6 class="text-muted ml-2"><del>₹{{ number_format($product->price, 2) }}</del></h6>
                @endif
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between bg-light border">
            <a href="{{ route('products.detail', $product->slug) }}" class="btn btn-sm text-dark p-0">
                <i class="fas fa-eye text-primary mr-1"></i>View Detail
            </a>
            <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-sm text-dark p-0">
                    <i class="fas fa-shopping-cart text-primary mr-1"></i>Add To Cart
                </button>
            </form>
        </div>
    </div>
</div>
