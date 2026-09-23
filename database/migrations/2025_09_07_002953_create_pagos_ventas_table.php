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
        Schema::create('pagos_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('numero_pago')->unique(); // PV-202509-000001
            $table->date('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->enum('moneda', ['PEN', 'USD'])->default('PEN');
            $table->decimal('tipo_cambio', 8, 4)->nullable(); // Si el pago es en moneda diferente a la venta
            $table->decimal('monto_convertido', 10, 2)->nullable(); // Monto en moneda de la venta
            $table->enum('metodo_pago', [
                'efectivo',
                'transferencia',
                'cheque', 
                'tarjeta_credito',
                'tarjeta_debito',
                'deposito',
                'otro'
            ])->default('efectivo');
            $table->string('referencia_pago')->nullable(); // Nro de cheque, transferencia, etc.
            $table->string('banco')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->boolean('validado')->default(false);
            $table->foreignId('validado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_validacion')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['venta_id', 'fecha_pago']);
            $table->index('numero_pago');
            $table->index('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_ventas');
    }
};
