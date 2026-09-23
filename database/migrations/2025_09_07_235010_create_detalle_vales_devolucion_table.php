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
        Schema::create('detalle_vales_devolucion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vale_devolucion_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('tipo_producto'); // 'parte' o 'vehiculo'
            $table->string('codigo_producto');
            $table->string('nombre_producto');
            $table->decimal('cantidad', 8, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('motivo_detalle')->nullable();
            $table->text('observaciones_detalle')->nullable();
            $table->timestamps();
            
            // Llaves foráneas
            $table->foreign('vale_devolucion_id')->references('id')->on('vales_devolucion')->onDelete('cascade');
            
            $table->index(['vale_devolucion_id']);
            $table->index(['tipo_producto', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_vales_devolucion');
    }
};
