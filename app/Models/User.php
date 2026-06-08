<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasRoles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use App\Notifications\UserAlert;
use Illuminate\Support\Facades\Notification;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            if ($user->role === 'customer') {
                $admins = self::where('role', 'admin')->get();
                Notification::send($admins, new UserAlert($user, 'signup'));
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the orders placed by this user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function vendorProfile()
    {
        return $this->hasOne(Vendor::class);
    }

    public function verifiedKycDocuments()
    {
        return $this->hasMany(VendorKycDocument::class, 'verifier_id');
    }

    public function approvedSettlements()
    {
        return $this->hasMany(VendorSettlementReport::class, 'approved_by');
    }

    /**
     * Get the coupons assigned specifically to this user.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get the wishlist items for this user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the addresses for this user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }
}
