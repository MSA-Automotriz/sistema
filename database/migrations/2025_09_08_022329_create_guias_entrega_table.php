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
        Schema::create('guias_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('fecha');
            $table->unsignedBigInteger('proveedor_id');
            $table->string('transportista')->nullable();
            $table->string('placa_vehiculo')->nullable();
            $table->string('conductor')->nullable();
            $table->string('dni_conductor')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'en_transito', 'recibida', 'cancelada'])->default('pendiente');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('recibido_por')->nullable();
            $table->timestamp('fecha_recepcion')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index('numero');
            $table->index('fecha');
            $table->index('estado');
            $table->index(['proveedor_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guias_entrega');
    }
};
