<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display wishlist page
     */
    public function index()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product', 'product.images')->paginate(12);
        return view('front.wishlist', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = auth()->id();

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if (!$exists) {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'wishlist_count' => auth()->user()->wishlists()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Product added to wishlist!');
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = auth()->id();

        Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'wishlist_count' => auth()->user()->wishlists()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Product removed from wishlist!');
    }

    /**
     * Move product from wishlist to cart
     */
    public function moveToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = auth()->id();

        // Remove from wishlist
        Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        // Add to cart via session
        $cart = session()->get('shopping_cart', []);
        $key = (string)$productId;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += 1;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => null,
                'quantity' => 1,
                'added_at' => now()->toDateTimeString(),
            ];
        }

        session()->put('shopping_cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product moved to cart',
                'wishlist_count' => auth()->user()->wishlists()->count(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product moved to cart!');
    }

    /**
     * Get wishlist count for header
     */
    public function getCount()
    {
        $count = auth()->user()->wishlists()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $inWishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        return response()->json(['in_wishlist' => $inWishlist]);
    }
}

