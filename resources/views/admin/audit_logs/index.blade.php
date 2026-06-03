@extends('layouts.dashboard')

@section('title', 'Audit Logs')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-history"></i> Audit Logs</h2>
</div>

<div class="card mt-3">
    <div class="card-header"><strong>Activity Log</strong></div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Coming soon:</strong> Audit logging will record admin actions such as order status changes, product edits, user management, and login events.
            To enable full audit logging, integrate a package like <code>spatie/laravel-activitylog</code> and run <code>php artisan migrate</code>.
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><strong>Recent Notifications (as activity proxy)</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Details</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(auth()->user()->notifications()->latest()->take(20)->get() as $notification)
                        <tr>
                            <td>
                                <span class="badge badge-secondary text-capitalize">
                                    {{ class_basename($notification->type) }}
                                </span>
                            </td>
                            <td>
                                @if(isset($notification->data['message']))
                                    {{ $notification->data['message'] }}
                                @elseif(isset($notification->data['order_id']))
                                    Order #{{ $notification->data['order_id'] }} event
                                @else
                                    System event
                                @endif
                            </td>
                            <td>{{ $notification->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No activity recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
