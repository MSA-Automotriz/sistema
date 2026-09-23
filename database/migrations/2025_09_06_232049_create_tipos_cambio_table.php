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
        Schema::create('tipos_cambio', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('compra', 8, 4)->comment('Tipo de cambio compra');
            $table->decimal('venta', 8, 4)->comment('Tipo de cambio venta');
            $table->date('fecha_inicio')->comment('Fecha desde cuando es válido');
            $table->date('fecha_fin')->nullable()->comment('Fecha hasta cuando es válido');
            $table->enum('origen', ['sunat', 'manual'])->default('sunat')->comment('Origen del tipo de cambio');
            $table->boolean('activo')->default(true)->comment('Si está activo para usar');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            $table->unsignedBigInteger('user_id')->comment('Usuario que registró/modificó');
            $table->timestamps();
            
            // Índices
            $table->index(['fecha', 'activo']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->unique(['fecha', 'activo'], 'unique_fecha_activa');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_cambio');
    }
};
