<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\InventoryLog;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorInventoryController extends Controller
{
    private function getVendor(): Vendor
    {
        $vendor = Auth::user()->vendorProfile;
        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }
        return $vendor;
    }

    /**
     * Stock tracking — variants belonging to this vendor's products only.
     */
    public function index()
    {
        $vendor = $this->getVendor();

        $inventory = ProductVariant::with('product')
            ->whereHas('product', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(30);

        return view('vendor.inventory.index', compact('inventory', 'vendor'));
    }

    /**
     * Low stock alerts — variants of this vendor's products only.
     */
    public function alerts(Request $request)
    {
        $vendor = $this->getVendor();
        $threshold = (int) $request->get('threshold', 10);

        $items = ProductVariant::with('product')
            ->whereHas('product', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->paginate(30);

        return view('vendor.inventory.alerts', compact('items', 'threshold', 'vendor'));
    }

    /**
     * Adjust stock for a variant that belongs to this vendor.
     */
    public function adjustStock(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'adjustment' => 'required|integer',
            'reason'     => 'required|string|max:255',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($validated['variant_id']);

        // Security: ensure this variant belongs to the vendor's product
        if ((int) $variant->product->vendor_id !== (int) $vendor->id) {
            abort(403, 'You cannot adjust stock for a product that is not yours.');
        }

        $oldStock = (int) $variant->quantity;
        $newStock = $oldStock + (int) $validated['adjustment'];
        if ($newStock < 0) {
            return back()->with('error', 'Stock cannot go below 0. Current stock: ' . $oldStock);
        }

        DB::transaction(function () use ($variant, $newStock, $oldStock, $validated, $request) {
            $variant->update(['quantity' => $newStock]);
            InventoryLog::create([
                'product_variant_id' => $variant->id,
                'user_id'            => $request->user()->id,
                'old_stock'          => $oldStock,
                'new_stock'          => $newStock,
                'adjustment'         => (int) $validated['adjustment'],
                'reason'             => '[Vendor] ' . $validated['reason'],
            ]);
        });

        return redirect()->route('vendor.inventory.index')
            ->with('success', 'Stock adjusted. New stock: ' . $newStock);
    }
}
