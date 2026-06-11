@extends('layouts.eshopper')

@section('content')
    <div class="container" style="padding-top: 30px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-theme-card shadow-sm">
                    <div class="auth-theme-header">
                        <div class="auth-theme-title">My Account</div>
                    </div>

                    <div class="auth-theme-body p-4">
                        <div class="text-center text-muted">
                            Manage your account details, orders, addresses, and more.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .auth-theme-card {
            border-radius: 14px;
            border: 2px solid #D19C97;
            background: #ffffff;
            overflow: hidden;
        }

        .auth-theme-header {
            background: #D19C97;
            padding: 18px 18px;
        }

        .auth-theme-title {
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            font-size: 1.35rem;
            letter-spacing: 0.2px;
        }

        .auth-theme-body {
            background: #ffffff;
        }
    </style>

    <div class="container-fluid pt-5 pb-5">
        <div class="row px-xl-5">
            <!-- Sidebar -->
            @include('front.partials.profile-sidebar')

            <!-- Main Content -->
            <div class="col-lg-9 mb-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <!-- Stats -->
                <div class="row mb-5">
                    <div class="col-md-4 mb-3">
                        <div class="border text-center p-4">
                            <i class="fa fa-shopping-bag fa-2x text-primary mb-3"></i>
                            <h4 class="font-weight-bold">{{ $orderCount }}</h4>
                            <p class="m-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border text-center p-4">
                            <i class="fa fa-heart fa-2x text-primary mb-3"></i>
                            <h4 class="font-weight-bold">{{ $wishlistCount }}</h4>
                            <p class="m-0">Wishlist Items</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border text-center p-4">
                            <i class="fa fa-map-marker-alt fa-2x text-primary mb-3"></i>
                            <h4 class="font-weight-bold">{{ $user->addresses()->count() }}</h4>
                            <p class="m-0">Saved Addresses</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-light p-4">
                    <h5 class="font-weight-semi-bold mb-4">Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('profile.personal-info') }}" class="d-flex align-items-center border bg-white p-3 text-dark text-decoration-none">
                                <i class="fa fa-user text-primary fa-lg mr-3"></i>
                                <div>
                                    <h6 class="mb-0">Edit Profile</h6>
                                    <small class="text-muted">Update your personal information</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center border bg-white p-3 text-dark text-decoration-none">
                                <i class="fa fa-shopping-bag text-primary fa-lg mr-3"></i>
                                <div>
                                    <h6 class="mb-0">View Orders</h6>
                                    <small class="text-muted">Track and manage your orders</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('profile.addresses') }}" class="d-flex align-items-center border bg-white p-3 text-dark text-decoration-none">
                                <i class="fa fa-map-marker-alt text-primary fa-lg mr-3"></i>
                                <div>
                                    <h6 class="mb-0">Manage Addresses</h6>
                                    <small class="text-muted">Add or edit delivery addresses</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('wishlist.index') }}" class="d-flex align-items-center border bg-white p-3 text-dark text-decoration-none">
                                <i class="fa fa-heart text-primary fa-lg mr-3"></i>
                                <div>
                                    <h6 class="mb-0">My Wishlist</h6>
                                    <small class="text-muted">Products you've saved</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
