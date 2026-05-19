<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Registra cada acción relevante que un alumno realiza en el sistema:
     *   login    – inicio de sesión
     *   insert   – alta de un registro
     *   update   – modificación de un registro
     *   delete   – eliminación de un registro
     *   sale     – venta / transacción
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['login', 'insert', 'update', 'delete', 'sale']);
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            // Sin updated_at – los logs son inmutables
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
