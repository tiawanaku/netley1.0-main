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
            // Distingue "se declaró un anticipo en el formulario" (anticipo_registrado_en)
            // de "el dinero fue efectivamente confirmado como recibido" (anticipo_confirmado_en).
            // Solo esto último dispara la emisión de un recibo (NET-002).
            $table->dateTime('anticipo_confirmado_en')->nullable()->after('anticipo_registrado_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finanzas', function (Blueprint $table) {
            $table->dropColumn('anticipo_confirmado_en');
        });
    }
};
