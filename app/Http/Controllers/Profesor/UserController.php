<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get all users of type 'alumno' and 'profesor'
     */
    public function index()
    {
        $personnel = Personnel::with('user:id,username,email,role')
            ->whereHas('user', function ($query) {
                $query->whereIn('role', ['alumno', 'profesor']);
            })
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
                    'role'      => $p->user?->role,
                ];
            });

        return response()->json([
            'users' => $personnel->values(),
        ]);
    }

    /**
     * Create a new personnel record and user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni'       => 'required|string|max:20|unique:personnel,dni',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => 'required|email|max:150|unique:personnel,correo',
            'username'  => 'required|string|max:50|unique:users,username',
            'password'  => 'required|string|min:4|max:100',
            'role'      => 'required|in:alumno,profesor',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => "{$validated['nombre']} {$validated['apellido']}",
                'username' => $validated['username'],
                'email'    => $validated['correo'],
                'role'     => $validated['role'],
                'password' => $validated['password'],
            ]);

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
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
                'user'    => [
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
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update personnel and user data.
     */
    public function update(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        // Security check: ensure the user being edited is alumno or profesor
        if ($personnel->user && !in_array($personnel->user->role, ['alumno', 'profesor'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar este tipo de usuario.',
            ], 403);
        }

        $validated = $request->validate([
            'dni'       => ['required', 'string', 'max:20', Rule::unique('personnel', 'dni')->ignore($personnel->id)],
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => ['required', 'email', 'max:150', Rule::unique('personnel', 'correo')->ignore($personnel->id)],
            'role'      => 'required|in:alumno,profesor',
        ]);

        try {
            DB::beginTransaction();

            $personnel->update([
                'dni'      => $validated['dni'],
                'nombre'   => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'],
                'correo'   => $validated['correo'],
            ]);

            if ($personnel->user) {
                $personnel->user->update([
                    'name'  => "{$validated['nombre']} {$validated['apellido']}",
                    'email' => $validated['correo'],
                    'role'  => $validated['role'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.',
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
     * Update password
     */
    public function updatePassword(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        if ($personnel->user && !in_array($personnel->user->role, ['alumno', 'profesor'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar este tipo de usuario.',
            ], 403);
        }

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
     * Delete user
     */
    public function destroy($id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        if ($personnel->user && !in_array($personnel->user->role, ['alumno', 'profesor'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este tipo de usuario.',
            ], 403);
        }

        try {
            DB::beginTransaction();

            if ($personnel->user) {
                $personnel->user->delete();
            }

            $personnel->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.',
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
