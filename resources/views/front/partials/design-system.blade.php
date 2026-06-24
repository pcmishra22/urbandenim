<style>
/* =====================================================
   JEANZO DESIGN SYSTEM  — Meesho-inspired
   Primary accent: #D19C97 (dusty rose)
   ===================================================== */

:root {
    --j-primary:     #D19C97;
    --j-primary-dk:  #b8807a;
    --j-primary-lt:  #f7edec;
    --j-dark:        #2d2d2d;
    --j-muted:       #888;
    --j-border:      #e8e0df;
    --j-radius:      14px;
    --j-radius-sm:   8px;
    --j-shadow:      0 2px 12px rgba(209,156,151,.15);
    --j-shadow-hover:0 6px 24px rgba(209,156,151,.28);
}

/* ---------- buttons ---------- */
.btn-primary,
.btn-primary:focus {
    background: var(--j-primary) !important;
    border-color: var(--j-primary) !important;
    color: #fff !important;
}
.btn-primary:hover {
    background: var(--j-primary-dk) !important;
    border-color: var(--j-primary-dk) !important;
}
.btn-outline-primary {
    color: var(--j-primary) !important;
    border-color: var(--j-primary) !important;
}
.btn-outline-primary:hover {
    background: var(--j-primary) !important;
    color: #fff !important;
}
.text-primary { color: var(--j-primary) !important; }

