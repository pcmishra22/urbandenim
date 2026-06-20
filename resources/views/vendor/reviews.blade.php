@extends('layouts.vendor')

@section('title', 'My Reviews & Ratings')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-star"></i> My Reviews & Ratings</h2>
</div>

{{-- Summary cards --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card text-center">
            <div style="font-size:2.5rem;font-weight:800;color:#f39c12;line-height:1.1;">{{ $avgRating }}</div>
            <div style="margin:6px 0;">
                @for($s=1;$s<=5;$s++)
                    <i class="fas fa-star" style="color:{{ $s <= round($avgRating) ? '#f39c12' : '#ddd' }};font-size:.9rem;"></i>
                @endfor
            </div>
            <h6 style="color:#888;font-size:.8rem;margin:0;">Average Rating</h6>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>Total Reviews</h6>
            <div class="value">{{ $totalReviews }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>5 ⭐ Reviews</h6>
            <div class="value" style="color:#27ae60;">{{ $reviews->where('rating', 5)->count() }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h6>Awaiting Reply</h6>
            <div class="value" style="color:#e67e22;">{{ $reviews->whereNull('vendor_reply')->count() }}</div>
        </div>
    </div>
</div>

{{-- Success/error flash --}}
@if(session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
@endif

{{-- Reviews list --}}
@if($reviews->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-star fa-3x mb-3" style="color:#ddd;"></i>
            <p>No reviews yet. Reviews from customers will appear here after they receive their orders.</p>
        </div>
    </div>
@else
    @foreach($reviews as $review)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#27ae60;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.92rem;color:#222;">{{ $review->user->name ?? 'Customer' }}</div>
                        <div>
                            @for($s=1;$s<=5;$s++)
                                <i class="fas fa-star" style="color:{{ $s <= $review->rating ? '#f39c12' : '#ddd' }};font-size:.8rem;"></i>
                            @endfor
                            <span style="font-size:.78rem;color:#aaa;margin-left:4px;">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if($review->product)
                        <div style="font-size:.75rem;color:#888;margin-top:2px;">
                            <i class="fas fa-tag mr-1"></i>{{ $review->product->name }}
                        </div>
                        @endif
                    </div>
                </div>
                <span class="badge badge-{{ $review->vendor_reply ? 'success' : 'warning' }}" style="font-size:.72rem;">
                    {{ $review->vendor_reply ? 'Replied' : 'Awaiting reply' }}
                </span>
            </div>

            @if($review->review)
            <p style="margin:12px 0 0 52px;font-size:.88rem;color:#444;line-height:1.6;">{{ $review->review }}</p>
            @else
            <p style="margin:12px 0 0 52px;font-size:.82rem;color:#aaa;font-style:italic;">No written review — rating only.</p>
            @endif

            {{-- Existing reply --}}
            @if($review->vendor_reply)
            <div style="margin:12px 0 0 52px;background:#f0faf4;border-left:3px solid #27ae60;padding:10px 14px;border-radius:0 8px 8px 0;font-size:.85rem;">
                <strong style="color:#1b5e20;"><i class="fas fa-store mr-1"></i>Your reply:</strong>
                <p style="margin:4px 0 0;color:#333;">{{ $review->vendor_reply }}</p>
            </div>
            @else
            {{-- Reply form --}}
            <div style="margin:12px 0 0 52px;">
                <form method="POST" action="{{ route('vendor.review.reply', $review->id) }}">
                    @csrf
                    <div class="input-group" style="max-width:500px;">
                        <input type="text" class="form-control" name="vendor_reply"
                               placeholder="Write a reply to this customer..." required maxlength="500"
                               style="border-radius:8px 0 0 8px;">
                        <div class="input-group-append">
                            <button class="btn btn-success btn-sm px-3" type="submit"
                                    style="border-radius:0 8px 8px 0;">
                                <i class="fas fa-reply mr-1"></i>Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-3">{{ $reviews->links() }}</div>
@endif

@endsection
