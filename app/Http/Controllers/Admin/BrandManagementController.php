<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandManagementController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        $brands = Brand::orderByDesc('updated_at')->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:brands,name',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:2000',
            'seo_keywords'    => 'nullable|string|max:2000',
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'is_featured'     => 'nullable|boolean',
        ]);

        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_active']   = $validated['is_active'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        unset($validated['logo']);

        $brand = Brand::create($validated);

        // Store logo: brands/{id}/images/{filename}
        if ($request->hasFile('logo')) {
            $stored = $request->file('logo')->store(
                'brands/' . $brand->id . '/images',
                'public'
            );
            $brand->update(['logo_url' => $stored]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully!');
    }

    public function edit(Brand $brand)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:2000',
            'seo_keywords'    => 'nullable|string|max:2000',
            'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'is_featured'     => 'nullable|boolean',
        ]);

        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_active']   = $validated['is_active'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo_url) {
                Storage::disk('public')->delete($brand->logo_url);
            }
            $validated['logo_url'] = $request->file('logo')->store(
                'brands/' . $brand->id . '/images',
                'public'
            );
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }

        Storage::disk('public')->deleteDirectory('brands/' . $brand->id);
        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully!');
    }
}
