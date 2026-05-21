<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna `tipo` a la tabla `products`.
     *
     * Tipos de producto:
     *   - reventa   → mercadería para revender, stock controlado, precio actualizado por compra.
     *   - insumo    → materia prima para elaborar, sin control de stock.
     *   - elaborado → producto fabricado para vender, stock cargado manualmente.
     *
     * Agrega también `product_id` a `compra_items` para vincular con el catálogo
     * y `tipo_producto` para persistir el tipo en el historial sin depender del producto.
     */
    public function up(): void
    {
        // Columna tipo en products
        Schema::table('products', function (Blueprint $table) {
            $table->enum('tipo', ['reventa', 'insumo', 'elaborado'])
                  ->default('reventa')
                  ->after('description');
        });

        // Vincular compra_items con el product (nullable para compatibilidad)
        Schema::table('compra_items', function (Blueprint $table) {
            $table->foreignId('product_id')
                  ->nullable()
                  ->after('compra_id')
                  ->constrained('products')
                  ->nullOnDelete();

            $table->enum('tipo_producto', ['reventa', 'insumo', 'elaborado'])
                  ->default('reventa')
                  ->after('producto_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('compra_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'tipo_producto']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
