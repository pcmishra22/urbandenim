<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::query()->latest();

        // Filter by category slug if provided
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag slug if provided
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search by title or content
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        $posts = $query->with('category')->where('status', 'published')->paginate(6);
        $categories = BlogCategory::withCount('posts')->get();
        $tags = BlogTag::all();
        $recentPosts = BlogPost::with('category')->where('status', 'published')->latest()->take(3)->get();

        return view('front.blog', compact('posts', 'categories', 'tags', 'recentPosts'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $post = BlogPost::with('category')->where('slug', $slug)->where('status', 'published')->firstOrFail();
        $categories = BlogCategory::withCount('posts')->get();
        $tags = BlogTag::all();
        $recentPosts = BlogPost::with('category')->where('id', '!=', $post->id)->where('status', 'published')->latest()->take(3)->get();

        return view('front.blog-detail', compact('post', 'categories', 'tags', 'recentPosts'));
    }
}