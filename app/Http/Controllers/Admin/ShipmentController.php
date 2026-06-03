<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Models\Courier;
use App\Models\Order;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = OrderShipment::with(['order.user', 'courier'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.shipments.index', compact('shipments'));
    }

    public function edit(OrderShipment $shipment)
    {
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();

        return view('admin.shipments.edit', compact('shipment', 'couriers'));
    }

    public function update(Request $request, OrderShipment $shipment)
    {
        $validated = $request->validate([
            'tracking_id' => 'nullable|string|max:255',
            'courier_id' => 'nullable|exists:couriers,id',
            'status' => 'required|string|max:50',
            'shipped_at' => 'nullable|date',
            'delivered_at' => 'nullable|date',
        ]);

        $shipment->update([
            'tracking_id' => $validated['tracking_id'] ?? null,
            'courier_id' => $validated['courier_id'] ?? null,
            'status' => $validated['status'],
            'shipped_at' => $validated['shipped_at'] ?? null,
            'delivered_at' => $validated['delivered_at'] ?? null,
        ]);

        return redirect()->route('admin.shipments.index')
            ->with('success', 'Shipment tracking updated successfully.');
    }
}

