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
        Schema::create('reglas_vencimiento_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->comment('Nombre descriptivo de la regla');
            $table->text('descripcion')->nullable()->comment('Descripción detallada de la regla');
            $table->integer('dias_vencimiento')->comment('Días después de los cuales la cotización vence si no hay seguimiento');
            $table->integer('dias_alerta')->default(0)->comment('Días antes del vencimiento para enviar alerta');
            $table->foreignId('estado_vencido_id')->constrained('estados_cotizacion')->comment('Estado al que cambia cuando vence');
            $table->boolean('permite_reasignacion')->default(true)->comment('Si permite que otro asesor tome la cotización vencida');
            $table->boolean('requiere_aprobacion')->default(false)->comment('Si requiere aprobación para reasignar');
            $table->boolean('notificar_vencimiento')->default(true)->comment('Si envía notificaciones de vencimiento');
            $table->boolean('activo')->default(true)->comment('Si la regla está activa');
            $table->json('condiciones')->nullable()->comment('Condiciones adicionales (usuarios, roles, canales, etc.)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_vencimiento_cotizaciones');
    }
};
