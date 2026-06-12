@include('front.partials.design-system')

<div class="col-lg-3 mb-5">
    <div class="profile-sidebar-card shadow-sm">

        {{-- User Header --}}
        <div class="profile-sidebar-header">
            <div class="profile-sidebar-avatar">
                <i class="fa fa-user fa-lg text-white"></i>
            </div>
            <p class="profile-sidebar-name">{{ auth()->user()->name }}</p>
            <p class="profile-sidebar-email">{{ auth()->user()->email }}</p>
        </div>

        {{-- Navigation --}}
        <div class="profile-sidebar-nav">
            <a href="{{ route('profile.dashboard') }}"
               class="{{ request()->routeIs('profile.dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('profile.personal-info') }}"
               class="{{ request()->routeIs('profile.personal-info') ? 'active' : '' }}">
                <i class="fa fa-user"></i> Personal Info
            </a>
            <a href="{{ route('profile.addresses') }}"
               class="{{ request()->routeIs('profile.addresses') || request()->routeIs('profile.address.*') ? 'active' : '' }}">
                <i class="fa fa-map-marker-alt"></i> My Addresses
            </a>
            <a href="{{ route('profile.orders') }}"
               class="{{ request()->routeIs('profile.orders') || request()->routeIs('profile.order-details') ? 'active' : '' }}">
                <i class="fa fa-shopping-bag"></i> My Orders
            </a>
            <a href="{{ route('profile.reviews') }}"
               class="{{ request()->routeIs('profile.reviews') ? 'active' : '' }}">
                <i class="fa fa-star"></i> My Reviews
            </a>
            <a href="{{ route('wishlist.index') }}"
               class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
                <i class="fa fa-heart"></i> Wishlist
            </a>
            <a href="{{ route('profile.change-password') }}"
               class="{{ request()->routeIs('profile.change-password') ? 'active' : '' }}">
                <i class="fa fa-lock"></i> Change Password
            </a>
            <div class="logout-btn">
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
