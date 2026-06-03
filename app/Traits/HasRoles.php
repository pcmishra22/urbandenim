<?php

namespace App\Traits;

trait HasRoles
{
    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a vendor.
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role, $roles);
    }

    /**
     * Check if user can manage products (admin or vendor).
     */
    public function canManageProducts(): bool
    {
        return in_array($this->role, ['admin', 'vendor']);
    }

    /**
     * Check if user can shop products (all roles can).
     */
    public function canShop(): bool
    {
        return true;
    }

    /**
     * Check if user can place orders (customer or admin).
     */
    public function canPlaceOrders(): bool
    {
        return in_array($this->role, ['admin', 'customer']);
    }

    /**
     * Check if user can manage inventory (vendor or admin).
     */
    public function canManageInventory(): bool
    {
        return in_array($this->role, ['admin', 'vendor']);
    }
}
