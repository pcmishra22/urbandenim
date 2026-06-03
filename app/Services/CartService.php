<?php

namespace App\Services;

use Illuminate\Session\SessionManager;

class CartService
{
    protected $session;
    protected $cartKey = 'shopping_cart';

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    /**
     * Get all items in cart
     */
    public function getItems()
    {
        return $this->session->get($this->cartKey, []);
    }

    /**
     * Add product to cart
     */
    public function addItem($productId, $quantity = 1, $variantId = null)
    {
        $cart = $this->getItems();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'added_at' => now()->toDateTimeString(),
            ];
        }

        $this->session->put($this->cartKey, $cart);
        return $cart;
    }

    /**
     * Update quantity of item in cart
     */
    public function updateQuantity($productId, $quantity, $variantId = null)
    {
        $cart = $this->getItems();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
            $this->session->put($this->cartKey, $cart);
        }

        return $cart;
    }

    /**
     * Remove item from cart
     */
    public function removeItem($productId, $variantId = null)
    {
        $cart = $this->getItems();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        $this->session->put($this->cartKey, $cart);
        return $cart;
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $this->session->forget($this->cartKey);
    }

    /**
     * Get item count
     */
    public function getCount()
    {
        $items = $this->getItems();
        $count = 0;

        foreach ($items as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotal()
    {
        $items = $this->getItems();
        $subtotal = 0;

        foreach ($items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if ($product) {
                $unitPrice = $product->sale_price ?? $product->price;
                $subtotal += $unitPrice * $item['quantity'];
            }
        }

        return $subtotal;
    }

    /**
     * Get cart with full product details
     */
    public function getCartWithProducts()
    {
        $items = $this->getItems();
        $cartItems = [];

        foreach ($items as $key => $item) {
            $product = \App\Models\Product::with('images', 'category', 'brand')->find($item['product_id']);

            if ($product) {
                $cartItems[$key] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'variant_id' => $item['variant_id'],
                    'subtotal' => ($product->sale_price ?? $product->price) * $item['quantity'],
                ];
            }
        }

        return $cartItems;
    }

    /**
     * Generate unique key for cart item
     */
    protected function generateKey($productId, $variantId = null)
    {
        return $variantId ? "{$productId}_{$variantId}" : (string)$productId;
    }
}
