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
        $catalog = [
            'ferramentas' => [
                [
                    'name' => 'Furadeira de Impacto 750W',
                    'sku' => 'FER-001',
                    'price' => 289.90,
                    'stock' => 30,
                    'description' => 'Furadeira com velocidade variável e função impacto para concreto, madeira e alvenaria.',
                    'image' => 'https://picsum.photos/seed/furadeira-impacto-750w/1600/900',
                ],
                [
                    'name' => 'Trena 5m Profissional',
                    'sku' => 'FER-002',
                    'price' => 24.90,
                    'stock' => 80,
                    'description' => 'Trena profissional de 5 metros com trava, fita resistente e corpo emborrachado.',
                    'image' => 'https://picsum.photos/seed/trena-5m-profissional/1600/900',
                ],
                [
                    'name' => 'Marreta Oitavada 1kg',
                    'sku' => 'FER-003',
                    'price' => 49.90,
                    'stock' => 45,
                    'description' => 'Marreta oitavada com cabo em madeira para demolições leves e ajustes em obra.',
                    'image' => 'https://picsum.photos/seed/marreta-oitavada-1kg/1600/900',
                ],
            ],
            'eletrica' => [
                [
                    'name' => 'Cabo Flexível 2,5mm 100m',
                    'sku' => 'ELE-001',
                    'price' => 319.00,
                    'stock' => 35,
                    'description' => 'Cabo flexível de cobre para circuitos de tomadas e instalações elétricas residenciais.',
                    'image' => 'https://picsum.photos/seed/cabo-flexivel-25mm/1600/900',
                ],
                [
                    'name' => 'Disjuntor Bipolar 32A',
                    'sku' => 'ELE-002',
                    'price' => 47.80,
                    'stock' => 90,
                    'description' => 'Disjuntor termomagnético bipolar para proteção de circuitos elétricos.',
                    'image' => 'https://picsum.photos/seed/disjuntor-bipolar-32a/1600/900',
                ],
                [
                    'name' => 'Tomada 2P+T 10A Branca',
                    'sku' => 'ELE-003',
                    'price' => 13.90,
                    'stock' => 140,
                    'description' => 'Tomada padrão brasileiro 2P+T de 10A com acabamento branco.',
                    'image' => 'https://picsum.photos/seed/tomada-2pt-10a-branca/1600/900',
                ],
            ],
            'hidraulica' => [
                [
                    'name' => 'Cano PVC 100mm',
                    'sku' => 'HID-001',
                    'price' => 68.90,
                    'stock' => 60,
                    'description' => 'Cano PVC de 100mm para redes de esgoto e instalações hidráulicas prediais.',
                    'image' => 'https://picsum.photos/seed/cano-pvc-100mm/1600/900',
                ],
                [
                    'name' => 'Torneira de Parede Cromada',
                    'sku' => 'HID-002',
                    'price' => 54.90,
                    'stock' => 55,
                    'description' => 'Torneira cromada de parede para áreas de serviço, tanques e pontos externos.',
                    'image' => 'https://picsum.photos/seed/torneira-parede-cromada/1600/900',
                ],
                [
                    'name' => 'Joelho PVC Soldável 25mm',
                    'sku' => 'HID-003',
                    'price' => 2.90,
                    'stock' => 250,
                    'description' => 'Joelho PVC soldável de 25mm para mudança de direção em tubulações de água fria.',
                    'image' => 'https://picsum.photos/seed/joelho-pvc-soldavel-25mm/1600/900',
                ],
            ],
            'materiais-basicos' => [
                [
                    'name' => 'Cimento CP-II 50kg',
                    'sku' => 'MAT-001',
                    'price' => 42.90,
                    'stock' => 120,
                    'description' => 'Cimento CP-II de uso geral para fundações, contrapiso, reboco e alvenaria.',
                    'image' => 'https://picsum.photos/seed/cimento-cp2-50kg/1600/900',
                ],
                [
                    'name' => 'Argamassa AC-II 20kg',
                    'sku' => 'MAT-002',
                    'price' => 28.50,
                    'stock' => 95,
                    'description' => 'Argamassa colante AC-II para assentamento de pisos e revestimentos internos e externos.',
                    'image' => 'https://picsum.photos/seed/argamassa-ac2-20kg/1600/900',
                ],
                [
                    'name' => 'Tijolo Cerâmico 6 Furos',
                    'sku' => 'MAT-003',
                    'price' => 1.39,
                    'stock' => 2500,
                    'description' => 'Tijolo cerâmico de 6 furos para vedação em obras residenciais e comerciais.',
                    'image' => 'https://picsum.photos/seed/tijolo-ceramico-6-furos/1600/900',
                ],
            ],
            'tintas-e-acabamentos' => [
                [
                    'name' => 'Tinta Acrílica Branca 18L',
                    'sku' => 'TIN-001',
                    'price' => 229.00,
                    'stock' => 40,
                    'description' => 'Tinta acrílica branca para paredes internas e externas com boa cobertura.',
                    'image' => 'https://picsum.photos/seed/tinta-acrilica-branca-18l/1600/900',
                ],
                [
                    'name' => 'Massa Corrida PVA 25kg',
                    'sku' => 'TIN-002',
                    'price' => 74.90,
                    'stock' => 50,
                    'description' => 'Massa corrida PVA para nivelamento e correção de paredes internas.',
                    'image' => 'https://picsum.photos/seed/massa-corrida-pva-25kg/1600/900',
                ],
                [
                    'name' => 'Verniz Marítimo Brilhante 3,6L',
                    'sku' => 'TIN-003',
                    'price' => 119.90,
                    'stock' => 28,
                    'description' => 'Verniz marítimo brilhante para proteção e acabamento de superfícies de madeira.',
                    'image' => 'https://picsum.photos/seed/verniz-maritimo-brilhante/1600/900',
                ],
            ],
            'seguranca' => [
                [
                    'name' => 'Capacete de Segurança Branco',
                    'sku' => 'SEG-001',
                    'price' => 29.90,
                    'stock' => 100,
                    'description' => 'Capacete de segurança branco com ajuste para proteção em obras e reformas.',
                    'image' => 'https://picsum.photos/seed/capacete-seguranca-branco/1600/900',
                ],
                [
                    'name' => 'Luva de Raspa Cano Curto',
                    'sku' => 'SEG-002',
                    'price' => 18.90,
                    'stock' => 120,
                    'description' => 'Luva de raspa com cano curto para proteção das mãos em serviços pesados.',
                    'image' => 'https://picsum.photos/seed/luva-raspa-cano-curto/1600/900',
                ],
                [
                    'name' => 'Óculos de Proteção Transparente',
                    'sku' => 'SEG-003',
                    'price' => 12.90,
                    'stock' => 150,
                    'description' => 'Óculos de proteção transparente para trabalhos com poeira, impacto e respingos.',
                    'image' => 'https://picsum.photos/seed/oculos-protecao-transparente/1600/900',
                ],
            ],
            'fixacao' => [
                [
                    'name' => 'Parafuso Philips 4,0x40mm 100un',
                    'sku' => 'FIX-001',
                    'price' => 19.90,
                    'stock' => 85,
                    'description' => 'Pacote com 100 parafusos Philips 4,0x40mm para madeira e uso geral.',
                    'image' => 'https://picsum.photos/seed/parafuso-philips-40x40/1600/900',
                ],
                [
                    'name' => 'Bucha Plástica S8 100un',
                    'sku' => 'FIX-002',
                    'price' => 14.90,
                    'stock' => 110,
                    'description' => 'Pacote com 100 buchas plásticas S8 para fixações em parede de alvenaria.',
                    'image' => 'https://picsum.photos/seed/bucha-plastica-s8/1600/900',
                ],
                [
                    'name' => 'Prego com Cabeça 17x27 1kg',
                    'sku' => 'FIX-003',
                    'price' => 22.90,
                    'stock' => 75,
                    'description' => 'Prego com cabeça 17x27 em embalagem de 1kg para estruturas e formas de madeira.',
                    'image' => 'https://picsum.photos/seed/prego-cabeca-17x27/1600/900',
                ],
            ],
        ];

        foreach ($catalog as $categorySlug => $products) {
            $category = Category::query()->where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($products as $payload) {
                $slug = Str::slug($payload['name']);

                $product = Product::query()->updateOrCreate(
                    ['sku' => $payload['sku']],
                    [
                        'category_id' => $category->id,
                        'name' => $payload['name'],
                        'slug' => $slug,
                        'description' => $payload['description'],
                        'price' => $payload['price'],
                        'stock' => $payload['stock'],
                        'is_active' => 1,
                        'image_url' => $payload['image'],
                    ]
                );

                $firstPayload = [
                    'url' => $payload['image'],
                    'alt_text' => $payload['name'],
                ];

                if (Schema::hasColumn('product_images', 'image_url')) {
                    $firstPayload['image_url'] = $payload['image'];
                }

                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'order' => 1,
                    ],
                    $firstPayload
                );

                ProductImage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'order' => 2,
                    ],
                    [
                        'url' => $payload['image'],
                        'alt_text' => $payload['name'].' - detalhe',
                        ...(Schema::hasColumn('product_images', 'image_url') ? ['image_url' => $payload['image']] : []),
                    ]
                );
            }
        }
    }
}
