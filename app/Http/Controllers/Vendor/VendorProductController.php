<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    /**
     * Get the authenticated vendor profile or abort 403.
     */
    private function getVendor(): Vendor
    {
        $vendor = Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found. Please contact the administrator.');
        }
        return $vendor;
    }

    /**
     * List only the vendor's own products.
     */
    public function index(Request $request)
    {
        $vendor = $this->getVendor();

        $query = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('vendor_id', $vendor->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active' ? 1 : 0);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('vendor.products.index', compact('products', 'categories', 'vendor'));
    }

    /**
     * Show the product creation form.
     */
    public function create()
    {
        $this->getVendor();
        $categories = Category::all();
        $brands = Brand::all();

        return view('vendor.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a new product assigned to this vendor.
     */
    public function store(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:products,slug',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'sku'               => 'nullable|string|unique:products,sku',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'gender'            => 'nullable|string',
            'age_group'         => 'nullable|string',
            'color_family'      => 'nullable|string',
            'is_featured'       => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['slug']       = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->boolean('is_active');
        $validated['vendor_id']   = $vendor->id;

        // Store vendor's price separately so admin can apply courier + profit on top
        if (isset($validated['sale_price'])) {
            $validated['vendor_sale_price'] = $validated['sale_price'];
            // sale_price stays as-is; admin sets courier_charge + profit_margin
            // which triggers jeanzo_price accessor on the frontend
        }

        $product = Product::create($validated);

        // Save variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $size = $variant['waist_size'] ?? $variant['size'] ?? null;
                if (!empty($size)) {
                    $product->variants()->create([
                        'waist_size' => $size,
                        'color'      => $variant['color'] ?? $product->color_family,
                        'quantity'   => $variant['quantity'] ?? $variant['stock'] ?? 0,
                        'price'      => $variant['price'] ?? $product->price,
                        'sku'        => $variant['sku'] ?? ($product->sku . '-' . $size . '-' . Str::random(4)),
                        'is_active'  => true,
                    ]);
                }
            }
        }

        // Save images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $stored   = $file->store('products/' . $product->id . '/images', 'public');
                $filename = basename($stored);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show single product (must belong to this vendor).
     */
    public function show(Product $product)
    {
        $vendor = $this->getVendor();
        $this->authorizeProduct($product, $vendor);

        $product->load(['category', 'brand', 'variants', 'images']);

        return view('vendor.products.show', compact('product'));
    }

    /**
     * Show edit form (must belong to this vendor).
     */
    public function edit(Product $product)
    {
        $vendor = $this->getVendor();
        $this->authorizeProduct($product, $vendor);

        $categories = Category::all();
        $brands = Brand::all();
        $product->load('images');

        return view('vendor.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update product (must belong to this vendor).
     */
    public function update(Request $request, Product $product)
    {
        $vendor = $this->getVendor();
        $this->authorizeProduct($product, $vendor);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:products,slug,' . $product->id,
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'sku'               => 'nullable|string|unique:products,sku,' . $product->id,
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'gender'            => 'nullable|string',
            'age_group'         => 'nullable|string',
            'color_family'      => 'nullable|string',
            'is_featured'       => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['slug']        = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active']   = $request->boolean('is_active');
        unset($validated['vendor_id']);

        // Keep vendor_sale_price in sync when vendor updates their price
        if (isset($validated['sale_price'])) {
            $validated['vendor_sale_price'] = $validated['sale_price'];
        }

        $product->update($validated);

        // Update variants
        if ($request->has('variants')) {
            $product->variants()->delete();
            foreach ($request->variants as $variant) {
                $size = $variant['waist_size'] ?? $variant['size'] ?? null;
                if (!empty($size)) {
                    $product->variants()->create([
                        'waist_size' => $size,
                        'color'      => $variant['color'] ?? $product->color_family,
                        'quantity'   => $variant['quantity'] ?? $variant['stock'] ?? 0,
                        'price'      => $variant['price'] ?? $product->price,
                        'sku'        => $variant['sku'] ?? ($product->sku . '-' . $size . '-' . Str::random(4)),
                        'is_active'  => true,
                    ]);
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            $lastOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $index => $file) {
                $stored   = $file->store('products/' . $product->id . '/images', 'public');
                $filename = basename($stored);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $filename,
                    'sort_order' => $lastOrder + $index + 1,
                ]);
            }
        }

        // Handle image deletions
        if ($request->filled('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $img = ProductImage::where('id', $imageId)->where('product_id', $product->id)->first();
                if ($img) {
                    Storage::disk('public')->delete('products/' . $product->id . '/images/' . $img->image);
                    $img->delete();
                }
            }
        }

        return redirect()->route('vendor.products.edit', $product)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product (must belong to this vendor).
     */
    public function destroy(Product $product)
    {
        $vendor = $this->getVendor();
        $this->authorizeProduct($product, $vendor);

        foreach ($product->images as $image) {
            Storage::disk('public')->delete('products/' . $product->id . '/images/' . $image->image);
        }
        Storage::disk('public')->deleteDirectory('products/' . $product->id);
        $product->delete();

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Delete a product image (must belong to vendor's product).
     */
    public function deleteImage(ProductImage $image)
    {
        $vendor = $this->getVendor();
        $product = Product::findOrFail($image->product_id);
        $this->authorizeProduct($product, $vendor);

        Storage::disk('public')->delete('products/' . $image->product_id . '/images/' . $image->image);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }

    /**
     * Abort 403 if this product does not belong to the vendor.
     */
    private function authorizeProduct(Product $product, Vendor $vendor): void
    {
        if ((int) $product->vendor_id !== (int) $vendor->id) {
            abort(403, 'You do not have permission to manage this product.');
        }
    }
}
