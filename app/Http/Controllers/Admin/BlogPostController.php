<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = BlogPost::with(['category', 'tags'])->orderBy('created_at', 'desc');

        // Featured view
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // SEO view (meta fields)
        if ($request->boolean('seo')) {
            $query->where(function ($q) {
                $q->whereNotNull('meta_title')
                    ->orWhereNotNull('meta_description')
                    ->orWhereNotNull('canonical_url');
            });

            $posts = $query->paginate(20);
            return view('admin.blog_posts.seo-index', compact('posts'));
        }

        // Published view
        if ($request->boolean('published')) {
            $query->where('status', 'published');
        }

        $posts = $query->paginate(20);
        return view('admin.blog_posts.index', compact('posts'));
    }


    public function create()
    {
        $this->checkAdmin();
        return $this->editBase(new BlogPost());
    }

    private function editBase(BlogPost $post)
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        $tags = BlogTag::where('is_active', true)->orderBy('name')->get();
        $allPosts = BlogPost::orderBy('created_at', 'desc')->get();

        return view('admin.blog_posts.create', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'allPosts' => $allPosts,
        ]);
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:2048',
            'blog_category_id' => 'nullable|exists:blog_categories,id',

            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',

            // SEO (VERY IMPORTANT FOR SEO)
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:2048',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',

            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|exists:blog_tags,id',

            'related_post_ids' => 'array',
            'related_post_ids.*' => 'integer|exists:blog_posts,id',
        ]);

        $post = BlogPost::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'featured_image_url' => $validated['featured_image_url'] ?? null,
            'blog_category_id' => $validated['blog_category_id'] ?? null,

            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null),

            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
        ]);

        $tagIds = $request->input('tag_ids', []);
        $post->tags()->sync($tagIds);

        $relatedIds = $request->input('related_post_ids', []);
        $sync = array_values(array_filter(array_unique($relatedIds), fn ($id) => (int)$id !== $post->id));
        $post->relatedPosts()->sync($sync);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Blog post created successfully');
    }

    public function edit(BlogPost $blog_post)
    {
        $this->checkAdmin();
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        $tags = BlogTag::where('is_active', true)->orderBy('name')->get();
        $allPosts = BlogPost::where('id', '!=', $blog_post->id)->orderBy('created_at', 'desc')->get();

        return view('admin.blog_posts.edit', [
            'post' => $blog_post,
            'categories' => $categories,
            'tags' => $tags,
            'allPosts' => $allPosts,
            'selectedTagIds' => $blog_post->tags()->pluck('blog_tags.id')->toArray(),
            'selectedRelatedIds' => $blog_post->relatedPosts()->pluck('blog_posts.id')->toArray(),
        ]);
    }

    public function update(Request $request, BlogPost $blog_post)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blog_post->id,
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image_url' => 'nullable|string|max:2048',
            'blog_category_id' => 'nullable|exists:blog_categories,id',

            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:2048',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',

            'tag_ids' => 'array',
            'tag_ids.*' => 'integer|exists:blog_tags,id',

            'related_post_ids' => 'array',
            'related_post_ids.*' => 'integer|exists:blog_posts,id',
        ]);

        $blog_post->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'featured_image_url' => $validated['featured_image_url'] ?? null,
            'blog_category_id' => $validated['blog_category_id'] ?? null,

            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null),

            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
        ]);

        $tagIds = $request->input('tag_ids', []);
        $blog_post->tags()->sync($tagIds);

        $relatedIds = $request->input('related_post_ids', []);
        $sync = array_values(array_filter(array_unique($relatedIds), fn ($id) => (int)$id !== $blog_post->id));
        $blog_post->relatedPosts()->sync($sync);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Blog post updated successfully');
    }

    public function destroy(BlogPost $blog_post)
    {
        $this->checkAdmin();
        $blog_post->delete();
        return redirect()->route('admin.blog.posts.index')->with('success', 'Blog post deleted successfully');
    }
}

