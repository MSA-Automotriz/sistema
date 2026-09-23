<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('documento_identidad')->unique();
            $table->enum('tipo_cliente', ['natural', 'juridica']);
            $table->string('departamento');
            $table->string('provincia');
            $table->string('distrito');
            $table->string('correo')->nullable();
            $table->foreignId('categoria_cliente_id')->constrained('categoria_clientes')->onDelete('restrict');
            $table->foreignId('canal_captacion_id')->constrained('canal_captacion')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}