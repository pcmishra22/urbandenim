@extends('layouts.dashboard')

@section('title', 'Edit Review')

@section('content')
    <div class="page-title">
        <h2><i class="fas fa-edit"></i> Edit Product Review</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', $review->product_id) == $product->id)>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer (User)</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">Guest (optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', $review->user_id) === (string) $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <input type="number" name="rating" min="1" max="5" step="1"
                               value="{{ old('rating', $review->rating) }}"
                               class="form-control @error('rating') is-invalid @enderror" required>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-9 mb-3">
                        <label class="form-label">Review Text</label>
                        <textarea name="review_text" class="form-control @error('review_text') is-invalid @enderror" rows="4" maxlength="5000">{{ old('review_text', $review->review_text) }}</textarea>
                        @error('review_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Approval Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @php
                                $currentStatus = old('status', $review->status);
                            @endphp
                            <option value="pending" @selected($currentStatus === 'pending')>Pending</option>
                            <option value="approved" @selected($currentStatus === 'approved')>Approved</option>
                            <option value="rejected" @selected($currentStatus === 'rejected')>Rejected</option>
                            <option value="spam" @selected($currentStatus === 'spam')>Spam</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Featured Review</label>
                        <select name="is_featured" class="form-select @error('is_featured') is-invalid @enderror">
                            <option value="0" @selected((string) old('is_featured', $review->is_featured ? '1' : '0') === '0')>No</option>
                            <option value="1" @selected((string) old('is_featured', $review->is_featured ? '1' : '0') === '1')>Yes</option>
                        </select>
                        @error('is_featured')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Spam Score (auto)</label>
                        <input class="form-control" value="{{ $review->spam_score }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reported Count</label>
                        <input class="form-control" value="{{ $review->reported_count }}" disabled>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Review
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

