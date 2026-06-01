<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              INICIAR SISTEMA – Arranque desde cero           ║
 * ╠══════════════════════════════════════════════════════════════╣
 * ║  Limpia TODAS las tablas y crea únicamente el Super Admin.   ║
 * ║  Usar en producción o cuando se quiere empezar de cero.      ║
 * ║                                                              ║
 * ║  Ejecutar con:                                               ║
 * ║    php artisan db:seed --class=IniciarSistemaSeeder          ║
 * ║                                                              ║
 * ║  Para empezar desde CERO TOTAL (también migra de nuevo):     ║
 * ║    php artisan migrate:fresh --seed                          ║
 * ║    (asegurarse de que DatabaseSeeder llame a este seeder)    ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class IniciarSistemaSeeder extends Seeder
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

        // ── Crear el Super Administrador ──────────────────────────
        User::create([
            'name'     => 'Super Admin',
            'username' => 'superadmin',
            'email'    => 'superadmin@kiosko.uner',
            'role'     => 'superadmin',
            'password' => 'superadmin', // Se hashea automáticamente por el cast del modelo
        ]);

        $this->command->info('');
        $this->command->info('  ✅  Sistema inicializado correctamente.');
        $this->command->info('  👤  Usuario: superadmin');
        $this->command->info('  🔑  Contraseña: superadmin');
        $this->command->info('  ⚠️   Cambia la contraseña después del primer inicio de sesión.');
        $this->command->info('');
    }
}
