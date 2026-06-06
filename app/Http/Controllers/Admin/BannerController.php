<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Added page_header for shop/cart/checkout/etc top banners
    private const TYPES = ['homepage', 'page_header', 'sale', 'mobile', 'category', 'popup'];

    private function types(): array { return self::TYPES; }

    public function index(Request $request)
    {
        $type    = $request->query('type');
        $query   = Banner::query();
        if ($type) $query->where('type', $type);

        $banners = $query->orderBy('type')->orderByDesc('sort_order')->orderByDesc('id')
                         ->paginate(25)->withQueryString();

        return view('admin.banners.index', compact('banners', 'type') + ['types' => $this->types()]);
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), $this->types()) ? $request->query('type') : null;
        return view('admin.banners.create', ['type' => $type, 'types' => $this->types()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'       => 'required|string|in:' . implode(',', $this->types()),
            'title'      => 'nullable|string|max:255',
            'heading'    => 'nullable|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'link_url'   => 'nullable|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $banner = Banner::create([
            'type'       => $validated['type'],
            'title'      => $validated['title'] ?? null,
            'heading'    => $validated['heading'] ?? null,
            'subtitle'   => $validated['subtitle'] ?? null,
            'image_url'  => '',
            'link_url'   => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $stored = $request->file('image')->store('banners/' . $banner->id . '/images', 'public');
            $banner->update(['image_url' => $stored]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', ['banner' => $banner, 'types' => $this->types()]);
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'type'       => 'required|string|in:' . implode(',', $this->types()),
            'title'      => 'nullable|string|max:255',
            'heading'    => 'nullable|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
            'link_url'   => 'nullable|url|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $imageUrl = $banner->image_url;
        if ($request->hasFile('image')) {
            if ($imageUrl) Storage::disk('public')->delete($imageUrl);
            $imageUrl = $request->file('image')->store('banners/' . $banner->id . '/images', 'public');
        }

        $banner->update([
            'type'       => $validated['type'],
            'title'      => $validated['title'] ?? null,
            'heading'    => $validated['heading'] ?? null,
            'subtitle'   => $validated['subtitle'] ?? null,
            'image_url'  => $imageUrl,
            'link_url'   => $validated['link_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.banners.index', ['type' => $banner->type])
                         ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $type = $banner->type;
        Storage::disk('public')->deleteDirectory('banners/' . $banner->id);
        $banner->delete();
        return redirect()->route('admin.banners.index', ['type' => $type])
                         ->with('success', 'Banner deleted.');
    }
}
