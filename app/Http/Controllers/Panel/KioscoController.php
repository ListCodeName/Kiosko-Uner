<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KioscoController extends Controller
{
    /* ══════════════════════════════════════════════════════════
     * GET /panel/api/kiosco/categories
     *
     * Devuelve todas las categorías con sus productos activos,
     * ordenadas por sort_order.
     * ══════════════════════════════════════════════════════════ */
    public function categories()
    {
        $categories = ProductCategory::ordered()
            ->with(['products' => function ($q) {
                $q->active()->orderBy('name');
            }])
            ->get()
            ->map(function ($cat) {
                return [
                    'id'          => $cat->id,
                    'name'        => $cat->name,
                    'icon'        => $cat->icon,
                    'is_produced' => $cat->is_produced,
                    'products'    => $cat->products->map(fn($p) => [
                        'id'    => $p->id,
                        'name'  => $p->name,
                        'price' => (float) $p->sale_price,
                        'stock' => $p->stock,
                    ]),
                ];
            });

        return response()->json($categories);
    }

    /* ══════════════════════════════════════════════════════════
     * POST /panel/api/kiosco/sale
     *
     * Recibe:  { items: [ { product_id, qty }, … ], cliente, metodo_pago, estado, observaciones }
     * Valida stock, crea la venta atómicamente, descuenta stock,
     * registra automáticamente un Ingreso si es pagado,
     * registra ActivityLog y retorna confirmación.
     * ══════════════════════════════════════════════════════════ */
    public function sale(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'cliente'            => 'nullable|string|max:255',
            'metodo_pago'        => 'nullable|in:efectivo,transferencia',
            'estado'             => 'nullable|in:pagado,pendiente',
            'observaciones'      => 'nullable|string',
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($request, $userId) {

            $total     = 0;
            $lineItems = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if (!$product->is_active) {
                    return response()->json([
                        'message' => "El producto «{$product->name}» ya no está disponible.",
                    ], 422);
                }

                if ($product->stock < $item['qty']) {
                    return response()->json([
                        'message' => "Stock insuficiente para «{$product->name}». Disponible: {$product->stock}.",
                    ], 422);
                }

                $subtotal = $product->sale_price * $item['qty'];
                $total   += $subtotal;

                $lineItems[] = [
                    'product'    => $product,
                    'qty'        => $item['qty'],
                    'unit_price' => $product->sale_price,
                    'subtotal'   => $subtotal,
                ];
            }

            // Crear la venta
            $sale = Sale::create([
                'user_id'       => $userId,
                'cliente'       => $request->input('cliente'),
                'total'         => $total,
                'metodo_pago'   => $request->input('metodo_pago', 'efectivo'),
                'estado'        => $request->input('estado', 'pagado'),
                'observaciones' => $request->input('observaciones'),
            ]);

            // Crear los ítems y descontar stock
            foreach ($lineItems as $line) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $line['product']->id,
                    'quantity'   => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'subtotal'   => $line['subtotal'],
                ]);

                $line['product']->decrement('stock', $line['qty']);
            }

            // Crear Ingreso si está cobrada
            if ($sale->estado === 'pagado') {
                \App\Models\Ingreso::create([
                    'fecha'       => now()->toDateString(),
                    'tipo'        => 'venta_kiosco',
                    'descripcion' => "Venta Kiosco #{$sale->id} registrada",
                    'monto'       => $total,
                    'estado'      => 'efectuado',
                    'detalle'     => "Venta #{$sale->id} registrada. Cliente: " . ($sale->cliente ?? 'Venta general'),
                    'user_id'     => $userId,
                ]);
            }

            // Registrar actividad
            $itemCount = count($lineItems);
            ActivityLog::log(
                $userId,
                'sale',
                'Kiosco',
                "Venta #{$sale->id} — {$itemCount} ítem(s) por \${$total}"
            );

            return response()->json([
                'message'  => 'Venta registrada con éxito',
                'sale_id'  => $sale->id,
                'total'    => (float) $total,
                'items'    => $itemCount,
            ]);
        });
    }

    /* ══════════════════════════════════════════════════════════
     * GET /panel/api/ventas
     *
     * Devuelve el historial de ventas completo mapeado para el front.
     * ══════════════════════════════════════════════════════════ */
    public function getSales()
    {
        $sales = Sale::with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'id'      => $sale->id,
                    'fecha'   => $sale->created_at->format('Y-m-d'),
                    'hora'    => $sale->created_at->format('H:i'),
                    'cliente' => $sale->cliente ?? 'Venta general',
                    'total'   => (float) $sale->total,
                    'metodo'  => $sale->metodo_pago,
                    'estado'  => $sale->estado,
                    'obs'     => $sale->observaciones ?? '',
                    'items'   => $sale->items->map(fn($item) => [
                        'product_id' => $item->product_id,
                        'nombre'     => $item->product ? $item->product->name : 'Producto Eliminado',
                        'cantidad'   => $item->quantity,
                        'precio'     => (float) $item->unit_price,
                    ]),
                ];
            });

        return response()->json($sales);
    }

    /* ══════════════════════════════════════════════════════════
     * POST /panel/api/ventas/{id}/efectivizar
     *
     * Transiciona una venta de pendiente a pagada e ingresa dinero.
     * ══════════════════════════════════════════════════════════ */
    public function efectivizar($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = Sale::findOrFail($id);

            if ($sale->estado === 'pagado') {
                return response()->json(['message' => 'La venta ya se encuentra pagada.'], 422);
            }

            $sale->estado = 'pagado';
            $sale->save();

            // Guardar automáticamente el Ingreso
            \App\Models\Ingreso::create([
                'fecha'       => now()->toDateString(),
                'tipo'        => 'venta_kiosco',
                'descripcion' => "Venta Kiosco #{$sale->id} cobrada (efectivizada)",
                'monto'       => $sale->total,
                'estado'      => 'efectuado',
                'detalle'     => "Cobro de venta #{$sale->id}. Cliente: " . ($sale->cliente ?? 'Venta general'),
                'user_id'     => Auth::id(),
            ]);

            // Registrar actividad
            ActivityLog::log(
                Auth::id(),
                'sale_collect',
                'Kiosco',
                "Venta #{$sale->id} efectivizada por \${$sale->total}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Venta efectivizada y registrada en ingresos.',
            ]);
        });
    }

    /* ══════════════════════════════════════════════════════════
     * POST /panel/api/kiosco/restore-stock
     *
     * Reintegra el stock de los productos de una venta que se anula/devuelve.
     * También elimina el registro de la venta y de los ingresos asociados.
     * ══════════════════════════════════════════════════════════ */
    public function restoreStock(Request $request)
    {
        $request->validate([
            'sale_id' => 'nullable|integer|exists:sales,id',
            'items'   => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request) {
            $saleId = $request->input('sale_id');

            if ($saleId) {
                $sale = Sale::with('items.product')->findOrFail($saleId);

                // Reintegrar stock para cada ítem en la base de datos
                foreach ($sale->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                // Eliminar ingresos contables vinculados a esta venta
                \App\Models\Ingreso::where('descripcion', 'like', "%Venta Kiosco #{$sale->id}%")
                    ->orWhere('detalle', 'like', "%venta #{$sale->id}%")
                    ->delete();

                // Registrar actividad
                ActivityLog::log(
                    Auth::id(),
                    'sale_return',
                    'Kiosco',
                    "Devolución de venta #{$sale->id} — Stock restablecido y venta eliminada"
                );

                // Eliminar items de venta y la venta
                $sale->items()->delete();
                $sale->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Venta devuelta, stock restablecido e ingresos eliminados.',
                ]);
            } else if ($request->filled('items')) {
                // Modo fallback por si no se especifica el sale_id directamente, pero sí los ítems
                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->increment('stock', $item['qty']);
                    }
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Stock restablecido (modo fallback).',
                ]);
            }

            return response()->json(['message' => 'Debe proveer sale_id o items.'], 422);
        });
    }
}
