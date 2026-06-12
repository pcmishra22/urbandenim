<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelledAdminMail;
use App\Mail\OrderCancelledMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $orderCount    = $user->orders()->count();
        $wishlistCount = $user->wishlists()->count();
        return view('front.profile.dashboard', compact('user', 'orderCount', 'wishlistCount'));
    }

    public function editPersonalInfo()
    {
        return view('front.profile.personal-info', ['user' => auth()->user()]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ]);

        auth()->user()->update($request->only('first_name','last_name','phone','date_of_birth'));
        return redirect()->route('profile.dashboard')->with('success', 'Personal information updated!');
    }

    public function changePassword()
    {
        return view('front.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return redirect()->route('profile.dashboard')->with('success', 'Password changed successfully!');
    }

    public function addresses()
    {
        return view('front.profile.addresses', [
            'user'      => auth()->user(),
            'addresses' => auth()->user()->addresses()->get(),
        ]);
    }

    public function createAddress()  { return view('front.profile.address-form'); }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'address_type' => 'required|in:billing,shipping',
            'full_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'street'       => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'state'        => 'required|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'country'      => 'required|string|max:255',
        ]);
        auth()->user()->addresses()->create($request->all());
        return redirect()->route('profile.addresses')->with('success', 'Address added!');
    }

    public function editAddress($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        return view('front.profile.address-form', compact('address'));
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'address_type' => 'required|in:billing,shipping',
            'full_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'street'       => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'state'        => 'required|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'country'      => 'required|string|max:255',
        ]);
        auth()->user()->addresses()->findOrFail($id)->update($request->all());
        return redirect()->route('profile.addresses')->with('success', 'Address updated!');
    }

    public function deleteAddress($id)
    {
        auth()->user()->addresses()->findOrFail($id)->delete();
        return redirect()->route('profile.addresses')->with('success', 'Address deleted.');
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with('products','shipments')->latest()->paginate(10);
        return view('front.profile.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = auth()->user()->orders()->with('products.images','shipments')->findOrFail($id);
        return view('front.profile.order-details', compact('order'));
    }

    /**
     * Cancel an order — only allowed for pending/processing statuses.
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order = auth()->user()->orders()->with('products','user')->findOrFail($id);

        // Only allow cancellation of cancellable statuses
        $cancellable = ['pending', 'processing'];
        if (!in_array($order->status, $cancellable)) {
            return redirect()->route('profile.order-details', $id)
                ->with('error', 'This order cannot be cancelled — it has already been ' . $order->status . '.');
        }

        $reason = $request->input('reason', '');
        $order->update(['status' => 'cancelled']);

        // Email to customer
        try {
            Mail::to($order->user->email)->send(new OrderCancelledMail($order, $reason));
        } catch (\Throwable $e) {
            Log::warning('Order cancellation email to user failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        // Email to all admins
        try {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            if ($adminEmails) {
                Mail::to($adminEmails)->send(new OrderCancelledAdminMail($order, $reason));
            }
        } catch (\Throwable $e) {
            Log::warning('Order cancellation admin email failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('profile.order-details', $id)
            ->with('success', "Order #{$order->id} has been cancelled. You'll receive a confirmation email shortly.");
    }

    public function wishlist()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product','product.images')->paginate(12);
        return view('front.profile.wishlist', compact('wishlistItems'));
    }

    public function reviews()
    {
        $reviews = \App\Models\Review::where('user_id', auth()->id())
            ->with('product', 'product.images')
            ->latest()
            ->paginate(10);

        return view('front.profile.reviews', compact('reviews'));
    }

    public function reorder($id)
    {
        $order = auth()->user()->orders()->with('products')->findOrFail($id);

        $cartService = app(\App\Services\CartService::class);

        foreach ($order->products as $product) {
            $qty = $product->pivot->quantity ?? 1;
            $cartService->add($product->id, $qty);
        }

        return redirect()->route('cart.index')
            ->with('success', 'Items from Order #' . $order->id . ' have been added to your cart.');
    }
}
