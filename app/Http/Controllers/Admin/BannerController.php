<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    private const TYPES = ['homepage', 'sale', 'mobile', 'category', 'popup'];

    private function types(): array
    {
        return self::TYPES;
    }

    public function index(Request $request)
    {
        $query = Banner::query();

        $type = $request->query('type');
        if ($type) {
            $query->where('type', $type);
        }

        $banners = $query
            ->orderBy('type')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.banners.index', [
            'banners' => $banners,
            'type'    => $type,
            'types'   => $this->types(),
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->query('type');
        if ($type && !in_array($type, $this->types(), true)) {
            $type = null;
        }

        return view('admin.banners.create', [
            'type'  => $type,
            'types' => $this->types(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'       => 'required|string|in:' . implode(',', $this->types()),
            'title'      => 'nullable|string|max:255',
            'image'      => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'link_url'   => 'nullable|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        // Create banner first to get ID
        $banner = Banner::create([
            'type'       => $validated['type'],
            'title'      => $validated['title'] ?? null,
            'image_url'  => '', // temporary, updated below
            'link_url'   => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $validated['is_active'] ?? true,
        ]);

        // Store image: banners/{id}/images/{filename}
        $stored = $request->file('image')->store(
            'banners/' . $banner->id . '/images',
            'public'
        );
        $banner->update(['image_url' => $stored]);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
            'types'  => $this->types(),
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'type'       => 'required|string|in:' . implode(',', $this->types()),
            'title'      => 'nullable|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'link_url'   => 'nullable|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $imageUrl = $banner->image_url;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($imageUrl) {
                Storage::disk('public')->delete($imageUrl);
            }
            $imageUrl = $request->file('image')->store(
                'banners/' . $banner->id . '/images',
                'public'
            );
        }

        $banner->update([
            'type'       => $validated['type'],
            'title'      => $validated['title'] ?? null,
            'image_url'  => $imageUrl,
            'link_url'   => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $validated['is_active'] ?? false,
        ]);

        return redirect()
            ->route('admin.banners.index', ['type' => $banner->type])
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $type = $banner->type;
        // Delete image folder
        Storage::disk('public')->deleteDirectory('banners/' . $banner->id);
        $banner->delete();

        return redirect()
            ->route('admin.banners.index', ['type' => $type])
            ->with('success', 'Banner deleted successfully.');
    }
}
