<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Egreso;
use App\Models\Ingreso;
use App\Models\Personnel;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              DATOS DEMO – Seed de pruebas completo           ║
 * ╠══════════════════════════════════════════════════════════════╣
 * ║  Limpia TODAS las tablas y recrea el sistema completo con:   ║
 * ║    • 1 Super Administrador                                   ║
 * ║    • 10 Alumnos  (pass = nombre de usuario)                  ║
 * ║    • 3  Profesores                                           ║
 * ║    • 2  Directivos                                           ║
 * ║    • Categorías, Productos, Proveedores, Compras             ║
 * ║    • Ingresos y Egresos de ejemplo                           ║
 * ║                                                              ║
 * ║  ⚠️  NO usar en producción. Solo para desarrollo/pruebas.    ║
 * ║                                                              ║
 * ║  Ejecutar con:                                               ║
 * ║    php artisan db:seed --class=DatosDemoSeeder               ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Deshabilitar restricciones de clave foránea ───────────
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // ── Vaciar TODAS las tablas del sistema ───────────────────
        $tables = [
            'activity_logs',
            'attendances',
            'group_user',
            'group_shifts',
            'groups',
            'order_items',
            'orders',
            'sale_items',
            'sales',
            'egresos',
            'ingresos',
            'compra_items',
            'compras',
            'products',
            'product_categories',
            'proveedores',
            'personnel',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // ── Rehabilitar restricciones de clave foránea ────────────
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // ══════════════════════════════════════════════════════════
        // 1. USUARIOS
        // ══════════════════════════════════════════════════════════

        // Super Administrador
        User::create([
            'name'     => 'Super Admin',
            'username' => 'superadmin',
            'email'    => 'superadmin@kiosko.uner',
            'role'     => 'superadmin',
            'password' => 'superadmin',
        ]);

        // 10 Alumnos
        $alumnos = [
            ['name' => 'Lucas Gómez',       'user' => 'alumno1',  'email' => 'alumno1@kiosko.uner',  'dni' => '42000001', 'tel' => '3434-000001'],
            ['name' => 'Sofía Rodríguez',   'user' => 'alumno2',  'email' => 'alumno2@kiosko.uner',  'dni' => '42000002', 'tel' => '3434-000002'],
            ['name' => 'Mateo Fernández',   'user' => 'alumno3',  'email' => 'alumno3@kiosko.uner',  'dni' => '42000003', 'tel' => '3434-000003'],
            ['name' => 'Valentina Silva',   'user' => 'alumno4',  'email' => 'alumno4@kiosko.uner',  'dni' => '42000004', 'tel' => '3434-000004'],
            ['name' => 'Thiago Díaz',       'user' => 'alumno5',  'email' => 'alumno5@kiosko.uner',  'dni' => '42000005', 'tel' => '3434-000005'],
            ['name' => 'Camila Alvarez',    'user' => 'alumno6',  'email' => 'alumno6@kiosko.uner',  'dni' => '42000006', 'tel' => '3434-000006'],
            ['name' => 'Lautaro Romero',    'user' => 'alumno7',  'email' => 'alumno7@kiosko.uner',  'dni' => '42000007', 'tel' => '3434-000007'],
            ['name' => 'Isabella González', 'user' => 'alumno8',  'email' => 'alumno8@kiosko.uner',  'dni' => '42000008', 'tel' => '3434-000008'],
            ['name' => 'Benjamín Medina',   'user' => 'alumno9',  'email' => 'alumno9@kiosko.uner',  'dni' => '42000009', 'tel' => '3434-000009'],
            ['name' => 'Martina Flores',    'user' => 'alumno10', 'email' => 'alumno10@kiosko.uner', 'dni' => '42000010', 'tel' => '3434-000010'],
        ];

        $alumnoUser = null;
        foreach ($alumnos as $a) {
            $user = User::create([
                'name'     => $a['name'],
                'username' => $a['user'],
                'email'    => $a['email'],
                'role'     => 'alumno',
                'password' => $a['user'],
            ]);

            $parts = explode(' ', $a['name'], 2);
            Personnel::create([
                'dni'      => $a['dni'],
                'nombre'   => $parts[0],
                'apellido' => $parts[1] ?? '',
                'telefono' => $a['tel'],
                'correo'   => $a['email'],
                'user_id'  => $user->id,
            ]);

            if ($alumnoUser === null) {
                $alumnoUser = $user; // Referencia al primero para los ingresos/egresos
            }
        }

        // 3 Profesores
        $profesores = [
            ['name' => 'Carlos Pérez',  'user' => 'profesor1', 'email' => 'profesor1@kiosko.uner', 'dni' => '35000001', 'tel' => '3434-100001'],
            ['name' => 'Ana Martínez',  'user' => 'profesor2', 'email' => 'profesor2@kiosko.uner', 'dni' => '35000002', 'tel' => '3434-100002'],
            ['name' => 'Jorge Sánchez', 'user' => 'profesor3', 'email' => 'profesor3@kiosko.uner', 'dni' => '35000003', 'tel' => '3434-100003'],
        ];

        foreach ($profesores as $p) {
            $user = User::create([
                'name'     => $p['name'],
                'username' => $p['user'],
                'email'    => $p['email'],
                'role'     => 'profesor',
                'password' => $p['user'],
            ]);

            $parts = explode(' ', $p['name'], 2);
            Personnel::create([
                'dni'      => $p['dni'],
                'nombre'   => $parts[0],
                'apellido' => $parts[1] ?? '',
                'telefono' => $p['tel'],
                'correo'   => $p['email'],
                'user_id'  => $user->id,
            ]);
        }

        // 2 Directivos
        $directivos = [
            ['name' => 'Clara Benítez',  'user' => 'directivo1', 'email' => 'directivo1@kiosko.uner', 'dni' => '28000001', 'tel' => '3434-200001'],
            ['name' => 'Eduardo Castro', 'user' => 'directivo2', 'email' => 'directivo2@kiosko.uner', 'dni' => '28000002', 'tel' => '3434-200002'],
        ];

        foreach ($directivos as $d) {
            $user = User::create([
                'name'     => $d['name'],
                'username' => $d['user'],
                'email'    => $d['email'],
                'role'     => 'directivo',
                'password' => $d['user'],
            ]);

            $parts = explode(' ', $d['name'], 2);
            Personnel::create([
                'dni'      => $d['dni'],
                'nombre'   => $parts[0],
                'apellido' => $parts[1] ?? '',
                'telefono' => $d['tel'],
                'correo'   => $d['email'],
                'user_id'  => $user->id,
            ]);
        }

        $alumnoUserId = $alumnoUser?->id;

        // ══════════════════════════════════════════════════════════
        // 2. CATEGORÍAS DE PRODUCTO
        // ══════════════════════════════════════════════════════════
        $categories = [
            ['name' => 'Bebidas',        'icon' => '🥤', 'sort_order' => 1,  'is_produced' => false],
            ['name' => 'Galletitas',     'icon' => '🍪', 'sort_order' => 2,  'is_produced' => false],
            ['name' => 'Golosinas',      'icon' => '🍬', 'sort_order' => 3,  'is_produced' => false],
            ['name' => 'Snacks',         'icon' => '🥨', 'sort_order' => 4,  'is_produced' => false],
            ['name' => 'Lácteos',        'icon' => '🥛', 'sort_order' => 5,  'is_produced' => false],
            ['name' => 'Insumos Cocina', 'icon' => '🍳', 'sort_order' => 6,  'is_produced' => false],
            ['name' => 'Elaborados',     'icon' => '🍕', 'sort_order' => 99, 'is_produced' => true],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['name']] = ProductCategory::create($cat);
        }

        // ══════════════════════════════════════════════════════════
        // 3. PROVEEDORES
        // ══════════════════════════════════════════════════════════
        $proveedores = [
            ['nombre' => 'Distribuidora Coca-Cola', 'contacto' => 'Hernán K.',  'telefono' => '3434-555444', 'correo' => 'soporte@cocacola-uner.com',           'direccion' => 'Ruta 14 Km 250, Concordia'],
            ['nombre' => 'Distribuidora Arcor',     'contacto' => 'Marcos L.',  'telefono' => '3434-555111', 'correo' => 'arcor@distribuidora.com',              'direccion' => 'Av. San Lorenzo 1820, Concordia'],
            ['nombre' => 'Fiambrería El Trébol',    'contacto' => 'Sofía M.',   'telefono' => '3434-555222', 'correo' => 'ventas@eltrebol.com',                  'direccion' => 'Urquiza 421, Concordia'],
            ['nombre' => 'Panificadora Concordia',  'contacto' => 'Roberto G.', 'telefono' => '3434-555333', 'correo' => 'pedidos@panificadoraconcordia.com',    'direccion' => 'Las Heras 987, Concordia'],
        ];

        $provModels = [];
        foreach ($proveedores as $prov) {
            $provModels[$prov['nombre']] = Proveedor::create($prov);
        }

        // ══════════════════════════════════════════════════════════
        // 4. PRODUCTOS
        // ══════════════════════════════════════════════════════════
        $productsData = [
            // REVENTA
            ['category' => 'Bebidas',        'name' => 'Coca-Cola 500ml',          'description' => 'Gaseosa refrescante original sabor cola.',                         'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Bebidas',        'name' => 'Sprite 500ml',             'description' => 'Gaseosa refrescante sabor lima-limón.',                            'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Bebidas',        'name' => 'Agua mineral 500ml',       'description' => 'Agua mineral sin gas baja en sodio.',                              'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Galletitas',     'name' => 'Oreo paq. triple',         'description' => 'Galletitas rellenas sabor chocolate.',                             'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Galletitas',     'name' => 'Criollitas paq. familiar', 'description' => 'Galletitas de agua saladas clásicas.',                             'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Golosinas',      'name' => 'Chicle Beldent menta',     'description' => 'Chicles sin azúcar sabor menta fresca.',                          'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Snacks',         'name' => 'Papas Lays clásicas 150g', 'description' => 'Papas fritas clásicas crujientes.',                                'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            ['category' => 'Lácteos',        'name' => 'Chocolatada Cindor 200ml', 'description' => 'Leche chocolatada premium sabor cacao.',                          'tipo' => 'reventa',   'price' => 0,       'stock' => 0],
            // INSUMOS
            ['category' => 'Insumos Cocina', 'name' => 'Harina 000 x1kg',          'description' => 'Materia prima esencial para preparar pizzas y masas.',             'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            ['category' => 'Insumos Cocina', 'name' => 'Queso mozzarella x1kg',    'description' => 'Queso mozzarella de alta calidad para derretir.',                  'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            ['category' => 'Insumos Cocina', 'name' => 'Salsa de tomate puré 520g','description' => 'Puré de tomate seleccionado para condimento de pizzas.',           'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            ['category' => 'Insumos Cocina', 'name' => 'Jamón cocido feteado x1kg','description' => 'Jamón cocido de primera calidad para sándwiches y empanadas.',    'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            ['category' => 'Insumos Cocina', 'name' => 'Aceite de girasol 1.5L',   'description' => 'Aceite para frituras y preparación culinaria.',                    'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            ['category' => 'Insumos Cocina', 'name' => 'Huevos maple x30',         'description' => 'Maple completo de huevos frescos de campo.',                       'tipo' => 'insumo',    'price' => 0,       'stock' => 0],
            // ELABORADOS
            ['category' => 'Elaborados',     'name' => 'Pizza Mozzarella entera',           'description' => 'Pizza elaborada artesanalmente en la cocina del Kiosco.',          'tipo' => 'elaborado', 'price' => 3500.00, 'stock' => 15],
            ['category' => 'Elaborados',     'name' => 'Empanada de carne x3',              'description' => 'Combo de tres empanadas fritas de carne cortada a cuchillo.',       'tipo' => 'elaborado', 'price' => 2400.00, 'stock' => 20],
            ['category' => 'Elaborados',     'name' => 'Sándwich Jamón y Queso gigante',    'description' => 'Sándwich en pan baguetín con jamón cocido, queso y lechuga.',       'tipo' => 'elaborado', 'price' => 1800.00, 'stock' => 12],
            ['category' => 'Elaborados',     'name' => 'Ensalada de frutas fresca',         'description' => 'Ensalada elaborada en el día con frutas de estación.',              'tipo' => 'elaborado', 'price' => 1200.00, 'stock' => 8],
        ];

        $productModels = [];
        foreach ($productsData as $p) {
            $cat        = $catModels[$p['category']];
            $esElaborado = $p['tipo'] === 'elaborado';
            $productModels[$p['name']] = Product::create([
                'category_id' => $cat->id,
                'name'        => $p['name'],
                'description' => $p['description'],
                'tipo'        => $p['tipo'],
                'price'       => $esElaborado ? 0           : $p['price'],
                'sale_price'  => $esElaborado ? $p['price'] : 0,
                'stock'       => $p['stock'],
                'is_active'   => true,
            ]);
        }

        // ══════════════════════════════════════════════════════════
        // 5. COMPRAS DE SIMULACIÓN (stock + historial de precios)
        // ══════════════════════════════════════════════════════════

        // Compra 1 – Bebidas (Coca-Cola)
        $c1 = Compra::create(['fecha' => '2026-05-10', 'total' => 66500.00, 'observaciones' => 'Abastecimiento de gaseosas para Kiosco escolar.']);
        foreach ([
            ['name' => 'Coca-Cola 500ml',    'cant' => 30, 'precio' => 1200.00, 'margen' => 1.30],
            ['name' => 'Sprite 500ml',       'cant' => 20, 'precio' => 1100.00, 'margen' => 1.30],
            ['name' => 'Agua mineral 500ml', 'cant' => 15, 'precio' => 600.00,  'margen' => 1.30],
        ] as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create(['compra_id' => $c1->id, 'product_id' => $prod->id, 'producto_nombre' => $prod->name, 'tipo_producto' => 'reventa', 'cantidad' => $i['cant'], 'precio_unitario' => $i['precio']]);
            $prod->stock += $i['cant'];
            $prod->price      = $i['precio'];
            $prod->sale_price = round($i['precio'] * $i['margen'], 2);
            $prod->save();
        }

        // Compra 2 – Golosinas y Galletitas (Arcor)
        $c2 = Compra::create(['fecha' => '2026-05-15', 'total' => 35500.00, 'observaciones' => 'Compra de galletas, Oreos y chicles.']);
        foreach ([
            ['name' => 'Oreo paq. triple',         'cant' => 25, 'precio' => 500.00,  'margen' => 1.35],
            ['name' => 'Criollitas paq. familiar',  'cant' => 15, 'precio' => 800.00,  'margen' => 1.35],
            ['name' => 'Chicle Beldent menta',      'cant' => 40, 'precio' => 200.00,  'margen' => 1.35],
            ['name' => 'Papas Lays clásicas 150g',  'cant' => 10, 'precio' => 1300.00, 'margen' => 1.35],
        ] as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create(['compra_id' => $c2->id, 'product_id' => $prod->id, 'producto_nombre' => $prod->name, 'tipo_producto' => 'reventa', 'cantidad' => $i['cant'], 'precio_unitario' => $i['precio']]);
            $prod->stock += $i['cant'];
            $prod->price      = $i['precio'];
            $prod->sale_price = round($i['precio'] * $i['margen'], 2);
            $prod->save();
        }

        // Compra 3 – Insumos (Fiambrería El Trébol)
        $c3 = Compra::create(['fecha' => '2026-05-18', 'total' => 54900.00, 'observaciones' => 'Insumos de fiambres y quesos.']);
        foreach ([
            ['name' => 'Queso mozzarella x1kg',    'cant' => 8,  'precio' => 4500.00],
            ['name' => 'Jamón cocido feteado x1kg', 'cant' => 4,  'precio' => 3800.00],
            ['name' => 'Salsa de tomate puré 520g', 'cant' => 10, 'precio' => 370.00],
        ] as $i) {
            $prod = $productModels[$i['name']];
            CompraItem::create(['compra_id' => $c3->id, 'product_id' => $prod->id, 'producto_nombre' => $prod->name, 'tipo_producto' => 'insumo', 'cantidad' => $i['cant'], 'precio_unitario' => $i['precio']]);
            $prod->price  = $i['precio'];
            $prod->stock  = 0; // Los insumos no acumulan stock de inventario
            $prod->save();
        }

        // ══════════════════════════════════════════════════════════
        // 6. INGRESOS
        // ══════════════════════════════════════════════════════════
        $ingresos = [
            ['fecha' => '2026-05-10', 'tipo' => 'donacion',            'descripcion' => 'Aporte inicial de la Cooperadora Escolar',          'monto' => 150000.00, 'estado' => 'efectuado', 'detalle' => 'Fondo de inicio para abastecimiento y caja chica.',              'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-15', 'tipo' => 'excedente_caja',      'descripcion' => 'Arqueo de caja diaria - Excedente detectado',        'monto' => 2450.00,   'estado' => 'efectuado', 'detalle' => 'Diferencia a favor detectada al cierre de jornada.',            'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-18', 'tipo' => 'subvencion',          'descripcion' => 'Subvención Centro de Estudiantes',                   'monto' => 75000.00,  'estado' => 'efectuado', 'detalle' => 'Aporte para compra de insumos de elaboración culinaria.',       'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-20', 'tipo' => 'donacion',            'descripcion' => 'Donación anónima de docente jubilada',               'monto' => 15000.00,  'estado' => 'efectuado', 'detalle' => 'Colaboración libre para actividades del Kiosco.',                'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-21', 'tipo' => 'subvencion',          'descripcion' => 'Subsidio por actividades extracurriculares',         'monto' => 45000.00,  'estado' => 'pendiente', 'detalle' => 'Pendiente de acreditación por transferencia bancaria.',         'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-21', 'tipo' => 'ingreso_excepcional', 'descripcion' => 'Venta de cajones plásticos reciclados obsoletos',    'monto' => 8500.00,   'estado' => 'pendiente', 'detalle' => 'Comprador retira y abona mañana por la mañana.',                 'user_id' => $alumnoUserId],
        ];

        foreach ($ingresos as $ing) {
            Ingreso::create($ing);
        }

        // ══════════════════════════════════════════════════════════
        // 7. EGRESOS
        // ══════════════════════════════════════════════════════════
        $egresos = [
            ['fecha' => '2026-05-11', 'tipo' => 'gasto_operativo', 'descripcion' => 'Compra urgente de bolsas plásticas y papel envoltura',  'monto' => 12400.00, 'estado' => 'efectuado', 'detalle' => 'Gasto de emergencia en papelera local por quiebre de stock.',    'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-13', 'tipo' => 'servicio',        'descripcion' => 'Abono de Internet Banda Ancha Fibra Óptica',             'monto' => 28000.00, 'estado' => 'efectuado', 'detalle' => 'Pago mensual del servicio para conectividad del Kiosco POS.',    'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-17', 'tipo' => 'impuesto',        'descripcion' => 'Tasa Bromatológica Municipalidad',                       'monto' => 10500.00, 'estado' => 'efectuado', 'detalle' => 'Tasa de inspección y habilitación culinaria.',                    'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-19', 'tipo' => 'pasivo',          'descripcion' => 'Pago adelanto saldo pendiente Arcor',                    'monto' => 35000.00, 'estado' => 'efectuado', 'detalle' => 'Cancelación parcial de cuenta corriente de proveedores.',        'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-21', 'tipo' => 'insumos',         'descripcion' => 'Compra de verduras frescas en verdulería',               'monto' => 7500.00,  'estado' => 'pendiente', 'detalle' => 'Encargo telefónico, se abona al recibir el pedido.',             'user_id' => $alumnoUserId],
            ['fecha' => '2026-05-21', 'tipo' => 'servicio',        'descripcion' => 'Pago factura del gas envasado de cocina',                'monto' => 42000.00, 'estado' => 'pendiente', 'detalle' => 'Factura emitida, vencimiento el 28 de mayo.',                     'user_id' => $alumnoUserId],
        ];

        foreach ($egresos as $egr) {
            Egreso::create($egr);
        }

        // ══════════════════════════════════════════════════════════
        // RESUMEN
        // ══════════════════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('  ✅  Datos demo cargados correctamente.');
        $this->command->info('  👥  Usuarios creados: 1 superadmin, 10 alumnos, 3 profesores, 2 directivos');
        $this->command->info('  📦  Productos: 8 reventa, 6 insumos, 4 elaborados');
        $this->command->info('  🛒  Compras: 3 órdenes de compra simuladas');
        $this->command->info('  💰  Ingresos: 6  |  📉  Egresos: 6');
        $this->command->info('');
        $this->command->warn('  ⚠️  Este seeder es SOLO para desarrollo/pruebas. No usar en producción.');
        $this->command->info('');
    }
}
