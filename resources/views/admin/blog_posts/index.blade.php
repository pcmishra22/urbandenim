@extends('layouts.dashboard')

@section('title', 'Blog Posts')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-file-alt"></i> Blog Posts</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">

            <i class="fas fa-plus"></i> Add Post
        </a>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>All Posts ({{ $posts->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td><strong>#{{ $post->id }}</strong></td>
                        <td>
                            <strong>{{ $post->title }}</strong>
                        </td>
                        <td><code>{{ $post->slug }}</code></td>
                        <td>
                            {{ $post->category?->name ?? '-' }}
                        </td>
                        <td>
                            @if($post->is_featured)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($post->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning text-dark">Draft</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
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

