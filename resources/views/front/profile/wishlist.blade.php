@extends('layouts.eshopper')
@section('title', 'My Wishlist - Jeanzo')

@section('content')

<style>
:root { --j-primary:#D19C97; --j-primary-lt:#f7edec; --j-primary-dk:#b8807a; }
.j-product-card { background:#fff; border:1.5px solid #e8e0df; border-radius:8px; overflow:hidden; transition:all .25s; }
.j-product-card:hover { box-shadow:0 6px 24px rgba(209,156,151,.28); transform:translateY(-4px); border-color:#D19C97; }
.price-tag { font-weight:800; color:#D19C97; font-size:1rem; }
.btn-primary { background:#D19C97 !important; border-color:#D19C97 !important; }
.btn-primary:hover { background:#b8807a !important; border-color:#b8807a !important; }
.btn-outline-danger { color:#dc3545; border-color:#dc3545; }
</style>

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:24px;">
    <div class="row px-xl-5">

        {{-- Page heading --}}
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="font-weight-bold mb-1" style="color:#2d2d2d;">
                        <i class="fa fa-heart mr-2" style="color:#D19C97;"></i>My Wishlist
                    </h3>
                    <div style="font-size:.85rem;">
                        <a href="{{ url('/') }}" class="text-muted" style="text-decoration:none;">Home</a>
                        <span class="text-muted mx-1">›</span>
                        <span style="color:#D19C97;">Wishlist</span>
                    </div>
                </div>
                <span style="background:#f7edec;color:#D19C97;padding:4px 12px;border-radius:20px;font-size:.8rem;font-weight:600;">
                    {{ $wishlistItems->total() }} items
                </span>
            </div>
            <div style="width:50px;height:3px;background:#D19C97;border-radius:2px;margin-top:10px;"></div>
        </div>

        @if(session('success'))
        <div class="col-12 mb-3">
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        </div>
        @endif

        @if($wishlistItems->isEmpty())
        <div class="col-12 text-center py-5">
            <div style="width:96px;height:96px;border-radius:50%;background:#f7edec;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <i class="far fa-heart fa-2x" style="color:#D19C97;"></i>
            </div>
            <h4 class="font-weight-bold mb-2">Your wishlist is empty</h4>
            <p class="text-muted mb-4">Save items you love and come back to them anytime.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary px-5 py-2">Browse Products</a>
        </div>
        @else

        @foreach($wishlistItems as $item)
        @php $product = $item->product; @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 pb-4">
            <div class="j-product-card h-100">
                {{-- Image --}}
                <div style="height:220px;overflow:hidden;">
                    <a href="{{ $product ? route('products.detail', $product->slug) : '#' }}">
                        @if($product && $product->images && $product->images->isNotEmpty())
                            <img class="w-100 h-100" style="object-fit:cover;transition:transform .4s;" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}">
                        @else
                            <img class="w-100 h-100" style="object-fit:cover;" src="{{ asset('eshopper/img/product-1.jpg') }}" alt="">
                        @endif
                    </a>
                </div>
                {{-- Body --}}
                <div style="padding:14px;">
                    <a href="{{ $product ? route('products.detail', $product->slug) : '#' }}"
                       class="d-block text-dark font-weight-bold mb-1"
                       style="font-size:.9rem;line-height:1.3;text-decoration:none;">
                        {{ Str::limit($product->name ?? 'Product', 50) }}
                    </a>
                    <div class="d-flex align-items-center mb-3" style="gap:8px;">
                        <span class="price-tag">₹{{ number_format($product->sale_price ?? $product->price ?? 0, 2) }}</span>
                        @if($product && $product->sale_price && $product->sale_price < $product->price)
                            <small class="text-muted"><del>₹{{ number_format($product->price, 2) }}</del></small>
                            <span style="background:#d4edda;color:#155724;padding:2px 8px;border-radius:10px;font-size:.72rem;font-weight:600;">
                                {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                            </span>
                        @endif
                    </div>
                    {{-- Actions --}}
                    <div class="d-flex" style="gap:8px;">
                        <form method="POST" action="{{ route('wishlist.move-to-cart') }}" style="flex:1;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </form>
                        <form method="POST" action="{{ route('wishlist.remove') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        @if($wishlistItems->hasPages())
        <div class="col-12 mt-2">
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $wishlistItems->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $wishlistItems->previousPageUrl() }}">‹ Prev</a>
                    </li>
                    @foreach($wishlistItems->getUrlRange(1, $wishlistItems->lastPage()) as $page => $url)
                    <li class="page-item {{ $wishlistItems->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach
                    <li class="page-item {{ $wishlistItems->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $wishlistItems->nextPageUrl() }}">Next ›</a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif

        @endif
    </div>
</div>

@endsection
