@extends('layouts.eshopper')
@section('title', 'My Reviews - Jeanzo')
@section('content')

@include('front.partials.page-banner', ['title' => 'My Reviews', 'breadcrumb' => 'My Reviews'])

<div class="container-fluid pt-5 pb-5">
    <div class="row px-xl-5">
        @include('front.partials.profile-sidebar')
        <div class="col-lg-9 mb-5">
            <h5 class="font-weight-semi-bold mb-4">My Reviews</h5>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
            @endif

            @if($reviews->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fa fa-star fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">You haven't reviewed any products yet.</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Browse Products</a>
                </div>
            @else
                @foreach($reviews as $review)
                <div class="border rounded p-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        @if($review->product && $review->product->images && $review->product->images->isNotEmpty())
                            <img src="{{ $review->product->images->first()->url }}" alt="{{ $review->product->name }}"
                                 style="width:60px;height:60px;object-fit:cover;border-radius:6px;margin-right:16px;">
                        @endif
                        <div class="flex-grow-1">
                            <a href="{{ route('product.detail', $review->product->slug ?? '#') }}" class="font-weight-bold text-dark">
                                {{ $review->product->name ?? 'Product Unavailable' }}
                            </a>
                            <div class="text-primary mt-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <i class="{{ $s <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                                <small class="text-muted ml-2">{{ $review->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                        <div>
                            @if($review->is_approved)
                                <span class="badge badge-success">Approved</span>
                            @elseif($review->is_spam)
                                <span class="badge badge-danger">Marked Spam</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </div>
                    </div>
                    <p class="mb-0 text-muted">{{ $review->review_text }}</p>
                </div>
                @endforeach
                <div class="mt-3">{{ $reviews->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
