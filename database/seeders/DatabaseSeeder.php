<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — Punto de entrada principal de Artisan.
 *
 * Por defecto ejecuta IniciarSistemaSeeder (arranque desde cero,
 * solo el superadmin). Para cargar datos de prueba correr:
 *
 *   php artisan db:seed --class=DatosDemoSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(IniciarSistemaSeeder::class);
    }
}
