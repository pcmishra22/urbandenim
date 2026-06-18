@php
    $reviewCount = $product->reviews_count ?? $product->reviews->count();
    $avgRating   = $product->reviews_avg_rating ?? ($product->reviews->avg('rating') ?? 0);
    $avgRating   = round($avgRating, 1);
    $detailUrl   = route('products.detail', $product->slug);
@endphp

<div class="col-lg-3 col-md-6 col-sm-12 pb-1">
    <div class="card product-item border-0 mb-4">

        {{-- Image — clickable --}}
        <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
            <a href="{{ $detailUrl }}" class="d-block" style="height:280px;overflow:hidden;">
                @php
                    $storageDisk = \Illuminate\Support\Facades\Storage::disk('public');
                    if ($product->images && $product->images->isNotEmpty()) {
                        $img = $product->images->first();
                        $relativePath = 'products/' . $product->id . '/images/' . ($img->image ?? '');
                        if ($storageDisk->exists($relativePath)) {
                            $cardImgSrc = $storageDisk->url($relativePath);
                        } else {
                            $cardImgSrc = file_exists(public_path('storage/default.jpg'))
                                ? asset('storage/default.jpg')
                                : (file_exists(public_path('storage/default.jpeg')) ? asset('storage/default.jpeg') : asset('eshopper/img/product-1.jpg'));
                        }
                    } else {
                        $cardImgSrc = file_exists(public_path('storage/default.jpg'))
                            ? asset('storage/default.jpg')
                            : (file_exists(public_path('storage/default.jpeg')) ? asset('storage/default.jpeg') : asset('eshopper/img/product-1.jpg'));
                    }
                @endphp
                <img style="width:100%;height:100%;object-fit:cover;object-position:top;"
                     src="{{ $cardImgSrc }}"
                     alt="{{ $product->name }}"
                     onerror="this.onerror=null;this.src='{{ asset('storage/default.jpg') }}';"
                     loading="lazy">
            </a>
        </div>

        {{-- Name + Price — title clickable --}}
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

        {{-- Footer: Star rating + review count (no Add to Cart) --}}
        <div class="card-footer d-flex justify-content-between align-items-center bg-light border">
            {{-- Stars --}}
            <div class="text-warning" style="font-size:13px; letter-spacing:1px;">
                @for($s = 1; $s <= 5; $s++)
                    @if($avgRating >= $s)
                        <i class="fas fa-star"></i>
                    @elseif($avgRating >= $s - 0.5)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            {{-- Review count --}}
            <small class="text-muted">
                @if($reviewCount > 0)
                    <a href="{{ $detailUrl }}#reviews" class="text-muted text-decoration-none">
                        {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}
                    </a>
                @else
                    No reviews yet
                @endif
            </small>
        </div>

    </div>
</div>
