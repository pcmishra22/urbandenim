<div class="col-lg-3 mb-5">
    <div class="bg-light p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:80px;height:80px;">
                <i class="fa fa-user fa-2x text-white"></i>
            </div>
            <h5 class="font-weight-semi-bold mb-1">{{ auth()->user()->name }}</h5>
            <small class="text-muted">{{ auth()->user()->email }}</small>
        </div>
        <div class="d-flex flex-column">
            <a href="{{ route('profile.dashboard') }}" class="btn {{ request()->routeIs('profile.dashboard') ? 'btn-primary' : 'btn-outline-dark' }} mb-2">
                <i class="fa fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="{{ route('profile.personal-info') }}" class="btn {{ request()->routeIs('profile.personal-info') ? 'btn-primary' : 'btn-outline-dark' }} mb-2">
                <i class="fa fa-user mr-2"></i>Personal Info
            </a>
            <a href="{{ route('profile.addresses') }}" class="btn {{ request()->routeIs('profile.addresses') || request()->routeIs('profile.address.*') ? 'btn-primary' : 'btn-outline-dark' }} mb-2">
                <i class="fa fa-map-marker-alt mr-2"></i>Addresses
            </a>
            <a href="{{ route('profile.orders') }}" class="btn {{ request()->routeIs('profile.orders') || request()->routeIs('profile.order-details') ? 'btn-primary' : 'btn-outline-dark' }} mb-2">
                <i class="fa fa-shopping-bag mr-2"></i>My Orders
            </a>
            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-dark mb-2">
                <i class="fa fa-heart mr-2"></i>Wishlist
            </a>
            <a href="{{ route('profile.change-password') }}" class="btn {{ request()->routeIs('profile.change-password') ? 'btn-primary' : 'btn-outline-dark' }} mb-2">
                <i class="fa fa-lock mr-2"></i>Change Password
            </a>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="fa fa-sign-out-alt mr-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>
