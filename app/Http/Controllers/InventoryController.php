<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\InventoryLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Stock Tracking: Display all variants with their current stock and warehouse.
     */
    public function index()
    {
        $inventory = ProductVariant::with(['product', 'warehouse'])
            ->get()
            ->map(function ($variant) {
                return [
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'variant' => "{$variant->color} / {$variant->waist_size}",
                    'stock' => $variant->quantity,
                    'warehouse' => $variant->warehouse->name ?? 'Unassigned',
                ];
            });

        return response()->json($inventory);
    }

    /**
     * Low Stock Alerts: Identify items with stock below a specific threshold.
     */
    public function alerts(Request $request)
    {
        $threshold = $request->get('threshold', 10);
        
        $lowStockItems = ProductVariant::with('product')
            ->where('quantity', '<=', $threshold)
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'count' => $lowStockItems->count(),
            'items' => $lowStockItems
        ]);
    }

    /**
     * Stock History: View logs of all inventory movements.
     */
    public function history()
    {
        $logs = InventoryLog::with(['variant.product', 'user'])
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Warehouse Inventory: Get stock breakdown by warehouse.
     */
    public function warehouses()
    {
        $warehouseStock = Warehouse::withSum('variants as total_stock', 'quantity')->get();

        return response()->json($warehouseStock);
    }

    /**
     * Adjust Stock: Manually update stock and create a history log.
     */
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'adjustment' => 'required|integer', // e.g., +5 or -3
            'reason' => 'required|string|max:255',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $oldStock = $variant->quantity;
        $newStock = $oldStock + $validated['adjustment'];

        DB::transaction(function () use ($variant, $newStock, $oldStock, $validated, $request) {
            $variant->update(['quantity' => $newStock]);

            InventoryLog::create([
                'product_variant_id' => $variant->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'adjustment' => $validated['adjustment'],
                'reason' => $validated['reason'],
                'user_id' => $request->user()->id,
            ]);
        });

        return response()->json(['message' => 'Stock adjusted successfully', 'new_stock' => $newStock]);
    }
}