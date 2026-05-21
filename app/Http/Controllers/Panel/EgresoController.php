<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EgresoController extends Controller
{
    /**
     * Devuelve todos los egresos.
     */
    public function index()
    {
        $egresos = Egreso::orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'egresos' => $egresos
        ]);
    }

    /**
     * Registra un nuevo egreso.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha'       => 'required|date',
            'tipo'        => 'required|string',
            'descripcion' => 'required|string|max:200',
            'monto'       => 'required|numeric|min:0.01',
            'estado'      => 'required|string|in:efectuado,pendiente',
            'detalle'     => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        $egreso = Egreso::create($data);

        return response()->json([
            'message' => 'Egreso registrado exitosamente',
            'egreso' => $egreso
        ], 201);
    }

    /**
     * Actualiza un egreso existente.
     */
    public function update(Request $request, Egreso $egreso)
    {
        $data = $request->validate([
            'fecha'       => 'required|date',
            'tipo'        => 'required|string',
            'descripcion' => 'required|string|max:200',
            'monto'       => 'required|numeric|min:0.01',
            'estado'      => 'required|string|in:efectuado,pendiente',
            'detalle'     => 'nullable|string',
        ]);

        $egreso->update($data);

        return response()->json([
            'message' => 'Egreso actualizado correctamente',
            'egreso' => $egreso
        ]);
    }

    /**
     * Elimina un egreso.
     */
    public function destroy(Egreso $egreso)
    {
        $egreso->delete();

        return response()->json([
            'message' => 'Egreso eliminado correctamente'
        ]);
    }
}
