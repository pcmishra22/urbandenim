@extends('layouts.eshopper')

@section('title', 'Blog - EShopper')

@section('content')

    <!-- Page Header Start -->
    <div class="container-fluid bg-secondary mb-5">
        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 300px">
            <h1 class="font-weight-semi-bold text-uppercase mb-3">Blog Detail</h1>
            <div class="d-inline-flex">
                <p class="m-0"><a href="{{ url('/') }}">Home</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0"><a href="/blog">Blog</a></p>
                <p class="m-0 px-2">-</p>
                <p class="m-0">Detail</p>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Blog Detail Start -->
    <div class="container-fluid pt-5">
        <div class="row px-xl-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                @php
                    $slug = request()->segment(2);
                    $post = null;
                    try { $post = \App\Models\BlogPost::where('slug', $slug)->where('status','published')->with('author')->first(); } catch(\Exception $e) {}
                @endphp

                @if($post)
                    @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid w-100 mb-4" alt="{{ $post->title }}">
                    @else
                    <img src="{{ asset('eshopper/img/blog-1.jpg') }}" class="img-fluid w-100 mb-4" alt="">
                    @endif
                    <div class="d-flex mb-3">
                        <small class="text-body mr-3"><i class="fa fa-calendar-alt text-primary mr-2"></i>{{ ($post->published_at ?? $post->created_at)->format('d M Y') }}</small>
                        @if($post->author)<small class="text-body mr-3"><i class="fa fa-user text-primary mr-2"></i>{{ $post->author }}</small>@endif
                    </div>
                    <h4 class="font-weight-semi-bold mb-3">{{ $post->title }}</h4>
                    <div class="mb-4">{!! $post->content !!}</div>
                @else
                    <img src="{{ asset('eshopper/img/blog-1.jpg') }}" class="img-fluid w-100 mb-4" alt="">
                    <div class="d-flex mb-3">
                        <small class="text-body mr-3"><i class="fa fa-calendar-alt text-primary mr-2"></i>{{ now()->format('d M Y') }}</small>
                        <small class="text-body mr-3"><i class="fa fa-user text-primary mr-2"></i>Admin</small>
                        <small class="text-body"><i class="fa fa-tag text-primary mr-2"></i>Fashion</small>
                    </div>
                    <h4 class="font-weight-semi-bold mb-3">Latest Fashion Trends You Should Know About</h4>
                    <p>Discover the hottest fashion trends this season. From bold colours to classic minimalism, we cover the styles you need to know to stay ahead of the curve.</p>
                    <p>Fashion is ever-evolving, and keeping up can feel like a full-time job. Our editors have curated the top looks from the latest runway shows and street styles to bring you the ultimate guide to dressing your best this year.</p>
                @endif

                <!-- Tags -->
                <div class="mt-4 mb-5">
                    <span class="font-weight-semi-bold mr-2">Tags:</span>
                    <a href="/blog" class="badge badge-secondary mr-1">Fashion</a>
                    <a href="/blog" class="badge badge-secondary mr-1">Style</a>
                    <a href="/blog" class="badge badge-secondary mr-1">Trends</a>
                </div>

                <!-- Comment Form -->
                <div class="mb-5">
                    <h4 class="font-weight-semi-bold mb-4">Leave a Comment</h4>
                    <form>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Your Name</label>
                                <input type="text" class="form-control" placeholder="Your name">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Your Email</label>
                                <input type="email" class="form-control" placeholder="Your email">
                            </div>
                            <div class="col-12 form-group">
                                <label>Comment</label>
                                <textarea class="form-control" rows="5" placeholder="Your comment..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">Post Comment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="mb-5">
                    <form action="/blog" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search blog posts">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mb-5">
                    <h5 class="font-weight-semi-bold border-bottom pb-2 mb-4">Recent Posts</h5>
                    @foreach(range(1,4) as $i)
                    <div class="d-flex mb-3">
                        <img src="{{ asset('eshopper/img/blog-' . $i . '.jpg') }}" class="img-fluid mr-3" style="width:80px;height:60px;object-fit:cover;" alt="">
                        <div>
                            <a href="/blog/post-{{ $i }}" class="text-dark d-block font-weight-semi-bold mb-1">Latest Blog Post {{ $i }}</a>
                            <small class="text-muted">{{ now()->subDays($i)->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mb-5">
                    <h5 class="font-weight-semi-bold border-bottom pb-2 mb-4">Tags</h5>
                    <div class="d-flex flex-wrap">
                        @foreach(['Fashion','Style','Electronics','Review','Trends','Lifestyle','Summer','Winter'] as $tag)
                        <a href="/blog?tag={{ strtolower($tag) }}" class="btn btn-outline-secondary btn-sm mr-2 mb-2">{{ $tag }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Blog Detail End -->

@endsection
