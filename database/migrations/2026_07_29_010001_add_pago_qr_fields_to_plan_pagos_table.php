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
        Schema::table('plan_pagos', function (Blueprint $table) {
            $table->string('qr_path')->nullable()->after('estado');
            $table->string('comprobante')->nullable()->after('qr_path');
            $table->dateTime('pagado_en')->nullable()->after('comprobante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_pagos', function (Blueprint $table) {
            $table->dropColumn(['qr_path', 'comprobante', 'pagado_en']);
        });
    }
};
