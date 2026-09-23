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
        if (Schema::hasTable('almacenes')) {
            Schema::table('almacenes', function (Blueprint $table) {
                if (!Schema::hasColumn('almacenes', 'activo')) {
                    $table->boolean('activo')->default(true)->after('parent_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('almacenes')) {
            Schema::table('almacenes', function (Blueprint $table) {
                if (Schema::hasColumn('almacenes', 'activo')) {
                    $table->dropColumn('activo');
                }
            });
        }
    }
};
