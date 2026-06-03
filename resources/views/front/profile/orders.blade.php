@extends('layouts.eshopper')

@section('content')
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">My Orders</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="{{ route('profile.dashboard') }}">My Account</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">My Orders</p>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')

            <div class="col-lg-9 mb-5">
                <h5 class="font-weight-semi-bold mb-4">Order History</h5>

                @if($orders->isEmpty())
                    <div class="text-center py-5 bg-light">
                        <i class="fa fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No orders yet</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Start Shopping</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-secondary text-dark">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>${{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'pending'   => 'warning',
                                                    'confirmed' => 'info',
                                                    'packed'    => 'secondary',
                                                    'shipped'   => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                $badge = $badges[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badge }} text-capitalize">{{ $order->status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('profile.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
