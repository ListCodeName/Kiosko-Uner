<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos ordenados por precio (mayor a menor).
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name', 'asc')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Crea un producto nuevo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $validated['is_active'] = true;
        Product::create($validated);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $validated['name'] . '" creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un producto.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Actualiza los datos de un producto.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $product->name . '" actualizado correctamente.');
    }

    /**
     * Eliminación suave del producto (desactivar).
     */
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->update(['is_active' => false]);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $name . '" eliminado correctamente.');
    }

    /**
     * Reactiva un producto eliminado con nuevo stock.
     */
    public function restore(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'is_active' => true,
            'stock'     => $request->stock,
        ]);

        return redirect()->route('panel')
            ->with('success', 'Producto "' . $product->name . '" repuesto correctamente.');
    }

    /**
     * Actualización rápida de stock desde la tabla.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $request->stock]);

        return redirect()->route('panel')
            ->with('success', 'Stock de "' . $product->name . '" actualizado a ' . $request->stock . ' unidades.');
    }
}
