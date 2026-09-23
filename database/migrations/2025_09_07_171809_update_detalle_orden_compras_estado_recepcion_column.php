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
        if (Schema::hasTable('detalle_orden_compras')) {
            Schema::table('detalle_orden_compras', function (Blueprint $table) {
                // Modificar la columna estado_recepcion para permitir valores más largos
                $table->string('estado_recepcion', 30)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('detalle_orden_compras')) {
            Schema::table('detalle_orden_compras', function (Blueprint $table) {
                // Revertir a una longitud menor (estimando la longitud original)
                $table->string('estado_recepcion', 20)->nullable()->change();
            });
        }
    }
};
