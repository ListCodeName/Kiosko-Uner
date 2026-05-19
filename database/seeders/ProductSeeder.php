<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Coca-Cola 2.25L',         'description' => 'Gaseosa Coca-Cola botella familiar',      'price' => 2800.00, 'stock' => 48],
            ['name' => 'Sprite 1.5L',              'description' => 'Gaseosa Sprite botella mediana',          'price' => 1950.00, 'stock' => 36],
            ['name' => 'Agua Mineral 500ml',       'description' => 'Agua mineral sin gas',                   'price' => 800.00,  'stock' => 120],
            ['name' => 'Alfajor Havanna',          'description' => 'Alfajor de chocolate relleno de dulce',  'price' => 1500.00, 'stock' => 60],
            ['name' => 'Chips Papas Fritas 200g',  'description' => 'Papas fritas sabor natural',             'price' => 1200.00, 'stock' => 0],
            ['name' => 'Jugo Cepita 1L',           'description' => 'Jugo de naranja sin azúcar',             'price' => 1750.00, 'stock' => 24],
            ['name' => 'Galletitas Oreo x9',       'description' => 'Paquete de galletitas Oreo rellenas',    'price' => 950.00,  'stock' => 80],
            ['name' => 'Yogur Activia x4',         'description' => 'Pack de yogur natural bebible',          'price' => 2200.00, 'stock' => 15],
            ['name' => 'Leche La Serenísima 1L',   'description' => 'Leche entera larga vida',                'price' => 1100.00, 'stock' => 72],
            ['name' => 'Cafe Cabrales 500g',       'description' => 'Café molido tradicional',                'price' => 3400.00, 'stock' => 8],
            ['name' => 'Sandwich Mixto',           'description' => 'Sandwich de jamón y queso',              'price' => 1800.00, 'stock' => 12],
            ['name' => 'Empanada de Carne',        'description' => 'Empanada de carne cortada a cuchillo',   'price' => 900.00,  'stock' => 30],
        ];

        foreach ($products as $product) {
            Product::create([...$product, 'is_active' => true]);
        }
    }
}
