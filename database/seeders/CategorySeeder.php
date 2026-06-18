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
                'name' => 'Ferramentas',
                'slug' => 'ferramentas',
                'description' => 'Ferramentas manuais e elétricas para obra',
            ],
            [
                'name' => 'Elétrica',
                'slug' => 'eletrica',
                'description' => 'Fios, disjuntores, tomadas e material elétrico',
            ],
            [
                'name' => 'Hidráulica',
                'slug' => 'hidraulica',
                'description' => 'Tubos, conexões, torneiras e material hidráulico',
            ],
            [
                'name' => 'Materiais Básicos',
                'slug' => 'materiais-basicos',
                'description' => 'Cimento, areia, tijolo, argamassa e agregados',
            ],
            [
                'name' => 'Tintas e Acabamentos',
                'slug' => 'tintas-e-acabamentos',
                'description' => 'Tintas, vernizes, massa corrida e textura',
            ],
            [
                'name' => 'Segurança',
                'slug' => 'seguranca',
                'description' => 'EPIs, capacetes, luvas e equipamentos de proteção',
            ],
            [
                'name' => 'Fixação',
                'slug' => 'fixacao',
                'description' => 'Parafusos, buchas, pregos e produtos de fixação',
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
