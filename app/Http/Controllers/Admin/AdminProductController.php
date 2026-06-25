<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images', 'variants']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        if ($request->filled('fit_type')) {
            $query->where('fit_type', $request->input('fit_type'));
        }
        if ($request->filled('color')) {
            $query->where('color_family', $request->input('color'));
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('waist_size', $request->input('size'));
            });
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
                'name'              => 'required|string|max:255',
                'slug'              => 'nullable|string|unique:products,slug',
                'category_id'       => 'required|exists:categories,id',
                'brand_id'          => 'nullable|exists:brands,id',
                'sku'               => 'nullable|string|unique:products,sku',
                'price'             => 'required|numeric|min:0',
                'sale_price'        => 'nullable|numeric|min:0',
                'short_description' => 'nullable|string|max:500',
                'model_info'        => 'nullable|string|max:100',
                'fabric_info'       => 'nullable|string|max:150',
                'cost_price'        => 'nullable|numeric|min:0',
                'courier_charge'    => 'nullable|numeric|min:0',
        ]);
        // Save Product Variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $size = $variant['waist_size'] ?? $variant['size'] ?? null;
                if (!empty($size)) {
                    $product->variants()->create([
                        'waist_size' => $size,
                        'color'      => $variant['color'] ?? $product->color_family,
                        'quantity'   => $variant['quantity'] ?? $variant['stock'] ?? 0,
                        'price'      => $variant['price'] ?? $product->price,
                        'sku'        => $variant['sku'] ?? $product->sku . '-' . $size . '-' . Str::random(4),
                        'is_active'  => true,
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                // Dynamically creates: storage/app/public/products/{id}/images/
                $stored   = $file->store('products/' . $product->id . '/images', 'public');
                $filename = basename($stored);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'variants', 'images']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        $product->load('images');

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:products,slug,' . $product->id,
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'sku'               => 'nullable|string|unique:products,sku,' . $product->id,
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'short_description' => 'nullable|string|max:500',
            'model_info'        => 'nullable|string|max:100',
            'fabric_info'       => 'nullable|string|max:150',
            'cost_price'        => 'nullable|numeric|min:0',
            'courier_charge'    => 'nullable|numeric|min:0',
            'profit_margin'     => 'nullable|numeric|min:0|max:1000',
            'description'       => 'nullable|string',
            'gender'            => 'nullable|string',
            'age_group'         => 'nullable|string',
            'color_family'      => 'nullable|string',
            'is_featured'       => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['slug']        = Str::slug($validated['slug'] ?? $validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->boolean('is_active');

        $product->update($validated);

        // Update Product Variants
        if ($request->has('variants')) {
            // Simple approach: delete existing and recreate
            $product->variants()->delete();
            foreach ($request->variants as $variant) {
                $size = $variant['waist_size'] ?? $variant['size'] ?? null;
                if (!empty($size)) {
                    $product->variants()->create([
                        'waist_size' => $size,
                        'color'      => $variant['color'] ?? $product->color_family,
                        'quantity'   => $variant['quantity'] ?? $variant['stock'] ?? 0,
                        'price'      => $variant['price'] ?? $product->price,
                        'sku'        => $variant['sku'] ?? $product->sku . '-' . $size . '-' . Str::random(4),
                        'is_active'  => true,
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            $lastOrder = $product->images()->max('sort_order') ?? 0;

            foreach ($request->file('images') as $index => $file) {
                // Dynamically creates: storage/app/public/products/{id}/images/
                $stored   = $file->store('products/' . $product->id . '/images', 'public');
                $filename = basename($stored);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                    'sort_order' => $lastOrder + $index + 1,
                ]);
            }
        }

        // Handle image deletions if checkboxes sent
        if ($request->filled('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $img = ProductImage::where('id', $imageId)->where('product_id', $product->id)->first();
                if ($img) {
                    Storage::disk('public')->delete('products/' . $product->id . '/images/' . $img->image);
                    $img->delete();
                }
            }
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete('products/' . $product->id . '/images/' . $image->image);
        }
        // Also remove the product folder if empty
        Storage::disk('public')->deleteDirectory('products/' . $product->id);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete('products/' . $image->product_id . '/images/' . $image->image);
        $image->delete();

        return back()->with('success', 'Image deleted successfully!');
    }
}
