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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('telefono');
            $table->string('ciudad');
            $table->string('email')->nullable();
            $table->string('tipo_proceso');
            $table->text('descripcion')->nullable();
            $table->string('origen');
            $table->string('forma_ingreso')->nullable();
            $table->string('colegio_otros')->nullable();
            $table->string('estado')->default('nueva');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
