@extends('layouts.eshopper')
@section('title', 'My Orders - Jeanzo')

@section('content')

<div class="container-fluid pb-5" style="background:#faf8f8;">
    <div class="row px-xl-5 pt-4">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-shopping-bag mr-2" style="color:var(--j-primary);"></i>Order History</div>

                @if($orders->isEmpty())
                    <div class="text-center py-5">
                        <div style="width:80px;height:80px;border-radius:50%;background:var(--j-primary-lt);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                            <i class="fa fa-shopping-bag fa-2x" style="color:var(--j-primary);"></i>
                        </div>
                        <h5 class="text-muted">No orders yet</h5>
                        <p class="text-muted small">Looks like you haven't placed any orders.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary px-5 mt-2">Start Shopping</a>
                    </div>
                @else
                    {{-- Mobile: card layout, Desktop: table --}}
                    <div class="d-none d-md-block table-responsive">
                        <table class="table j-table mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Payment</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                @php
                                    $statusClass = ['pending'=>'j-badge-pending','processing'=>'j-badge-processing','shipped'=>'j-badge-shipped','delivered'=>'j-badge-delivered','cancelled'=>'j-badge-cancelled'][$order->status] ?? 'j-badge-pending';
                                    $payClass = $order->payment_status === 'paid' ? 'j-badge-paid' : 'j-badge-awaiting';
                                    $cancellable = in_array($order->status, ['pending','processing']);
                                @endphp
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td><span class="j-badge" style="background:#eee;color:#333;">{{ $order->products->count() }}</span></td>
                                    <td><strong>₹{{ number_format($order->total_price, 2) }}</strong></td>
                                    <td><span class="j-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                                    <td><span class="j-badge {{ $payClass }}">{{ ucfirst($order->payment_status ?? 'pending') }}</span></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            <form method="POST" action="{{ route('profile.reorder', $order->id) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success"><i class="fa fa-redo mr-1"></i>Reorder</button>
                                            </form>
                                            @if($cancellable)
                                                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#cancelModal{{ $order->id }}">Cancel</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @if($cancellable)
                                <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog"><div class="modal-content" style="border-radius:12px;overflow:hidden;">
                                        <div class="modal-header" style="background:var(--j-primary);color:#fff;">
                                            <h5 class="modal-title">Cancel Order #{{ $order->id }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="POST" action="{{ route('profile.cancel-order', $order->id) }}">
                                            @csrf @method('PATCH')
                                            <div class="modal-body">
                                                <p>Are you sure you want to cancel <strong>Order #{{ $order->id }}</strong>?</p>
                                                <select class="form-control" name="reason">
                                                    <option value="">-- Select a reason --</option>
                                                    <option>Changed my mind</option>
                                                    <option>Ordered by mistake</option>
                                                    <option>Found a better price elsewhere</option>
                                                    <option>Delivery time is too long</option>
                                                    <option>Other</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Keep Order</button>
                                                <button type="submit" class="btn btn-danger">Cancel Order</button>
                                            </div>
                                        </form>
                                    </div></div>
                                </div>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile card layout --}}
                    <div class="d-md-none">
                        @foreach($orders as $order)
                        @php
                            $statusClass = ['pending'=>'j-badge-pending','processing'=>'j-badge-processing','shipped'=>'j-badge-shipped','delivered'=>'j-badge-delivered','cancelled'=>'j-badge-cancelled'][$order->status] ?? 'j-badge-pending';
                            $cancellable = in_array($order->status, ['pending','processing']);
                        @endphp
                        <div class="j-cart-item mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>#{{ $order->id }}</strong>
                                <span class="j-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="text-muted small mb-2">{{ $order->created_at->format('d M Y') }} &bull; {{ $order->products->count() }} items</div>
                            <div class="font-weight-bold mb-3">₹{{ number_format($order->total_price, 2) }}</div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                @if($cancellable)
                                    <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#cancelModal{{ $order->id }}">Cancel</button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
