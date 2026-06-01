<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos en el panel de alumno.
     * Los productos se agrupan por tipo y se ordenan alfabéticamente.
     */
    public function index(Request $request)
    {
        $products = Product::where('is_active', true)
            ->orderBy('tipo')
            ->orderBy('name', 'asc')
            ->get();

        $deletedProducts = Product::where('is_active', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('panel.index', compact('products', 'deletedProducts'));
    }

    /**
     * Crea un producto nuevo.
     * Precio y stock quedan en 0 — se actualizan a través de las compras.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:reventa,insumo,elaborado',
            'sale_price'  => 'nullable|numeric|min:0',
            'grupo'       => 'nullable|string',
        ]);

        $tipo = $validated['tipo'];
        $grupo = $request->input('grupo');

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
            $grupoName = $grupo ? trim($grupo) : 'Otros';
            $category = \App\Models\ProductCategory::firstOrCreate(
                ['name' => $grupoName],
                ['icon' => '📦', 'sort_order' => 10, 'is_produced' => false]
            );
        }

        // El precio de compra inicia en 0 para todos.
        $validated['price']      = 0;
        // El precio de venta solo se admite para reventa y elaborado.
        $validated['sale_price'] = (($tipo === 'reventa' || $tipo === 'elaborado') && isset($validated['sale_price']))
                                    ? $validated['sale_price']
                                    : 0;
        $validated['stock']      = 0;
        $validated['is_active']  = true;
        $validated['category_id'] = $category->id;

        $product = Product::create($validated);
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'INSERT', 'Productos', 'Creó el producto: ' . $product->name . ' (Tipo: ' . $product->tipo . ')');

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $validated['name'] . '" creado correctamente.');
    }

    /**
     * Actualiza los datos básicos de un producto (solo nombre, descripción, tipo y categoría).
     * El precio y stock se gestionan exclusivamente a través de las compras.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:reventa,insumo,elaborado',
            'sale_price'  => 'nullable|numeric|min:0',
            'grupo'       => 'nullable|string',
        ]);

        $tipo = $validated['tipo'];
        $grupo = $request->input('grupo');

        if ($tipo === 'elaborado') {
            $category = \App\Models\ProductCategory::firstOrCreate(
                ['name' => 'Elaborados'],
                ['icon' => '🍕', 'sort_order' => 99, 'is_produced' => true]
            );
            $product->price = 0; // Elaborados no tienen precio de compra
        } elseif ($tipo === 'insumo') {
            $category = \App\Models\ProductCategory::firstOrCreate(
                ['name' => 'Insumos Cocina'],
                ['icon' => '🍳', 'sort_order' => 6, 'is_produced' => false]
            );
            $product->sale_price = 0; // Insumos no tienen precio de venta
        } else { // reventa
            $grupoName = $grupo ? trim($grupo) : 'Otros';
            $category = \App\Models\ProductCategory::firstOrCreate(
                ['name' => $grupoName],
                ['icon' => '📦', 'sort_order' => 10, 'is_produced' => false]
            );
        }

        // Actualizar precio de venta si es aplicable
        if ($tipo === 'reventa' || $tipo === 'elaborado') {
            if (isset($validated['sale_price'])) {
                $product->sale_price = $validated['sale_price'];
            }
        }

        $product->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? $product->description,
            'tipo'        => $validated['tipo'],
            'category_id' => $category->id,
        ]);
        $product->save();
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Actualizó el producto: ' . $product->name);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $product->name . '" actualizado correctamente.');
    }

    /**
     * Soft-delete del producto (desactivar is_active).
     */
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->update(['is_active' => false]);
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'DELETE', 'Productos', 'Eliminó lógicamente el producto: ' . $name);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $name . '" eliminado correctamente.');
    }

    /**
     * Reactiva un producto eliminado.
     * No requiere stock — este se actualiza por compras.
     */
    public function restore(Request $request, Product $product)
    {
        $product->update(['is_active' => true]);
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Reactivó el producto: ' . $product->name);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $product->name . '" reactivado correctamente.');
    }

    /**
     * Carga manual de unidades para productos ELABORADOS.
     * Solo aplica al tipo 'elaborado' — suma las unidades producidas al stock.
     */
    public function cargarUnidades(Request $request, Product $product)
    {
        if ($product->tipo !== 'elaborado') {
            return redirect()->route('panel')
                ->with('error', 'Solo los productos elaborados admiten carga manual de unidades.');
        }

        $request->validate([
            'unidades' => 'required|integer|min:1',
            'precio'   => 'nullable|numeric|min:0',
        ]);

        $product->stock += $request->unidades;
        if ($request->filled('precio')) {
            $product->sale_price = $request->precio;
        }
        $product->save();
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Cargó ' . $request->unidades . ' unidades de elaborado para: ' . $product->name);

        return redirect()->route('panel')
            ->with('success', $request->unidades . ' unidades cargadas para "' . $product->name . '".');
    }

    /**
     * Da de baja sobrantes de productos ELABORADOS.
     * Descuenta del stock las unidades que no se vendieron.
     */
    public function bajaElaborados(Request $request, Product $product)
    {
        if ($product->tipo !== 'elaborado') {
            return redirect()->route('panel')
                ->with('error', 'Solo los productos elaborados admiten baja de sobrantes.');
        }

        $request->validate([
            'sobrantes' => 'required|integer|min:1',
        ]);

        $product->stock = max(0, $product->stock - $request->sobrantes);
        $product->save();
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Dio de baja ' . $request->sobrantes . ' sobrantes de elaborado para: ' . $product->name);

        return redirect()->route('panel')
            ->with('success', $request->sobrantes . ' sobrantes dados de baja para "' . $product->name . '".');
    }

    /* ──────────────────────────────────────────────────────────
     * API JSON — Endpoints para el módulo de Compras
     * ────────────────────────────────────────────────────────── */

    /**
     * Búsqueda de productos para autocompletado en compras.
     * Normaliza acentos y mayúsculas para evitar duplicados por error ortográfico.
     *
     * GET /panel/api/products/search?q=galletitas
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $tipo = $request->get('tipo', '');

        $query = Product::where('is_active', true)
            ->when($tipo !== '', function ($query) use ($tipo) {
                $tipos = explode(',', $tipo);
                $query->whereIn('tipo', $tipos);
            })
            ->orderBy('tipo')
            ->orderBy('name');

        if ($q === '') {
            // Si la consulta q está vacía, no limitamos para permitir la inicialización completa del caché del frontend
            $products = $query->get(['id', 'name', 'tipo', 'price', 'sale_price', 'stock', 'description']);
        } else {
            // Traemos los productos relevantes para filtrar de forma robusta e insensible a acentos en PHP (SQLite fix)
            $products = $query->get(['id', 'name', 'tipo', 'price', 'sale_price', 'stock', 'description']);
            
            $normalizedQ = Str::lower(Str::ascii($q));
            $products = $products->filter(function ($product) use ($normalizedQ) {
                $normalizedName = Str::lower(Str::ascii($product->name));
                return str_contains($normalizedName, $normalizedQ);
            })->take(15)->values();
        }

        return response()->json($products);
    }

    /**
     * Creación rápida de producto desde el módulo de compras.
     * Retorna el producto creado como JSON para ser seleccionado inmediatamente.
     *
     * POST /panel/api/products
     */
    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:reventa,insumo,elaborado',
            'grupo'       => 'nullable|string',
        ]);

        $tipo = $validated['tipo'];
        $grupo = $request->input('grupo');

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
            $grupoName = $grupo ? trim($grupo) : 'Otros';
            $category = \App\Models\ProductCategory::firstOrCreate(
                ['name' => $grupoName],
                ['icon' => '📦', 'sort_order' => 10, 'is_produced' => false]
            );
        }

        $validated['price']      = 0;
        $validated['sale_price'] = 0;
        $validated['stock']      = 0;
        $validated['is_active']  = true;
        $validated['category_id'] = $category->id;

        $product = Product::create($validated);
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'INSERT', 'Productos', 'Creación rápida del producto: ' . $product->name . ' (Tipo: ' . $product->tipo . ')');

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente.',
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'tipo'        => $product->tipo,
                'price'       => 0,
                'sale_price'  => 0,
                'stock'       => 0,
                'description' => $product->description,
            ],
        ], 201);
    }

    /**
     * API: Carga de unidades de elaborados (JSON, desde compras o kiosco).
     */
    public function apiCargarUnidades(Request $request, Product $product)
    {
        if ($product->tipo !== 'elaborado') {
            return response()->json(['message' => 'Solo aplica a elaborados.'], 422);
        }

        $request->validate([
            'unidades' => 'required|integer|min:1',
            'precio'   => 'nullable|numeric|min:0',
        ]);

        $product->stock += $request->unidades;
        if ($request->filled('precio')) {
            $product->sale_price = $request->precio;
        }
        $product->save();
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Cargó ' . $request->unidades . ' unidades de elaborado para: ' . $product->name . ' (API)');
        return response()->json([
            'success'   => true,
            'message'   => $request->unidades . ' unidades cargadas.',
            'new_stock' => $product->stock,
        ]);
    }

    /**
     * API: Baja de sobrantes de elaborados (JSON).
     */
    public function apiBajaElaborados(Request $request, Product $product)
    {
        if ($product->tipo !== 'elaborado') {
            return response()->json(['message' => 'Solo aplica a elaborados.'], 422);
        }

        $request->validate([
            'sobrantes' => 'required|integer|min:1',
        ]);

        $product->stock = max(0, $product->stock - $request->sobrantes);
        $product->save();
        \App\Models\ActivityLog::log(\Illuminate\Support\Facades\Auth::id(), 'UPDATE', 'Productos', 'Dio de baja ' . $request->sobrantes . ' sobrantes de elaborado para: ' . $product->name . ' (API)');
        return response()->json([
            'success'   => true,
            'message'   => $request->sobrantes . ' sobrantes dados de baja.',
            'new_stock' => $product->stock,
        ]);
    }

    /**
     * Actualiza específicamente el precio de venta (sale_price) desde el modal dedicado.
     */
    public function updateSalePrice(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sale_price' => 'required|numeric|min:0',
        ]);

        $oldPrice = $product->sale_price;
        $product->sale_price = $validated['sale_price'];
        $product->save();

        \App\Models\ActivityLog::log(
            \Illuminate\Support\Facades\Auth::id(),
            'UPDATE',
            'Productos',
            'Actualizó el precio de venta de: ' . $product->name . ' de $' . $oldPrice . ' a $' . $product->sale_price
        );

        return redirect()->route('panel')
            ->with('success', 'Precio de venta de "' . $product->name . '" actualizado a $' . number_format($product->sale_price, 2, ',', '.') . '.');
    }
}
