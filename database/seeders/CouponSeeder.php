<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'OBRA10',
                'description' => '10% de desconto em produtos.',
                'discount_type' => Coupon::TYPE_PERCENTAGE,
                'discount_value' => 10,
            ],
            [
                'code' => 'FRETEGRATIS',
                'description' => 'Frete grátis no pedido.',
                'discount_type' => Coupon::TYPE_FREE_SHIPPING,
                'discount_value' => 0,
            ],
            [
                'code' => 'OBRA5',
                'description' => 'R$ 5,00 de desconto em produtos.',
                'discount_type' => Coupon::TYPE_FIXED,
                'discount_value' => 5,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::query()->updateOrCreate(
                ['code' => $coupon['code']],
                [
                    'description' => $coupon['description'],
                    'discount_type' => $coupon['discount_type'],
                    'discount_value' => $coupon['discount_value'],
                    'min_order_amount' => null,
                    'starts_at' => null,
                    'expires_at' => null,
                    'usage_limit' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
