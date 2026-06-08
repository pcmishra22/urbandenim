<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Models\Order;
use App\Models\Courier;
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

    public function create()
    {
        // Get orders that don't have a shipment yet
        // Order model defines relationship as `shipments()` (plural)
        $orders = Order::whereDoesntHave('shipments')->get();
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.shipments.create', compact('orders', 'couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id|unique:order_shipments,order_id',
            'courier_id' => 'required|exists:couriers,id',
            'tracking_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,shipped,in_transit,delivered,cancelled',
        ]);

        OrderShipment::create($request->all());

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment created successfully.');
    }

    public function edit(OrderShipment $shipment)
    {
        $couriers = Courier::where('is_active', true)->orderBy('name')->get();

        return view('admin.shipments.edit', compact('shipment', 'couriers'));
    }

    public function update(Request $request, OrderShipment $shipment)
    {
        $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'tracking_id' => 'nullable|string|max:255',
            'status' => 'required|in:pending,shipped,in_transit,delivered,cancelled',
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

    public function destroy(OrderShipment $shipment)
    {
        $shipment->delete();
        return redirect()->route('admin.shipments.index')->with('success', 'Shipment deleted successfully.');
    }
}
