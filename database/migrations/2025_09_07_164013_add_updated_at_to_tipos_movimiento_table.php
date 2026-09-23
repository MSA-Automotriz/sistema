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
        if (Schema::hasTable('tipos_movimiento')) {
            Schema::table('tipos_movimiento', function (Blueprint $table) {
                if (!Schema::hasColumn('tipos_movimiento', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tipos_movimiento')) {
            Schema::table('tipos_movimiento', function (Blueprint $table) {
                if (Schema::hasColumn('tipos_movimiento', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
