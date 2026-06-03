<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::orderByDesc('id')->paginate(25);
        return view('admin.couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('admin.couriers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:couriers,code',
            'is_active' => 'nullable|boolean',
        ]);

        Courier::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier created successfully.');
    }

    public function edit(Courier $courier)
    {
        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:couriers,code,' . $courier->id,
            'is_active' => 'nullable|boolean',
        ]);

        $courier->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier updated successfully.');
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier deleted successfully.');
    }
}

