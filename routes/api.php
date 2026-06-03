<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('auth:sanctum')->group(function () {
    // User endpoints
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // Brand endpoints (authenticated group; can be moved to public later)
    Route::get('/brands', [\App\Http\Controllers\BrandController::class, 'index']);
    Route::get('/brands/featured', [\App\Http\Controllers\BrandController::class, 'featured']);
    Route::get('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'show']);


    // Category routes - all authenticated users can view
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Category management - admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::patch('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // Product routes - all authenticated users can view
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Product management - admin and vendor only
    Route::middleware('role:admin,vendor')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
    });

    // Product deletion - admin only
    Route::middleware('role:admin')->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Order creation - customer and admin only
    Route::middleware('role:admin,customer')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });

    // Order cancellation - customer can cancel own, admin can cancel any
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Order management - admin only
    Route::middleware('role:admin')->group(function () {
        Route::put('/orders/{order}', [OrderController::class, 'update']);
        Route::patch('/orders/{order}', [OrderController::class, 'update']);
        Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    });
});

// Admin dashboard - admin only
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/stats', [OrderController::class, 'stats']);

    // Inventory Management
    Route::prefix('admin/inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        Route::get('/alerts', [InventoryController::class, 'alerts']);
        Route::get('/history', [InventoryController::class, 'history']);
        Route::post('/adjust', [InventoryController::class, 'adjustStock']);
        Route::get('/warehouses', [InventoryController::class, 'warehouses']);
    });
});

// Vendor dashboard - vendor only
Route::middleware(['auth:sanctum', 'role:vendor'])->group(function () {
    Route::get('/vendor/inventory', [ProductController::class, 'inventory']);
});
