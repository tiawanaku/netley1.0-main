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
        Schema::table('finanzas', function (Blueprint $table) {
            $table->decimal('anticipo', 10, 2)->default(0)->after('costo');
            $table->dateTime('anticipo_registrado_en')->nullable()->after('anticipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finanzas', function (Blueprint $table) {
            $table->dropColumn(['anticipo', 'anticipo_registrado_en']);
        });
    }
};
