@php
    /**
     * Dynamic Page Header Banner
     *
     * Looks up an active `page_header` banner matching the current page $title key.
     * Falls back to a global banner (title IS NULL) if no specific one is found.
     * Falls back to a plain dark background if no banner exists at all.
     *
     * Admin: Banners > Add Banner > Type: page_header > Page Key = one of the keys below
     * Page keys: shop, product-detail, Shopping Cart, checkout, Order Confirmed,
     *            My Account, My Orders, My Wishlist, My Addresses, Contact Us, FAQs, etc.
     */
    $banner = \App\Models\Banner::where('type', 'page_header')
        ->where('is_active', true)
        ->where('title', $title)
        ->orderByDesc('sort_order')
        ->first();

    // Fall back to global (null title) banner if no page-specific one
    if (!$banner) {
        $banner = \App\Models\Banner::where('type', 'page_header')
            ->where('is_active', true)
            ->whereNull('title')
            ->orderByDesc('sort_order')
            ->first();
    }

    $hasBg       = $banner && $banner->image_url;
    $bgImageUrl  = $hasBg ? asset('storage/' . $banner->image_url) : null;
    $headingText = $banner->heading ?? $title ?? $breadcrumb ?? '';
    $linkUrl     = $banner->link_url ?? null;
@endphp

<div class="page-header-banner position-relative overflow-hidden mb-5"
     style="min-height: 280px; background-color: #1c1c2e;">

    {{-- Background image --}}
    @if($hasBg)
        <div class="page-header-bg"
             style="position:absolute;inset:0;
                    background: url('{{ $bgImageUrl }}') center center / cover no-repeat;
                    z-index: 0;">
        </div>
        {{-- Dark overlay for text readability --}}
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);z-index:1;"></div>
    @else
        {{-- Gradient fallback when no image is set --}}
        <div style="position:absolute;inset:0;
                    background: linear-gradient(135deg, #1c1c2e 0%, #2d3561 100%);
                    z-index:0;">
        </div>
    @endif

    {{-- Content --}}
    <div class="position-relative d-flex flex-column align-items-center justify-content-center text-center"
         style="min-height: 280px; z-index: 2; padding: 40px 20px;">

        <h1 class="font-weight-bold text-white text-uppercase mb-3"
            style="font-size: clamp(1.5rem, 4vw, 2.5rem); letter-spacing: 2px; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
            {{ $headingText }}
        </h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent p-0 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="text-white-50" style="text-decoration:none;">Home</a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    {{ $breadcrumb ?? $title }}
                </li>
            </ol>
        </nav>

        {{-- Optional CTA if banner has a link --}}
        @if($linkUrl && $hasBg)
            <a href="{{ $linkUrl }}" class="btn btn-outline-light btn-sm mt-3 px-4" style="border-radius:20px;">
                Shop Now <i class="fa fa-arrow-right ml-1"></i>
            </a>
        @endif
    </div>
</div>

<style>
.page-header-banner .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
    content: "›";
}
</style>
