<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EShopper Admin') - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1e2a3a;
            --sidebar-hover: #2d3e52;
            --sidebar-active: #3498db;
            --sidebar-text: rgba(255,255,255,0.75);
            --sidebar-heading: rgba(255,255,255,0.35);
            --topbar-bg: #2c3e50;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
        }

        /* ── Top navbar ── */
        .admin-topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1040;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .admin-topbar .brand {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
            margin-right: auto;
        }
        .admin-topbar .brand i { margin-right: 8px; }
        .admin-topbar .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }
        .admin-topbar .nav-right a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: color .2s;
        }
        .admin-topbar .nav-right a:hover { color: #fff; }

        /* ── Sidebar ── */
        .admin-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            bottom: 0;
            background: var(--sidebar-bg);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1030;
            padding-bottom: 30px;
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

        .sidebar-section-title {
            padding: 18px 20px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--sidebar-heading);
            user-select: none;
        }

        .admin-sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            border-left: 3px solid transparent;
            transition: all .2s;
            white-space: nowrap;
        }
        .admin-sidebar a i {
            width: 18px;
            text-align: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        .admin-sidebar a:hover {
            color: #fff;
            background: var(--sidebar-hover);
            border-left-color: rgba(52,152,219,0.5);
        }
        .admin-sidebar a.active {
            color: #fff;
            background: rgba(52,152,219,0.2);
            border-left-color: var(--sidebar-active);
        }
        .sidebar-sub a {
            padding-left: 46px;
            font-size: 0.82rem;
        }

        /* ── Main content ── */
        .admin-main {
            margin-left: 250px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            padding: 28px 28px 40px;
        }

        /* ── Page title ── */
        .page-title {
            margin-bottom: 24px;
        }
        .page-title h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            padding: 22px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center;
            margin-bottom: 20px;
            transition: transform .25s, box-shadow .25s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
        .stat-card h6 { color: #999; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .stat-card .value { font-size: 2.2rem; font-weight: 700; color: #3498db; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e8e8e8;
            padding: 16px 20px;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }

        /* ── Table ── */
        .table thead th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            border-top: none;
            border-bottom: 2px solid #e8e8e8;
            white-space: nowrap;
        }
        .table td { vertical-align: middle; }
        .table-hover tbody tr:hover { background-color: #f8fbff; }

        /* ── Badges ── */
        .badge { padding: 5px 10px; border-radius: 20px; font-weight: 500; font-size: 0.78rem; }

        /* ── Pagination fix ── */
        .pagination { flex-wrap: wrap; gap: 4px; margin-bottom: 0; }
        .pagination .page-link {
            border-radius: 6px !important;
            border: 1px solid #dee2e6;
            color: #3498db;
            padding: 6px 12px;
            font-size: 0.875rem;
            transition: all .2s;
        }
        .pagination .page-item.active .page-link {
            background-color: #3498db;
            border-color: #3498db;
            color: #fff;
        }
        .pagination .page-link:hover { background-color: #e8f4fd; border-color: #3498db; }
        .pagination .page-item.disabled .page-link { color: #aaa; }

        /* ── Alerts ── */
        .alert { border: none; border-radius: 8px; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform .3s; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="admin-topbar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <i class="fas fa-store"></i> EShopper Admin
        </a>
        <div class="nav-right">
            @auth
            <span><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}</span>
            @endauth
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
            <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a>
            <a href="javascript:void(0)" onclick="document.getElementById('logoutForm').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="admin-sidebar">

        <div class="sidebar-section-title">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        <div class="sidebar-section-title">Catalog</div>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-th"></i> Categories
        </a>
        <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Brands
        </a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt"></i> Coupons
        </a>

        <div class="sidebar-section-title">Orders</div>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Orders
        </a>
        <a href="{{ route('admin.returns.index') }}" class="{{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
            <i class="fas fa-undo"></i> Returns & Refunds
        </a>
        <a href="{{ route('admin.shipments.index') }}" class="{{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Shipments
        </a>

        <div class="sidebar-section-title">Inventory</div>
        <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
            <i class="fas fa-cubes"></i> Stock Tracking
        </a>
        <a href="{{ route('admin.inventory.alerts') }}" class="{{ request()->routeIs('admin.inventory.alerts') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
        </a>
        <a href="{{ route('admin.inventory.history') }}" class="{{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Stock History
        </a>

        <div class="sidebar-section-title">Logistics</div>
        <a href="{{ route('admin.couriers.index') }}" class="{{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
            <i class="fas fa-shipping-fast"></i> Couriers
        </a>
        <a href="{{ route('admin.shipping_rules.index') }}" class="{{ request()->routeIs('admin.shipping_rules.*') ? 'active' : '' }}">
            <i class="fas fa-route"></i> Shipping Rules
        </a>
        <a href="{{ route('admin.delivery_charges.index') }}" class="{{ request()->routeIs('admin.delivery_charges.*') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign"></i> Delivery Charges
        </a>

        <div class="sidebar-section-title">Users</div>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Customers
        </a>
        <a href="{{ route('admin.vendors.index') }}" class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Vendors
        </a>
        <a href="{{ route('admin.register') }}" class="{{ request()->routeIs('admin.register') ? 'active' : '' }}">
            <i class="fas fa-user-shield"></i> Create Admin
        </a>

        <div class="sidebar-section-title">Content</div>
        <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="fas fa-image"></i> Banners
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="{{ request()->routeIs('admin.blogs.*') || request()->routeIs('admin.blog.*') ? 'active' : '' }}">
            <i class="fas fa-blog"></i> Blog Posts
        </a>
        <a href="{{ route('admin.cms.pages.index') }}" class="{{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> CMS Pages & FAQs
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="fas fa-star"></i> Reviews
        </a>
        <a href="{{ route('admin.homepage.index') }}" class="{{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Homepage Sections
        </a>

        <div class="sidebar-section-title">System</div>
        <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifications
        </a>
        <a href="{{ route('admin.seo.index') }}" class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
            <i class="fas fa-search"></i> SEO
        </a>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="{{ route('admin.auditlogs.index') }}" class="{{ request()->routeIs('admin.auditlogs.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Audit Logs
        </a>

    </nav>

    <!-- Main Content -->
    <main class="admin-main">
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
    <form id="logoutForm" action="@yield('logout_action', route('admin.logout'))" method="POST" style="display:none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
