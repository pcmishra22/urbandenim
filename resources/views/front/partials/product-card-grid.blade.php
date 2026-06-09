@php
    /**
     * Grid card for shop page infinite-scroll.
     * Expects: $product
     */
    $img = $product->images->first() ?? null;
    $relativePath = $img ? 'products/' . $product->id . '/images/' . ($img->image ?? '') : '';
    $publicUrl    = $relativePath ? asset('storage/' . $relativePath) : asset('eshopper/img/product-1.jpg');
    $fallbackUrl  = asset('storage/default.jpeg');
    $imgSrc       = ($relativePath && file_exists(public_path('storage/' . $relativePath)))
        ? $publicUrl
        : ($product->images->isNotEmpty() ? $fallbackUrl : asset('eshopper/img/product-1.jpg'));

    $detailUrl    = route('products.detail', $product->slug);
    $reviewCount  = $product->reviews_count ?? ($product->reviews->count() ?? 0);
    $avgRating    = round($product->reviews_avg_rating ?? ($product->reviews->avg('rating') ?? 0), 1);
@endphp

<div class="col-lg-4 col-md-6 col-sm-12 pb-1">
    <div class="card product-item border-0 mb-4">
        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            <a href="{{ $detailUrl }}">
                <img class="img-fluid w-100" src="{{ $imgSrc }}" alt="{{ $product->name }}">
            </a>
        </div>

        <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
            <a href="{{ $detailUrl }}" class="text-dark text-decoration-none">
                <h6 class="text-truncate mb-3">{{ $product->name }}</h6>
            </a>
            <div class="d-flex justify-content-center">
                <h6>₹{{ number_format($product->sale_price ?? $product->price, 2) }}</h6>
                @if($product->sale_price && $product->sale_price < $product->price)
                    <h6 class="text-muted ml-2"><del>₹{{ number_format($product->price, 2) }}</del></h6>
                @endif
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center bg-light border">
            <div class="text-warning" style="font-size:13px;letter-spacing:1px;">
                @for($s = 1; $s <= 5; $s++)
                    @if($avgRating >= $s)<i class="fas fa-star"></i>
                    @elseif($avgRating >= $s - 0.5)<i class="fas fa-star-half-alt"></i>
                    @else<i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <small class="text-muted">
                @if($reviewCount > 0)
                    <a href="{{ $detailUrl }}#reviews" class="text-muted text-decoration-none">
                        {{ $reviewCount }} {{ \Str::plural('review', $reviewCount) }}
                    </a>
                @else
                    No reviews yet
                @endif
            </small>
        </div>
    </div>
</div>

