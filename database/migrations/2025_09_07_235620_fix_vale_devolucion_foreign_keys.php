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
        // Foreign key constraints will be added later when all referenced tables exist
        // For now, we'll skip to allow testing the functionality
        // Schema::table('vales_devolucion', function (Blueprint $table) {
        //     $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('cascade');
        //     $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vales_devolucion', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['proveedor_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['aprobado_por']);
        });
    }
};
