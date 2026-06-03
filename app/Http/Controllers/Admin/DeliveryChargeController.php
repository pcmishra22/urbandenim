<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCharge;
use App\Models\ShippingRule;
use Illuminate\Http\Request;

class DeliveryChargeController extends Controller
{
    public function index()
    {
        $charges = DeliveryCharge::with('shippingRule')
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.delivery_charges.index', compact('charges'));
    }

    public function create()
    {
        $rules = ShippingRule::orderByDesc('id')->get();
        return view('admin.delivery_charges.create', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_rule_id' => 'required|exists:shipping_rules,id',
            'weight_from' => 'required|numeric|min:0',
            'weight_to' => 'required|numeric|min:0',
            'charge_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'is_active' => 'nullable|boolean',
        ]);

        if ((float) $validated['weight_to'] < (float) $validated['weight_from']) {
            return back()->withErrors(['weight_to' => 'weight_to must be >= weight_from'])->withInput();
        }

        DeliveryCharge::create([
            'shipping_rule_id' => (int) $validated['shipping_rule_id'],
            'weight_from' => $validated['weight_from'],
            'weight_to' => $validated['weight_to'],
            'charge_amount' => $validated['charge_amount'],
            'currency' => strtoupper($validated['currency'] ?? 'USD'),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.delivery_charges.index')
            ->with('success', 'Delivery charge created successfully.');
    }

    public function edit(DeliveryCharge $deliveryCharge)
    {
        $rules = ShippingRule::orderByDesc('id')->get();
        return view('admin.delivery_charges.edit', [
            'charge' => $deliveryCharge,
            'rules' => $rules,
        ]);
    }

    public function update(Request $request, DeliveryCharge $deliveryCharge)
    {
        $validated = $request->validate([
            'shipping_rule_id' => 'required|exists:shipping_rules,id',
            'weight_from' => 'required|numeric|min:0',
            'weight_to' => 'required|numeric|min:0',
            'charge_amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'is_active' => 'nullable|boolean',
        ]);

        if ((float) $validated['weight_to'] < (float) $validated['weight_from']) {
            return back()->withErrors(['weight_to' => 'weight_to must be >= weight_from'])->withInput();
        }

        $deliveryCharge->update([
            'shipping_rule_id' => (int) $validated['shipping_rule_id'],
            'weight_from' => $validated['weight_from'],
            'weight_to' => $validated['weight_to'],
            'charge_amount' => $validated['charge_amount'],
            'currency' => strtoupper($validated['currency'] ?? 'USD'),
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.delivery_charges.index')
            ->with('success', 'Delivery charge updated successfully.');
    }

    public function destroy(DeliveryCharge $deliveryCharge)
    {
        $deliveryCharge->delete();

        return redirect()->route('admin.delivery_charges.index')
            ->with('success', 'Delivery charge deleted successfully.');
    }
}

