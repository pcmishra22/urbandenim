@extends('layouts.eshopper')
@section('title', 'My Dashboard - Jeanzo')
@section('content')
    @include('front.partials.page-banner', ['title' => 'My Account', 'breadcrumb' => 'Dashboard'])
    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            @include('front.partials.profile-sidebar')
            <div class="col-lg-9 mb-5">
                {{-- Email verification banner (soft nudge, not a blocker) --}}
                @if(!Auth::user()->hasVerifiedEmail())
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-envelope me-2"></i>
                    <div>
                        <strong>Please verify your email address.</strong>
                        Check your inbox for the verification link we sent to <strong>{{ Auth::user()->email }}</strong>.
                        <form method="POST" action="{{ route('verification.resend') }}" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Resend Email</button>
                        </form>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                <h5 class="font-weight-semi-bold mb-4">Welcome, {{ Auth::user()->name }}!</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-4 text-center">
                            <i class="fa fa-shopping-bag fa-2x text-primary mb-2"></i>
                            <h3 class="font-weight-bold">{{ $orders->total() }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-4 text-center">
                            <i class="fa fa-clock fa-2x text-warning mb-2"></i>
                            <h3 class="font-weight-bold">{{ \App\Models\Order::where('user_id',Auth::id())->whereIn('status',['pending','processing'])->count() }}</h3>
                            <p class="text-muted mb-0">Active Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-4 text-center">
                            <i class="fa fa-rupee-sign fa-2x text-success mb-2"></i>
                            <h3 class="font-weight-bold">₹{{ number_format(\App\Models\Order::where('user_id',Auth::id())->whereNotIn('status',['cancelled'])->sum('total_price'),0) }}</h3>
                            <p class="text-muted mb-0">Total Spent</p>
                        </div>
                    </div>
                </div>
                <h6 class="font-weight-semi-bold mb-3">Recent Orders</h6>
                @if($orders->isEmpty())
                    <div class="text-center py-5 bg-light rounded">
                        <i class="fa fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No orders yet</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Start Shopping</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-secondary"><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                @foreach($orders as $order)
                                @php $bm=['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>₹{{ number_format($order->total_price,2) }}</td>
                                    <td><span class="badge badge-{{ $bm[$order->status]??'secondary' }} text-capitalize">{{ $order->status }}</span></td>
                                    <td><a href="{{ route('profile.order-details',$order->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
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
