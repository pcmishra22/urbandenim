@extends('layouts.eshopper')
@section('title', $vendor->shop_name . ' — Jeanzo Marketplace')
@section('meta_description', 'Shop all jeans from ' . $vendor->shop_name . ' on Jeanzo. Premium denim with fast delivery across India.')
@section('canonical', route('brands.show', \Illuminate\Support\Str::slug($vendor->shop_name)))
@section('meta_robots', 'index, follow')

@section('content')
@include('front.partials.design-system')

{{-- ── Hero banner ────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 60%,#0f3460 100%); padding:48px 0 36px; margin-bottom:0;">
    <div class="container">
        <div class="d-flex align-items-center flex-wrap" style="gap:24px;">

            {{-- Avatar --}}
            <div style="width:80px;height:80px;border-radius:50%;background:var(--j-primary,#D19C97);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:2rem;font-weight:800;color:#fff;border:3px solid rgba(255,255,255,.2);">
                {{ strtoupper(substr($vendor->shop_name, 0, 1)) }}
            </div>

            {{-- Info --}}
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                    <h1 style="font-size:1.6rem;font-weight:800;color:#fff;margin:0;">
                        {{ $vendor->shop_name }}
                    </h1>
                    @if($vendor->approval_status === 'approved')
                        <span style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.4);color:#4ade80;font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.05em;">
                            ✓ VERIFIED SELLER
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap:16px;">
                    {{-- Rating --}}
                    @if($avgRating > 0)
                        <div style="display:flex;align-items:center;gap:5px;">
                            @for($s = 1; $s <= 5; $s++)
                                <i class="fas fa-star{{ $s > $avgRating ? ($s - 0.5 <= $avgRating ? '-half-alt' : ' far fa-star') : '' }}"
                                   style="color:#f59e0b;font-size:.85rem;"></i>
                            @endfor
                            <span style="color:#fff;font-weight:700;font-size:.9rem;margin-left:4px;">{{ number_format($avgRating, 1) }}</span>
                            <span style="color:rgba(255,255,255,.5);font-size:.8rem;">({{ $reviewCount }} reviews)</span>
                        </div>
                    @endif

                    {{-- Product count --}}
                    <span style="color:rgba(255,255,255,.6);font-size:.85rem;">
                        <i class="fas fa-box-open" style="margin-right:5px;"></i>{{ $products->total() }} Products
                    </span>

                    {{-- Member since --}}
                    <span style="color:rgba(255,255,255,.6);font-size:.85rem;">
                        <i class="fas fa-calendar-alt" style="margin-right:5px;"></i>Since {{ $vendor->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

            {{-- WhatsApp / Contact CTA --}}
            <a href="https://wa.me/7340753780?text=I+want+to+know+more+about+{{ urlencode($vendor->shop_name) }}"
               target="_blank"
               style="background:#25D366;color:#fff;padding:10px 20px;border-radius:8px;font-weight:700;font-size:.85rem;text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:8px;">
                <i class="fab fa-whatsapp" style="font-size:1.1rem;"></i> Ask About This Brand
            </a>
        </div>
    </div>
</div>

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div style="background:#f8f8f8;border-bottom:1px solid #eee;padding:10px 0;">
    <div class="container">
        <nav style="font-size:.8rem;color:#6b7280;">
            <a href="{{ url('/') }}" style="color:#6b7280;text-decoration:none;">Home</a>
            <span style="margin:0 8px;">›</span>
            <a href="{{ route('products.index') }}" style="color:#6b7280;text-decoration:none;">Shop</a>
            <span style="margin:0 8px;">›</span>
            <span style="color:#111;font-weight:600;">{{ $vendor->shop_name }}</span>
        </nav>
    </div>
</div>

{{-- ── Main content ────────────────────────────────────────────── --}}
<div class="container py-4">
    <div class="row">

        {{-- ── Sidebar ──────────────────────────────────────────── --}}
        <div class="col-lg-3 mb-4">

            {{-- Stats card --}}
            <div class="j-section mb-3" style="padding:16px;">
                <div class="j-section-title" style="font-size:.85rem;margin-bottom:12px;">Seller Stats</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:.82rem;color:#6b7280;">Products</span>
                        <span style="font-weight:700;font-size:.9rem;">{{ $products->total() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:.82rem;color:#6b7280;">Avg Rating</span>
                        <span style="font-weight:700;font-size:.9rem;color:#f59e0b;">
                            {{ $avgRating > 0 ? number_format($avgRating, 1) . ' ★' : 'New' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:.82rem;color:#6b7280;">Reviews</span>
                        <span style="font-weight:700;font-size:.9rem;">{{ $reviewCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:.82rem;color:#6b7280;">Status</span>
                        <span style="font-weight:700;font-size:.82rem;color:#16a34a;">
                            {{ ucfirst($vendor->approval_status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Filter by category --}}
            @if($categories->count() > 0)
            <div class="j-section" style="padding:16px;">
                <div class="j-section-title" style="font-size:.85rem;margin-bottom:12px;">Filter by Category</div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <a href="{{ route('brands.show', \Illuminate\Support\Str::slug($vendor->shop_name)) }}"
                       class="{{ !request('category') ? 'active' : '' }}"
                       style="font-size:.82rem;padding:5px 0;color:#374151;text-decoration:none;border-bottom:1px solid #f3f4f6;
                              {{ !request('category') ? 'color:var(--j-primary,#D19C97);font-weight:700;' : '' }}">
                        All Products <span style="color:#9ca3af;">({{ $products->total() }})</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('brands.show', \Illuminate\Support\Str::slug($vendor->shop_name)) }}?category={{ $cat->id }}"
                           style="font-size:.82rem;padding:5px 0;color:#374151;text-decoration:none;border-bottom:1px solid #f3f4f6;
                                  {{ request('category') == $cat->id ? 'color:var(--j-primary,#D19C97);font-weight:700;' : '' }}">
                            {{ $cat->name }} <span style="color:#9ca3af;">({{ $cat->products_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ── Products grid ────────────────────────────────────── --}}
        <div class="col-lg-9">

            {{-- Sort + count bar --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px;">
                <span style="font-size:.85rem;color:#6b7280;">
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                </span>
                <div>
                    <select class="form-control form-control-sm" style="font-size:.82rem;width:auto;"
                            onchange="window.location=this.value">
                        @php
                            $baseUrl = route('brands.show', \Illuminate\Support\Str::slug($vendor->shop_name));
                            $category = request('category') ? '&category='.request('category') : '';
                        @endphp
                        <option value="{{ $baseUrl }}?sort=latest{{ $category }}"  {{ request('sort','latest') == 'latest'    ? 'selected' : '' }}>Latest</option>
                        <option value="{{ $baseUrl }}?sort=price_asc{{ $category }}" {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="{{ $baseUrl }}?sort=price_desc{{ $category }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="{{ $baseUrl }}?sort=name_asc{{ $category }}" {{ request('sort') == 'name_asc'   ? 'selected' : '' }}>Name A–Z</option>
                    </select>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        @include('front.partials.product-card-grid', ['product' => $product])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open" style="font-size:3rem;color:#d1d5db;margin-bottom:16px;display:block;"></i>
                    <p style="color:#6b7280;font-size:1rem;">No products found from this seller.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Browse All Jeans</a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Vendor reviews section ──────────────────────────────── --}}
    @if($vendorReviews->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="j-section">
                <div class="j-section-title">
                    <i class="fas fa-star mr-2" style="color:var(--j-primary);"></i>
                    Customer Reviews for {{ $vendor->shop_name }}
                </div>
                <div class="row mt-3">
                    @foreach($vendorReviews as $review)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div style="background:#fafafa;border:1px solid #f3f4f6;border-radius:10px;padding:14px;">
                                <div class="d-flex align-items-center mb-2" style="gap:8px;">
                                    <div style="width:34px;height:34px;border-radius:50%;background:var(--j-primary,#D19C97);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:.85rem;flex-shrink:0;">
                                        {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:.85rem;color:#111;">{{ $review->user->name ?? 'Anonymous' }}</div>
                                        <div style="font-size:.72rem;color:#9ca3af;">{{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="ml-auto" style="color:#f59e0b;font-size:.8rem;">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="fas fa-star{{ $s > $review->rating ? ' text-muted' : '' }}" style="font-size:.7rem;"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p style="font-size:.82rem;color:#374151;margin:0;line-height:1.5;">{{ $review->review }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
