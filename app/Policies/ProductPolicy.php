<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view products.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor', 'customer']);
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor', 'customer']);
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor']);
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user): bool
    {
        return in_array($user->role, ['admin', 'vendor']);
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the product.
     */
    public function restore(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the product.
     */
    public function forceDelete(User $user): bool
    {
        return $user->role === 'admin';
    }
}
