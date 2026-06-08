@php
    // Fetch up to 4 active page header banners for the given page key ($title)
    $pageBanners = \App\Models\Banner::where('type', 'page_header')
        ->where('is_active', true)
        ->where(function($q) use ($title) {
            // Match current page key, but also allow global banners where title is null
            $q->where('title', $title)->orWhereNull('title');
        })
        ->orderByDesc('sort_order')
        ->orderByDesc('id')
        ->take(4)
        ->get();

    $hasAny = $pageBanners->isNotEmpty();

    // Use the first banner (if available) for title/breadcrumb styling
    $pageBannerForText = $pageBanners->first();
    $hasBg  = $pageBannerForText && $pageBannerForText->image_url;
    $bgCss  = $hasBg ? 'background:url('.asset('storage/'.$pageBannerForText->image_url).') center/cover no-repeat;position:relative;' : '';
    $txtCls = $hasBg ? 'text-white' : '';
    $lnkCls = $hasBg ? 'text-white' : '';
@endphp

<div class="container-fluid bg-secondary mb-5" style="min-height:300px;{{ $bgCss }}">
    {{-- Overlay when using background text styling --}}
    @if($hasBg)
        <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);z-index:0;"></div>
    @endif

    {{-- 4-banner grid --}}
    @if($hasAny)
        <div class="row g-3" style="position:relative;z-index:1; padding: 18px;">
            @foreach($pageBanners as $b)
                <div class="col-12 col-md-6">
                    <div class="h-100 overflow-hidden" style="border-radius: 6px; background: rgba(0,0,0,.05);">
                        <a href="{{ $b->link_url ?: url('/') }}" target="_blank" rel="noopener" class="d-block h-100">
                            <img
                                src="{{ $b->image }}"
                                alt="{{ $b->title ?? 'banner' }}"
                                class="w-100"
                                style="height: 170px; object-fit: cover;"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            @endforeach

            {{-- If fewer than 4 exist, keep layout consistent with empty cells --}}
            @for($i = $pageBanners->count(); $i < 4; $i++)
                <div class="col-12 col-md-6"></div>
            @endfor
        </div>
    @endif

    {{-- Page heading + breadcrumb (keeps existing UX) --}}
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:300px;position:relative;z-index:2; {{ $hasAny ? 'margin-top:-150px;' : '' }}">
        <h1 class="font-weight-semi-bold text-uppercase mb-3 {{ $txtCls }}">
            {{ $pageBannerForText->heading ?? $title }}
        </h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}" class="{{ $lnkCls }}">Home</a></p>
            <p class="m-0 px-2 {{ $txtCls }}">-</p>
            <p class="m-0 {{ $txtCls }}">{{ $breadcrumb ?? $title }}</p>
        </div>
    </div>
</div>

