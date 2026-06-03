@extends('layouts.dashboard')

@section('title', 'SEO Management')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-search"></i> SEO Management</h2>
</div>

<div class="card mt-3">
    <div class="card-header"><strong>SEO Overview</strong></div>
    <div class="card-body">
        <p class="text-muted mb-4"><i class="fas fa-info-circle"></i> Manage SEO meta tags, sitemaps and structured data for your store.</p>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="border p-3 text-center">
                    <i class="fas fa-box fa-2x text-primary mb-2"></i>
                    <h6 class="font-weight-bold">Products SEO</h6>
                    <p class="text-muted small mb-2">Edit meta titles and descriptions for products</p>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="border p-3 text-center">
                    <i class="fas fa-th fa-2x text-primary mb-2"></i>
                    <h6 class="font-weight-bold">Category SEO</h6>
                    <p class="text-muted small mb-2">Edit meta data for category pages</p>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="border p-3 text-center">
                    <i class="fas fa-blog fa-2x text-primary mb-2"></i>
                    <h6 class="font-weight-bold">Blog SEO</h6>
                    <p class="text-muted small mb-2">Edit meta data for blog posts</p>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
