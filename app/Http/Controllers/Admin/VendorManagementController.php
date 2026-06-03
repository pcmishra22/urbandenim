<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use App\Models\VendorKycDocument;
use App\Models\VendorSettlementReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorManagementController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index()
    {
        $vendors = Vendor::with(['user', 'kycDocuments', 'wallet', 'commissionRule', 'performanceMetrics', 'settlementReports'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Approve a vendor registration.
     */
    public function approve(Request $request, $vendorId)
    {
        $vendor = Vendor::with('user')->findOrFail($vendorId);

        $vendor->update([
            'approval_status' => 'approved',
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Vendor approved successfully.');
    }

    /**
     * Reject a vendor registration.
     */
    public function reject(Request $request, $vendorId)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $vendor = Vendor::with('user')->findOrFail($vendorId);

        $vendor->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'is_active' => false,
        ]);

        return back()->with('success', 'Vendor rejected successfully.');
    }

    /**
     * Approve a vendor KYC document.
     */
    public function approveKyc(Request $request, $kycDocumentId)
    {
        $kyc = VendorKycDocument::with('vendor')->findOrFail($kycDocumentId);

        $kyc->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verifier_id' => $request->user()->id ?? null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'KYC document approved.');
    }

    /**
     * Reject a vendor KYC document.
     */
    public function rejectKyc(Request $request, $kycDocumentId)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $kyc = VendorKycDocument::with('vendor')->findOrFail($kycDocumentId);

        $kyc->update([
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verifier_id' => $request->user()->id ?? null,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return back()->with('success', 'KYC document rejected.');
    }

    /**
     * Approve a settlement report and credit vendor wallet.
     */
    public function approveSettlement(Request $request, $settlementReportId)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($settlementReportId) {
            $settlement = VendorSettlementReport::with('vendor')->lockForUpdate()->findOrFail($settlementReportId);

            if (!in_array($settlement->status, ['submitted', 'approved'], true)) {
                // allow idempotent approval if already approved/paid is handled elsewhere
            }

            $settlement->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $request->user()->id ?? null,
            ]);

            $wallet = $settlement->vendor->wallet()->first();
            if (!$wallet) {
                // wallet is unique per vendor
                $wallet = $settlement->vendor->wallet()->create(['balance' => 0]);
            }

            // credit net payout
            $wallet->increment('balance', $settlement->net_payout_amount);

            $settlement->vendor->wallet()->first()->transactions()->create([
                'type' => 'credit',
                'source' => 'settlement_approved',
                'amount' => $settlement->net_payout_amount,
                'meta' => [
                    'settlement_report_id' => $settlement->id,
                    'period_start' => (string) $settlement->period_start,
                    'period_end' => (string) $settlement->period_end,
                    'note' => $request->input('note'),
                ],
            ]);
        });

        return back()->with('success', 'Settlement approved and wallet credited.');
    }

    /**
     * Store a newly created vendor (admin-created onboarding).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'shop_name' => ['required', 'string', 'max:255'],
            'vendor_code' => ['nullable', 'string', 'max:255', 'unique:vendors,vendor_code'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'vendor',
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'shop_name' => $validated['shop_name'],
            'vendor_code' => $validated['vendor_code'] ?? null,
            'approval_status' => 'pending',
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.vendors.edit', $vendor)->with('success', 'Vendor created (pending approval).');
    }

    // Resource methods (create/edit/update/destroy) are intentionally minimal for now.
}
