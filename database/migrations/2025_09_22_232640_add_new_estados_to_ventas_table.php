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
        if (Schema::hasTable('ventas')) {
            Schema::table('ventas', function (Blueprint $table) {
                // Ampliar el enum del campo estado para incluir los nuevos estados de gestión de stock
                $table->enum('estado', [
                    'pendiente',
                    'pagado',
                    'no_pagado',
                    'en_cotizacion',
                    'pendiente_stock',    // Nuevo: esperando productos en stock
                    'en_compra',          // Nuevo: comprando productos para el pedido
                    'listo_entrega',      // Nuevo: productos listos para entrega
                    'despachado',
                    'para_importacion',
                    'pedido_especial',
                    'cancelado'
                ])->default('pendiente')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ventas')) {
            Schema::table('ventas', function (Blueprint $table) {
                // Revertir al enum anterior
                $table->enum('estado', [
                    'pendiente',
                    'pagado',
                    'no_pagado',
                    'en_cotizacion',
                    'despachado',
                    'para_importacion',
                    'pedido_especial',
                    'cancelado'
                ])->default('pendiente')->change();
            });
        }
    }
};
