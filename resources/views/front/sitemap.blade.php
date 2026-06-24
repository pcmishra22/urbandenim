<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    {{-- ── Homepage ── --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- ── Shop / Products ── --}}
    <url>
        <loc>{{ route('products.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.95</priority>
    </url>

    {{-- ── Category / Fit pages ── --}}
    @foreach(['Slim Fit','Straight Fit','Regular Fit','Wide Leg','Bootcut','Skinny'] as $fit)
    <url>
        <loc>{{ route('products.index', ['category_name' => $fit]) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    @endforeach

    {{-- ── Dynamic category pages ── --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ route('products.category', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>
    @endforeach

    {{-- ── Blog ── --}}
    <url>
        <loc>{{ route('blog.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.70</priority>
    </url>

    {{-- ── About / FAQ / Help / Contact ── --}}
    <url>
        <loc>{{ route('about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.55</priority>
    </url>
    <url>
        <loc>{{ route('faq') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>
    <url>
        <loc>{{ route('help') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.60</priority>
    </url>

    {{-- ── Legal / Policy pages ── --}}
    <url>
        <loc>{{ route('legal.terms') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.30</priority>
    </url>
    <url>
        <loc>{{ route('legal.privacy') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.30</priority>
    </url>
    <url>
        <loc>{{ route('legal.refund') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.35</priority>
    </url>
    <url>
        <loc>{{ route('legal.shipping') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.35</priority>
    </url>
    <url>
        <loc>{{ route('legal.cancellation') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.30</priority>
    </url>

    {{-- ── Product pages (with image tags) ── --}}
    @foreach($products as $product)
    @php
        $img = $product->images->first();
        $imgUrl = $img ? asset('storage/products/'.$product->id.'/images/'.($img->image ?? '')) : null;
    @endphp
    <url>
        <loc>{{ route('products.detail', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
        @if($imgUrl)
        <image:image>
            <image:loc>{{ $imgUrl }}</image:loc>
            <image:title>{{ $product->name }}</image:title>
            <image:caption>{{ Str::limit($product->name, 100) }} — Jeanzo</image:caption>
        </image:image>
        @endif
    </url>
    @endforeach

    {{-- ── Blog posts ── --}}
    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.60</priority>
    </url>
    @endforeach

</urlset>
