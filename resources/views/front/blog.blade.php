@extends('layouts.eshopper')

@section('title', 'Blog - Jeanzo')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Our Blog</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Blog</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Blog Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                @forelse($posts as $post)
                <div class="d-flex mb-5">
                    <div style="min-width: 250px; max-width: 250px; height: 180px; overflow:hidden;" class="mr-4">
                        @if($post->featured_image)
                            <img class="img-fluid w-100 h-100" style="object-fit:cover;" src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                        @else
                            <img class="img-fluid w-100 h-100" style="object-fit:cover;" src="{{ asset('eshopper/img/blog-1.jpg') }}" alt="{{ $post->title }}">
                        @endif
                    </div>
                    <div>
                        <div class="d-flex mb-2">
                            <small class="text-body mr-3"><i class="fa fa-calendar-alt text-primary mr-1"></i>{{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</small>
                            @if($post->author)<small class="text-body"><i class="fa fa-user text-primary mr-1"></i>{{ $post->author }}</small>@endif
                        </div>
                        <a href="{{ route('blog.show', $post->slug) }}" class="h5 d-block mb-2 text-dark font-weight-semi-bold">{{ $post->title }}</a>
                        <p class="mb-3">{{ Str::limit(strip_tags($post->content ?? $post->excerpt ?? ''), 150) }}</p>
                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-primary py-1">Read More</a>
                    </div>
                </div>
                @empty
                    <div class="text-center py-5">
                        <h4 class="text-muted">No blog posts found.</h4>
                    </div>
                @endforelse

                @if($posts->hasPages())
                <nav class="mt-2">
                    <ul class="pagination">
                        <li class="page-item {{ $posts->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $posts->previousPageUrl() }}">Previous</a>
                        </li>
                        @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                        <li class="page-item {{ $posts->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                        @endforeach
                        <li class="page-item {{ $posts->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $posts->nextPageUrl() }}">Next</a>
                        </li>
                    </ul>
                </nav>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="mb-5">
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search blog posts" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Recent Posts -->
                <div class="mb-5">
                    <h5 class="font-weight-semi-bold border-bottom pb-2 mb-4">Recent Posts</h5>
                    @foreach($recentPosts as $recent)
                    <div class="d-flex mb-3">
                        @if($recent->featured_image)
                            <img src="{{ asset('storage/' . $recent->featured_image) }}" class="img-fluid mr-3" style="width:80px;height:60px;object-fit:cover;" alt="">
                        @else
                            <img src="{{ asset('eshopper/img/blog-1.jpg') }}" class="img-fluid mr-3" style="width:80px;height:60px;object-fit:cover;" alt="">
                        @endif
                        <div>
                            <a href="{{ route('blog.show', $recent->slug) }}" class="text-dark d-block font-weight-semi-bold mb-1">{{ $recent->title }}</a>
                            <small class="text-muted">{{ ($recent->published_at ?? $recent->created_at)->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Categories -->
                <div class="mb-5">
                    <h5 class="font-weight-semi-bold border-bottom pb-2 mb-4">Categories</h5>
                    @forelse($categories as $cat)
                    <div class="d-flex justify-content-between mb-2">
                        <a class="text-dark" href="{{ route('blog.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                        <span class="badge badge-primary">{{ $cat->posts_count }}</span>
                    </div>
                    @empty
                        <p class="text-muted small">No categories found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Blog End -->

@endsection
