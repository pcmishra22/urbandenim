@php
    // $showCategories — set false on pages that have their own left sidebar
    // (profile pages, cart, checkout). Defaults true for shop/product/blog/contact.
    $showCategories = $showCategories ?? true;

    $banner = \App\Models\Banner::where('type', 'page_header')
        ->where('is_active', true)
        ->where('title', $title)
        ->orderByDesc('sort_order')
        ->first();
    if (!$banner) {
        $banner = \App\Models\Banner::where('type', 'page_header')
            ->where('is_active', true)
            ->whereNull('title')
            ->orderByDesc('sort_order')
            ->first();
    }
    $hasBg       = $banner && $banner->image_url;
    $bgImageUrl  = $hasBg ? asset('storage/' . $banner->image_url) : null;
    $headingText = $banner->heading ?? $title ?? '';
    $linkUrl     = $banner->link_url ?? null;
    $navCategories = \App\Models\Category::where('is_active', true)->take(12)->get();

    // Banner height: taller when showing alongside category sidebar (3+9),
    // shorter when full-width (no sidebar).
    $bannerHeight = $showCategories ? '410px' : '200px';
@endphp

<div class="container-fluid mb-0">
    <div class="row border-top px-xl-5">

        {{-- Left: Category sidebar — only when $showCategories is true --}}
        @if($showCategories)
        <div class="col-lg-3 d-none d-lg-block">
            <a class="btn shadow-none d-flex align-items-center justify-content-between bg-primary text-white w-100"
               data-toggle="collapse" href="#navbar-vertical-banner"
               style="height:65px;margin-top:-1px;padding:0 30px;">
                <h6 class="m-0">Categories</h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>
            <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 border border-top-0 border-bottom-0 bg-light"
                 id="navbar-vertical-banner"
                 style="width:calc(100% - 30px);z-index:999;">
                <div class="navbar-nav w-100 overflow-hidden" style="height:{{ $bannerHeight }};">
                    @forelse($navCategories as $cat)
                        <a href="{{ ($cat->slug ? url('/'. $cat->slug) : route('products.index', ['category' => $cat->id])) }}"
                           class="nav-item nav-link {{ request('category') == $cat->id ? 'active' : '' }}">
                            {{ $cat->name }}
                        </a>
                    @empty
                        <a href="{{ route('products.index') }}" class="nav-item nav-link">All Products</a>
                    @endforelse
                </div>
            </nav>
        </div>
        @endif

        {{-- Right: Page banner --}}
        <div class="{{ $showCategories ? 'col-lg-9' : 'col-12' }}">
            <div class="position-relative overflow-hidden"
                 style="min-height:{{ $bannerHeight }};{{ $showCategories ? 'margin-left:-15px;margin-right:-15px;' : '' }}">

                @if($hasBg)
                    <div style="position:absolute;inset:0;background:url('{{ $bgImageUrl }}') center/cover no-repeat;z-index:0;"></div>
                    <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);z-index:1;"></div>
                @else
                    <div style="position:absolute;inset:0;background:var(--j-primary, #D19C97);opacity:0.95;z-index:0;"></div>
                @endif

                <div class="d-flex flex-column align-items-center justify-content-center text-center"
                     style="min-height:{{ $bannerHeight }};position:relative;z-index:2;padding:32px 30px;">
                    <h1 class="text-white text-uppercase font-weight-bold mb-3"
                        style="font-size:clamp(1.4rem,3vw,2.2rem);letter-spacing:3px;text-shadow:0 3px 12px rgba(0,0,0,.5);">
                        {{ $headingText }}
                    </h1>
                    <div class="d-inline-flex align-items-center" style="gap:8px;">
                        <a href="{{ url('/') }}" class="text-white-50" style="text-decoration:none;font-size:.9rem;">Home</a>
                        <span class="text-white-50">›</span>
                        <span class="text-white" style="font-size:.9rem;">{{ $breadcrumb ?? $title }}</span>
                    </div>
                    @if($linkUrl && $hasBg)
                        <a href="{{ $linkUrl }}" class="btn btn-outline-light btn-sm mt-3 px-5" style="border-radius:25px;">
                            Shop Now &nbsp;<i class="fa fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
