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
        ]);

        $validated['price']     = 0;
        $validated['stock']     = 0;
        $validated['is_active'] = true;

        Product::create($validated);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $validated['name'] . '" creado correctamente.');
    }

    /**
     * Actualiza los datos básicos de un producto (solo nombre, descripción y tipo).
     * El precio y stock se gestionan exclusivamente a través de las compras.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:reventa,insumo,elaborado',
        ]);

        $product->update($validated);

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
            $product->price = $request->precio;
        }
        $product->save();

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

        $products = Product::where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                // Búsqueda por nombre (LIKE, case-insensitive en MySQL)
                $query->where('name', 'like', '%' . $q . '%');
            })
            ->orderBy('tipo')
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'tipo', 'price', 'stock', 'description']);

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
        ]);

        $validated['price']     = 0;
        $validated['stock']     = 0;
        $validated['is_active'] = true;

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente.',
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'tipo'        => $product->tipo,
                'price'       => 0,
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
            $product->price = $request->precio;
        }
        $product->save();

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

        return response()->json([
            'success'   => true,
            'message'   => $request->sobrantes . ' sobrantes dados de baja.',
            'new_stock' => $product->stock,
        ]);
    }
}
