@extends('layouts.dashboard')

@section('title', 'SEO Blogs')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-search"></i> SEO Blogs</h2>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>All SEO-targeted Posts</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Meta Title</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td><strong>#{{ $post->id }}</strong></td>
                    <td><strong>{{ $post->title }}</strong></td>
                    <td><code>{{ $post->slug }}</code></td>
                    <td>
                        @if($post->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning text-dark">Draft</span>
                        @endif
                    </td>
                    <td>{{ $post->meta_title ?? '-' }}</td>
                    <td>
                            <a href="{{ route('admin.blogs.edit', $post) }}" class="btn btn-sm btn-warning">

                            <i class="fas fa-edit"></i> Edit SEO
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-inbox"></i> No posts found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $posts->links() }}
</div>
@endsection

