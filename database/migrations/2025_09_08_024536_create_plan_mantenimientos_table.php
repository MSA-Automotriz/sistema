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
        Schema::create('plan_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('modelo_vehiculo');
            $table->year('ano_modelo');
            $table->enum('tipo_transmision', ['MT', 'AT', 'CVT']);
            $table->string('tono_vehiculo')->nullable();
            $table->integer('intervalo_base'); // En kilómetros
            $table->integer('kilometraje_maximo');
            $table->integer('relacion_horas_km')->default(250); // 250 horas = 5000 km por defecto
            $table->decimal('tarifa_mano_obra', 8, 2)->default(0);
            $table->decimal('impuestos', 5, 2)->default(18); // Porcentaje
            $table->decimal('margen_beneficio', 5, 2)->default(0); // Porcentaje
            $table->enum('moneda_principal', ['USD', 'PEN'])->default('PEN');
            $table->unsignedInteger('proveedor_predeterminado_id')->nullable();
            $table->boolean('mostrar_precios')->default(true);
            $table->boolean('activo')->default(true);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            
            $table->index(['modelo_vehiculo', 'ano_modelo']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_mantenimientos');
    }
};
