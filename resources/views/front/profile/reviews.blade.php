@extends('layouts.eshopper')
@section('title', 'My Reviews | Jeanzo India')
@section('meta_description', 'View and manage all the product reviews you have submitted on Jeanzo. Help other shoppers find the perfect pair of jeans.')
@section('meta_robots', 'noindex, nofollow')

@section('content')

<div class="container-fluid pb-5" style="background:#faf8f8;">
    <div class="row px-xl-5 pt-4">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-star mr-2" style="color:var(--j-primary);"></i>My Reviews</div>

                @if($reviews->isEmpty())
                    <div class="text-center py-5">
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--j-primary-lt);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                            <i class="fa fa-star fa-2x" style="color:var(--j-primary);"></i>
                        </div>
                        <h5 class="text-muted">No reviews yet</h5>
                        <p class="text-muted small">Share your thoughts on products you've purchased.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-2 px-5">Browse Products</a>
                    </div>
                @else
                    @foreach($reviews as $review)
                    <div class="j-cart-item mb-3">
                        <div class="d-flex align-items-start gap-3">
                            @if($review->product && $review->product->images && $review->product->images->isNotEmpty())
                                <img src="{{ $review->product->images->first()->url }}" alt="{{ $review->product->name }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                            @else
                                <div style="width:60px;height:60px;border-radius:8px;background:var(--j-primary-lt);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fa fa-box" style="color:var(--j-primary);"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <a href="{{ route('products.detail', $review->product->slug ?? '#') }}" class="font-weight-700 text-dark" style="font-size:.95rem;">
                                        {{ $review->product->name ?? 'Product Unavailable' }}
                                    </a>
                                    @if(isset($review->is_approved) && $review->is_approved)
                                        <span class="j-badge j-badge-delivered">Approved</span>
                                    @elseif(isset($review->is_spam) && $review->is_spam)
                                        <span class="j-badge j-badge-cancelled">Spam</span>
                                    @else
                                        <span class="j-badge j-badge-awaiting">Pending</span>
                                    @endif
                                </div>
                                <div class="mt-1 mb-2">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="{{ $s <= $review->rating ? 'fas' : 'far' }} fa-star" style="color:#f39c12;font-size:.85rem;"></i>
                                    @endfor
                                    <small class="text-muted ml-2">{{ $review->created_at->format('d M Y') }}</small>
                                </div>
                                <p class="text-muted mb-0 small">{{ $review->review_text }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-3">{{ $reviews->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
