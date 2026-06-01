<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Devuelve todos los pedidos.
     */
    public function index()
    {
        $orders = Order::with('items')
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id'           => $o->id,
                    'cliente'      => $o->cliente,
                    'fecha'        => $o->fecha,
                    'hora'         => substr($o->hora, 0, 5),
                    'horaEntrega'  => $o->hora_entrega ? substr($o->hora_entrega, 0, 5) : null,
                    'estado'       => $o->estado,
                    'total'        => (float) $o->total,
                    'productos'    => $o->items->map(fn($i) => [
                        'nombre' => $i->product_name,
                        'precio' => (float) $i->unit_price,
                        'cantidad' => $i->quantity,
                        'subtotal' => (float) $i->subtotal,
                    ]),
                ];
            });

        return response()->json($orders);
    }

    /**
     * Registra un nuevo pedido.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente'      => 'required|string|max:150',
            'hora_entrega' => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:150',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $total     = 0;
            $lineItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('name', $item['product_name'])->first();
                $price   = $product ? (float) $product->sale_price : 0;
                $subtotal = $price * $item['quantity'];
                $total   += $subtotal;

                $lineItems[] = [
                    'product_id'   => $product?->id,
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $price,
                    'subtotal'     => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id'      => Auth::id(),
                'cliente'      => $validated['cliente'],
                'fecha'        => now()->format('Y-m-d'),
                'hora'         => now()->format('H:i:s'),
                'hora_entrega' => $validated['hora_entrega'] ?: null,
                'estado'       => 'pending',
                'total'        => $total,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create($line);
            }

            \App\Models\ActivityLog::log(Auth::id(), 'INSERT', 'Pedidos', "Creó el pedido #{$order->id} para el cliente: {$order->cliente}");
            return response()->json([
                'success' => true,
                'message' => 'Pedido registrado exitosamente.',
                'order'   => $order->load('items'),
            ], 201);
        });
    }

    /**
     * Actualiza un pedido existente.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->estado === 'delivered') {
            return response()->json(['message' => 'No se puede editar un pedido ya entregado.'], 422);
        }

        $validated = $request->validate([
            'cliente'      => 'required|string|max:150',
            'hora_entrega' => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:150',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($order, $validated) {
            $total     = 0;
            $lineItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('name', $item['product_name'])->first();
                $price   = $product ? (float) $product->sale_price : 0;
                $subtotal = $price * $item['quantity'];
                $total   += $subtotal;

                $lineItems[] = [
                    'product_id'   => $product?->id,
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $price,
                    'subtotal'     => $subtotal,
                ];
            }

            $order->update([
                'cliente'      => $validated['cliente'],
                'hora_entrega' => $validated['hora_entrega'] ?: null,
                'total'        => $total,
            ]);

            // Eliminar ítems anteriores y crear nuevos
            $order->items()->delete();
            foreach ($lineItems as $line) {
                $order->items()->create($line);
            }

            \App\Models\ActivityLog::log(Auth::id(), 'UPDATE', 'Pedidos', "Actualizó el pedido #{$order->id} del cliente: {$order->cliente}");
            return response()->json([
                'success' => true,
                'message' => 'Pedido actualizado correctamente.',
                'order'   => $order->load('items'),
            ]);
        });
    }

    /**
     * Confirma un pedido (pasa a Entregas).
     */
    public function confirm($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['estado' => 'confirmed']);

        \App\Models\ActivityLog::log(Auth::id(), 'UPDATE', 'Pedidos', "Confirmó el pedido #{$order->id} del cliente: {$order->cliente}");
        return response()->json([
            'success' => true,
            'message' => 'Pedido confirmado con éxito.',
        ]);
    }

    /**
     * Rechaza un pedido.
     */
    public function reject($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['estado' => 'rejected']);

        \App\Models\ActivityLog::log(Auth::id(), 'UPDATE', 'Pedidos', "Rechazó el pedido #{$order->id} del cliente: {$order->cliente}");
        return response()->json([
            'success' => true,
            'message' => 'Pedido rechazado con éxito.',
        ]);
    }

    /**
     * Reactiva un pedido rechazado o entregado.
     */
    public function reactivate($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['estado' => 'pending']);

        \App\Models\ActivityLog::log(Auth::id(), 'UPDATE', 'Pedidos', "Reactivó el pedido #{$order->id} del cliente: {$order->cliente}");
        return response()->json([
            'success' => true,
            'message' => 'Pedido reactivado con éxito.',
        ]);
    }

    /**
     * Marca el pedido como entregado, crea venta, descuenta stock e ingresos.
     */
    public function deliver(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);
        if ($order->estado === 'delivered') {
            return response()->json(['message' => 'El pedido ya fue entregado.'], 422);
        }

        $metodoPago = $request->input('metodo_pago', 'efectivo');

        return DB::transaction(function () use ($order, $metodoPago) {
            // 1. Marcar como entregado
            $order->update(['estado' => 'delivered']);

            // 2. Crear la Venta
            $sale = Sale::create([
                'user_id'       => Auth::id(),
                'cliente'       => $order->cliente,
                'total'         => $order->total,
                'metodo_pago'   => $metodoPago,
                'estado'        => 'pagado',
                'observaciones' => "Pedido #{$order->id} entregado y cobrado automáticamente",
            ]);

            // 3. Crear ítems de venta y descontar stock
            foreach ($order->items as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal'   => $item->subtotal,
                ]);

                if ($item->product_id) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }

            // 4. Crear registro en Ingreso
            Ingreso::create([
                'fecha'       => now()->format('Y-m-d'),
                'tipo'        => 'venta_kiosco',
                'descripcion' => "Pedido #{$order->id} - Entrega Kiosco",
                'monto'       => $order->total,
                'estado'      => 'efectuado',
                'detalle'     => "Venta por pedido entregado a: {$order->cliente}. Pago por {$metodoPago}.",
                'user_id'     => Auth::id(),
            ]);

            // 5. Registrar actividad
            \App\Models\ActivityLog::log(
                Auth::id(),
                'order_delivery',
                'Kiosco',
                "Pedido #{$order->id} entregado y cobrado — \${$order->total}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Pedido entregado, venta registrada y stock descontado con éxito.',
            ]);
        });
    }

    /**
     * Elimina un pedido.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $cliente = $order->cliente;
        $order->delete();
        \App\Models\ActivityLog::log(Auth::id(), 'DELETE', 'Pedidos', "Eliminó el pedido #{$id} del cliente: {$cliente}");

        return response()->json([
            'success' => true,
            'message' => 'Pedido eliminado con éxito.',
        ]);
    }
}
