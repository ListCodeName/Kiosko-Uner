<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Personnel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpiar tablas para evitar duplicados en reinyección
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        Personnel::truncate();
        User::truncate();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 1. Super Administrador (sin registro de personal necesario, o directo)
        User::create([
            'name'     => 'Super Admin',
            'username' => 'superadmin',
            'email'    => 'superadmin@kiosko.uner',
            'role'     => 'superadmin',
            'password' => 'superadmin', // Hashed por Eloquent Cast
        ]);

        // 2. 10 Alumnos (con sus registros de Personal asociados)
        $alumnos = [
            ['name' => 'Lucas Gómez',        'user' => 'alumno1',   'email' => 'alumno1@kiosko.uner',   'dni' => '42000001', 'tel' => '3434-000001'],
            ['name' => 'Sofía Rodríguez',    'user' => 'alumno2',   'email' => 'alumno2@kiosko.uner',   'dni' => '42000002', 'tel' => '3434-000002'],
            ['name' => 'Mateo Fernández',    'user' => 'alumno3',   'email' => 'alumno3@kiosko.uner',   'dni' => '42000003', 'tel' => '3434-000003'],
            ['name' => 'Valentina Silva',    'user' => 'alumno4',   'email' => 'alumno4@kiosko.uner',   'dni' => '42000004', 'tel' => '3434-000004'],
            ['name' => 'Thiago Díaz',        'user' => 'alumno5',   'email' => 'alumno5@kiosko.uner',   'dni' => '42000005', 'tel' => '3434-000005'],
            ['name' => 'Camila Alvarez',     'user' => 'alumno6',   'email' => 'alumno6@kiosko.uner',   'dni' => '42000006', 'tel' => '3434-000006'],
            ['name' => 'Lautaro Romero',     'user' => 'alumno7',   'email' => 'alumno7@kiosko.uner',   'dni' => '42000007', 'tel' => '3434-000007'],
            ['name' => 'Isabella González',  'user' => 'alumno8',   'email' => 'alumno8@kiosko.uner',   'dni' => '42000008', 'tel' => '3434-000008'],
            ['name' => 'Benjamín Medina',    'user' => 'alumno9',   'email' => 'alumno9@kiosko.uner',   'dni' => '42000009', 'tel' => '3434-000009'],
            ['name' => 'Martina Flores',     'user' => 'alumno10',  'email' => 'alumno10@kiosko.uner',  'dni' => '42000010', 'tel' => '3434-000010'],
        ];

        foreach ($alumnos as $a) {
            $user = User::create([
                'name'     => $a['name'],
                'username' => $a['user'],
                'email'    => $a['email'],
                'role'     => 'alumno',
                'password' => $a['user'], // clave = nombre de usuario
            ]);

            $parts = explode(' ', $a['name'], 2);
            Personnel::create([
                'dni'       => $a['dni'],
                'nombre'    => $parts[0],
                'apellido'  => $parts[1] ?? '',
                'telefono'  => $a['tel'],
                'correo'    => $a['email'],
                'user_id'   => $user->id,
            ]);
        }

        // 3. 3 Profesores
        $profesores = [
            ['name' => 'Carlos Pérez',  'user' => 'profesor1', 'email' => 'profesor1@kiosko.uner', 'dni' => '35000001', 'tel' => '3434-100001'],
            ['name' => 'Ana Martínez',   'user' => 'profesor2', 'email' => 'profesor2@kiosko.uner', 'dni' => '35000002', 'tel' => '3434-100002'],
            ['name' => 'Jorge Sánchez',  'user' => 'profesor3', 'email' => 'profesor3@kiosko.uner', 'dni' => '35000003', 'tel' => '3434-100003'],
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
                'dni'       => $p['dni'],
                'nombre'    => $parts[0],
                'apellido'  => $parts[1] ?? '',
                'telefono'  => $p['tel'],
                'correo'    => $p['email'],
                'user_id'   => $user->id,
            ]);
        }

        // 4. 2 Directivos
        $directivos = [
            ['name' => 'Clara Benítez',   'user' => 'directivo1', 'email' => 'directivo1@kiosko.uner', 'dni' => '28000001', 'tel' => '3434-200001'],
            ['name' => 'Eduardo Castro',  'user' => 'directivo2', 'email' => 'directivo2@kiosko.uner', 'dni' => '28000002', 'tel' => '3434-200002'],
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
                'dni'       => $d['dni'],
                'nombre'    => $parts[0],
                'apellido'  => $parts[1] ?? '',
                'telefono'  => $d['tel'],
                'correo'    => $d['email'],
                'user_id'   => $user->id,
            ]);
        }

        // 5. Poblamiento del Negocio (Productos, Compras, Ingresos, Egresos)
        $this->call(KioscoDataSeeder::class);
    }
}
