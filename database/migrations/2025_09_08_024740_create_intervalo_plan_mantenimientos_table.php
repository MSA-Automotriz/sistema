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
        Schema::create('intervalo_plan_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_mantenimiento_id')->constrained('plan_mantenimientos')->onDelete('cascade');
            $table->foreignId('componente_plan_id')->constrained('componente_plan_mantenimientos')->onDelete('cascade');
            $table->integer('kilometraje');
            $table->integer('horas')->nullable();
            $table->decimal('cantidad_especifica', 8, 2)->nullable();
            $table->decimal('precio_especifico', 10, 2)->nullable();
            $table->enum('moneda_precio', ['USD', 'PEN'])->default('PEN');
            $table->boolean('aplica')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->unique(['componente_plan_id', 'kilometraje'], 'int_plan_comp_km_unique');
            $table->index(['plan_mantenimiento_id', 'kilometraje'], 'int_plan_plan_km_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervalo_plan_mantenimientos');
    }
};
