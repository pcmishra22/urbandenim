<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ReturnRequestedMail;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReturnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show return request form for a specific order.
     */
    public function create(int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->with(['products.vendor'])
            ->firstOrFail();

        // Only delivered orders can be returned
        if ($order->status !== 'delivered') {
            return redirect()->route('profile.order-details', $orderId)
                ->with('error', 'Returns are only available for delivered orders.');
        }

        // Check if a return already exists
        $existing = ReturnRequest::where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['rejected'])
            ->first();

        if ($existing) {
            return redirect()->route('profile.order-details', $orderId)
                ->with('error', "A return request (#$existing->id) already exists for this order.");
        }

        // Check 7-day window
        if ($order->updated_at->diffInDays(now()) > 7) {
            return redirect()->route('profile.order-details', $orderId)
                ->with('error', 'The 7-day return window for this order has expired.');
        }

        return view('front.profile.return-request', compact('order'));
    }

    /**
     * Store the return request and notify admin + vendor.
     */
    public function store(Request $request, int $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', auth()->id())
            ->with(['products.vendor'])
            ->firstOrFail();

        if ($order->status !== 'delivered') {
            return redirect()->route('profile.order-details', $orderId)
                ->with('error', 'Returns are only available for delivered orders.');
        }

        // Check 7-day window
        if ($order->updated_at->diffInDays(now()) > 7) {
            return redirect()->route('profile.order-details', $orderId)
                ->with('error', 'The 7-day return window has expired.');
        }

        $request->validate([
            'reason'      => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => 'required|in:return,exchange',
        ]);

        // Get vendor from first product in order
        $vendor = $order->products->first()?->vendor;

        $returnRequest = ReturnRequest::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'vendor_id'   => $vendor?->id,
            'type'        => $request->type,
            'reason'      => $request->reason,
            'description' => $request->description,
            'status'      => 'requested',
            'refund_amount' => $order->total_price,
        ]);

        // ── Notify Admin ──
        try {
            $adminEmail = env('ADMIN_EMAIL', 'support@jeanzo.in');
            Mail::to($adminEmail)->send(new ReturnRequestedMail($returnRequest->load(['order', 'user']), 'admin'));
            Log::info('Return request admin email sent', ['return_id' => $returnRequest->id]);
        } catch (\Throwable $e) {
            Log::error('Return request admin email failed', ['error' => $e->getMessage()]);
        }

        // ── Notify Vendor ──
        if ($vendor && $vendor->user) {
            try {
                Mail::to($vendor->user->email)->send(new ReturnRequestedMail($returnRequest, 'vendor'));
                Log::info('Return request vendor email sent', ['return_id' => $returnRequest->id, 'vendor' => $vendor->shop_name]);
            } catch (\Throwable $e) {
                Log::error('Return request vendor email failed', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('profile.order-details', $orderId)
            ->with('success', "Return request #$returnRequest->id submitted successfully. We'll review it within 24–48 hours and email you an update.");
    }
}
