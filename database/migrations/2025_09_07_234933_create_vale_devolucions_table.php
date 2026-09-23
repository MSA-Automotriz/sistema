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
        Schema::create('vales_devolucion', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('fecha');
            $table->unsignedBigInteger('proveedor_id');
            $table->string('motivo');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'procesado'])->default('pendiente');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            
            // Solo índices por ahora - las llaves foráneas se agregan después
            
            $table->index('numero');
            $table->index('fecha');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vales_devolucion');
    }
};
