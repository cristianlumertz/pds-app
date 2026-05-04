<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::query()->get();

        foreach ($categories as $category) {
            for ($index = 1; $index <= 3; $index++) {
                $name = $category->name.' Produto '.$index;
                $slug = Str::slug($category->slug.'-produto-'.$index);

                $product = Product::query()->updateOrCreate(
                    ['sku' => strtoupper(substr($category->slug, 0, 3)).'-00'.$index],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => $slug,
                        'description' => 'Produto de teste para a categoria '.$category->name.'.',
                        'price' => 49.90 + ($index * 25),
                        'stock' => 10 * $index,
                        'is_active' => 1,
                        'image_url' => 'https://picsum.photos/seed/'.$slug.'/800/600',
                    ]
                );

                $firstUrl = 'https://picsum.photos/seed/'.$slug.'/1200/800';
                $firstPayload = [
                    'url' => $firstUrl,
                    'alt_text' => $name,
                ];
                if (Schema::hasColumn('product_images', 'image_url')) {
                    $firstPayload['image_url'] = $firstUrl;
                }
                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'order' => 1,
                    ],
                    $firstPayload
                );

                $secondUrl = 'https://picsum.photos/seed/'.$slug.'-alt/1200/800';
                $secondPayload = [
                    'url' => $secondUrl,
                    'alt_text' => $name.' - imagem 2',
                ];
                if (Schema::hasColumn('product_images', 'image_url')) {
                    $secondPayload['image_url'] = $secondUrl;
                }
                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'order' => 2,
                    ],
                    $secondPayload
                );
            }
        }
    }
}
