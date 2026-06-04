<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:categories',
            'slug'             => 'nullable|string|unique:categories',
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'        => 'boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url'    => 'nullable|url|max:255',
        ]);

        // Create the category first (without image) to get the ID
        unset($validated['image']);
        $category = Category::create($validated);

        // Now handle image upload using the category ID
        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store(
                'categories/' . $category->id . '/images',
                'public'
            );
            $category->update(['image_url' => $stored]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['children', 'products']);
        $products = $category->products;
        $children = $category->children;
        return view('admin.categories.show', compact('category', 'products', 'children'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug'             => 'nullable|string|unique:categories,slug,' . $category->id,
            'description'      => 'nullable|string',
            'parent_id'        => 'nullable|exists:categories,id',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active'        => 'boolean',
            'sort_order'       => 'nullable|integer',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url'    => 'nullable|url|max:255',
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($category->image_url) {
                Storage::disk('public')->delete($category->image_url);
            }
            $stored = $request->file('image')->store(
                'categories/' . $category->id . '/images',
                'public'
            );
            $validated['image_url'] = $stored;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Delete category image folder
        Storage::disk('public')->deleteDirectory('categories/' . $category->id);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
