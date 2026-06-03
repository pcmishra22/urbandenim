@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div class="page-title d-flex justify-content-between align-items-center">
    <h2><i class="fas fa-bell"></i> Notifications</h2>
    <div>
        <form method="POST" action="{{ route('admin.notifications.markAllAsRead') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary mr-2">
                <i class="fas fa-check-double"></i> Mark All Read
            </button>
        </form>
        <form method="POST" action="{{ route('admin.notifications.clearAll') }}" class="d-inline"
              onsubmit="return confirm('Clear all notifications?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                    <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                        <td>
                            <span class="badge badge-info text-capitalize">
                                {{ class_basename($notification->type) }}
                            </span>
                        </td>
                        <td>
                            @if(isset($notification->data['message']))
                                {{ $notification->data['message'] }}
                            @elseif(isset($notification->data['order_id']))
                                Order #{{ $notification->data['order_id'] }} notification
                            @else
                                Notification
                            @endif
                        </td>
                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                        <td>
                            @if($notification->read_at)
                                <span class="badge badge-secondary">Read</span>
                            @else
                                <span class="badge badge-warning">Unread</span>
                            @endif
                        </td>
                        <td>
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('admin.notifications.markAsRead', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Mark Read</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-bell-slash"></i> No notifications found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $notifications->links() }}
</div>
@endsection