/* ---------- cards (auth / section boxes) ---------- */
.j-card {
    border-radius: var(--j-radius);
    border: 2px solid var(--j-primary);
    background: #fff;
    overflow: hidden;
    box-shadow: var(--j-shadow);
}
.j-card-header {
    background: var(--j-primary);
    padding: 16px 20px;
}
.j-card-title {
    color: #fff;
    font-weight: 700;
    text-align: center;
    font-size: 1.25rem;
    letter-spacing: .3px;
    margin: 0;
}
.j-card-body { background: #fff; }

/* backward-compat aliases used on existing pages */
.auth-theme-card  { border-radius: var(--j-radius); border: 2px solid var(--j-primary); background: #fff; overflow: hidden; box-shadow: var(--j-shadow); }
.auth-theme-header{ background: var(--j-primary); padding: 16px 20px; }
.auth-theme-title { color: #fff; font-weight: 700; text-align: center; font-size: 1.25rem; letter-spacing: .3px; margin: 0; }
.auth-theme-body  { background: #fff; }

/* ---------- section boxes (content areas inside profile) ---------- */
.j-section {
    background: #fff;
    border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius-sm);
    padding: 24px;
    margin-bottom: 20px;
}
.j-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--j-dark);
    padding-bottom: 12px;
    border-bottom: 2px solid var(--j-primary-lt);
    margin-bottom: 18px;
}

/* ---------- profile sidebar ---------- */
.profile-sidebar-card {
    background: #fff;
    border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius);
    overflow: hidden;
    position: sticky;
    top: 80px;
}
.profile-sidebar-header {
    background: linear-gradient(135deg, var(--j-primary) 0%, #c4857f 100%);
    padding: 24px 16px;
    text-align: center;
}
.profile-sidebar-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.profile-sidebar-name { color: #fff; font-weight: 700; font-size: 1rem; margin: 0; }
.profile-sidebar-email{ color: rgba(255,255,255,.8); font-size: .78rem; margin: 2px 0 0; }
.profile-sidebar-nav  { padding: 12px 8px; }
.profile-sidebar-nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: var(--j-radius-sm);
    color: var(--j-dark); text-decoration: none;
    font-size: .9rem; transition: all .2s;
    margin-bottom: 2px;
}
.profile-sidebar-nav a:hover { background: var(--j-primary-lt); color: var(--j-primary); }
.profile-sidebar-nav a.active { background: var(--j-primary); color: #fff; font-weight: 600; }
.profile-sidebar-nav a i { width: 18px; text-align: center; }
.profile-sidebar-nav .logout-btn {
    margin-top: 8px; border-top: 1px solid var(--j-border); padding-top: 10px;
}
.profile-sidebar-nav .logout-btn button {
    width: 100%; display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: var(--j-radius-sm);
    background: none; border: none; color: #e74c3c; font-size: .9rem;
    cursor: pointer; transition: background .2s;
}
.profile-sidebar-nav .logout-btn button:hover { background: #ffeaea; }

/* ---------- page banner ---------- */
.j-page-banner {
    position: relative; overflow: hidden;
    min-height: 220px;
    background: linear-gradient(135deg, #2d1b1a 0%, #6b3530 100%);
    margin-bottom: 0;
}
.j-page-banner-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    z-index: 0;
}
.j-page-banner-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45); z-index: 1;
}
.j-page-banner-content {
    position: relative; z-index: 2;
    min-height: 220px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 32px 20px; text-align: center;
}
.j-page-banner-title {
    font-size: clamp(1.4rem, 3.5vw, 2.2rem);
    font-weight: 800; color: #fff; text-transform: uppercase;
    letter-spacing: 2px; text-shadow: 0 2px 10px rgba(0,0,0,.4);
    margin: 0 0 10px;
}
.j-breadcrumb { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: center; }
.j-breadcrumb a   { color: rgba(255,255,255,.75); text-decoration: none; font-size: .85rem; }
.j-breadcrumb a:hover { color: #fff; }
.j-breadcrumb span{ color: rgba(255,255,255,.5); font-size: .85rem; }
.j-breadcrumb .current { color: #fff; font-size: .85rem; }

/* ---------- stat cards ---------- */
.j-stat-card {
    background: #fff; border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius-sm); padding: 20px;
    text-align: center; transition: all .25s;
}
.j-stat-card:hover { border-color: var(--j-primary); box-shadow: var(--j-shadow-hover); transform: translateY(-3px); }
.j-stat-card .stat-icon {
    width: 48px; height: 48px; border-radius: 50%;
    background: var(--j-primary-lt); display: inline-flex;
    align-items: center; justify-content: center; margin-bottom: 10px;
    color: var(--j-primary); font-size: 1.2rem;
}
.j-stat-card .stat-val { font-size: 1.8rem; font-weight: 800; color: var(--j-dark); line-height: 1; }
.j-stat-card .stat-lbl { font-size: .8rem; color: var(--j-muted); margin-top: 4px; }

/* ---------- quick action cards ---------- */
.j-action-card {
    display: flex; align-items: center; gap: 14px;
    background: #fff; border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius-sm); padding: 16px;
    text-decoration: none; color: var(--j-dark); transition: all .2s;
}
.j-action-card:hover { border-color: var(--j-primary); background: var(--j-primary-lt); color: var(--j-dark); text-decoration: none; box-shadow: var(--j-shadow); }
.j-action-card .action-icon {
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--j-primary-lt); display: flex;
    align-items: center; justify-content: center;
    color: var(--j-primary); font-size: 1.1rem; flex-shrink: 0;
}
.j-action-card h6 { margin: 0 0 2px; font-weight: 700; font-size: .9rem; }
.j-action-card small { color: var(--j-muted); font-size: .78rem; }

/* ---------- table styling ---------- */
.j-table thead th {
    background: var(--j-primary); color: #fff;
    font-size: .8rem; text-transform: uppercase;
    letter-spacing: .5px; border: none; padding: 12px 16px;
}
.j-table tbody td { vertical-align: middle; padding: 12px 16px; font-size: .9rem; }
.j-table tbody tr:hover { background: var(--j-primary-lt); }

/* ---------- badges ---------- */
.j-badge {
    display: inline-block; padding: 4px 10px;
    border-radius: 20px; font-size: .75rem; font-weight: 600;
}
.j-badge-pending    { background: #fff3cd; color: #856404; }
.j-badge-processing { background: #cff4fc; color: #055160; }
.j-badge-shipped    { background: #cfe2ff; color: #084298; }
.j-badge-delivered  { background: #d1e7dd; color: #0f5132; }
.j-badge-cancelled  { background: #f8d7da; color: #842029; }
.j-badge-paid       { background: #d1e7dd; color: #0f5132; }
.j-badge-awaiting   { background: #fff3cd; color: #856404; }

/* ---------- form controls ---------- */
.form-control:focus {
    border-color: var(--j-primary) !important;
    box-shadow: 0 0 0 3px rgba(209,156,151,.2) !important;
}

/* ---------- address cards ---------- */
.j-address-card {
    background: #fff; border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius-sm); padding: 18px;
    position: relative; transition: all .2s;
}
.j-address-card:hover { border-color: var(--j-primary); box-shadow: var(--j-shadow); }
.j-address-card.default-addr { border-color: var(--j-primary); background: var(--j-primary-lt); }

/* ---------- product card (shop) ---------- */
.j-product-card {
    background: #fff; border: 1.5px solid var(--j-border);
    border-radius: var(--j-radius-sm); overflow: hidden; transition: all .25s;
}
.j-product-card:hover { box-shadow: var(--j-shadow-hover); transform: translateY(-4px); border-color: var(--j-primary); }
.j-product-card .card-img-wrap { position: relative; overflow: hidden; }
.j-product-card .card-img-wrap img { transition: transform .4s; }
.j-product-card:hover .card-img-wrap img { transform: scale(1.05); }
.j-product-card .card-body { padding: 14px; }
.j-product-card .price-tag { font-weight: 800; color: var(--j-primary); font-size: 1.1rem; }

/* ---------- cart / checkout ---------- */
.j-cart-item { background: #fff; border: 1.5px solid var(--j-border); border-radius: var(--j-radius-sm); padding: 16px; margin-bottom: 12px; }
.j-order-summary { background: #fff; border: 1.5px solid var(--j-border); border-radius: var(--j-radius-sm); padding: 20px; position: sticky; top: 80px; }
.j-order-summary .summary-title { font-weight: 700; font-size: 1rem; color: var(--j-dark); padding-bottom: 12px; border-bottom: 2px solid var(--j-primary-lt); margin-bottom: 14px; }
.j-order-summary .summary-row { display: flex; justify-content: space-between; font-size: .9rem; margin-bottom: 8px; color: #555; }
.j-order-summary .summary-total { display: flex; justify-content: space-between; font-weight: 800; font-size: 1.1rem; color: var(--j-dark); padding-top: 12px; border-top: 2px solid var(--j-border); margin-top: 8px; }

/* ---------- checkout steps ---------- */
.checkout-steps { display: flex; gap: 0; margin-bottom: 28px; }
.checkout-step { flex: 1; text-align: center; position: relative; }
.checkout-step-circle {
    width: 32px; height: 32px; border-radius: 50%;
    background: #e0e0e0; color: #888;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem; position: relative; z-index: 1;
}
.checkout-step.active .checkout-step-circle { background: var(--j-primary); color: #fff; }
.checkout-step.done .checkout-step-circle   { background: #27ae60; color: #fff; }
.checkout-step-label { font-size: .75rem; color: var(--j-muted); margin-top: 4px; }
.checkout-step.active .checkout-step-label { color: var(--j-primary); font-weight: 600; }
.checkout-step::before {
    content: ''; position: absolute; top: 15px; left: 50%; right: -50%;
    height: 2px; background: #e0e0e0; z-index: 0;
}
.checkout-step:last-child::before { display: none; }

/* ---------- responsive tweaks ---------- */
@media (max-width: 767px) {
    .j-page-banner-title { font-size: 1.3rem; letter-spacing: 1px; }
    .profile-sidebar-card { position: static; }
}
</style>

<style>
/* ============================================================
   JEANZO — GLOBAL RESPONSIVE ADDITIONS
   Applies to all front pages (cart, checkout, products, detail)
   ============================================================ */

/* ── Prevent horizontal scroll globally ── */
html, body { overflow-x: hidden !important; }

/* ── Page banner mobile ── */
@media (max-width: 575px) {
    .j-page-banner      { min-height: 140px; }
    .j-page-banner-content { min-height: 140px; padding: 24px 16px; }
    .j-page-banner-title { font-size: 1.2rem; letter-spacing: 1px; }
}

/* ── Cart page ── */
@media (max-width: 767px) {
    .j-cart-item { padding: 12px; }
    .j-cart-item img { width: 70px !important; height: 70px !important; }
    .j-order-summary { position: static; }
}
@media (max-width: 575px) {
    .j-cart-item .row > div { margin-bottom: 8px; }
}

/* ── Checkout ── */
@media (max-width: 767px) {
    .j-order-summary { position: static; top: auto; }
    .checkout-steps  { gap: 0; overflow-x: auto; padding-bottom: 4px; }
    .checkout-step-label { font-size: .65rem; }
}

/* ── Product listing page ── */
@media (max-width: 991px) {
    /* Filter sidebar becomes collapsible on mobile */
    .filter-sidebar-wrap { position: static !important; max-height: none !important; }
}
@media (max-width: 575px) {
    .products-row .col-6 { padding-left: 6px; padding-right: 6px; }
}

/* ── Product detail ── */
@media (max-width: 767px) {
    .product-detail-gallery { margin-bottom: 24px; }
    .product-detail-info    { padding: 0; }
}
@media (max-width: 575px) {
    .product-detail-thumb   { display: none; } /* hide thumbs on tiny screens */
    .product-variants-wrap  { gap: 8px; }
    .product-variant-btn    { min-width: 44px; padding: 6px 10px; }
}

/* ── Profile pages ── */
@media (max-width: 767px) {
    .profile-sidebar-card { position: static; margin-bottom: 20px; }
    .j-section { padding: 16px; }
}

/* ── Tables on mobile — horizontal scroll ── */
@media (max-width: 575px) {
    .j-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .j-table      { min-width: 500px; }
    .j-table tbody td, .j-table thead th { padding: 8px 10px; font-size: .8rem; }
}

/* ── Footer ── */
@media (max-width: 767px) {
    .container-fluid.bg-secondary .row.px-xl-5 > div { margin-bottom: 32px; }
    .container-fluid.bg-secondary h5 { font-size: .95rem; margin-bottom: 12px !important; }
}
@media (max-width: 575px) {
    .container-fluid.bg-secondary { padding-top: 32px !important; }
    .container-fluid.bg-secondary .row.border-top { flex-direction: column; gap: 8px; text-align: center !important; }
    .container-fluid.bg-secondary .col-md-6 { text-align: center !important; }
}

/* ── Utility helpers ── */
@media (max-width: 575px) {
    .hide-mobile { display: none !important; }
    .text-sm-center { text-align: center !important; }
    .btn-block-mobile { display: block !important; width: 100% !important; }
    .px-mobile-2 { padding-left: 8px !important; padding-right: 8px !important; }
}
</style>

<style>
/* ============================================================
   JEANZO — COMPREHENSIVE MOBILE RESPONSIVE FIXES
   Covers: Home, Products, Product Detail, Cart, Checkout
   ============================================================ */

/* ── BASE: Prevent any horizontal overflow ── */
*, *::before, *::after { box-sizing: border-box; }
html, body { overflow-x: hidden !important; max-width: 100%; }
img { max-width: 100%; height: auto; }

/* ============================================================
   PRODUCTS PAGE — Filter sidebar + product grid
   ============================================================ */

/* Mobile filter toggle button — shown only on mobile */
#mobile-filter-toggle {
    display: none;
    width: 100%;
    background: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: .9rem;
    font-weight: 700;
    color: #1a1a1a;
    cursor: pointer;
    text-align: left;
    margin-bottom: 16px;
    gap: 8px;
    align-items: center;
    justify-content: space-between;
}
#mobile-filter-toggle i { font-size: .85rem; }

/* Filter sidebar collapse on mobile */
#filter-sidebar-collapse { transition: max-height .3s ease; }

@media (max-width: 991px) {
    /* Products page: sidebar stacks above products */
    .products-page-sidebar { margin-bottom: 16px !important; }
    .products-page-sidebar .j-section {
        position: static !important;
        max-height: none !important;
        overflow-y: visible !important;
    }
    #mobile-filter-toggle { display: flex; }
    #filter-sidebar-collapse.collapsed { display: none; }
    #filter-sidebar-collapse.expanded { display: block; }

    /* Products toolbar wraps nicely */
    .products-toolbar {
        flex-direction: column;
        align-items: stretch !important;
        gap: 8px !important;
    }
    .products-toolbar form { max-width: 100% !important; }
    .products-toolbar .dropdown { align-self: flex-end; }

    /* Product grid — 2 cols on tablet */
    #products-container .col-6 { padding-left: 8px; padding-right: 8px; }
}

@media (max-width: 575px) {
    /* Full width container on mobile */
    .container-fluid.px-xl-5 { padding-left: 12px !important; padding-right: 12px !important; }
    .row.px-xl-5 { padding-left: 0 !important; padding-right: 0 !important; }

    /* Product cards — 2 per row, tighter */
    #products-container { margin-left: -6px !important; margin-right: -6px !important; }
    #products-container .col-6 { padding-left: 6px; padding-right: 6px; }

    /* Sort dropdown full width */
    .products-toolbar .dropdown .dropdown-toggle { width: 100%; text-align: left; }

    /* Active filter badges wrap */
    .filter-badge-row { flex-wrap: wrap; gap: 6px; }
}

/* ============================================================
   PRODUCT CARD — grid card tweaks for small screens
   ============================================================ */
@media (max-width: 400px) {
    .prod-card-name { font-size: .72rem !important; }
    .prod-card-price { font-size: .78rem !important; }
    .prod-card-img { min-height: 160px; }
}

/* ============================================================
   PRODUCT DETAIL PAGE
   ============================================================ */
@media (max-width: 991px) {
    /* Two-col layout stacks */
    #pd-page .pd-two-col {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0 !important;
    }
    /* Gallery takes full width */
    #pd-page .pd-two-col > div:first-child {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    /* Info panel full width */
    #pd-page .pd-two-col > div:last-child {
        flex: 1 1 100% !important;
        padding: 20px 0 0 !important;
    }
}

@media (max-width: 767px) {
    /* Main image shorter on mobile */
    .pd-img-aspect { min-height: 260px !important; max-height: 420px !important; }

    /* Product title smaller */
    #pd-page h1 { font-size: 1.35rem !important; }

    /* Price row */
    #pd-page .pd-price-main { font-size: 1.6rem !important; }

    /* Thumbnails — 4 per row max */
    .pd-thumb-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 6px !important; }

    /* Size options — smaller on mobile */
    .pd-size-btn { min-width: 42px !important; padding: 6px 8px !important; font-size: .82rem !important; }

    /* Sticky bar padding */
    #pd-sticky { padding: 10px 12px 14px !important; }
    #pd-sticky-btn { padding: 12px 20px !important; font-size: .85rem !important; }

    /* Related products — 2 cols */
    .pd-related-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; }

    /* Reviews section */
    .pd-review-card { padding: 14px !important; }
}

@media (max-width: 575px) {
    #pd-page { padding-bottom: 90px !important; } /* space for sticky bar */

    /* Breadcrumb — smaller */
    .pd-breadcrumb { font-size: .72rem !important; padding: 8px 12px !important; }

    /* Main image full viewport width feel */
    .pd-img-aspect { min-height: 220px !important; }

    /* Thumbnails — 5 per row, very small */
    .pd-thumb-grid { grid-template-columns: repeat(5, 1fr) !important; gap: 4px !important; }

    /* Color swatches wrap */
    .pd-color-wrap { gap: 6px !important; flex-wrap: wrap !important; }

    /* Size chart link — smaller */
    .pd-size-chart-link { font-size: .78rem !important; }

    /* Product tabs — smaller font */
    .pd-tab-btn { font-size: .8rem !important; padding: 8px 12px !important; }

    /* Add to cart button — full width on tiny */
    .pd-atc-btn { width: 100% !important; }
    .pd-buy-now-btn { width: 100% !important; }

    /* Button row stacks */
    .pd-btn-row { flex-direction: column !important; gap: 10px !important; }
    .pd-btn-row a, .pd-btn-row button { width: 100% !important; justify-content: center !important; }

    /* Related products */
    .pd-related-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
    .pd-prod-card-name { font-size: .78rem !important; }
}

/* ============================================================
   CART PAGE
   ============================================================ */
@media (max-width: 767px) {
    /* Cart items: stack image + info */
    .j-cart-item {
        flex-wrap: wrap !important;
        gap: 10px !important;
        padding: 12px !important;
    }
    .j-cart-item > div:first-child { flex-shrink: 0; } /* image */

    /* Name takes remaining space next to image */
    .j-cart-item .cart-item-name { flex: 1 1 140px !important; }

    /* Price, qty, total, remove — inline on one row after image+name */
    .j-cart-item .cart-item-price,
    .j-cart-item .cart-item-qty,
    .j-cart-item .cart-item-total,
    .j-cart-item .cart-item-remove {
        flex: 0 0 auto !important;
        text-align: left !important;
    }
}

@media (max-width: 575px) {
    /* Cart items — compact */
    .j-cart-item { padding: 10px !important; gap: 8px !important; }
    .j-cart-item img { width: 64px !important; height: 64px !important; border-radius: 6px !important; }

    /* Qty control — smaller */
    .j-cart-item .quantity { width: 90px !important; }
    .j-cart-item .btn-minus,
    .j-cart-item .btn-plus { padding: 4px 8px !important; font-size: .75rem !important; }
    .j-cart-item .qty-input { font-size: .82rem !important; }

    /* Continue shopping button — full width */
    .cart-continue-btn { width: 100% !important; display: block !important; text-align: center !important; }

    /* Order summary stacks below cart on mobile */
    .j-order-summary { margin-top: 0 !important; }

    /* Coupon input row */
    .coupon-row { flex-direction: row !important; gap: 6px !important; }
    .coupon-row input { flex: 1 !important; }
    .coupon-row button { flex-shrink: 0 !important; white-space: nowrap; }
}

/* ============================================================
   CHECKOUT PAGE
   ============================================================ */
@media (max-width: 991px) {
    /* Summary sidebar stacks below form on tablet */
    .checkout-summary-col { order: 2; }
    .checkout-form-col { order: 1; }
}

@media (max-width: 767px) {
    /* Address form — inputs full width */
    .checkout-addr-row .col-md-6,
    .checkout-addr-row .col-sm-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Identity gateway */
    #identity-gateway { padding: 16px !important; }

    /* Guest continue buttons stack */
    #identity-gateway .d-flex.justify-content-between { flex-direction: column !important; gap: 8px !important; }
    #identity-gateway .btn { width: 100% !important; }

    /* Order summary — no sticky */
    .j-order-summary { position: static !important; top: auto !important; }

    /* Checkout steps bar — scrollable */
    .checkout-steps { overflow-x: auto; padding-bottom: 6px; flex-wrap: nowrap !important; }
    .checkout-step-label { font-size: .62rem !important; white-space: nowrap; }
}

@media (max-width: 575px) {
    /* Full width on tiny */
    #checkout-form .form-control { font-size: .88rem; }
    #checkout-form label { font-size: .82rem; }

    /* Payment options */
    .checkout-pay-option { padding: 10px 12px !important; }

    /* Place order button */
    .checkout-submit-btn { font-size: .95rem !important; padding: 14px !important; }

    /* COD badge smaller */
    .cod-recommended-badge { font-size: .6rem !important; padding: 2px 6px !important; }

    /* Trust badges — compact */
    .checkout-trust-badges { font-size: .72rem !important; }

    /* Coupon in checkout */
    .checkout-coupon-row { flex-direction: row !important; gap: 6px; }
    .checkout-coupon-row input { flex: 1 !important; }
    .checkout-coupon-row button { flex-shrink: 0 !important; white-space: nowrap; }
}

/* ============================================================
   HOME PAGE — additional fixes
   ============================================================ */
@media (max-width: 575px) {
    /* Hero buttons */
    .hero-btns { gap: 8px !important; flex-wrap: wrap !important; }
    .btn-hero-solid, .btn-hero-ghost { flex: 1 1 120px !important; text-align: center !important; justify-content: center !important; }

    /* Section heading */
    .jz-heading h2 { font-size: 1.1rem !important; }

    /* Carousel indicators */
    .carousel-indicators { bottom: 4px !important; }
    .carousel-indicators li { width: 6px !important; height: 6px !important; margin: 0 3px !important; }

    /* Newsletter form */
    .nl-form { flex-direction: column !important; }
    .nl-form input { border-right: 1.5px solid #ccc !important; border-bottom: none; border-radius: 8px 8px 0 0 !important; }
    .nl-form button { border-radius: 0 0 8px 8px !important; padding: 12px !important; }
}

/* ============================================================
   FOOTER — mobile layout
   ============================================================ */
@media (max-width: 767px) {
    /* Footer columns stack */
    .footer-col { margin-bottom: 28px !important; }
    .footer-col h5 { font-size: .95rem !important; margin-bottom: 10px !important; }
    .footer-col ul li { margin-bottom: 6px !important; }
    .footer-col ul li a { font-size: .82rem !important; }

    /* Footer bottom row */
    .footer-bottom-row { flex-direction: column !important; text-align: center !important; gap: 6px !important; }
    .footer-bottom-row .col-md-6 { text-align: center !important; }
}

/* ============================================================
   PAGE BANNER — all pages
   ============================================================ */
@media (max-width: 575px) {
    .j-page-banner { min-height: 110px !important; }
    .j-page-banner-content { padding: 20px 12px !important; min-height: 110px !important; }
    .j-page-banner-title { font-size: 1.1rem !important; letter-spacing: .5px !important; }
    .j-breadcrumb { font-size: .72rem !important; }
}

/* ============================================================
   GLOBAL — form & button touch targets
   ============================================================ */
@media (max-width: 767px) {
    /* Minimum touch target 44px height */
    .btn, button, input[type=submit] { min-height: 40px; }
    .form-control { min-height: 40px; font-size: 16px !important; } /* 16px prevents iOS zoom */
    select.form-control { font-size: 16px !important; }

    /* Input group height */
    .input-group .form-control { min-height: 40px; }
    .input-group-text, .input-group .btn { min-height: 40px; }

    /* Alert messages — smaller */
    .alert { font-size: .85rem; padding: 10px 14px; }
}

/* ============================================================
   PROFILE PAGES — mobile
   ============================================================ */
@media (max-width: 767px) {
    .profile-sidebar-card { position: static !important; margin-bottom: 16px !important; }
    .profile-page-content { padding: 0 !important; }
    .j-section { padding: 14px !important; }
}

/* ============================================================
   WISHLIST PAGE — mobile grid
   ============================================================ */
@media (max-width: 575px) {
    .wishlist-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
    .wishlist-item { padding: 8px !important; }
}

/* ============================================================
   MOBILE SAFE PADDING — bottom sticky bars
   ============================================================ */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
    #pd-sticky { padding-bottom: calc(12px + env(safe-area-inset-bottom)) !important; }
}
</style>
