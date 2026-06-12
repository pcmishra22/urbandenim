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
