@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit CMS Page</h3>
        <a class="btn btn-sm btn-secondary" href="{{ route('admin.cms.pages.index') }}">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cms.pages.update', $page->slug) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="title">Title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $page->title) }}"
                        required
                        maxlength="255"
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        class="form-control @error('content') is-invalid @enderror"
                        rows="10"
                    >{{ old('content', $page->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.cms.pages.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

