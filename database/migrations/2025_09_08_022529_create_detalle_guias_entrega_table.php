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
        Schema::create('detalle_guias_entrega', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guia_entrega_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('tipo_producto'); // 'parte' o 'vehiculo'
            $table->string('codigo_producto');
            $table->string('nombre_producto');
            $table->decimal('cantidad_enviada', 8, 2);
            $table->decimal('cantidad_recibida', 8, 2)->default(0);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('observaciones_detalle')->nullable();
            $table->timestamps();
            
            // Llaves foráneas
            $table->foreign('guia_entrega_id')->references('id')->on('guias_entrega')->onDelete('cascade');
            
            $table->index(['guia_entrega_id']);
            $table->index(['tipo_producto', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_guias_entrega');
    }
};
