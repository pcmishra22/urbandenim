@php
    $pageBanner = \App\Models\Banner::where('type', 'page_header')
        ->where('is_active', true)
        ->where(function($q) use ($title) {
            $q->where('title', $title)->orWhereNull('title');
        })
        ->orderByDesc('sort_order')
        ->first();

    $bgStyle = $pageBanner && $pageBanner->image_url
        ? 'background: url(' . asset('storage/' . $pageBanner->image_url) . ') center/cover no-repeat; position:relative;'
        : '';

    $overlayStyle = $pageBanner && $pageBanner->image_url
        ? 'position:absolute;inset:0;background:rgba(0,0,0,0.45);'
        : '';
@endphp

<div class="container-fluid mb-5" style="min-height:300px; display:flex; align-items:center; justify-content:center; {{ $bgStyle ?: 'background:#dee2e6;' }}">
    @if($bgStyle)<div style="{{ $overlayStyle }}"></div>@endif
    <div class="d-flex flex-column align-items-center justify-content-center w-100" style="min-height:300px; position:relative; z-index:1;">
        <h1 class="font-weight-semi-bold text-uppercase mb-3 {{ $pageBanner && $pageBanner->image_url ? 'text-white' : '' }}">
            {{ $pageBanner->heading ?? $title }}
        </h1>
        <div class="d-inline-flex">
            <p class="m-0 {{ $pageBanner && $pageBanner->image_url ? 'text-white' : '' }}">
                <a href="{{ url('/') }}" class="{{ $pageBanner && $pageBanner->image_url ? 'text-white' : '' }}">Home</a>
            </p>
            <p class="m-0 px-2 {{ $pageBanner && $pageBanner->image_url ? 'text-white' : '' }}">-</p>
            <p class="m-0 {{ $pageBanner && $pageBanner->image_url ? 'text-white' : '' }}">{{ $breadcrumb ?? $title }}</p>
        </div>
    </div>
</div>
