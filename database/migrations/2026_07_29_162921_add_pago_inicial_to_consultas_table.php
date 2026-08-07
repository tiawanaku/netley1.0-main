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
        Schema::table('consultas', function (Blueprint $table) {
            $table->decimal('pago_inicial_monto', 10, 2)->nullable()->after('estado');
            $table->dateTime('pago_inicial_registrado_en')->nullable()->after('pago_inicial_monto');
            $table->foreignId('atendido_por')->nullable()->after('pago_inicial_registrado_en')->constrained('personal')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('atendido_por');
            $table->dropColumn(['pago_inicial_monto', 'pago_inicial_registrado_en']);
        });
    }
};
