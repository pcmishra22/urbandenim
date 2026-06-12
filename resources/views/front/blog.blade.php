@extends('layouts.eshopper')
@section('title', 'Blog - Jeanzo')

@section('content')
@include('front.partials.design-system')

<div class="container-fluid pb-5" style="background:#faf8f8;padding-top:28px;">
    <div class="row px-xl-5">

        {{-- Blog Posts --}}
        <div class="col-lg-8 mb-5">

            {{-- Page heading --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-weight-bold mb-1" style="color:#2d2d2d;">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('slug', request('category'))->name ?? 'Category' }}
                        @else
                            Latest Posts
                        @endif
                    </h3>
                    <div class="d-flex align-items-center" style="gap:6px;font-size:.85rem;">
                        <a href="{{ url('/') }}" class="text-muted" style="text-decoration:none;">Home</a>
                        <span class="text-muted">›</span>
                        <span style="color:var(--j-primary);">Blog</span>
                    </div>
                </div>
                <span class="j-badge" style="background:var(--j-primary-lt);color:var(--j-primary);">
                    {{ $posts->total() }} posts
                </span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
            @endif

            @forelse($posts as $post)
            <div class="j-section mb-4 p-0 overflow-hidden" style="display:flex;flex-direction:row;">
                {{-- Thumbnail --}}
                <div style="flex-shrink:0;width:220px;height:180px;overflow:hidden;" class="d-none d-md-block">
                    <img class="w-100 h-100" style="object-fit:cover;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
                         src="{{ $post->featured_image ? asset('storage/'.$post->featured_image) : asset('eshopper/img/blog-1.jpg') }}"
                         alt="{{ $post->title }}">
                </div>
                {{-- Content --}}
                <div style="padding:20px 22px;flex:1;">
                    {{-- Meta --}}
                    <div class="d-flex align-items-center flex-wrap mb-2" style="gap:12px;">
                        <span class="small text-muted">
                            <i class="fa fa-calendar-alt mr-1" style="color:var(--j-primary);"></i>
                            {{ ($post->published_at ?? $post->created_at)->format('d M Y') }}
                        </span>
                        @if($post->author)
                        <span class="small text-muted">
                            <i class="fa fa-user mr-1" style="color:var(--j-primary);"></i>{{ $post->author }}
                        </span>
                        @endif
                        @if($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                           class="j-badge" style="background:var(--j-primary-lt);color:var(--j-primary);text-decoration:none;font-size:.72rem;">
                            {{ $post->category->name }}
                        </a>
                        @endif
                    </div>
                    {{-- Title --}}
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="d-block font-weight-bold text-dark mb-2"
                       style="font-size:1.05rem;line-height:1.4;text-decoration:none;"
                       onmouseover="this.style.color='var(--j-primary)'" onmouseout="this.style.color='#2d2d2d'">
                        {{ $post->title }}
                    </a>
                    {{-- Excerpt --}}
                    <p class="text-muted mb-3" style="font-size:.88rem;line-height:1.6;">
                        {{ Str::limit(strip_tags($post->content ?? $post->excerpt ?? ''), 130) }}
                    </p>
                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary btn-sm px-4" style="border-radius:20px;">
                        Read More <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="j-section text-center py-5">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--j-primary-lt);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="fa fa-newspaper fa-2x" style="color:var(--j-primary);"></i>
                </div>
                <h5 class="text-muted mb-2">No posts found</h5>
                @if(request('search') || request('category'))
                    <a href="{{ route('blog.index') }}" class="btn btn-primary px-4">View All Posts</a>
                @endif
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($posts->hasPages())
            <nav class="mt-4">
                <ul class="pagination">
                    <li class="page-item {{ $posts->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $posts->previousPageUrl() }}">‹ Prev</a>
                    </li>
                    @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                    <li class="page-item {{ $posts->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}"
                           style="{{ $posts->currentPage() == $page ? 'background:var(--j-primary);border-color:var(--j-primary);' : '' }}">{{ $page }}</a>
                    </li>
                    @endforeach
                    <li class="page-item {{ $posts->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $posts->nextPageUrl() }}">Next ›</a>
                    </li>
                </ul>
            </nav>
            @endif
        </div>

        {{-- Blog Sidebar --}}
        <div class="col-lg-4 mb-5">

            {{-- Search --}}
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-search mr-2" style="color:var(--j-primary);"></i>Search</div>
                <form action="{{ route('blog.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search posts…" value="{{ request('search') }}" style="border-radius:8px 0 0 8px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" style="border-radius:0 8px 8px 0;"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Recent Posts --}}
            @if($recentPosts->isNotEmpty())
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-clock mr-2" style="color:var(--j-primary);"></i>Recent Posts</div>
                @foreach($recentPosts as $recent)
                <div class="d-flex align-items-center gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                    <div style="flex-shrink:0;width:64px;height:52px;overflow:hidden;border-radius:6px;">
                        <img src="{{ $recent->featured_image ? asset('storage/'.$recent->featured_image) : asset('eshopper/img/blog-1.jpg') }}"
                             class="w-100 h-100" style="object-fit:cover;" alt="">
                    </div>
                    <div>
                        <a href="{{ route('blog.show', $recent->slug) }}"
                           class="text-dark font-weight-600 d-block" style="font-size:.88rem;line-height:1.3;text-decoration:none;">
                            {{ Str::limit($recent->title, 50) }}
                        </a>
                        <small class="text-muted">{{ ($recent->published_at ?? $recent->created_at)->format('d M Y') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="j-section mb-3">
                <div class="j-section-title"><i class="fa fa-folder mr-2" style="color:var(--j-primary);"></i>Categories</div>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                   class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}"
                   style="text-decoration:none;color:{{ request('category') === $cat->slug ? 'var(--j-primary)' : '#2d2d2d' }};">
                    <span style="font-size:.9rem;">{{ $cat->name }}</span>
                    <span class="j-badge" style="background:var(--j-primary-lt);color:var(--j-primary);font-size:.72rem;">{{ $cat->posts_count }}</span>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Tags --}}
            @if(isset($tags) && $tags->isNotEmpty())
            <div class="j-section">
                <div class="j-section-title"><i class="fa fa-tags mr-2" style="color:var(--j-primary);"></i>Tags</div>
                <div class="d-flex flex-wrap" style="gap:8px;">
                    @foreach($tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                       class="j-badge"
                       style="background:{{ request('tag') === $tag->slug ? 'var(--j-primary)' : 'var(--j-primary-lt)' }};color:{{ request('tag') === $tag->slug ? '#fff' : 'var(--j-primary)' }};text-decoration:none;padding:5px 12px;">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@endsection
