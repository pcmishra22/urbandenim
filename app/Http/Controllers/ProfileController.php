<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancelledAdminMail;
use App\Mail\OrderCancelledMail;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller implements HasMiddleware
{
    /**
     * Laravel 11+ middleware via interface (replaces constructor middleware).
     */
    public static function middleware(): array
    {
        return [new Middleware('auth')];
    }

    /* ── Dashboard ───────────────────────────────────────── */
    public function dashboard()
    {
        $user          = auth()->user();
        $orderCount    = $user->orders()->count();
        $wishlistCount = $user->wishlists()->count();
        $orders        = $user->orders()->with('products')->latest()->paginate(5);
        return view('front.profile.dashboard', compact('user','orderCount','wishlistCount','orders'));
    }

    /* ── Personal Info ───────────────────────────────────── */
    public function editPersonalInfo()
    {
        return view('front.profile.personal-info', ['user' => auth()->user()]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:20']);
        auth()->user()->update($request->only('name','phone'));
        return redirect()->route('profile.dashboard')->with('success','Personal information updated!');
    }

    /* ── Password ────────────────────────────────────────── */
    public function changePassword() { return view('front.profile.change-password'); }

    public function updatePassword(Request $request)
    {
        $request->validate(['current_password'=>'required','new_password'=>'required|string|min:8|confirmed']);
        if (!Hash::check($request->current_password, auth()->user()->password))
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        auth()->user()->update(['password' => Hash::make($request->new_password)]);
        return redirect()->route('profile.dashboard')->with('success','Password changed!');
    }

    /* ── Addresses ───────────────────────────────────────── */
    public function addresses()
    {
        return view('front.profile.addresses', ['user'=>auth()->user(),'addresses'=>auth()->user()->addresses()->get()]);
    }
    public function createAddress() { return view('front.profile.address-form'); }

    public function storeAddress(Request $request)
    {
        $request->validate(['address_type'=>'required|in:billing,shipping','full_name'=>'required|string|max:255',
            'phone'=>'required|string|max:20','street'=>'required|string|max:255','city'=>'required|string|max:255',
            'state'=>'required|string|max:255','postal_code'=>'required|string|max:20','country'=>'required|string|max:255']);
        auth()->user()->addresses()->create($request->all());
        return redirect()->route('profile.addresses')->with('success','Address added!');
    }

    public function editAddress($id)
    {
        return view('front.profile.address-form', ['address' => auth()->user()->addresses()->findOrFail($id)]);
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate(['address_type'=>'required|in:billing,shipping','full_name'=>'required|string|max:255',
            'phone'=>'required|string|max:20','street'=>'required|string|max:255','city'=>'required|string|max:255',
            'state'=>'required|string|max:255','postal_code'=>'required|string|max:20','country'=>'required|string|max:255']);
        auth()->user()->addresses()->findOrFail($id)->update($request->all());
        return redirect()->route('profile.addresses')->with('success','Address updated!');
    }

    public function deleteAddress($id)
    {
        auth()->user()->addresses()->findOrFail($id)->delete();
        return redirect()->route('profile.addresses')->with('success','Address deleted.');
    }

    /* ── Orders ──────────────────────────────────────────── */
    public function orders()
    {
        $orders = auth()->user()->orders()->with('products.images','shipments')->latest()->paginate(10);
        return view('front.profile.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = auth()->user()->orders()->with('products.images','shipments')->findOrFail($id);
        return view('front.profile.order-details', compact('order'));
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $order = auth()->user()->orders()->with('products','user')->findOrFail($id);
        if (!in_array($order->status, ['pending','processing']))
            return redirect()->route('profile.order-details',$id)
                ->with('error','This order cannot be cancelled — it is already '.$order->status.'.');
        $reason = $request->input('reason','');
        $order->update(['status' => 'cancelled']);
        try { Mail::to($order->user->email)->send(new OrderCancelledMail($order,$reason)); }
        catch (\Throwable $e) { Log::warning('Cancel mail user',['e'=>$e->getMessage()]); }
        try {
            $adminEmails = User::where('role','admin')->pluck('email')->toArray();
            if ($adminEmails) Mail::to($adminEmails)->send(new OrderCancelledAdminMail($order,$reason));
        } catch (\Throwable $e) { Log::warning('Cancel mail admin',['e'=>$e->getMessage()]); }
        return redirect()->route('profile.order-details',$id)
            ->with('success',"Order #{$order->id} cancelled. Confirmation email sent.");
    }

    /**
     * Re-order: add all items from a past order back to the cart.
     */
    public function reorder($id)
    {
        $order = auth()->user()->orders()->with('products')->findOrFail($id);
        $cart  = session()->get('cart', []);
        foreach ($order->products as $product) {
            if (!$product->is_active) continue;
            $pid = $product->id;
            $qty = $product->pivot->quantity;
            if (isset($cart[$pid])) {
                $cart[$pid]['quantity'] += $qty;
            } else {
                $cart[$pid] = [
                    'product_id' => $pid,
                    'quantity'   => $qty,
                    'price'      => $product->sale_price ?? $product->price,
                ];
            }
        }
        session()->put('cart', $cart);
        return redirect()->route('cart.index')
            ->with('success', "Items from Order #{$order->id} added to your cart.");
    }

    /* ── Reviews ─────────────────────────────────────────── */
    public function reviews()
    {
        $reviews = Review::where('user_id', auth()->id())
            ->with('product.images')->latest()->paginate(10);
        return view('front.profile.reviews', compact('reviews'));
    }

    /* ── Wishlist ─────────────────────────────────────────── */
    public function wishlist()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product','product.images')->paginate(12);
        return view('front.profile.wishlist', compact('wishlistItems'));
    }
}
