<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_price', 10, 2)->default(0.00)->after('price');
        });

        // Inicialización inteligente para productos existentes
        // Copia el valor del antiguo campo 'price' (compra/venta combinada) al nuevo 'sale_price'
        // solo para productos de tipo 'reventa' y 'elaborado'.
        DB::table('products')
            ->whereIn('tipo', ['reventa', 'elaborado'])
            ->update([
                'sale_price' => DB::raw('price')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });
    }
};
