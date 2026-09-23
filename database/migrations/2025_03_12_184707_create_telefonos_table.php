<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelefonosTable extends Migration
{
    public function up()
    {
        Schema::create('telefonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('numero');
            $table->enum('tipo', ['telefono', 'celular']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('telefonos');
    }
}