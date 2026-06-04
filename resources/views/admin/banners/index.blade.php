@extends('layouts.dashboard')

@section('title', 'Banners Management')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-bullhorn"></i> Banners Management</h2>
    <a href="{{ route('admin.banners.create', ['type' => $type ?: null]) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Banner
    </a>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            All Banners
            @if($type)
                <span class="text-muted">(Type: {{ ucfirst($type) }})</span>
            @endif
            ({{ $banners->total() }})
        </span>

        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="type" class="form-select form-select-sm" style="width: 220px;">
                <option value="" @selected(!$type)>All Types</option>
                @foreach($types as $t)
                    <option value="{{ $t }}" @selected($type === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-secondary" type="submit">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Link</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td><strong>#{{ $banner->id }}</strong></td>
                        <td><code>{{ $banner->type }}</code></td>
                        <td>{{ $banner->title ?? '-' }}</td>
                        <td>
                            <img src="{{ $banner->image }}" alt="{{ $banner->title ?? 'banner' }}" style="height:50px; width:auto; object-fit:contain;" />
                        </td>
                        <td>
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" rel="noopener">Open</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            @if($banner->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" style="display:inline;">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox"></i> No banners found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $banners->links() }}
</div>
@endsection

