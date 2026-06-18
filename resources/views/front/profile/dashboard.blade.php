@extends('layouts.eshopper')
@section('title', 'My Account — Dashboard | Jeanzo India')
@section('meta_description', 'Manage your Jeanzo account. View orders, track shipments, manage addresses, wishlist and personal information.')
@section('meta_robots', 'noindex, nofollow')

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

            {{-- Welcome banner --}}
            <div class="j-section mb-4" style="background:linear-gradient(135deg,#D19C97 0%,#c4857f 100%);border:none;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-user fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="text-white font-weight-bold mb-0">Welcome back, {{ auth()->user()->name }}!</h5>
                        <small class="text-white" style="opacity:.85;">Manage your orders, addresses and profile from here.</small>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="j-stat-card">
                        <div class="stat-icon"><i class="fa fa-shopping-bag"></i></div>
                        <div class="stat-val">{{ $orderCount }}</div>
                        <div class="stat-lbl">Total Orders</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="j-stat-card">
                        <div class="stat-icon"><i class="fa fa-heart"></i></div>
                        <div class="stat-val">{{ $wishlistCount }}</div>
                        <div class="stat-lbl">Wishlist Items</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="j-stat-card">
                        <div class="stat-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div class="stat-val">{{ $user->addresses()->count() }}</div>
                        <div class="stat-lbl">Saved Addresses</div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="j-section">
                <div class="j-section-title">Quick Actions</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('profile.personal-info') }}" class="j-action-card">
                            <div class="action-icon"><i class="fa fa-user"></i></div>
                            <div><h6>Edit Profile</h6><small>Update your personal information</small></div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('profile.orders') }}" class="j-action-card">
                            <div class="action-icon"><i class="fa fa-shopping-bag"></i></div>
                            <div><h6>View Orders</h6><small>Track and manage your orders</small></div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('profile.addresses') }}" class="j-action-card">
                            <div class="action-icon"><i class="fa fa-map-marker-alt"></i></div>
                            <div><h6>Manage Addresses</h6><small>Add or edit delivery addresses</small></div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('wishlist.index') }}" class="j-action-card">
                            <div class="action-icon"><i class="fa fa-heart"></i></div>
                            <div><h6>My Wishlist</h6><small>Products you've saved</small></div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
