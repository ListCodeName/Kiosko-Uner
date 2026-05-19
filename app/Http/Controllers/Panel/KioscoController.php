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
                        'price' => (float) $p->price,
                        'stock' => $p->stock,
                    ]),
                ];
            });

        return response()->json($categories);
    }

    /* ══════════════════════════════════════════════════════════
     * POST /panel/api/kiosco/sale
     *
     * Recibe:  { items: [ { product_id, qty }, … ] }
     * Valida stock, crea la venta atómicamente, descuenta stock,
     * registra ActivityLog y retorna confirmación.
     * ══════════════════════════════════════════════════════════ */
    public function sale(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
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

                $subtotal = $product->price * $item['qty'];
                $total   += $subtotal;

                $lineItems[] = [
                    'product'    => $product,
                    'qty'        => $item['qty'],
                    'unit_price' => $product->price,
                    'subtotal'   => $subtotal,
                ];
            }

            // Crear la venta
            $sale = Sale::create([
                'user_id' => $userId,
                'total'   => $total,
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
}
