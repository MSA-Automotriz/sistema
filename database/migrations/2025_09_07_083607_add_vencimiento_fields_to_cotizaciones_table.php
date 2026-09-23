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
        if (Schema::hasTable('cotizaciones')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                if (!Schema::hasColumn('cotizaciones', 'regla_vencimiento_id')) {
                    $table->foreignId('regla_vencimiento_id')->nullable()->constrained('reglas_vencimiento_cotizaciones')->comment('Regla de vencimiento aplicada');
                }
                if (!Schema::hasColumn('cotizaciones', 'fecha_ultimo_seguimiento')) {
                    $table->datetime('fecha_ultimo_seguimiento')->nullable()->comment('Última fecha de seguimiento o actividad');
                }
                if (!Schema::hasColumn('cotizaciones', 'fecha_vencimiento')) {
                    $table->datetime('fecha_vencimiento')->nullable()->comment('Fecha calculada de vencimiento');
                }
                if (!Schema::hasColumn('cotizaciones', 'fecha_alerta')) {
                    $table->datetime('fecha_alerta')->nullable()->comment('Fecha para enviar alerta de vencimiento próximo');
                }
                if (!Schema::hasColumn('cotizaciones', 'vencida')) {
                    $table->boolean('vencida')->default(false)->comment('Si la cotización está vencida');
                }
                if (!Schema::hasColumn('cotizaciones', 'reasignable')) {
                    $table->boolean('reasignable')->default(false)->comment('Si puede ser reasignada a otro asesor');
                }
                if (!Schema::hasColumn('cotizaciones', 'historial_vencimiento')) {
                    $table->json('historial_vencimiento')->nullable()->comment('Historial de cambios por vencimiento');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cotizaciones')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                if (Schema::hasColumn('cotizaciones', 'regla_vencimiento_id')) {
                    $table->dropConstrainedForeignId('regla_vencimiento_id');
                }
                $table->dropColumn(array_filter([
                    Schema::hasColumn('cotizaciones', 'fecha_ultimo_seguimiento') ? 'fecha_ultimo_seguimiento' : null,
                    Schema::hasColumn('cotizaciones', 'fecha_vencimiento') ? 'fecha_vencimiento' : null,
                    Schema::hasColumn('cotizaciones', 'fecha_alerta') ? 'fecha_alerta' : null,
                    Schema::hasColumn('cotizaciones', 'vencida') ? 'vencida' : null,
                    Schema::hasColumn('cotizaciones', 'reasignable') ? 'reasignable' : null,
                    Schema::hasColumn('cotizaciones', 'historial_vencimiento') ? 'historial_vencimiento' : null,
                ]));
            });
        }
    }
};
