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
        Schema::table('procesos', function (Blueprint $table) {
            $table->string('estado')->default('activo')->after('tiempo_proceso_meses');
            $table->foreignId('abogado_id')->nullable()->after('estado')->constrained('personal')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procesos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('abogado_id');
            $table->dropColumn('estado');
        });
    }
};
