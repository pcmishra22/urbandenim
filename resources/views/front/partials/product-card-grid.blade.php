@php
    /**
     * Grid card for shop page infinite-scroll.
     * Expects: $product
     */

    $img = $product->images->first();

    $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
    $relativePath = $img
        ? 'products/' . $product->id . '/images/' . ($img->image ?? '')
        : null;

    // Use default.jpg if image doesn't exist on disk
    if ($relativePath && $img && $storageDisk->exists($relativePath)) {
        $imgSrc = $storageDisk->url($relativePath);
    } else {
        // Fallback to default.jpg
        $imgSrc = asset('storage/default.jpg');
        // Try default.jpeg if default.jpg doesn't exist
        if (!file_exists(public_path('storage/default.jpg'))) {
            $imgSrc = file_exists(public_path('storage/default.jpeg'))
                ? asset('storage/default.jpeg')
                : asset('eshopper/img/product-1.jpg');
        }
    }

    $detailUrl    = route('products.detail', $product->slug);
    $reviewCount  = $product->reviews_count ?? ($product->reviews->count() ?? 0);
    $avgRating    = round($product->reviews_avg_rating ?? ($product->reviews->avg('rating') ?? 0), 1);
@endphp

<div class="col-6 col-md-6 col-lg-4 pb-1 jz-prod-col">
    <div class="card product-item border-0 mb-4 jz-prod-card">
        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            <a href="{{ $detailUrl }}" class="d-block jz-prod-img-wrap">
                <img class="jz-prod-img" src="{{ $imgSrc }}" alt="{{ $product->name }}"
                     onerror="this.onerror=null;this.src='{{ asset('storage/default.jpg') }}';"
                     loading="lazy">
            </a>
        </div>

        <div class="card-body border-left border-right text-center p-0 pt-3 pb-2 jz-prod-body">
            <a href="{{ $detailUrl }}" class="text-dark text-decoration-none">
                <h6 class="text-truncate mb-2 jz-prod-name">{{ $product->name }}</h6>
            </a>
            <div class="d-flex justify-content-center align-items-baseline flex-wrap jz-prod-price">
                <span class="jz-price-main">₹{{ number_format($product->jeanzo_price ?: ($product->sale_price ?? $product->price), 0) }}</span>
                @if(($product->jeanzo_price ?: $product->sale_price) && ($product->jeanzo_price ?: $product->sale_price) < $product->price)
                    <span class="jz-price-del text-muted ml-1"><del>₹{{ number_format($product->price, 0) }}</del></span>
                @endif
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center bg-light border jz-prod-footer">
            <div class="text-warning jz-stars">
                @for($s = 1; $s <= 5; $s++)
                    @if($avgRating >= $s)<i class="fas fa-star"></i>
                    @elseif($avgRating >= $s - 0.5)<i class="fas fa-star-half-alt"></i>
                    @else<i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <small class="text-muted jz-review-count">
                @if($reviewCount > 0)
                    <a href="{{ $detailUrl }}#reviews" class="text-muted text-decoration-none">
                        {{ $reviewCount }} {{ \Str::plural('review', $reviewCount) }}
                    </a>
                @else
                    No reviews
                @endif
            </small>
        </div>
    </div>
</div>

<style>
/* ── Product card base ── */
.jz-prod-img-wrap {
    display: block;
    height: 400px;
    overflow: hidden;
}
.jz-prod-img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: top;
}
.jz-price-main { font-size: 1rem; font-weight: 700; color: #1a1a1a; }
.jz-price-del  { font-size: .82rem; }
.jz-stars      { font-size: 12px; letter-spacing: .5px; }
.jz-review-count { font-size: .72rem; }

/* ── Tablet ── */
@media (max-width: 991px) {
    .jz-prod-img-wrap { height: 320px; }
}

/* ── Mobile: 2-column compact cards ── */
@media (max-width: 575px) {
    .jz-prod-col {
        padding-left: 5px;
        padding-right: 5px;
    }
    .jz-prod-card { margin-bottom: 12px !important; }
    .jz-prod-img-wrap { height: 200px; }
    .jz-prod-body { padding-top: 8px !important; padding-bottom: 6px !important; }
    .jz-prod-name { font-size: .78rem !important; margin-bottom: 4px !important; }
    .jz-price-main { font-size: .88rem; }
    .jz-price-del  { font-size: .72rem; }
    .jz-prod-footer { padding: 6px 8px !important; }
    .jz-stars      { font-size: 10px; }
    .jz-review-count { font-size: .65rem; }
}

/* ── Tiny screens ── */
@media (max-width: 380px) {
    .jz-prod-img-wrap { height: 170px; }
    .jz-prod-name { font-size: .72rem !important; }
    .jz-price-main { font-size: .82rem; }
}
</style>

