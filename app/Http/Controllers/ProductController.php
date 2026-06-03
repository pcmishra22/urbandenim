<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products (all roles can view).
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('fit_type')) {
            $query->where('fit_type', $request->fit_type);
        }

        if ($request->has('color')) {
            $query->where('color', $request->color);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('price_min') || $request->has('price_max')) {
            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }
            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }
        }

        $products = $query->with('category', 'variants')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load('category', 'variants');
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Store a newly created product (admin and vendor only).
     */
    public function store(Request $request)
    {
        if (!$request->user()->canManageProducts()) {
            abort(403, 'Unauthorized to create products');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|unique:products|max:100',
            'fit_type' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'stretch' => 'nullable|string|max:100',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Update the specified product (admin and vendor only).
     */
    public function update(Request $request, Product $product)
    {
        if (!$request->user()->canManageProducts()) {
            abort(403, 'Unauthorized to update products');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|unique:products,sku,' . $product->id . '|max:100',
            'fit_type' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'stretch' => 'nullable|string|max:100',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Delete the specified product (admin only).
     */
    public function destroy(Request $request, Product $product)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can delete products');
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get inventory status (vendor and admin only).
     */
    public function inventory(Request $request)
    {
        if (!$request->user()->canManageInventory()) {
            abort(403, 'Unauthorized to view inventory');
        }

        $products = Product::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'status' => $product->quantity === 0 ? 'out_of_stock' : ($product->quantity < 5 ? 'low_stock' : 'in_stock'),
            ];
        });

        return response()->json($products);
    }
}
