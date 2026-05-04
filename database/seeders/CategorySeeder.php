<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Eletronicos',
                'slug' => 'eletronicos',
                'description' => 'Dispositivos e acessorios para uso diario.',
            ],
            [
                'name' => 'Casa e Decoracao',
                'slug' => 'casa-e-decoracao',
                'description' => 'Itens para conforto e estilo do ambiente.',
            ],
            [
                'name' => 'Esporte',
                'slug' => 'esporte',
                'description' => 'Produtos para treino, saude e desempenho.',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'is_active' => 1,
                ]
            );
        }
    }
}
