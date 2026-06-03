<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::with('children')->where('parent_id', null)->paginate(15);
        $allCategories = Category::all();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $categories = Category::where('parent_id', null)->get();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image_url' => 'nullable|url',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing a category
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->where('parent_id', null)->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the category in database
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id . '|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category
     */
    public function destroy(Category $category)
    {
        $childCount = $category->children()->count();

        if ($childCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with subcategories. Delete subcategories first.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
