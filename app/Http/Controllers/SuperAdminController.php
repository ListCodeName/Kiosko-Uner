<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Personnel;
use App\Models\Product;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /* ══════════════════════════════════════════════════
     *  PROVEEDORES
     * ══════════════════════════════════════════════════ */

    /**
     * Get all proveedores as JSON.
     */
    public function getProveedores()
    {
        $proveedores = Proveedor::withTrashed()
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'proveedores' => $proveedores,
            'total'       => $proveedores->count(),
        ]);
    }

    /**
     * Create a new proveedor.
     */
    public function storeProveedor(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:150',
            'contacto'  => 'nullable|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor = Proveedor::create($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Proveedor creado exitosamente.',
            'proveedor' => $proveedor,
        ], 201);
    }

    /**
     * Update an existing proveedor.
     */
    public function updateProveedor(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre'    => 'required|string|max:150',
            'contacto'  => 'nullable|string|max:100',
            'telefono'  => 'nullable|string|max:30',
            'correo'    => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado correctamente.',
        ]);
    }

    /**
     * Delete a proveedor.
     */
    public function destroyProveedor($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proveedor eliminado correctamente.',
        ]);
    }

    /* ══════════════════════════════════════════════════
     *  COMPRAS
     * ══════════════════════════════════════════════════ */

    /**
     * Get all compras with their items.
     * Incluye tipo_producto para cada ítem.
     */
    public function getCompras()
    {
        $compras = Compra::with('items')
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($c) {
                return [
                    'id'            => $c->id,
                    'fecha'         => $c->fecha->format('d/m/Y'),
                    'fecha_raw'     => $c->fecha->format('Y-m-d'),
                    'total'         => (float) $c->total,
                    'observaciones' => $c->observaciones,
                    'sincronizado'  => (bool) $c->sincronizado,
                    'items'         => $c->items->map(fn($i) => [
                        'id'              => $i->id,
                        'product_id'      => $i->product_id,
                        'producto_nombre' => $i->producto_nombre,
                        'tipo_producto'   => $i->tipo_producto,
                        'cantidad'        => (float) $i->cantidad,
                        'precio_unitario' => (float) $i->precio_unitario,
                        'subtotal'        => round((float)$i->cantidad * (float)$i->precio_unitario, 2),
                    ]),
                ];
            });

        return response()->json([
            'compras' => $compras,
            'total'   => $compras->count(),
        ]);
    }

    /**
     * Store a new compra con su lógica de stock diferencial por tipo:
     *   - Compara al 100% por nombre: si coincide, actualiza cantidad y precio de costo.
     *   - Si no coincide: crea un nuevo producto en el catálogo.
     *   - Se marca como sincronizado directamente.
     */
    public function storeCompra(Request $request)
    {
        $validated = $request->validate([
            'fecha'                       => 'required|date',
            'observaciones'               => 'nullable|string|max:500',
            'items'                       => 'required|array|min:1',
            'items.*.product_id'          => 'nullable|integer|exists:products,id',
            'items.*.producto_nombre'     => 'required|string|max:150',
            'items.*.tipo_producto'       => 'required|in:reventa,insumo,elaborado',
            'items.*.cantidad'            => 'required|numeric|min:0.01',
            'items.*.precio_unitario'     => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = collect($validated['items'])->sum(
                fn($i) => $i['cantidad'] * $i['precio_unitario']
            );

            // Se registra directamente como sincronizado ya que impacta de inmediato en stock y catálogo
            $compra = Compra::create([
                'fecha'         => $validated['fecha'],
                'total'         => $total,
                'observaciones' => $validated['observaciones'] ?? null,
                'sincronizado'  => true,
            ]);

            foreach ($validated['items'] as $item) {
                $nombre = trim($item['producto_nombre']);
                
                // Intentar buscar primero por product_id y luego por coincidencia de nombre exacta al 100%
                $product = null;
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                }
                if (!$product) {
                    $product = Product::where('name', $nombre)->first();
                }

                if ($product) {
                    // Si existe el producto, se actualizan la cantidad (adición al actual) y el precio de costo (sobreescritura)
                    $product->stock += (float) $item['cantidad'];
                    $product->price  = (float) $item['precio_unitario'];
                    $product->save();
                } else {
                    // Si no existe, se genera un nuevo producto con todos sus datos
                    $tipo = $item['tipo_producto'] ?? 'reventa';
                    if ($tipo === 'elaborado') {
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Elaborados'],
                            ['icon' => '🍕', 'sort_order' => 99, 'is_produced' => true]
                        );
                    } elseif ($tipo === 'insumo') {
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Insumos Cocina'],
                            ['icon' => '🍳', 'sort_order' => 6, 'is_produced' => false]
                        );
                    } else { // reventa
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Otros'],
                            ['icon' => '📦', 'sort_order' => 10, 'is_produced' => false]
                        );
                    }

                    $product = Product::create([
                        'category_id' => $category->id,
                        'name'        => $nombre,
                        'tipo'        => $tipo,
                        'price'       => (float) $item['precio_unitario'],
                        'sale_price'  => 0.00, // Los nuevos productos inician con precio de venta en 0
                        'stock'       => (float) $item['cantidad'],
                        'is_active'   => true,
                        'description' => 'Creado automáticamente desde compra #' . $compra->id,
                    ]);

                    ActivityLog::log(
                        Auth::id(),
                        'INSERT',
                        'Productos',
                        "Creó automáticamente el producto: {$product->name} (Tipo: {$product->tipo}) al registrar la compra #{$compra->id}"
                    );
                }

                // Registrar ítem de la compra vinculándolo al producto correspondiente
                CompraItem::create([
                    'compra_id'       => $compra->id,
                    'product_id'      => $product->id,
                    'producto_nombre' => $nombre,
                    'tipo_producto'   => $item['tipo_producto'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            // Crear automáticamente el Egreso contable
            \App\Models\Egreso::create([
                'fecha'       => $validated['fecha'],
                'tipo'        => 'insumos',
                'descripcion' => "Compra mercadería #{$compra->id}",
                'monto'       => $total,
                'estado'      => 'efectuado',
                'detalle'     => "Compra registrada para abastecimiento de stock. Observaciones: " . ($validated['observaciones'] ?? 'Ninguna'),
                'user_id'     => Auth::id(),
            ]);

            // Registrar actividad
            ActivityLog::log(
                Auth::id(),
                'INSERT',
                'Compras',
                "Registró una compra de mercadería #{$compra->id} por \${$total}"
            );

            DB::commit();

            $compra->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada exitosamente.',
                'compra'  => [
                    'id'            => $compra->id,
                    'fecha'         => $compra->fecha->format('d/m/Y'),
                    'fecha_raw'     => $compra->fecha->format('Y-m-d'),
                    'total'         => (float) $compra->total,
                    'observaciones' => $compra->observaciones,
                    'sincronizado'  => (bool) $compra->sincronizado,
                    'items'         => $compra->items->map(fn($i) => [
                        'id'              => $i->id,
                        'product_id'      => $i->product_id,
                        'producto_nombre' => $i->producto_nombre,
                        'tipo_producto'   => $i->tipo_producto,
                        'cantidad'        => (float) $i->cantidad,
                        'precio_unitario' => (float) $i->precio_unitario,
                        'subtotal'        => round((float)$i->cantidad * (float)$i->precio_unitario, 2),
                    ]),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sincroniza una compra histórica con la tabla de productos de forma segura.
     */
    public function sincronizarCompra(Request $request)
    {
        $validated = $request->validate([
            'compra_id' => 'required|integer|exists:compras,id',
        ]);

        $compra = Compra::with('items')->findOrFail($validated['compra_id']);

        if ($compra->sincronizado) {
            return response()->json([
                'success' => false,
                'message' => 'Esta compra ya ha sido sincronizada anteriormente.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($compra->items as $item) {
                $nombre = trim($item->producto_nombre);
                
                // Intentar buscar primero por product_id y luego por coincidencia de nombre exacta al 100%
                $product = null;
                if (!empty($item->product_id)) {
                    $product = Product::find($item->product_id);
                }
                if (!$product) {
                    $product = Product::where('name', $nombre)->first();
                }

                if ($product) {
                    // Si ya existía el producto, actualizamos su stock (adición al actual) y precio de compra (sobreescritura)
                    $product->stock += (float) $item->cantidad;
                    $product->price  = (float) $item->precio_unitario;
                    $product->save();
                } else {
                    // Si no existía, se crea un producto nuevo
                    $tipo = $item->tipo_producto ?? 'reventa';
                    if ($tipo === 'elaborado') {
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Elaborados'],
                            ['icon' => '🍕', 'sort_order' => 99, 'is_produced' => true]
                        );
                    } elseif ($tipo === 'insumo') {
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Insumos Cocina'],
                            ['icon' => '🍳', 'sort_order' => 6, 'is_produced' => false]
                        );
                    } else { // reventa
                        $category = \App\Models\ProductCategory::firstOrCreate(
                            ['name' => 'Otros'],
                            ['icon' => '📦', 'sort_order' => 10, 'is_produced' => false]
                        );
                    }

                    $product = Product::create([
                        'category_id' => $category->id,
                        'name'        => $nombre,
                        'tipo'        => $tipo,
                        'price'       => (float) $item->precio_unitario,
                        'sale_price'  => 0.00,
                        'stock'       => (float) $item->cantidad,
                        'is_active'   => true,
                        'description' => 'Creado automáticamente al sincronizar compra #' . $compra->id,
                    ]);

                    ActivityLog::log(
                        Auth::id(),
                        'INSERT',
                        'Productos',
                        "Creó automáticamente el producto: {$product->name} (Tipo: {$product->tipo}) al sincronizar la compra #{$compra->id}"
                    );
                }

                // Vinculamos el item de compra con el product_id
                $item->product_id = $product->id;
                $item->save();
            }

            // Marcamos la compra como sincronizada
            $compra->sincronizado = true;
            $compra->save();

            // Registrar actividad
            ActivityLog::log(
                Auth::id(),
                'UPDATE',
                'Compras',
                "Sincronizó manualmente la compra #{$compra->id} de fecha " . $compra->fecha->format('d/m/Y')
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra sincronizada exitosamente y productos actualizados.',
                'compra'  => [
                    'id'            => $compra->id,
                    'fecha'         => $compra->fecha->format('d/m/Y'),
                    'fecha_raw'     => $compra->fecha->format('Y-m-d'),
                    'total'         => (float) $compra->total,
                    'observaciones' => $compra->observaciones,
                    'sincronizado'  => (bool) $compra->sincronizado,
                    'items'         => $compra->items->map(fn($i) => [
                        'id'              => $i->id,
                        'product_id'      => $i->product_id,
                        'producto_nombre' => $i->producto_nombre,
                        'tipo_producto'   => $i->tipo_producto,
                        'cantidad'        => (float) $i->cantidad,
                        'precio_unitario' => (float) $i->precio_unitario,
                        'subtotal'        => round((float)$i->cantidad * (float)$i->precio_unitario, 2),
                    ]),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina una compra y revierte el stock de los ítems de tipo 'reventa'.
     */
    public function destroyCompra($id)
    {
        $compra = Compra::with('items')->findOrFail($id);

        try {
            DB::beginTransaction();

            // Revertir stock de los ítems reventa
            foreach ($compra->items as $item) {
                if ($item->tipo_producto === 'reventa' && $item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock = max(0, $product->stock - (int) $item->cantidad);
                        $product->save();
                    }
                }
            }

            // Eliminar automáticamente el Egreso contable correspondiente
            \App\Models\Egreso::where('descripcion', "Compra mercadería #{$compra->id}")->delete();

            $total = $compra->total;
            $compra->delete(); // cascade elimina compra_items

            // Registrar actividad
            ActivityLog::log(
                Auth::id(),
                'DELETE',
                'Compras',
                "Eliminó la compra de mercadería #{$id} por \${$total}"
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra eliminada y stock revertido correctamente.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
