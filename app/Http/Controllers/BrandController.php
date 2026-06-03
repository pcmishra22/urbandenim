<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * List all active brands.
     */
    public function index()
    {
        $brands = Brand::where('is_active', true)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }

    /**
     * List featured active brands.
     */
    public function featured()
    {
        $brands = Brand::where('is_active', true)
            ->where('is_featured', true)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }

    /**
     * Show an active brand by slug.
     */
    public function show(string $brand)
    {
        $brandModel = Brand::where('slug', $brand)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $brandModel,
        ]);
    }
}

