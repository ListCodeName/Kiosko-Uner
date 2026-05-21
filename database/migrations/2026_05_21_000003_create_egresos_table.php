<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('tipo'); // e.g. gasto_operativo, pasivo, insumos, servicio, etc.
            $table->string('descripcion', 200);
            $table->decimal('monto', 10, 2);
            $table->string('estado')->default('efectuado'); // efectuado, pendiente
            $table->text('detalle')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
