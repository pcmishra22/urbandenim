@php
    $pageBanner = \App\Models\Banner::where('type', 'page_header')
        ->where('is_active', true)
        ->where(function($q) use ($title) {
            $q->where('title', $title)->orWhereNull('title');
        })
        ->orderByDesc('id')
        ->first();

    $hasBg  = $pageBanner && $pageBanner->image_url;
    $bgCss  = $hasBg ? 'background:url('.asset('storage/'.$pageBanner->image_url).') center/cover no-repeat;position:relative;' : '';
    $txtCls = $hasBg ? 'text-white' : '';
    $lnkCls = $hasBg ? 'text-white' : '';
@endphp

<div class="container-fluid bg-secondary mb-5" style="min-height:300px;{{ $bgCss }}">
    @if($hasBg)<div style="position:absolute;inset:0;background:rgba(0,0,0,.45);z-index:0;"></div>@endif
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:300px;position:relative;z-index:1;">
        <h1 class="font-weight-semi-bold text-uppercase mb-3 {{ $txtCls }}">
            {{ $pageBanner->heading ?? $title }}
        </h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{ url('/') }}" class="{{ $lnkCls }}">Home</a></p>
            <p class="m-0 px-2 {{ $txtCls }}">-</p>
            <p class="m-0 {{ $txtCls }}">{{ $breadcrumb ?? $title }}</p>
        </div>
    </div>
</div>
