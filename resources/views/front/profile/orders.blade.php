@extends('layouts.eshopper')
@section('title', 'My Orders - EShopper')
@section('content')

@include('front.partials.page-banner', ['title' => 'My Orders', 'breadcrumb' => 'My Orders'])

<div class="container-fluid pt-5 pb-5">
    <div class="row px-xl-5">
        @include('front.partials.profile-sidebar')

        <div class="col-lg-9 mb-5">
            <h5 class="font-weight-semi-bold mb-4">Order History</h5>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
            @endif

            @if($orders->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fa fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders yet</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Start Shopping</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-secondary">
                            <tr>
                                <th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            @php
                                $bm = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                                $badge = $bm[$order->status] ?? 'secondary';
                                $cancellable = in_array($order->status, ['pending','processing']);
                            @endphp
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge badge-info">{{ $order->products->count() }} items</span></td>
                                <td>₹{{ number_format($order->total_price, 2) }}</td>
                                <td><span class="badge badge-{{ $badge }} text-capitalize">{{ $order->status }}</span></td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gap-1">
                                        <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        {{-- Reorder --}}
                                        <form method="POST" action="{{ route('profile.reorder', $order->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Add all items to cart again">
                                                <i class="fa fa-redo mr-1"></i>Reorder
                                            </button>
                                        </form>
                                        {{-- Cancel --}}
                                        @if($cancellable)
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-toggle="modal" data-target="#cancelModal{{ $order->id }}">Cancel</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($cancellable)
                            <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Cancel Order #{{ $order->id }}</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                <div class="mt-3">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
