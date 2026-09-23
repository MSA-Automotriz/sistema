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
        Schema::create('componente_plan_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_mantenimiento_id')->constrained('plan_mantenimientos')->onDelete('cascade');
            $table->foreignId('parte_id')->constrained('partes');
            $table->decimal('cantidad', 8, 2);
            $table->string('unidad_medida'); // Litros, Unidades, Lb, etc.
            $table->enum('accion', ['Reemplazar', 'Inspeccionar', 'Lubricar'])->default('Reemplazar');
            $table->unsignedInteger('proveedor_id')->nullable();
            $table->decimal('precio_base', 10, 2)->nullable();
            $table->enum('moneda', ['USD', 'PEN'])->default('PEN');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['plan_mantenimiento_id', 'parte_id'], 'comp_plan_mant_plan_parte_idx');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componente_plan_mantenimientos');
    }
};
