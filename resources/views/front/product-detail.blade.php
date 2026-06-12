@extends('layouts.eshopper')
@section('title', $product->name . ' - Jeanzo')

@section('content')
@include('front.partials.design-system')

@php $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0; $reviewCount = $product->reviews ? $product->reviews->count() : 0; @endphp

<div class="container-fluid px-xl-5 py-3" style="background:#faf8f8;">
    <div class="row">

        <!-- Images -->
        <div class="col-lg-5 mb-4">
            <div class="j-section p-2">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" style="border-radius:10px;overflow:hidden;background:#f5f5f5;">
                        @if($product->images && $product->images->isNotEmpty())
                            @foreach($product->images as $i => $image)
                            @php $rel='products/'.$product->id.'/images/'.($image->image??''); $url=asset('storage/'.$rel); $fb=asset('storage/default.jpeg'); @endphp
                            <div class="carousel-item {{ $i===0?'active':'' }}">
                                <img class="w-100" style="height:400px;object-fit:contain;" src="{{ file_exists(public_path('storage/'.$rel))?$url:$fb }}" alt="{{ $product->name }}">
                            </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img class="w-100" style="height:400px;object-fit:contain;" src="{{ asset('storage/default.jpeg') }}" alt="{{ $product->name }}">
                            </div>
                        @endif
                    </div>
                    @if($product->images && $product->images->count() > 1)
                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <span style="background:var(--j-primary);border-radius:50%;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-angle-left text-white"></i></span>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <span style="background:var(--j-primary);border-radius:50%;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-angle-right text-white"></i></span>
                    </a>
                    @endif
                </div>
                @if($product->images && $product->images->count() > 1)
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    @foreach($product->images->take(5) as $i => $image)
                    @php $rel='products/'.$product->id.'/images/'.($image->image??''); $url=asset('storage/'.$rel); $fb=asset('storage/default.jpeg'); @endphp
                    <a href="#product-carousel" data-target="#product-carousel" data-slide-to="{{ $i }}"
                       style="border:2px solid transparent;border-radius:6px;overflow:hidden;transition:.2s;"
                       onmouseover="this.style.borderColor='var(--j-primary)'" onmouseout="this.style.borderColor='transparent'">
                        <img style="width:60px;height:60px;object-fit:cover;" src="{{ file_exists(public_path('storage/'.$rel))?$url:$fb }}" alt="">
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Info -->
        <div class="col-lg-7 mb-4">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <a href="{{ route('cart.index') }}" class="btn btn-sm btn-primary ml-2"><i class="fa fa-shopping-bag mr-1"></i>View Cart</a>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            <div class="j-section">
                <!-- Breadcrumb -->
                <nav class="mb-2" style="font-size:.82rem;">
                    <a href="{{ route('products.index') }}" style="color:var(--j-primary);">Shop</a>
                    @if($product->category) › <a href="{{ route('products.index',['category'=>$product->category_id]) }}" style="color:var(--j-primary);">{{ $product->category->name }}</a> @endif
                    › <span class="text-muted">{{ Str::limit($product->name,30) }}</span>
                </nav>

                <h2 class="font-weight-bold mb-2" style="font-size:1.4rem;color:#2d2d2d;">{{ $product->name }}</h2>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-2">
                        @for($s=1;$s<=5;$s++)
                        <i class="{{ $s<=floor($avgRating)?'fas':($s-0.5<=$avgRating?'fas fa-star-half-alt':'far') }} fa-star" style="color:#f39c12;font-size:.85rem;"></i>
                        @endfor
                    </div>
                    <small class="text-muted">{{ number_format($avgRating,1) }} ({{ $reviewCount }} reviews)</small>
                    @if($product->is_featured)<span class="j-badge ml-2" style="background:#fff3cd;color:#856404;"><i class="fa fa-star mr-1" style="font-size:.7rem;"></i>Featured</span>@endif
                </div>

                <!-- Price -->
                <div class="d-flex align-items-center mb-3">
                    <h3 class="font-weight-bold mb-0 mr-3" style="color:var(--j-primary);font-size:1.8rem;">
                        ₹{{ number_format($product->sale_price ?? $product->price, 2) }}
                    </h3>
                    @if($product->sale_price && $product->sale_price < $product->price)
                    <h5 class="text-muted mb-0 mr-2"><del>₹{{ number_format($product->price,2) }}</del></h5>
                    <span class="j-badge" style="background:#d4edda;color:#155724;font-size:.85rem;">
                        {{ round((1-$product->sale_price/$product->price)*100) }}% OFF
                    </span>
                    @endif
                </div>

                @if($product->short_description)
                <p class="text-muted mb-3" style="font-size:.9rem;line-height:1.6;">{{ $product->short_description }}</p>
                @endif

                <!-- Meta chips -->
                <div class="d-flex flex-wrap mb-4" style="gap:8px;">
                    @if($product->category)
                    <span class="j-badge" style="background:var(--j-primary-lt);color:var(--j-primary);">
                        <i class="fa fa-folder mr-1" style="font-size:.7rem;"></i>{{ $product->category->name }}
                    </span>
                    @endif
                    @if($product->brand)
                    <span class="j-badge" style="background:#eee;color:#555;">
                        <i class="fa fa-tag mr-1" style="font-size:.7rem;"></i>{{ $product->brand->name }}
                    </span>
                    @endif
                    @if($product->gender)
                    <span class="j-badge" style="background:#eee;color:#555;">{{ ucfirst($product->gender) }}</span>
                    @endif
                    @if($product->color_family)
                    <span class="j-badge" style="background:#eee;color:#555;">{{ ucfirst($product->color_family) }}</span>
                    @endif
                </div>

                <!-- Sold by -->
                @if($product->sold_by)
                <div class="mb-3 small text-muted"><i class="fa fa-store mr-1"></i>Sold by: <strong>{{ $product->sold_by }}</strong></div>
                @endif

                <!-- Variants -->
                @if($product->variants->isNotEmpty())
                <div class="mb-4">
                    <p class="font-weight-bold mb-2" style="font-size:.9rem;">Select Size:</p>
                    <div class="d-flex flex-wrap" style="gap:8px;">
                        @foreach($product->variants as $variant)
                        <label for="variant-{{ $variant->id }}"
                               class="d-flex align-items-center justify-content-center"
                               style="min-width:52px;height:40px;padding:0 12px;border:1.5px solid #ddd;border-radius:6px;cursor:pointer;font-size:.85rem;font-weight:600;transition:.2s;user-select:none;"
                               onclick="this.style.borderColor='var(--j-primary)';this.style.background='var(--j-primary-lt)';this.style.color='var(--j-primary)';">
                            <input type="radio" class="custom-control-input variant-radio" id="variant-{{ $variant->id }}"
                                   name="variant_id" value="{{ $variant->id }}" form="add-to-cart-form" required style="display:none;">
                            {{ $variant->waist_size }}{{ $variant->color ? ' · '.$variant->color : '' }}
                            @if($variant->quantity <= 0)<small style="font-size:.65rem;display:block;color:#e74c3c;">OOS</small>@endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add to cart -->
                <div class="mb-4">
                    <form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                            {{-- Qty stepper --}}
                            <div class="input-group quantity" style="width:120px;flex-shrink:0;">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-sm btn-minus"><i class="fa fa-minus"></i></button>
                                </div>
                                <input type="text" name="quantity" class="form-control form-control-sm text-center bg-light" value="1" min="1">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary btn-sm btn-plus"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            {{-- Add to cart --}}
                            <button type="submit" class="btn btn-primary" style="min-width:140px;" id="add-to-cart-button" {{ $product->variants->isNotEmpty() ? 'disabled' : '' }}>
                                <i class="fa fa-cart-plus mr-1"></i> Add to Cart
                            </button>

                        </div>
                    </form>

                    @if($product->variants->isNotEmpty())
                        <div id="select-size-message" class="mt-2 small text-danger" style="display:none;">
                            <i class="fa fa-exclamation-circle mr-1"></i> Please select a size before adding to cart.
                        </div>
                    @endif

                    {{-- Go to Cart + Wishlist on separate row --}}

                    <div class="d-flex align-items-center mt-3" style="gap:10px;flex-wrap:wrap;">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary" style="min-width:130px;">
                            <i class="fa fa-shopping-bag mr-1"></i> View Cart
                        </a>
                        @auth
                        <form method="POST" action="{{ route('wishlist.add') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-secondary" style="min-width:50px;" title="Add to Wishlist">
                                <i class="far fa-heart" style="color:var(--j-primary);"></i> Wishlist
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>

                <!-- Delivery badges -->
                <div class="d-flex flex-wrap pt-3 border-top" style="gap:12px;">
                    <div class="d-flex align-items-center gap-1 small text-muted">
                        <i class="fa fa-truck" style="color:var(--j-primary);"></i> Free shipping above ₹500
                    </div>
                    <div class="d-flex align-items-center gap-1 small text-muted">
                        <i class="fa fa-undo" style="color:var(--j-primary);"></i> 14-day returns
                    </div>
                    <div class="d-flex align-items-center gap-1 small text-muted">
                        <i class="fa fa-shield-alt" style="color:var(--j-primary);"></i> Secure checkout
                    </div>
                </div>

                <!-- Share -->
                <div class="d-flex align-items-center pt-3 mt-2" style="gap:10px;">
                    <small class="text-muted">Share:</small>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-twitter"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($product->name.' '.request()->fullUrl()) }}" target="_blank" class="text-muted"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="j-section">
                <ul class="nav mb-4" style="border-bottom:2px solid var(--j-primary-lt);">
                    <li class="nav-item">
                        <a class="nav-link active px-4 py-2 font-weight-bold" data-toggle="tab" href="#tab-description"
                           style="color:var(--j-primary);border-bottom:3px solid var(--j-primary);">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-2" data-toggle="tab" href="#tab-info" style="color:#555;">Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-2" data-toggle="tab" href="#tab-reviews" style="color:#555;">Reviews ({{ $reviewCount }})</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-description">
                        <p style="line-height:1.8;color:#555;">{{ $product->description ?? 'No description available.' }}</p>
                    </div>
                    <div class="tab-pane fade" id="tab-info">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" style="width:140px;">Wash</td><td>{{ $product->wash ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Shade</td><td>{{ $product->shade ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Length</td><td>{{ $product->length ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Stretch</td><td>{{ $product->stretch ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Waist Rise</td><td>{{ $product->waist_rise ?? '—' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr><td class="text-muted" style="width:140px;">Fabric</td><td>{{ $product->fabric ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Brand</td><td>{{ $product->brand->name ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">Country</td><td>{{ $product->country_of_origin ?? '—' }}</td></tr>
                                    @if($product->sku)<tr><td class="text-muted">SKU</td><td>{{ $product->sku }}</td></tr>@endif
                                    <tr><td class="text-muted">Gender</td><td>{{ ucfirst($product->gender ?? '—') }}</td></tr>
                                    <tr><td class="text-muted">Stock</td>
                                        <td><span class="j-badge {{ $product->quantity > 0 ? 'j-badge-delivered' : 'j-badge-cancelled' }}">{{ $product->quantity > 0 ? 'In Stock ('.$product->quantity.')' : 'Out of Stock' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-reviews">
                        <div class="row">
                            <div class="col-md-6">
                                @if($product->reviews && $product->reviews->isNotEmpty())
                                    @foreach($product->reviews->take(5) as $review)
                                    <div class="d-flex mb-4 gap-3">
                                        <div style="width:40px;height:40px;border-radius:50%;background:var(--j-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="fa fa-user text-white" style="font-size:.85rem;"></i>
                                        </div>
                                        <div>
                                            <strong style="font-size:.9rem;">{{ $review->user->name ?? 'Anonymous' }}</strong>
                                            <small class="text-muted ml-2">{{ $review->created_at->format('d M Y') }}</small>
                                            <div class="mt-1 mb-1">
                                                @for($s=1;$s<=5;$s++)<i class="{{ $s<=$review->rating?'fas':'far' }} fa-star" style="color:#f39c12;font-size:.8rem;"></i>@endfor
                                            </div>
                                            <p class="mb-0 small text-muted">{{ $review->review_text }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No reviews yet. Be the first!</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Write a Review</h6>
                                @auth
                                <form method="POST" action="{{ route('products.review', $product->id) ?? '#' }}">
                                    @csrf
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="text-muted small mr-2">Rating:</span>
                                        <div id="star-rating">
                                            @for($s=1;$s<=5;$s++)
                                            <i class="far fa-star star-btn" data-val="{{ $s }}" style="cursor:pointer;font-size:1.3rem;color:#f39c12;"></i>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" id="rating-val" value="0">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="review_text" rows="4" class="form-control" placeholder="Share your experience…" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4">Submit Review</button>
                                </form>
                                @else
                                <div class="alert alert-light border">
                                    <a href="{{ route('customer.login') }}" style="color:var(--j-primary);">Login</a> to leave a review.
                                </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-4">
        <h4 class="font-weight-bold mb-4" style="border-left:4px solid var(--j-primary);padding-left:12px;">Related Products</h4>
        <div class="row">
            @foreach($relatedProducts as $related)
            @include('front.partials.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).on('click','.btn-plus',function(){var $i=$(this).closest('.quantity').find('input');$i.val(parseInt($i.val())||1+1);});
$(document).on('click','.btn-minus',function(){var $i=$(this).closest('.quantity').find('input');var v=parseInt($i.val())||1;if(v>1)$i.val(v-1);});
$('.star-btn').on('click',function(){var v=$(this).data('val');$('#rating-val').val(v);$('.star-btn').each(function(){$(this).toggleClass('fas',$(this).data('val')<=v).toggleClass('far',$(this).data('val')>v);});});
// Variant label highlight
$('.variant-radio').on('change',function(){
    $('label[for^="variant-"]').css({borderColor:'#ddd',background:'#fff',color:'#333'});
$('label[for="'+this.id+'" ]').css({borderColor:'var(--j-primary)',background:'var(--j-primary-lt)',color:'var(--j-primary)'});


    // Toggle Add to Cart button based on selected size


    var hasSelection = $('input[name="variant_id"]:checked').length > 0;
    if(hasSelection){
        $('#add-to-cart-button').prop('disabled', false);
        $('#select-size-message').hide();
    }
});

// Initial toggle on page load
(function(){
    var hasSelection = $('input[name="variant_id"]:checked').length > 0;
    if($('#add-to-cart-button').length){
        if(hasSelection){
            $('#add-to-cart-button').prop('disabled', false);
            $('#select-size-message').hide();
        }else{
            $('#add-to-cart-button').prop('disabled', true);
            $('#select-size-message').show();
        }
    }
})();

</script>
@endpush
@endsection
