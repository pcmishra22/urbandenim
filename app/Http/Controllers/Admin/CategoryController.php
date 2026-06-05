<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:categories',
            'slug'             => 'nullable|string|unique:categories,slug',
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url'    => 'nullable|url|max:255',
        ]);

        // Auto-generate slug if empty
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Create category first (without image) to get ID
        unset($validated['image']);
        $category = Category::create($validated);

        // Store image now that we have the ID — folder created dynamically
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store(
                'categories/' . $category->id . '/images', 'public'
            );
            $category->update(['image_url' => $stored]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->load(['children', 'products']);
        return view('admin.categories.show', compact('category',
            'products', 'children'))->with([
            'products' => $category->products,
            'children' => $category->children,
        ]);
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug'             => 'nullable|string|unique:categories,slug,' . $category->id,
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url'    => 'nullable|url|max:255',
        ]);

        $validated['slug']      = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image']);

        // Handle remove image checkbox
        if ($request->boolean('remove_image') && $category->image_url) {
            Storage::disk('public')->delete($category->image_url);
            $validated['image_url'] = null;
        }

        // New image upload replaces existing
        if ($request->hasFile('image')) {
            if ($category->image_url) {
                Storage::disk('public')->delete($category->image_url);
            }
            $stored = $request->file('image')->store(
                'categories/' . $category->id . '/images', 'public'
            );
            $validated['image_url'] = $stored;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        Storage::disk('public')->deleteDirectory('categories/' . $category->id);
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
