<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->string('ci')->nullable()->after('apellido_materno');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('ci')->nullable()->after('apellidos');
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn('ci');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('ci');
        });
    }
};
