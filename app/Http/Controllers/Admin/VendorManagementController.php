<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorKycDocument;
use App\Models\VendorSettlementReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorManagementController extends Controller
{
    /**
     * List all vendors with related data eager-loaded.
     */
    public function index()
    {
        $vendors = Vendor::with([
            'user',
            'kycDocuments',
            'wallet',
            'commissionRule',
            'settlementReports',
        ])->latest()->paginate(15);

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        return view('admin.vendors.create');
    }

    /**
     * Store a newly created vendor (creates User + Vendor profile).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => 'required|string|min:8',
            'shop_name'   => 'required|string|max:255',
            'vendor_code' => 'nullable|string|max:100|unique:vendors,vendor_code',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'vendor',
            ]);

            Vendor::create([
                'user_id'         => $user->id,
                'shop_name'       => $validated['shop_name'],
                'vendor_code'     => $validated['vendor_code'] ?? null,
                'approval_status' => 'pending',
                'is_active'       => false,
            ]);
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully and is pending approval.');
    }

    /**
     * Show a single vendor with full detail (edit view doubles as show).
     */
    public function show(Vendor $vendor)
    {
        $vendor->load([
            'user',
            'kycDocuments',
            'wallet',
            'commissionRule',
            'settlementReports',
        ]);

        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Show the edit form for an existing vendor.
     */
    public function edit(Vendor $vendor)
    {
        $vendor->load([
            'user',
            'kycDocuments',
            'wallet',
            'commissionRule',
            'settlementReports',
        ]);

        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the vendor profile (shop name, vendor code, active status).
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'shop_name'   => 'required|string|max:255',
            'vendor_code' => 'nullable|string|max:100|unique:vendors,vendor_code,' . $vendor->id,
            'is_active'   => 'boolean',
            // User fields
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email,' . $vendor->user_id,
        ]);

        DB::transaction(function () use ($validated, $vendor) {
            $vendor->update([
                'shop_name'   => $validated['shop_name'],
                'vendor_code' => $validated['vendor_code'] ?? null,
                'is_active'   => $request->boolean('is_active'),
            ]);

            $vendor->user?->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);
        });

        return redirect()->route('admin.vendors.edit', $vendor)
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Delete a vendor and their user account.
     */
    public function destroy(Vendor $vendor)
    {
        DB::transaction(function () use ($vendor) {
            $userId = $vendor->user_id;
            $vendor->delete();

            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Approval / Rejection
    // -------------------------------------------------------------------------

    public function approve(Vendor $vendor)
    {
        $vendor->update([
            'approval_status'  => 'approved',
            'rejection_reason' => null,
            'is_active'        => true,
        ]);

        return redirect()->back()->with('success', 'Vendor approved successfully.');
    }

    public function reject(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $vendor->update([
            'approval_status'  => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? 'Not specified',
            'is_active'        => false,
        ]);

        return redirect()->back()->with('success', 'Vendor rejected.');
    }

    // -------------------------------------------------------------------------
    // KYC
    // -------------------------------------------------------------------------

    public function approveKyc(VendorKycDocument $kyc)
    {
        $kyc->update([
            'verification_status' => 'approved',
            'verified_at'         => now(),
            'verifier_id'         => auth()->id(),
            'rejection_reason'    => null,
        ]);

        return redirect()->back()->with('success', 'KYC document approved.');
    }

    public function rejectKyc(Request $request, VendorKycDocument $kyc)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $kyc->update([
            'verification_status' => 'rejected',
            'verified_at'         => now(),
            'verifier_id'         => auth()->id(),
            'rejection_reason'    => $validated['rejection_reason'] ?? 'Invalid document',
        ]);

        return redirect()->back()->with('success', 'KYC document rejected.');
    }

    // -------------------------------------------------------------------------
    // Settlements
    // -------------------------------------------------------------------------

    public function approveSettlement(VendorSettlementReport $settlement)
    {
        $settlement->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Settlement report approved.');
    }
}
