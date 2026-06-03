<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerManagementController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function showOrders(User $customer)
    {
        $this->authorizeCustomer($customer);

        $orders = Order::with('products')
            ->where('user_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.customers.orders', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    public function showWallet(User $customer)
    {
        $this->authorizeCustomer($customer);

        $customer->load('wallet');

        return view('admin.customers.wallet', [
            'customer' => $customer,
        ]);
    }

    public function showAddresses(User $customer)
    {
        $this->authorizeCustomer($customer);

        $addresses = $customer->addresses()->latest()->paginate(10);

        return view('admin.customers.addresses', [
            'customer' => $customer,
            'addresses' => $addresses,
        ]);
    }

    public function toggleBlock(User $customer, Request $request)
    {
        $this->authorizeCustomer($customer);

        $action = $request->boolean('block');

        // convention: is_blocked boolean column on users
        $customer->is_blocked = $action;
        $customer->save();

        return redirect()
            ->back()
            ->with('success', $action ? 'Customer blocked successfully.' : 'Customer unblocked successfully.');
    }

    private function authorizeCustomer(User $customer): void
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}

