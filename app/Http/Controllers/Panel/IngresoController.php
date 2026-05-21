<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoController extends Controller
{
    /**
     * Devuelve todos los ingresos.
     */
    public function index()
    {
        $ingresos = Ingreso::orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'ingresos' => $ingresos
        ]);
    }

    /**
     * Registra un nuevo ingreso.
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

        $ingreso = Ingreso::create($data);
        \App\Models\ActivityLog::log(Auth::id(), 'INSERT', 'Ingresos', "Registró un ingreso manual: {$ingreso->descripcion} por \${$ingreso->monto}");

        return response()->json([
            'message' => 'Ingreso registrado exitosamente',
            'ingreso' => $ingreso
        ], 201);
    }

    /**
     * Actualiza un ingreso existente.
     */
    public function update(Request $request, Ingreso $ingreso)
    {
        $data = $request->validate([
            'fecha'       => 'required|date',
            'tipo'        => 'required|string',
            'descripcion' => 'required|string|max:200',
            'monto'       => 'required|numeric|min:0.01',
            'estado'      => 'required|string|in:efectuado,pendiente',
            'detalle'     => 'nullable|string',
        ]);

        $ingreso->update($data);
        \App\Models\ActivityLog::log(Auth::id(), 'UPDATE', 'Ingresos', "Actualizó el ingreso: {$ingreso->descripcion} por \${$ingreso->monto}");

        return response()->json([
            'message' => 'Ingreso actualizado correctamente',
            'ingreso' => $ingreso
        ]);
    }

    /**
     * Elimina un ingreso.
     */
    public function destroy(Ingreso $ingreso)
    {
        $descripcion = $ingreso->descripcion;
        $monto = $ingreso->monto;
        $ingreso->delete();
        \App\Models\ActivityLog::log(Auth::id(), 'DELETE', 'Ingresos', "Eliminó el ingreso: {$descripcion} por \${$monto}");

        return response()->json([
            'message' => 'Ingreso eliminado correctamente'
        ]);
    }
}
