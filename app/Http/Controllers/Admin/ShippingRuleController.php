<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRule;
use Illuminate\Http\Request;

class ShippingRuleController extends Controller
{
    public function index()
    {
        $rules = ShippingRule::orderByDesc('id')->paginate(25);
        return view('admin.shipping_rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.shipping_rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|size:2',
            'region' => 'nullable|string|max:255',
            'service_level' => 'nullable|string|max:255',
            'base_days' => 'required|integer|min:0',
            'extra_days' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        ShippingRule::create([
            'country' => strtoupper($validated['country']),
            'region' => $validated['region'] ?? null,
            'service_level' => $validated['service_level'] ?? null,
            'base_days' => $validated['base_days'],
            'extra_days' => $validated['extra_days'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.shipping_rules.index')
            ->with('success', 'Shipping rule created successfully.');
    }

    public function edit(ShippingRule $shippingRule)
    {
        return view('admin.shipping_rules.edit', ['rule' => $shippingRule]);
    }

    public function update(Request $request, ShippingRule $shippingRule)
    {
        $validated = $request->validate([
            'country' => 'required|string|size:2',
            'region' => 'nullable|string|max:255',
            'service_level' => 'nullable|string|max:255',
            'base_days' => 'required|integer|min:0',
            'extra_days' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $shippingRule->update([
            'country' => strtoupper($validated['country']),
            'region' => $validated['region'] ?? null,
            'service_level' => $validated['service_level'] ?? null,
            'base_days' => $validated['base_days'],
            'extra_days' => $validated['extra_days'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.shipping_rules.index')
            ->with('success', 'Shipping rule updated successfully.');
    }

    public function destroy(ShippingRule $shippingRule)
    {
        $shippingRule->delete();

        return redirect()->route('admin.shipping_rules.index')
            ->with('success', 'Shipping rule deleted successfully.');
    }
}

