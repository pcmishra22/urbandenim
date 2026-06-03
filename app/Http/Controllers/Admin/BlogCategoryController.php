<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
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
        $categories = BlogCategory::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.blog_categories.index', compact('categories'));
    }

    public function create()
    {
        $this->checkAdmin();
        return view('admin.blog_categories.create');
    }

    public function store(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:2048',
            'is_active' => 'boolean',
        ]);

        BlogCategory::create([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'slug' => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.categories.index')->with('success', 'Blog category created successfully');
    }

    public function edit(BlogCategory $blog_category)
    {
        $this->checkAdmin();
        return view('admin.blog_categories.edit', ['category' => $blog_category]);
    }

    public function update(Request $request, BlogCategory $blog_category)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $blog_category->id,
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|max:2048',
            'is_active' => 'boolean',
        ]);

        $blog_category->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'slug' => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.blog.categories.index')->with('success', 'Blog category updated successfully');
    }

    public function destroy(BlogCategory $blog_category)
    {
        $this->checkAdmin();
        $blog_category->delete();
        return redirect()->route('admin.blog.categories.index')->with('success', 'Blog category deleted successfully');
    }
}

