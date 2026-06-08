<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderAlert;
use Illuminate\Support\Facades\Notification;
use App\Mail\OrderDispatchedMail;
use App\Mail\OrderDeliveredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

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

        $flow = ['pending' => 'confirmed', 'confirmed' => 'packed', 'packed' => 'shipped', 'shipped' => 'delivered'];

        $current = $order->status;
        $target = $validated['status'];

        if ($current === $target) {
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order status unchanged.');
        }

        $allowed = ($flow[$current] ?? null) === $target;

        // allow cancel/cancelled if your system uses it
        if (in_array($target, ['cancelled', 'canceled'], true)) {
            $order->update(['status' => 'cancelled']);
            
            // Notify admins about cancellation
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new OrderAlert($order, 'cancelled'));
            
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order cancelled.');
        }

        if (!$allowed) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Invalid status transition from ' . $current . ' to ' . $target);
        }

        $order->update(['status' => $target]);

        // Send email to customer based on new status
        $order->load('products', 'user');
        try {
            if ($target === 'shipped') {
                // Get tracking info from latest shipment if available
                $shipment      = $order->shipments()->latest()->first();
                $trackingNumber = $shipment?->tracking_number;
                $courierName   = $shipment?->courier?->name;
                Mail::to($order->user->email)
                    ->send(new OrderDispatchedMail($order, $trackingNumber, $courierName));
            } elseif ($target === 'delivered') {
                Mail::to($order->user->email)->send(new OrderDeliveredMail($order));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Order status email failed', [
                'order_id' => $order->id,
                'status'   => $target,
                'error'    => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated to ' . ucfirst($target) . '.');
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
