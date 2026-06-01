<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bebidas',     'icon' => '🥤', 'sort_order' => 1, 'is_produced' => false],
            ['name' => 'Galletitas',  'icon' => '🍪', 'sort_order' => 2, 'is_produced' => false],
            ['name' => 'Golosinas',   'icon' => '🍬', 'sort_order' => 3, 'is_produced' => false],
            ['name' => 'Snacks',      'icon' => '🥨', 'sort_order' => 4, 'is_produced' => false],
            ['name' => 'Lácteos',     'icon' => '🥛', 'sort_order' => 5, 'is_produced' => false],
            ['name' => 'Elaborados',  'icon' => '🍕', 'sort_order' => 99, 'is_produced' => true],
        ];

        foreach ($categories as $cat) {
            ProductCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // ── Productos de ejemplo ──────────────────────────────
        $products = [
            // Bebidas
            ['category' => 'Bebidas', 'name' => 'Agua mineral 500ml', 'price' => 800,  'stock' => 40],
            ['category' => 'Bebidas', 'name' => 'Coca-Cola 500ml',    'price' => 1500, 'stock' => 30],
            ['category' => 'Bebidas', 'name' => 'Jugo Cepita 250ml',  'price' => 1200, 'stock' => 25],
            ['category' => 'Bebidas', 'name' => 'Powerade 500ml',     'price' => 1800, 'stock' => 15],

            // Galletitas
            ['category' => 'Galletitas', 'name' => 'Oreo x3',             'price' => 500,  'stock' => 50],
            ['category' => 'Galletitas', 'name' => 'Pepitos choco',       'price' => 600,  'stock' => 35],
            ['category' => 'Galletitas', 'name' => 'Criollitas paq.',     'price' => 900,  'stock' => 20],

            // Golosinas
            ['category' => 'Golosinas', 'name' => 'Alfajor Havanna',      'price' => 1400, 'stock' => 25],
            ['category' => 'Golosinas', 'name' => 'Barra de cereal',      'price' => 700,  'stock' => 40],
            ['category' => 'Golosinas', 'name' => 'Chicle Beldent x3',    'price' => 400,  'stock' => 60],
            ['category' => 'Golosinas', 'name' => 'Caramelos Flynn Paff', 'price' => 200,  'stock' => 80],

            // Snacks
            ['category' => 'Snacks', 'name' => 'Papas Lays clásicas',   'price' => 1600, 'stock' => 20],
            ['category' => 'Snacks', 'name' => 'Palitos salados',       'price' => 500,  'stock' => 30],

            // Lácteos
            ['category' => 'Lácteos', 'name' => 'Yogur bebible',  'price' => 1100, 'stock' => 15],
            ['category' => 'Lácteos', 'name' => 'Chocolatada 200ml', 'price' => 900, 'stock' => 20],

            // Elaborados (producidos a partir de pedidos)
            ['category' => 'Elaborados', 'name' => 'Sándwich de miga x3', 'price' => 2500, 'stock' => 10],
            ['category' => 'Elaborados', 'name' => 'Empanadas x3',        'price' => 3000, 'stock' => 8],
            ['category' => 'Elaborados', 'name' => 'Tarta de jamón y queso', 'price' => 3500, 'stock' => 5],
        ];

        foreach ($products as $p) {
            $cat = ProductCategory::where('name', $p['category'])->first();
            if ($cat) {
                $tipo = ($cat->name === 'Elaborados') ? 'elaborado' : 'reventa';
                $price = ($tipo === 'elaborado') ? 0 : round($p['price'] * 0.7, 2); // costo estimado del 70%
                $sale_price = $p['price'];

                Product::updateOrCreate(
                    ['name' => $p['name'], 'category_id' => $cat->id],
                    [
                        'tipo'        => $tipo,
                        'price'       => $price,
                        'sale_price'  => $sale_price,
                        'stock'       => $p['stock'],
                        'is_active'   => true
                    ]
                );
            }
        }
    }
}
