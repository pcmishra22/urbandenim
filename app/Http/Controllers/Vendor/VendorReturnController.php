<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Mail\ReturnStatusUpdatedMail;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VendorReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function vendor()
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) abort(403, 'Vendor account not found.');
        return $vendor;
    }

    /**
     * List all return requests for this vendor's products.
     */
    public function index()
    {
        $vendor = $this->vendor();

        $returns = ReturnRequest::where('vendor_id', $vendor->id)
            ->with(['order', 'user'])
            ->latest()
            ->paginate(15);

        $pendingCount = ReturnRequest::where('vendor_id', $vendor->id)
            ->whereIn('status', ['requested', 'approved'])
            ->count();

        return view('vendor.returns.index', compact('returns', 'pendingCount'));
    }

    /**
     * Show a single return request.
     */
    public function show(int $returnId)
    {
        $vendor = $this->vendor();

        $return = ReturnRequest::where('id', $returnId)
            ->where('vendor_id', $vendor->id)
            ->with(['order.products', 'user'])
            ->firstOrFail();

        return view('vendor.returns.show', compact('return'));
    }

    /**
     * Vendor acknowledges return, arranges pickup, or initiates refund.
     */
    public function updateStatus(Request $request, int $returnId)
    {
        $vendor = $this->vendor();

        $return = ReturnRequest::where('id', $returnId)
            ->where('vendor_id', $vendor->id)
            ->with(['order', 'user'])
            ->firstOrFail();

        $request->validate([
            'vendor_status' => 'required|in:acknowledged,pickup_arranged,received,refund_initiated',
            'vendor_note'   => 'nullable|string|max:500',
        ]);

        // Map vendor_status to main status
        $statusMap = [
            'acknowledged'    => 'approved',
            'pickup_arranged' => 'pickup_requested',
            'received'        => 'pickup_received',
            'refund_initiated'=> 'refund_wallet_queued',
        ];

        $return->update([
            'vendor_status' => $request->vendor_status,
            'vendor_note'   => $request->vendor_note,
            'status'        => $statusMap[$request->vendor_status] ?? $return->status,
        ]);

        // ── Notify customer of status change ──
        try {
            Mail::to($return->user->email)
                ->send(new ReturnStatusUpdatedMail($return->fresh(), 'vendor'));
            Log::info('Return status update email sent to customer', ['return_id' => $return->id]);
        } catch (\Throwable $e) {
            Log::error('Return status update email failed', ['error' => $e->getMessage()]);
        }

        $labels = [
            'acknowledged'    => 'Return acknowledged. Customer notified.',
            'pickup_arranged' => 'Pickup arranged. Customer notified.',
            'received'        => 'Item received. Customer notified.',
            'refund_initiated'=> 'Refund initiated. Customer notified.',
        ];

        return redirect()->route('vendor.returns.show', $returnId)
            ->with('success', $labels[$request->vendor_status] ?? 'Status updated.');
    }
}
