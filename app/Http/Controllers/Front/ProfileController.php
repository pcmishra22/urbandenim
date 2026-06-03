<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user profile dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $orderCount = $user->orders()->count();
        $wishlistCount = $user->wishlists()->count();

        return view('front.profile.dashboard', compact('user', 'orderCount', 'wishlistCount'));
    }

    /**
     * Display personal information page
     */
    public function editPersonalInfo()
    {
        $user = auth()->user();
        return view('front.profile.personal-info', compact('user'));
    }

    /**
     * Update personal information
     */
    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = auth()->user();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return redirect()->route('profile.dashboard')->with('success', 'Personal information updated successfully!');
    }

    /**
     * Display change password page
     */
    public function changePassword()
    {
        return view('front.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.dashboard')->with('success', 'Password changed successfully!');
    }

    /**
     * Display addresses page
     */
    public function addresses()
    {
        $user = auth()->user();
        $addresses = $user->addresses()->get();

        return view('front.profile.addresses', compact('user', 'addresses'));
    }

    /**
     * Display add address form
     */
    public function createAddress()
    {
        return view('front.profile.address-form');
    }

    /**
     * Store new address
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'address_type' => 'required|in:billing,shipping',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        auth()->user()->addresses()->create($request->all());

        return redirect()->route('profile.addresses')->with('success', 'Address added successfully!');
    }

    /**
     * Display edit address form
     */
    public function editAddress($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        return view('front.profile.address-form', compact('address'));
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'address_type' => 'required|in:billing,shipping',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        $address = auth()->user()->addresses()->findOrFail($id);
        $address->update($request->all());

        return redirect()->route('profile.addresses')->with('success', 'Address updated successfully!');
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        auth()->user()->addresses()->findOrFail($id)->delete();
        return redirect()->route('profile.addresses')->with('success', 'Address deleted successfully!');
    }

    /**
     * Display orders page
     */
    public function orders()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('items', 'shipments')->paginate(10);

        return view('front.profile.orders', compact('orders'));
    }

    /**
     * Display order details
     */
    public function orderDetails($id)
    {
        $order = auth()->user()->orders()->with('items', 'shipments')->findOrFail($id);
        return view('front.profile.order-details', compact('order'));
    }

    /**
     * Display wishlist
     */
    public function wishlist()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product', 'product.images')->paginate(12);
        return view('front.profile.wishlist', compact('wishlistItems'));
    }
}
