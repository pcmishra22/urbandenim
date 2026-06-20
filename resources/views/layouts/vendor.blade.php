<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Panel') - EShopper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a3c34;
            --sidebar-hover: #264d43;
            --sidebar-active: #27ae60;
            --sidebar-text: rgba(255,255,255,0.78);
            --sidebar-heading: rgba(255,255,255,0.35);
            --topbar-bg: #1a3c34;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; }

        /* Top Bar */
        .vendor-topbar {
            position: fixed; top: 0; left: 0; right: 0; height: 60px;
            background: linear-gradient(135deg, #1a3c34 0%, #27ae60 100%);
            display: flex; align-items: center; padding: 0 20px;
            z-index: 1040; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .vendor-topbar .brand { color: #fff; font-size: 1.3rem; font-weight: 700; text-decoration: none; margin-right: auto; }
        .vendor-topbar .brand i { margin-right: 8px; }
        .vendor-topbar .nav-right { display: flex; align-items: center; gap: 16px; color: rgba(255,255,255,0.9); font-size: 0.9rem; }
        .vendor-topbar .nav-right a { color: rgba(255,255,255,0.85); text-decoration: none; transition: color .2s; }
        .vendor-topbar .nav-right a:hover { color: #fff; }
        .vendor-badge { background: #27ae60; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; margin-left: 6px; font-weight: 600; letter-spacing: 0.5px; }

        /* Sidebar */
        .vendor-sidebar {
            position: fixed; top: 60px; left: 0; width: 250px; bottom: 0;
            background: var(--sidebar-bg); overflow-y: auto; overflow-x: hidden;
            z-index: 1030; padding-bottom: 30px;
        }
        .vendor-sidebar::-webkit-scrollbar { width: 4px; }
        .vendor-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
        .sidebar-section-title {
            padding: 18px 20px 6px; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase; color: var(--sidebar-heading); user-select: none;
        }
        .vendor-sidebar a {
            display: flex; align-items: center; gap: 10px; padding: 9px 20px;
            color: var(--sidebar-text); text-decoration: none; font-size: 0.875rem;
            border-left: 3px solid transparent; transition: all .2s; white-space: nowrap;
        }
        .vendor-sidebar a i { width: 18px; text-align: center; font-size: 0.85rem; flex-shrink: 0; }
        .vendor-sidebar a:hover { color: #fff; background: var(--sidebar-hover); border-left-color: rgba(39,174,96,0.5); }
        .vendor-sidebar a.active { color: #fff; background: rgba(39,174,96,0.2); border-left-color: var(--sidebar-active); }

        /* Vendor info box */
        .vendor-info-box {
            margin: 16px 14px 4px;
            background: rgba(39,174,96,0.12);
            border: 1px solid rgba(39,174,96,0.25);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .vendor-info-box .shop-name { font-size: 0.9rem; font-weight: 700; color: #fff; }
        .vendor-info-box .vendor-role { font-size: 0.72rem; color: rgba(255,255,255,0.55); margin-top: 2px; }
        .vendor-info-box .status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #27ae60; margin-right: 4px; }

        /* Main content */
        .vendor-main { margin-left: 250px; margin-top: 60px; min-height: calc(100vh - 60px); padding: 28px 28px 40px; }
        .page-title { margin-bottom: 24px; }
        .page-title h2 { font-size: 1.4rem; font-weight: 700; color: #2c3e50; margin: 0; }

        /* Stat cards */
        .stat-card {
            background: #fff; padding: 22px 20px; border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-align: center; margin-bottom: 20px;
            transition: transform .25s, box-shadow .25s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
        .stat-card h6 { color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .stat-card .value { font-size: 2.2rem; font-weight: 700; color: #27ae60; }

        /* Cards */
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .card-header { background: #f8f9fa; border-bottom: 1px solid #e8e8e8; padding: 16px 20px; font-weight: 600; border-radius: 10px 10px 0 0 !important; }

        /* Table */
        .table thead th { background: #f8f9fa; color: #2c3e50; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-top: none; border-bottom: 2px solid #e8e8e8; white-space: nowrap; }
        .table td { vertical-align: middle; }
        .table-hover tbody tr:hover { background-color: #f0fff5; }
        .badge { padding: 5px 10px; border-radius: 20px; font-weight: 500; font-size: 0.78rem; }
        .pagination { flex-wrap: wrap; gap: 4px; margin-bottom: 0; }
        .pagination .page-link { border-radius: 6px !important; border: 1px solid #dee2e6; color: #27ae60; padding: 6px 12px; font-size: 0.875rem; transition: all .2s; }
        .pagination .page-item.active .page-link { background-color: #27ae60; border-color: #27ae60; color: #fff; }
        .pagination .page-link:hover { background-color: #eafaf1; border-color: #27ae60; }
        .pagination .page-item.disabled .page-link { color: #aaa; }
        .alert { border: none; border-radius: 8px; }
        .btn-primary { background-color: #27ae60; border-color: #27ae60; }
        .btn-primary:hover { background-color: #219a52; border-color: #219a52; }
        .access-denied-banner {
            background: linear-gradient(135deg, #1a3c34 0%, #27ae60 100%);
            color: #fff; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .vendor-sidebar { transform: translateX(-100%); transition: transform .3s; }
            .vendor-sidebar.open { transform: translateX(0); }
            .vendor-main { margin-left: 0; }
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="vendor-topbar">
        <a href="{{ route('vendor.dashboard') }}" class="brand">
            <i class="fas fa-store"></i> EShopper
            <span class="vendor-badge">Vendor</span>
        </a>
        <div class="nav-right">
            @auth
            <span><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}</span>
            @endauth
            <a href="{{ route('vendor.dashboard') }}"><i class="fas fa-home"></i></a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a>
            <a href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="vendor-sidebar">

        {{-- Vendor Profile Info Box --}}
        @auth
        @php $vendorProfile = Auth::user()->vendorProfile; @endphp
        @if($vendorProfile)
        <div class="vendor-info-box">
            <div class="shop-name"><i class="fas fa-store me-1"></i> {{ $vendorProfile->shop_name }}</div>
            <div class="vendor-role"><span class="status-dot"></span> Vendor Admin &bull; Own Products Only</div>
        </div>
        @endif
        @endauth

        <div class="sidebar-section-title">My Store</div>
        <a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Dashboard
        </a>

        <div class="sidebar-section-title">My Products</div>
        <a href="{{ route('vendor.products.index') }}" class="{{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> My Products
        </a>
        <a href="{{ route('vendor.products.create') }}" class="{{ request()->routeIs('vendor.products.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Add New Product
        </a>

        <div class="sidebar-section-title">My Orders</div>
        <a href="{{ route('vendor.orders.index') }}" class="{{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> My Orders
        </a>

        <div class="sidebar-section-title">My Inventory</div>
        <a href="{{ route('vendor.inventory.index') }}" class="{{ request()->routeIs('vendor.inventory.index') ? 'active' : '' }}">
            <i class="fas fa-cubes"></i> Stock Tracking
        </a>
        <a href="{{ route('vendor.inventory.alerts') }}" class="{{ request()->routeIs('vendor.inventory.alerts') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
        </a>

        <div class="sidebar-section-title">Account</div>
        <a href="{{ route('vendor.profile') }}" class="{{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
            <i class="fas fa-user-cog"></i> My Profile
        </a>

        <div class="sidebar-section-title">Reviews</div>
        <a href="{{ route('vendor.reviews') }}" class="{{ request()->routeIs('vendor.reviews') ? 'active' : '' }}">
            <i class="fas fa-star"></i> My Reviews & Ratings
        </a>

    </nav>

    <!-- Main Content -->
    <main class="vendor-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Logout form -->
    <form id="logoutForm" action="{{ route('vendor.logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
