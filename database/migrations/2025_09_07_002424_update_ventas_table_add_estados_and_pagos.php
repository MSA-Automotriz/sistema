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
                // Modificar campo estado existente para usar enum con los nuevos estados
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
                
                // Agregar campos adicionales si no existen
                if (!Schema::hasColumn('ventas', 'monto_abonado')) {
                    $table->decimal('monto_abonado', 10, 2)->default(0)->after('total');
                }
                
                if (!Schema::hasColumn('ventas', 'saldo_pendiente')) {
                    $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('monto_abonado');
                }
                
                // Nuevos campos para gestión avanzada
                $table->date('fecha_vencimiento')->nullable()->after('fecha');
                $table->date('fecha_despacho')->nullable()->after('fecha_vencimiento');
                $table->string('numero_factura')->nullable()->after('codigo');
                $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media')->after('estado');
                $table->text('notas_internas')->nullable()->after('observaciones');
                $table->decimal('tipo_cambio_usado', 8, 4)->nullable()->after('moneda');
                $table->boolean('requiere_importacion')->default(false)->after('prioridad');
                $table->json('detalle_estados')->nullable()->after('notas_internas'); // Para tracking de cambios de estado
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
                // Revertir campo estado al valor anterior (si existía)
                $table->string('estado')->change();
                
                // Eliminar campos agregados (excepto monto_abonado y saldo_pendiente que ya existían)
                $table->dropColumn([
                    'fecha_vencimiento',
                    'fecha_despacho', 
                    'numero_factura',
                    'prioridad',
                    'notas_internas',
                    'tipo_cambio_usado',
                    'requiere_importacion',
                    'detalle_estados'
                ]);
            });
        }
    }
};
