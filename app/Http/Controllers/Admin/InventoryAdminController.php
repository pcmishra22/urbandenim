<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAdminController extends Controller
{
    // Note: Base Controller in this repo does not provide middleware() helper.
    // Admin access is handled by routes group middleware in routes/web.php.


    /**
     * Stock Tracking: show all variants with current stock.
     */
    public function index()
    {
        // ProductVariant in this repo uses `quantity` as stock.
        // For stock tracking, show variant + product.
        $inventory = ProductVariant::with('product')->orderBy('id', 'desc')->paginate(30);


        return view('admin.inventory.index', compact('inventory'));
    }

    /**
     * Low Stock Alerts: variants with stock <= threshold.
     */
    public function alerts(Request $request)
    {
        $threshold = (int) $request->get('threshold', 10);

        $items = ProductVariant::with('product')
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->paginate(30);


        return view('admin.inventory.alerts', compact('items', 'threshold'));
    }

    /**
     * Warehouse Inventory: current stock breakdown.
     * NOTE: ProductVariant does not have a warehouse_id in model. We'll show warehouses list.
     */
    public function warehouses()
    {
        $warehouses = Warehouse::with('variants')->orderBy('id', 'desc')->paginate(15);

        // If the DB doesn't have the warehouses table yet, this endpoint would 500.
        // This controller assumes `warehouses` exists (per migrations). If you still see 42S02,
        // run migrations: php artisan migrate.


        return view('admin.inventory.warehouses', compact('warehouses'));
    }

    /**
     * Stock History: show inventory movement logs.
     */
    public function history()
    {
        $logs = InventoryLog::with(['variant.product', 'user'])
            ->latest()
            ->paginate(25);

        return view('admin.inventory.history', compact('logs'));
    }

    /**
     * Adjust Stock: manually adjust stock and create history log.
     */
    public function adjustStock(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $variant = ProductVariant::findOrFail($validated['variant_id']);
        $oldStock = (int) $variant->quantity;
        $newStock = $oldStock + (int) $validated['adjustment'];


        DB::transaction(function () use ($variant, $newStock, $oldStock, $validated, $request) {
            $variant->update(['quantity' => $newStock]);


            InventoryLog::create([
                'product_variant_id' => $variant->id,
                'user_id' => $request->user()->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'adjustment' => (int) $validated['adjustment'],
                'reason' => $validated['reason'],
            ]);
        });

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Stock adjusted successfully. New stock: ' . $newStock);
    }
}

