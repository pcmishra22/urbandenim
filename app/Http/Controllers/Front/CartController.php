<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display shopping cart page
     */
    public function index()
    {
        $cartItems = $this->cartService->getCartWithProducts();
        $subtotal = $this->cartService->getSubtotal();

        return view('front.cart', compact('cartItems', 'subtotal'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1|max:100',
            'variant_id' => 'sometimes|integer',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $variantId = $request->variant_id ?? null;

        $this->cartService->addItem($productId, $quantity, $variantId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => $this->cartService->getCount(),
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:100',
            'variant_id' => 'sometimes|integer',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;
        $variantId = $request->variant_id ?? null;

        $this->cartService->updateQuantity($productId, $quantity, $variantId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => $this->cartService->getCount(),
                'subtotal' => $this->cartService->getSubtotal(),
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'sometimes|integer',
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id ?? null;

        $this->cartService->removeItem($productId, $variantId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->cartService->getCount(),
                'subtotal' => $this->cartService->getSubtotal(),
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $this->cartService->clear();

        return redirect()->back()->with('success', 'Cart cleared!');
    }

    /**
     * Get cart count (for AJAX)
     */
    public function getCount()
    {
        return response()->json([
            'count' => $this->cartService->getCount(),
        ]);
    }
}
