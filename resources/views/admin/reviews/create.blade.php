@extends('layouts.dashboard')

@section('title', 'Create Review')

@section('content')
    <div class="page-title">
        <h2><i class="fas fa-plus"></i> Add Product Review</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reviews.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer (User) <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">Guest (optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <input type="number" name="rating" min="1" max="5" step="1" value="{{ old('rating') }}"
                               class="form-control @error('rating') is-invalid @enderror" required>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Review Text</label>
                        <textarea name="review_text" class="form-control @error('review_text') is-invalid @enderror" rows="4" maxlength="5000" placeholder="Write review...">{{ old('review_text') }}</textarea>
                        @error('review_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Review
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

