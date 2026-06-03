<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display orders (admin can see all, vendors/customers see their own).
     */
    public function index(Request $request)
    {
        if ($request->user()->isAdmin()) {
            $orders = Order::with('user', 'products')->get();
        } else {
            $orders = $request->user()->orders()->with('products')->get();
        }

        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, Order $order)
    {
        // Check authorization - only user who placed order or admin can view
        if ($order->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized to view this order');
        }

        return response()->json($order->load('products'));
    }

    /**
     * Create a new order (customers and admin only).
     */
    public function store(Request $request)
    {
        // Check authorization - only customers and admins can place orders
        if (!$request->user()->canPlaceOrders()) {
            abort(403, 'Only customers and admins can place orders');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total_price' => $validated['total_price'],
            'status' => 'pending',
        ]);

        // Attach products to order with real price from DB
        foreach ($validated['items'] as $item) {
            $product = \App\Models\Product::findOrFail($item['product_id']);
            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'price'    => $product->sale_price ?? $product->price,
            ]);
        }

        return response()->json($order->load('products'), 201);
    }

    /**
     * Update order status (admin only).
     */
    public function update(Request $request, Order $order)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can update orders');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);
        return response()->json($order);
    }

    /**
     * Cancel an order (customer can cancel own, admin can cancel any).
     */
    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        // Only customer who placed order or admin can cancel
        if ($order->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized to cancel this order');
        }

        $order->update(['status' => 'cancelled']);
        return response()->json($order);
    }

    /**
     * Delete the specified order (admin only).
     */
    public function destroy(Request $request, Order $order)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can delete orders');
        }

        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Get admin statistics (admin only).
     */
    public function stats(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can view statistics');
        }

        $stats = [
            'total_orders' => Order::count(),
            'total_users' => \App\Models\User::count(),
            'total_products' => \App\Models\Product::count(),
            'total_revenue' => Order::sum('total_price'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'orders_by_status' => Order::selectRaw('status, count(*) as count')->groupBy('status')->get(),
        ];

        return response()->json($stats);
    }
}
