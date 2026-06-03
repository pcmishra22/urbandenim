@extends('layouts.dashboard')

@section('title', 'Blog Tags')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-tags"></i> Blog Tags</h2>
    <a href="{{ route('admin.blog.tags.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Tag
    </a>
</div>

<div class="card mt-3">
    <div class="card-header">
        <span>All Tags ({{ $tags->total() }})</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tags as $tag)
                    <tr>
                        <td><strong>#{{ $tag->id }}</strong></td>
                        <td>{{ $tag->name }}</td>
                        <td><code>{{ $tag->slug }}</code></td>
                        <td>
                            @if($tag->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.blog.tags.edit', $tag) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.blog.tags.destroy', $tag) }}" method="POST" style="display:inline;">
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
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No tags found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $tags->links() }}
</div>
@endsection

