<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumnoUserController extends Controller
{
    /**
     * Devuelve todos los alumnos del sistema excepto el usuario autenticado.
     * Solo expone datos mínimos: id de personnel, dni, nombre, apellido, username.
     */
    public function index()
    {
        $currentUserId = Auth::id();

        $alumnos = Personnel::with('user:id,username,role')
            ->whereHas('user', function ($query) use ($currentUserId) {
                $query->where('role', 'alumno')
                      ->where('id', '!=', $currentUserId);
            })
            ->orderBy('apellido')
            ->get()
            ->map(function ($p) {
                return [
                    'id'       => $p->id,
                    'dni'      => $p->dni,
                    'nombre'   => $p->nombre,
                    'apellido' => $p->apellido,
                    'username' => $p->user?->username,
                ];
            });

        return response()->json(['users' => $alumnos->values()]);
    }

    /**
     * Actualiza la contraseña de un alumno específico.
     * Doble protección:
     *   - No se puede cambiar la contraseña del propio usuario autenticado.
     *   - El target debe ser estrictamente un alumno.
     */
    public function updatePassword(Request $request, $id)
    {
        $personnel = Personnel::with('user')->findOrFail($id);

        // Seguridad 1: No puede cambiarse su propia contraseña
        if ($personnel->user && $personnel->user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No podés cambiar tu propia contraseña desde este módulo.',
            ], 403);
        }

        // Seguridad 2: Solo puede modificar alumnos
        if (!$personnel->user || $personnel->user->role !== 'alumno') {
            return response()->json([
                'success' => false,
                'message' => 'No tenés permisos para modificar este usuario.',
            ], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:4|max:100',
        ]);

        $personnel->user->update(['password' => $validated['password']]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }
}
