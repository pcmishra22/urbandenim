@extends('layouts.dashboard')

@section('title', 'Reviews Management')

@section('content')
    <div class="page-title d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-star"></i> Reviews Management</h2>
        <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Review
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <span>
                    Reviews ({{ $reviews->total() }})
                    @if($filter !== 'all')
                        <span class="text-muted">- Filter: <strong>{{ ucfirst($filter) }}</strong></span>
                    @endif
                </span>

                <form method="GET" action="{{ route('admin.reviews.index') }}" class="d-flex gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}" />
                    <select name="filter" class="form-select" style="width: 190px;" onchange="this.form.submit()">
                        <option value="all" @selected($filter === 'all')>All</option>
                        <option value="pending" @selected($filter === 'pending')>Pending Approval</option>
                        <option value="approved" @selected($filter === 'approved')>Approved</option>
                        <option value="spam" @selected($filter === 'spam')>Spam</option>
                        <option value="featured" @selected($filter === 'featured')>Featured</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Approval</th>
                    <th>Spam</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td><strong>#{{ $review->id }}</strong></td>
                        <td>
                            @if($review->product)
                                <a href="{{ route('admin.products.edit', $review->product->id) }}" class="text-decoration-none">
                                    {{ $review->product->name }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($review->user)
                                {{ $review->user->name }}
                            @else
                                <span class="text-muted">Guest</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $review->rating }}/5</span>
                        </td>
                        <td>
                            {{ 
                                $review->review_text
                                    ? (Str::limit($review->review_text, 80))
                                    : '-' }}
                        </td>
                        <td>
                            @if($review->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($review->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($review->is_spam)
                                <span class="badge bg-danger">Spam</span>
                            @else
                                <span class="badge bg-success">Clean</span>
                            @endif
                            <div class="small text-muted">Score: {{ $review->spam_score }}</div>
                        </td>
                        <td>
                            @if($review->is_featured)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if(!$review->is_spam && $review->status !== 'approved')
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-success" type="submit">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endif

                                @if(!$review->is_spam && $review->status !== 'rejected')
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-danger" type="submit">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                @endif

                                @if(!$review->is_spam)
                                    <form action="{{ route('admin.reviews.markSpam', $review) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-ban"></i> Spam
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.reviews.toggleFeatured', $review) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button class="btn btn-sm {{ $review->is_featured ? 'btn-warning' : 'btn-outline-warning' }}" type="submit">
                                        <i class="fas fa-star"></i> {{ $review->is_featured ? 'Featured' : 'Feature' }}
                                    </button>
                                </form>

                                <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No reviews found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
@endsection

