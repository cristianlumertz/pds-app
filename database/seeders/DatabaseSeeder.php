<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@pds.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin12345'),
                'is_admin' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'cliente@pds.local'],
            [
                'name' => 'Cliente Demo',
                'password' => Hash::make('cliente12345'),
                'is_admin' => false,
            ]
        );

        $categories = [
            [
                'name' => 'Eletronicos',
                'slug' => 'eletronicos',
                'description' => 'Dispositivos e acessorios para uso diario.',
            ],
            [
                'name' => 'Casa e Decoracao',
                'slug' => 'casa-e-decoracao',
                'description' => 'Itens para conforto e estilo do seu ambiente.',
            ],
            [
                'name' => 'Esporte',
                'slug' => 'esporte',
                'description' => 'Produtos para treino, saude e desempenho.',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'is_active' => true,
                ]
            );

            Product::query()->updateOrCreate(
                ['sku' => strtoupper(substr($category->slug, 0, 3)).'-001'],
                [
                    'category_id' => $category->id,
                    'name' => "{$category->name} Premium",
                    'slug' => "{$category->slug}-premium",
                    'description' => "Produto destaque da categoria {$category->name}.",
                    'price' => 199.90,
                    'stock' => 25,
                    'is_active' => true,
                ]
            );
        }
    }
}
