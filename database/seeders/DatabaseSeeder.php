<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\ReturnRequestSeeder;
use Database\Seeders\ReturnRequestItemSeeder;
use Database\Seeders\ReversePickupRequestSeeder;
use Database\Seeders\ExchangeRequestSeeder;
use Database\Seeders\ExchangeRequestItemSeeder;
use Database\Seeders\WalletRefundTransactionSeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\VendorSeeder;
use Database\Seeders\BannerSeeder;
use Database\Seeders\CourierSeeder;
use Database\Seeders\ShippingRuleSeeder;
use Database\Seeders\DeliveryChargeSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call(CategorySeeder::class);

        // Seed brands
        $this->call(BrandSeeder::class);

        // Seed products and variants
        $this->call(ProductSeeder::class);

        // Ensure all products have variants and images
        $this->call(ProductVariantSeeder::class);
        $this->call(ProductImageSeeder::class);

        // Seed CMS content
        $this->call(CmsPageSeeder::class);
        $this->call(FaqSeeder::class);

        // Seed blog content
        $this->call(BlogSeeder::class);

        // Create admin user
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->admin()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);
        }

        // Create vendor users
        if (User::where('role', 'vendor')->count() === 0) {
            User::factory(3)->vendor()->create();
        }

        // Create customer users
        if (User::where('role', 'customer')->count() === 0) {
            User::factory(10)->customer()->create();
        }

        // Create orders
        if (Order::count() === 0) {
            Order::factory(15)->pending()->create();
            Order::factory(10)->processing()->create();
            Order::factory(5)->shipped()->create();
            Order::factory(8)->delivered()->create();
            Order::factory(2)->cancelled()->create();
        }

        // Seed vendors + banners
        $this->call(VendorSeeder::class);
        $this->call(BannerSeeder::class);

        // Seed couriers + shipping/charges (used by reverse pickup + shipments)
        $this->call(CourierSeeder::class);
        $this->call(ShippingRuleSeeder::class);
        $this->call(DeliveryChargeSeeder::class);

        // Seed coupons
        $this->call(CouponSeeder::class);



        // Seed returns schema (run after orders + users exist)
        $this->call(ReturnRequestSeeder::class);
        $this->call(ReturnRequestItemSeeder::class);
        $this->call(ReversePickupRequestSeeder::class);
        $this->call(ExchangeRequestSeeder::class);
        $this->call(ExchangeRequestItemSeeder::class);
        $this->call(WalletRefundTransactionSeeder::class);

    }
}

