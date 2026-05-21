<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Proveedor;
use App\Models\Ingreso;
use App\Models\Egreso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KioscoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpieza de tablas de negocio
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        CompraItem::truncate();
        Compra::truncate();
        Product::truncate();
        ProductCategory::truncate();
        Proveedor::truncate();
        Ingreso::truncate();
        Egreso::truncate();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 2. Categorías de Producto
        $categories = [
            ['name' => 'Bebidas',          'icon' => '🥤', 'sort_order' => 1,  'is_produced' => false],
            ['name' => 'Galletitas',       'icon' => '🍪', 'sort_order' => 2,  'is_produced' => false],
            ['name' => 'Golosinas',        'icon' => '🍬', 'sort_order' => 3,  'is_produced' => false],
            ['name' => 'Snacks',           'icon' => '🥨', 'sort_order' => 4,  'is_produced' => false],
            ['name' => 'Lácteos',          'icon' => '🥛', 'sort_order' => 5,  'is_produced' => false],
            ['name' => 'Insumos Cocina',   'icon' => '🍳', 'sort_order' => 6,  'is_produced' => false],
            ['name' => 'Elaborados',       'icon' => '🍕', 'sort_order' => 99, 'is_produced' => true],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['name']] = ProductCategory::create($cat);
        }

        // 3. Proveedores
        $proveedores = [
            [
                'nombre'    => 'Distribuidora Coca-Cola',
                'contacto'  => 'Hernán K.',
                'telefono'  => '3434-555444',
                'correo'    => 'soporte@cocacola-uner.com',
                'direccion' => 'Ruta 14 Km 250, Concordia',
            ],
            [
                'nombre'    => 'Distribuidora Arcor',
                'contacto'  => 'Marcos L.',
                'telefono'  => '3434-555111',
                'correo'    => 'arcor@distribuidora.com',
                'direccion' => 'Av. San Lorenzo 1820, Concordia',
            ],
            [
                'nombre'    => 'Fiambrería El Trébol',
                'contacto'  => 'Sofía M.',
                'telefono'  => '3434-555222',
                'correo'    => 'ventas@eltrebol.com',
                'direccion' => 'Urquiza 421, Concordia',
            ],
            [
                'nombre'    => 'Panificadora Concordia',
                'contacto'  => 'Roberto G.',
                'telefono'  => '3434-555333',
                'correo'    => 'pedidos@panificadoraconcordia.com',
                'direccion' => 'Las Heras 987, Concordia',
            ],
        ];

        $provModels = [];
        foreach ($proveedores as $prov) {
            $provModels[$prov['nombre']] = Proveedor::create($prov);
        }

        // 4. Productos Base (reventa, insumos y elaborados)
        // Nota: reventa e insumo iniciarán con stock 0 y precio 0. Se poblarán mediante las compras simuladas abajo.
        $productsData = [
            // REVENTA
            [
                'category'    => 'Bebidas',
                'name'        => 'Coca-Cola 500ml',
                'description' => 'Gaseosa refrescante original sabor cola.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Bebidas',
                'name'        => 'Sprite 500ml',
                'description' => 'Gaseosa refrescante sabor lima-limón.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Bebidas',
                'name'        => 'Agua mineral 500ml',
                'description' => 'Agua mineral sin gas baja en sodio.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Galletitas',
                'name'        => 'Oreo paq. triple',
                'description' => 'Galletitas rellenas sabor chocolate.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Galletitas',
                'name'        => 'Criollitas paq. familiar',
                'description' => 'Galletitas de agua saladas clásicas.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Golosinas',
                'name'        => 'Chicle Beldent menta',
                'description' => 'Chicles sin azúcar sabor menta fresca.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Snacks',
                'name'        => 'Papas Lays clásicas 150g',
                'description' => 'Papas fritas clásicas crujientes.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Lácteos',
                'name'        => 'Chocolatada Cindor 200ml',
                'description' => 'Leche chocolatada premium sabor cacao.',
                'tipo'        => 'reventa',
                'price'       => 0,
                'stock'       => 0,
            ],

            // INSUMOS COCINA
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Harina 000 x1kg',
                'description' => 'Materia prima esencial para preparar pizzas y masas.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Queso mozzarella x1kg',
                'description' => 'Queso mozzarella de alta calidad para derretir.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Salsa de tomate puré 520g',
                'description' => 'Puré de tomate seleccionado para condimento de pizzas.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Jamón cocido feteado x1kg',
                'description' => 'Jamón cocido de primera calidad para sándwiches y empanadas.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Aceite de girasol 1.5L',
                'description' => 'Aceite para frituras y preparación culinaria.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],
            [
                'category'    => 'Insumos Cocina',
                'name'        => 'Huevos maple x30',
                'description' => 'Maple completo de huevos frescos de campo.',
                'tipo'        => 'insumo',
                'price'       => 0,
                'stock'       => 0,
            ],

            // ELABORADOS
            // Estos productos tienen precio establecido manualmente y stock cargado manualmente.
            [
                'category'    => 'Elaborados',
                'name'        => 'Pizza Mozzarella entera',
                'description' => 'Pizza elaborada artesanalmente en la cocina del Kiosco.',
                'tipo'        => 'elaborado',
                'price'       => 3500.00,
                'stock'       => 15, // Unidades ya listas para la venta
            ],
            [
                'category'    => 'Elaborados',
                'name'        => 'Empanada de carne x3',
                'description' => 'Combo de tres empanadas fritas de carne cortada a cuchillo.',
                'tipo'        => 'elaborado',
                'price'       => 2400.00,
                'stock'       => 20,
            ],
            [
                'category'    => 'Elaborados',
                'name'        => 'Sándwich Jamón y Queso gigante',
                'description' => 'Sándwich en pan baguetín con jamón cocido, queso mozzarella y lechuga.',
                'tipo'        => 'elaborado',
                'price'       => 1800.00,
                'stock'       => 12,
            ],
            [
                'category'    => 'Elaborados',
                'name'        => 'Ensalada de frutas fresca',
                'description' => 'Ensalada elaborada en el día con frutas de estación seleccionadas.',
                'tipo'        => 'elaborado',
                'price'       => 1200.00,
                'stock'       => 8,
            ],
        ];

        $productModels = [];
        foreach ($productsData as $p) {
            $cat = $catModels[$p['category']];
            $productModels[$p['name']] = Product::create([
                'category_id' => $cat->id,
                'name'        => $p['name'],
                'description' => $p['description'],
                'tipo'        => $p['tipo'],
                'price'       => $p['price'],
                'stock'       => $p['stock'],
                'is_active'   => true,
            ]);
        }

        // 5. Compras de Simulación (Para dotar de stock e historial de precios a los de reventa/insumos)
        
        // Compra 1: Bebidas a Distribuidora Coca-Cola
        $c1 = Compra::create([
            'fecha'         => '2026-05-10',
            'total'         => 66500.00,
            'observaciones' => 'Abastecimiento de gaseosas para Kiosco escolar.',
        ]);
        
        $itemsC1 = [
            ['name' => 'Coca-Cola 500ml',      'cant' => 30,  'precio' => 1200.00, 'tipo' => 'reventa'],
            ['name' => 'Sprite 500ml',         'cant' => 20,  'precio' => 1100.00, 'tipo' => 'reventa'],
            ['name' => 'Agua mineral 500ml',   'cant' => 15,  'precio' => 600.00,  'tipo' => 'reventa'],
        ];
        
        foreach ($itemsC1 as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create([
                'compra_id'       => $c1->id,
                'product_id'      => $prod->id,
                'producto_nombre' => $prod->name,
                'tipo_producto'   => $i['tipo'],
                'cantidad'        => $i['cant'],
                'precio_unitario' => $i['precio'],
            ]);
            
            // Actualizar stock y precio
            $prod->stock += $i['cant'];
            $prod->price  = $i['precio'] * 1.30; // 30% margen de ganancia para la reventa
            $prod->save();
        }

        // Compra 2: Golosinas y Galletitas a Distribuidora Arcor
        $c2 = Compra::create([
            'fecha'         => '2026-05-15',
            'total'         => 35500.00,
            'observaciones' => 'Compra de galletas de agua, Oreos y chicles.',
        ]);

        $itemsC2 = [
            ['name' => 'Oreo paq. triple',            'cant' => 25,  'precio' => 500.00,  'tipo' => 'reventa'],
            ['name' => 'Criollitas paq. familiar',    'cant' => 15,  'precio' => 800.00,  'tipo' => 'reventa'],
            ['name' => 'Chicle Beldent menta',        'cant' => 40,  'precio' => 200.00,  'tipo' => 'reventa'],
            ['name' => 'Papas Lays clásicas 150g',    'cant' => 10,  'precio' => 1300.00, 'tipo' => 'reventa'],
        ];

        foreach ($itemsC2 as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create([
                'compra_id'       => $c2->id,
                'product_id'      => $prod->id,
                'producto_nombre' => $prod->name,
                'tipo_producto'   => $i['tipo'],
                'cantidad'        => $i['cant'],
                'precio_unitario' => $i['precio'],
            ]);

            // Actualizar stock y precio
            $prod->stock += $i['cant'];
            $prod->price  = $i['precio'] * 1.35; // 35% de ganancia en snacks/golosinas
            $prod->save();
        }

        // Compra 3: Insumos de fiambrería a Fiambrería El Trébol
        $c3 = Compra::create([
            'fecha'         => '2026-05-18',
            'total'         => 54900.00,
            'observaciones' => 'Insumos de fiambres y quesos para sándwiches y pizzas.',
        ]);

        $itemsC3 = [
            ['name' => 'Queso mozzarella x1kg',       'cant' => 8,   'precio' => 4500.00, 'tipo' => 'insumo'],
            ['name' => 'Jamón cocido feteado x1kg',    'cant' => 4,   'precio' => 3800.00, 'tipo' => 'insumo'],
            ['name' => 'Salsa de tomate puré 520g',    'cant' => 10,  'precio' => 370.00,  'tipo' => 'insumo'],
        ];

        foreach ($itemsC3 as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create([
                'compra_id'       => $c3->id,
                'product_id'      => $prod->id,
                'producto_nombre' => $prod->name,
                'tipo_producto'   => $i['tipo'],
                'cantidad'        => $i['cant'],
                'precio_unitario' => $i['precio'],
            ]);

            // Nota: los insumos NO acumulan stock en inventario y su precio de venta es irrelevante
            $prod->price = $i['precio'];
            $prod->stock = 0;
            $prod->save();
        }

        // 6. Seed de Ingresos (con algunos vinculados al primer usuario alumno)
        $alumnoUser = User::where('username', 'alumno1')->first();
        $alumnoUserId = $alumnoUser ? $alumnoUser->id : null;

        $ingresos = [
            [
                'fecha'       => '2026-05-10',
                'tipo'        => 'donacion',
                'descripcion' => 'Aporte inicial de la Cooperadora Escolar',
                'monto'       => 150000.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Fondo de inicio para abastecimiento y caja chica.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-15',
                'tipo'        => 'excedente_caja',
                'descripcion' => 'Arqueo de caja diaria - Excedente detectado',
                'monto'       => 2450.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Diferencia a favor detectada al cierre de jornada.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-18',
                'tipo'        => 'subvencion',
                'descripcion' => 'Subvención Centro de Estudiantes',
                'monto'       => 75000.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Aporte para compra de insumos de elaboración culinaria.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-20',
                'tipo'        => 'donacion',
                'descripcion' => 'Donación anónima de docente jubilada',
                'monto'       => 15000.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Colaboración libre para actividades del Kiosco.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-21',
                'tipo'        => 'subvencion',
                'descripcion' => 'Subsidio por actividades extracurriculares (Aprobado)',
                'monto'       => 45000.00,
                'estado'      => 'pendiente',
                'detalle'     => 'Pendiente de acreditación por transferencia bancaria.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-21',
                'tipo'        => 'ingreso_excepcional',
                'descripcion' => 'Venta de cajones plásticos reciclados obsoletos',
                'monto'       => 8500.00,
                'estado'      => 'pendiente',
                'detalle'     => 'Comprador retira y abona mañana por la mañana.',
                'user_id'     => $alumnoUserId,
            ],
        ];

        foreach ($ingresos as $ing) {
            Ingreso::create($ing);
        }

        // 7. Seed de Egresos
        $egresos = [
            [
                'fecha'       => '2026-05-11',
                'tipo'        => 'gasto_operativo',
                'descripcion' => 'Compra urgente de bolsas plásticas y papel envoltura',
                'monto'       => 12400.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Gasto de emergencia en papelera local por quiebre de stock.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-13',
                'tipo'        => 'servicio',
                'descripcion' => 'Abono de Internet Banda Ancha Fibra Óptica',
                'monto'       => 28000.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Pago mensual del servicio para conectividad del Kiosco POS.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-17',
                'tipo'        => 'impuesto',
                'descripcion' => 'Tasa Bromatológica Municipalidad',
                'monto'       => 10500.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Tasa de inspección y habilitación culinaria.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-19',
                'tipo'        => 'pasivo',
                'descripcion' => 'Pago adelanto saldo pendiente Arcor',
                'monto'       => 35000.00,
                'estado'      => 'efectuado',
                'detalle'     => 'Cancelación parcial de cuenta corriente de proveedores.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-21',
                'tipo'        => 'insumos',
                'descripcion' => 'Compra de verduras frescas (tomate, lechuga) en verdulería',
                'monto'       => 7500.00,
                'estado'      => 'pendiente',
                'detalle'     => 'Encargo telefónico, se abona al recibir el pedido.',
                'user_id'     => $alumnoUserId,
            ],
            [
                'fecha'       => '2026-05-21',
                'tipo'        => 'servicio',
                'descripcion' => 'Pago factura del gas envasado de cocina',
                'monto'       => 42000.00,
                'estado'      => 'pendiente',
                'detalle'     => 'Factura emitida, vencimiento el 28 de mayo.',
                'user_id'     => $alumnoUserId,
            ],
        ];

        foreach ($egresos as $egr) {
            Egreso::create($egr);
        }
    }
}
