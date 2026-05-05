<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    /**
     * Show the superadmin panel.
     */
    public function index()
    {
        return view('superadmin.index');
    }

    /**
     * Get all personnel as JSON (with associated user).
     */
    public function getPersonnel()
    {
        $personnel = Personnel::with('user:id,username,email,role')
            ->orderBy('apellido')
            ->get()
            ->map(function ($p) {
                return [
                    'id'        => $p->id,
                    'dni'       => $p->dni,
                    'nombre'    => $p->nombre,
                    'apellido'  => $p->apellido,
                    'full_name' => $p->full_name,
                    'telefono'  => $p->telefono,
                    'correo'    => $p->correo,
                    'user_id'   => $p->user_id,
                    'username'  => $p->user?->username,
                    'role'      => $p->user?->role ?? 'alumno',
                ];
            });

        // Summary counts
        $counts = [
            'total'      => $personnel->count(),
            'alumnos'    => $personnel->where('role', 'alumno')->count(),
            'profesores' => $personnel->where('role', 'profesor')->count(),
            'directivos' => $personnel->where('role', 'directivo')->count(),
        ];

        return response()->json([
            'personnel' => $personnel->values(),
            'counts'    => $counts,
        ]);
    }

    /**
     * Create a new personnel record with an associated user account.
     */
    public function storePersonnel(Request $request)
    {
        $validated = $request->validate([
            'dni'       => 'required|string|max:20|unique:personnel,dni',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => 'required|email|max:150|unique:personnel,correo',
            'username'  => 'required|string|max:50|unique:users,username',
            'password'  => 'required|string|min:4|max:100',
            'role'      => 'required|in:alumno,profesor,directivo',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name'     => "{$validated['nombre']} {$validated['apellido']}",
                'username' => $validated['username'],
                'email'    => $validated['correo'],
                'role'     => $validated['role'],
                'password' => $validated['password'],
            ]);

            // Create personnel record linked to user
            $personnel = Personnel::create([
                'dni'       => $validated['dni'],
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'telefono'  => $validated['telefono'],
                'correo'    => $validated['correo'],
                'user_id'   => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Personal creado exitosamente.',
                'personnel' => [
                    'id'        => $personnel->id,
                    'dni'       => $personnel->dni,
                    'nombre'    => $personnel->nombre,
                    'apellido'  => $personnel->apellido,
                    'full_name' => $personnel->full_name,
                    'telefono'  => $personnel->telefono,
                    'correo'    => $personnel->correo,
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'role'      => $user->role,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el personal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update personnel data.
     */
    public function updatePersonnel(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        $validated = $request->validate([
            'dni'       => ['required', 'string', 'max:20', Rule::unique('personnel', 'dni')->ignore($personnel->id)],
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => ['required', 'email', 'max:150', Rule::unique('personnel', 'correo')->ignore($personnel->id)],
        ]);

        try {
            DB::beginTransaction();

            $personnel->update($validated);

            // Sync user name and email
            if ($personnel->user) {
                $personnel->user->update([
                    'name'  => "{$validated['nombre']} {$validated['apellido']}",
                    'email' => $validated['correo'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Datos actualizados correctamente.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change the role of a personnel's associated user.
     */
    public function updateRole(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:alumno,profesor,directivo',
        ]);

        if (!$personnel->user) {
            return response()->json([
                'success' => false,
                'message' => 'Este personal no tiene cuenta de usuario asociada.',
            ], 422);
        }

        $personnel->user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado a ' . ucfirst($validated['role']) . '.',
        ]);
    }

    /**
     * Change the password of a personnel's associated user.
     */
    public function updatePassword(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:4|max:100',
        ]);

        if (!$personnel->user) {
            return response()->json([
                'success' => false,
                'message' => 'Este personal no tiene cuenta de usuario asociada.',
            ], 422);
        }

        $personnel->user->update(['password' => $validated['password']]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }

    /**
     * Delete a personnel record and its associated user.
     */
    public function destroyPersonnel($id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        try {
            DB::beginTransaction();

            // The user will be deleted by cascade, but let's be explicit
            if ($personnel->user) {
                $personnel->user->delete();
            }

            $personnel->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Personal eliminado correctamente.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
