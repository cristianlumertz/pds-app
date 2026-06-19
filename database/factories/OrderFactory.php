<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'pix',
            'tracking_number' => null,
            'total_amount' => fake()->randomFloat(2, 50, 1000),
        ];
    }
}
