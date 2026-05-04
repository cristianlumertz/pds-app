<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 10, 2000),
            'stock' => fake()->numberBetween(0, 50),
            'sku' => strtoupper(fake()->bothify('MAT-####')),
            'is_active' => true,
            'image_url' => null,
        ];
    }
}
