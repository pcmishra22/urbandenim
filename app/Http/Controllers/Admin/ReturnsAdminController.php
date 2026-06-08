<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\WalletRefundTransaction;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\PaymentAlert;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class ReturnsAdminController extends Controller
{
    /**
     * List all return and exchange requests.
     */
    public function index()
    {
        $returns = ReturnRequest::with(['user', 'order'])->latest()->paginate(15);
        return view('admin.returns.index', compact('returns'));
    }

    /**
     * Show details of a specific return request.
     */
    public function show(ReturnRequest $return)
    {
        // Ensure related collections are available for the blade:
        // - items
        // - pickupRequests
        // - exchangeRequest
        $return->load([
            'user',
            'order.products',
            'items.product',
            'pickupRequests',
            'exchangeRequest',
        ]);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Approve the refund and update status.
     *
     * This does NOT move money itself; it just approves the return.
     */
    public function approveRefund(ReturnRequest $return)
    {
        $validated = request()->validate([
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'reason' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'status' => 'approved',
            'approved_at' => now(),
        ];

        // Store wallet refund amount if provided.
        if (array_key_exists('amount', $validated) && $validated['amount'] !== null) {
            $payload['refund_wallet_amount'] = (float) $validated['amount'];
        }
        if (!empty($validated['currency'])) {
            $payload['refund_wallet_currency'] = strtoupper($validated['currency']);
        }

        $return->update($payload);

        return redirect()->back()->with('success', 'Refund approved. You can now trigger wallet refund (or pickup flow).');
    }

    /**
     * Schedule a reverse pickup for the items.
     */
    public function requestReversePickup(ReturnRequest $return)
    {
        $validated = request()->validate([
            'courier_id' => 'nullable|integer|exists:couriers,id',
            'tracking_id' => 'nullable|string|max:255',
        ]);

        if (!in_array($return->status, ['approved', 'pickup_requested', 'refund_completed', 'approved_for_pickup'], true)) {
            return redirect()->back()->with('error', 'Reverse pickup can only be requested after refund approval.');
        }

        DB::transaction(function () use ($return, $validated) {
            // Idempotency: don’t create duplicate requested pickup if one is already in-flight.
            $existing = $return->pickupRequests()
                ->whereIn('status', ['requested', 'picked_up', 'received'])
                ->latest('id')
                ->first();

            if ($existing) {
                return;
            }

            $return->pickupRequests()->create([
                'status' => 'requested',
                'courier_id' => $validated['courier_id'] ?? null,
                'tracking_id' => $validated['tracking_id'] ?? null,
                'requested_at' => now(),
            ]);

            $return->update(['status' => 'pickup_requested']);
        });

        return redirect()->back()->with('success', 'Reverse pickup has been scheduled.');
    }

    /**
     * Approve an exchange request.
     */
    public function approveExchange(ReturnRequest $return)
    {
        if ($return->type !== 'exchange') {
            return redirect()->back()->with('error', 'This is not an exchange request.');
        }

        $validated = request()->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($return, $validated) {
            $return->exchangeRequest()->updateOrCreate([], [
                'status' => 'approved',
                'approved_at' => now(),
                'exchange_wallet_amount' => (float) $validated['amount'],
                'approved_by_admin_id' => auth()->id(),
            ]);

            $return->update(['status' => 'approved']);
        });

        return redirect()->back()->with('success', 'Exchange request approved.');
    }

    /**
     * Process refund directly to user's wallet.
     */
    public function refundToWallet(Request $request, ReturnRequest $return)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
        ]);

        $amount = (float) $validated['amount'];
        $currency = strtoupper($validated['currency'] ?? ($return->refund_wallet_currency ?? 'USD'));

        // Guard: refunding should only happen after approval.
        if ($return->status !== 'approved' && !in_array($return->status, ['refund_completed', 'pickup_requested'], true)) {
            return redirect()->back()->with('error', 'Refund to wallet can only be done after refund approval.');
        }

        DB::transaction(function () use ($return, $amount, $currency) {
            // Idempotency: if already refunded/completed, don’t create duplicates.
            $alreadyCompleted = WalletRefundTransaction::query()
                ->where('return_request_id', $return->id)
                ->where('status', 'completed')
                ->exists();

            if ($alreadyCompleted) {
                // Still ensure ReturnRequest reflects values.
                $return->update([
                    'refund_wallet_amount' => $amount,
                    'refund_wallet_currency' => $currency,
                    'status' => 'refund_completed',
                    'approved_at' => $return->approved_at ?? now(),
                ]);

                return;
            }

            WalletRefundTransaction::create([
                'user_id' => $return->user_id,
                'order_id' => $return->order_id,
                'return_request_id' => $return->id,
                'type' => 'credit',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'meta' => [
                    'admin_id' => auth()->id(),
                    'approved_at' => ($return->approved_at ?? now())->toISOString(),
                    'source' => 'admin_returns',
                ],
                'created_by_admin_id' => auth()->id(),
            ]);

            // NOTE: there is no customer wallet model/balance in the repo.
            $return->update([
                'refund_wallet_amount' => $amount,
                'refund_wallet_currency' => $currency,
                'status' => 'refund_completed',
                'approved_at' => $return->approved_at ?? now(),
            ]);

            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new PaymentAlert([
                'order_id' => $return->order_id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'message' => "A wallet refund of {$amount} {$currency} was processed for Order #{$return->order_id}."
            ]));
        });

        return redirect()->back()->with('success', 'Wallet refund transaction created and marked completed.');
    }
}
