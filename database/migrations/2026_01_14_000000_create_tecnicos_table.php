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
        Schema::create('tecnicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->string('especialidad')->nullable();
            $table->string('cedula_profesional')->nullable();
            $table->string('telefono')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->text('certificaciones')->nullable();
            $table->text('habilidades')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'vacaciones', 'licencia'])->default('activo');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnicos');
    }
};
