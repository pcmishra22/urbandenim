<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /** Any role can browse products. */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor', 'customer']);
    }

    /** Admin can see all. Vendor can only see their own. */
    public function view(User $user, Product $product): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'vendor') {
            return (int) $product->vendor_id === (int) optional($user->vendorProfile)->id;
        }
        return true; // customers can view
    }

    /** Admin and vendor can create. */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor']);
    }

    /** Admin can update any. Vendor only their own. */
    public function update(User $user, Product $product): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'vendor') {
            return (int) $product->vendor_id === (int) optional($user->vendorProfile)->id;
        }
        return false;
    }

    /** Admin can delete any. Vendor only their own. */
    public function delete(User $user, Product $product): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'vendor') {
            return (int) $product->vendor_id === (int) optional($user->vendorProfile)->id;
        }
        return false;
    }

    public function restore(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user): bool
    {
        return $user->role === 'admin';
    }
}
