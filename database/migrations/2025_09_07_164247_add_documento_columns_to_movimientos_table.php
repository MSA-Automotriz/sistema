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
        if (Schema::hasTable('movimientos')) {
            Schema::table('movimientos', function (Blueprint $table) {
                if (!Schema::hasColumn('movimientos', 'documento_tipo')) {
                    $table->string('documento_tipo')->nullable()->after('stock_resultante');
                }
                if (!Schema::hasColumn('movimientos', 'documento_id')) {
                    $table->unsignedBigInteger('documento_id')->nullable()->after('documento_tipo');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('movimientos')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->dropColumn(array_filter([
                    Schema::hasColumn('movimientos', 'documento_tipo') ? 'documento_tipo' : null,
                    Schema::hasColumn('movimientos', 'documento_id') ? 'documento_id' : null,
                ]));
            });
        }
    }
};
