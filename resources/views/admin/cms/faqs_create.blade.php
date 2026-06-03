@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Add New FAQ</h1>
        <a href="{{ route('admin.cms.faqs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to FAQs
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cms.faqs.store') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="question" class="font-weight-bold">Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" id="question"
                           class="form-control @error('question') is-invalid @enderror"
                           value="{{ old('question') }}"
                           placeholder="Enter FAQ question">
                    @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="answer" class="font-weight-bold">Answer <span class="text-danger">*</span></label>
                    <textarea name="answer" id="answer" rows="6"
                              class="form-control @error('answer') is-invalid @enderror"
                              placeholder="Enter FAQ answer">{{ old('answer') }}</textarea>
                    @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-4">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active (visible on site)</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save mr-1"></i> Save FAQ
                </button>
                <a href="{{ route('admin.cms.faqs.index') }}" class="btn btn-outline-secondary ml-2 px-4">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
