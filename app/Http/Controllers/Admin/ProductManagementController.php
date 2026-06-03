<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of products
     */
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::all();
        $brands = \App\Models\Brand::all();

        return view('admin.products.create', compact('categories','brands'));
    }

    /**
     * Store a newly created product in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|unique:products|max:100',
            'fit_type' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'stretch' => 'nullable|string|max:100',
        ]);

        \$validated['title'] = \$validated['name'];
        unset(\$validated['name']);
        \$product = Product::create(\$validated);

        if (\$request->has('variants')) {
            foreach (\$request->variants as \$variant) {
                if (!empty(\$variant['size'])) {
                    \$product->variants()->create([
                        'size' => \$variant['size'],
                        'color' => \$variant['color'] ?? null,
                        'quantity' => \$variant['stock'] ?? 0,
                        'stock' => \$variant['stock'] ?? 0,
                        'price' => \$variant['price'] ?? \$product->price,
                        'sku' => \$variant['sku'] ?? uniqid('VAR-'),
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing a product
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = \App\Models\Brand::all();

        return view('admin.products.edit', compact('product', 'categories','brands'));
    }

    /**
     * Update the product in database
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|unique:products,sku,' . $product->id . '|max:100',
            'fit_type' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'stretch' => 'nullable|string|max:100',
        ]);

        \$validated['title'] = \$validated['name'];
        unset(\$validated['name']);
        \$product->update(\$validated);

        \$product->variants()->delete();
        if (\$request->has('variants')) {
            foreach (\$request->variants as \$variant) {
                if (!empty(\$variant['size'])) {
                    \$product->variants()->create([
                        'size' => \$variant['size'],
                        'color' => \$variant['color'] ?? null,
                        'quantity' => \$variant['stock'] ?? 0,
                        'stock' => \$variant['stock'] ?? 0,
                        'price' => \$variant['price'] ?? \$product->price,
                        'sku' => \$variant['sku'] ?? uniqid('VAR-'),
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show product details
     */
    public function show(Product $product)
    {
        $product->load('category', 'variants');

        return view('admin.products.show', compact('product'));
    }
}
