@extends('layouts.dashboard')

@section('title', 'Homepage Management')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-home"></i> Homepage Management</h2>
</div>

<div class="row mt-3">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-images fa-2x text-primary mb-3"></i>
                <h5 class="card-title">Banners & Sliders</h5>
                <p class="card-text text-muted small">Manage hero banners and promotional sliders displayed on the homepage.</p>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-primary btn-sm">Manage Banners</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-star fa-2x text-warning mb-3"></i>
                <h5 class="card-title">Featured Products</h5>
                <p class="card-text text-muted small">Toggle which products appear in the featured section on the homepage.</p>
                <a href="{{ route('admin.products.index') }}?featured=1" class="btn btn-warning btn-sm">Manage Featured</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-th fa-2x text-success mb-3"></i>
                <h5 class="card-title">Category Highlights</h5>
                <p class="card-text text-muted small">Choose which categories are showcased on the homepage category section.</p>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-success btn-sm">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-blog fa-2x text-info mb-3"></i>
                <h5 class="card-title">Latest Blog Posts</h5>
                <p class="card-text text-muted small">The 3 most recent published blog posts are shown on the homepage automatically.</p>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-info btn-sm">Manage Blog</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-search fa-2x text-secondary mb-3"></i>
                <h5 class="card-title">SEO Settings</h5>
                <p class="card-text text-muted small">Configure homepage meta title, description and Open Graph tags.</p>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary btn-sm">Manage SEO</a>
            </div>
        </div>
    </div>
</div>
@endsection
