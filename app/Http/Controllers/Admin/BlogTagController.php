<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogTagController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $tags = BlogTag::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.blog_tags.index', compact('tags'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.blog_tags.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug',
            'is_active' => 'boolean',
        ]);

        BlogTag::create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.tags.index')->with('success', 'Blog tag created successfully');
    }

    public function edit(BlogTag $blog_tag)
    {
        $this->checkAdmin();
        return view('admin.blog_tags.edit', ['tag' => $blog_tag]);
    }

    public function update(Request $request, BlogTag $blog_tag)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug,' . $blog_tag->id,
            'is_active' => 'boolean',
        ]);

        $blog_tag->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.tags.index')->with('success', 'Blog tag updated successfully');
    }

    public function destroy(BlogTag $blog_tag)
    {
        $this->checkAdmin();
        $blog_tag->delete();
        return redirect()->route('admin.blog.tags.index')->with('success', 'Blog tag deleted successfully');
    }
}

