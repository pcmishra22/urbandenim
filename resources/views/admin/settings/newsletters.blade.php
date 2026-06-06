@extends('layouts.dashboard')
@section('title', 'Newsletter Subscribers')
@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-envelope"></i> Newsletter Subscribers ({{ $subscribers->total() }})</h2>
    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Settings</a>
</div>

@if(session('success'))
    <div class="alert alert-success mt-3 alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Subscribed</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($subscribers as $sub)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sub->name ?: '—' }}</td>
                    <td>{{ $sub->email }}</td>
                    <td><span class="badge {{ $sub->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $sub->is_active ? 'Active' : 'Unsubscribed' }}</span></td>
                    <td>{{ $sub->created_at->format('d M Y') }}</td>
                    <td class="d-flex gap-1">
                        <form method="POST" action="{{ route('admin.newsletter.toggle', $sub) }}">
                            @csrf
                            <button class="btn btn-sm {{ $sub->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                {{ $sub->is_active ? 'Deactivate' : 'Reactivate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('Remove subscriber?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No subscribers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $subscribers->links() }}</div>
@endsection
