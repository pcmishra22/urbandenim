<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderAlert;
use Illuminate\Support\Facades\Notification;
use App\Mail\OrderDispatchedMail;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderCancelledMail;
use App\Mail\OrderDeliveredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Log;
use App\Notifications\LowStockAlert;


class OrderAdminController extends Controller
{
    // Note: Base Controller in this repo does not provide middleware() helper.
    // Admin access is handled by routes group middleware in routes/web.php.


    /**
     * Order Listing
     */
    public function index()
    {
        $orders = Order::with('user')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Order Details + status flow
     */
    public function show(Order $order)
    {
        $order->load(['user', 'products']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status with enforced flow.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $current = $order->status;
        $target = strtolower($validated['status']);

        if ($current === $target) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order status unchanged.');
        }

        // Define all allowed transitions
        $allowedTransitions = [
            'pending'    => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped'    => ['delivered', 'cancelled'],
            'delivered'  => ['cancelled'],
            'cancelled'  => ['pending', 'processing', 'shipped', 'delivered'],
        ];

        // Check if the transition is allowed
        $isAllowed = in_array($target, $allowedTransitions[$current] ?? []);

        if (!$isAllowed) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Invalid status transition from ' . $current . ' to ' . $target);
        }

        // allow cancel/cancelled if your system uses it
        if (in_array($target, ['cancelled', 'canceled'], true)) {
            $order->update(['status' => 'cancelled']);
            
            // Notify admins about cancellation
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new OrderAlert($order, 'cancelled'));
            
            // Notify customer about cancellation via email
            try {
                Mail::to($order->user->email)
                    ->send((new OrderCancelledMail($order, 'Your order has been cancelled by an administrator.'))
                    ->subject("Order #{$order->id} Cancelled — Jeanzo"));
            } catch (\Throwable $e) {
                Log::warning('Order status cancellation email failed', [
                    'order_id' => $order->id,
                    'status'   => $target,
                    'error'    => $e->getMessage(),
                ]);
            }

            return redirect()->route('admin.orders.show', $order)->with('success', 'Order cancelled.');
        }

        // Keep status update + inventory operations atomic
        DB::beginTransaction();
        try {
            $order->update(['status' => $target]);

            // Decrement inventory only when order reaches "confirmed" (next stage after pending)
            // and only once per order status transition.
            if ($target === 'processing') { // Changed from 'confirmed' to 'processing'
                $this->applyInventoryForOrderStatus($order, $target); // Pass the correct target status
            }

            // Send email to customer based on new status
            $order->load('products', 'user');

            // Send processing/confirmation email
            try {
                if ($target === 'processing') {
                    Mail::to($order->user->email)
                        ->send((new OrderConfirmedMail($order))
                        ->subject("Order #{$order->id} Confirmed — Jeanzo"));
                } elseif ($target === 'shipped' || $target === 'dispatched') {
                    // Get tracking info from latest shipment if available
                    $shipment      = $order->shipments()->latest()->first();
                    $trackingNumber = $shipment?->tracking_number;
                    $courierName   = $shipment?->courier?->name;
                    Mail::to($order->user->email)
                        ->send((new OrderDispatchedMail($order, $trackingNumber, $courierName))
                        ->subject("Your Order #{$order->id} Has Been Shipped 🚚 — Jeanzo"));
                } elseif ($target === 'delivered') {
                    Mail::to($order->user->email)
                        ->send((new OrderDeliveredMail($order))
                        ->subject("Your Order #{$order->id} Has Been Delivered — Jeanzo"));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Order status email failed', [
                    'order_id' => $order->id,
                    'status'   => $target,
                    'error'    => $e->getMessage(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated to ' . ucfirst($target) . '.');
    }

    /**
     * Apply inventory deduction and reorder alert for an order status.
     * Currently deducts when status becomes "confirmed".
     */
    protected function applyInventoryForOrderStatus(Order $order, string $target): void
    {   // Only supported stage
        if ($target !== 'processing') { // Changed from 'confirmed' to 'processing'
            return;
        }

        // Ensure pivot data is available
        $order->load('products');

        $adminUsers = User::where('role', 'admin')->get();

        foreach ($order->products as $product) {
            // Determine ordered quantity from pivot
            $orderedQty = (int) ($product->pivot?->quantity ?? 1);

            // Deduct from product quantity (reorder_level lives on products)
            $product->decrement('quantity', $orderedQty);
            $product->refresh();

            // Log inventory movement
            InventoryLog::create([
                'product_variant_id' => null, // variants are not decremented in this flow
                'user_id' => $order->user_id,  // best-effort; adjust if you prefer admin id
                'old_stock' => max(0, $product->quantity + $orderedQty),
                'new_stock' => $product->quantity,
                'adjustment' => -$orderedQty,
                'reason' => 'Order #' . $order->id . ' processing', // Changed from 'confirmed' to 'processing'
            ]);

            // Trigger reorder alert if needed
            if ((int) $product->quantity <= (int) $product->reorder_level) {
                // If you later add a dedicated LowStockAlert notification, this call will work.
                if (class_exists(LowStockAlert::class)) {
                    \Illuminate\Support\Facades\Notification::send(
                        $adminUsers,
                        new LowStockAlert($product)
                    );
                }
            }
        }
    }


    /**
     * Invoice download.
     * Generates an HTML document and stores it as a .html file on first request.
     */
    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'products']);

        $dir = storage_path('app/invoices');
        $filename = 'invoice-order-' . $order->id . '.html';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($path)) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $html = view('admin.orders.invoice', compact('order'))->render();
            file_put_contents($path, $html);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/html',
        ]);
    }

    /**
     * Shipping label download.
     * Generates an HTML document and stores it as a .html file on first request.
     */
    public function downloadShippingLabel(Order $order)
    {
        $order->load(['shipments.courier']);

        $dir = storage_path('app/shipping-labels');
        $filename = 'shipping-label-order-' . $order->id . '.html';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($path)) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $html = view('admin.orders.shipping-label', compact('order'))->render();
            file_put_contents($path, $html);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'text/html',
        ]);
    }
}
