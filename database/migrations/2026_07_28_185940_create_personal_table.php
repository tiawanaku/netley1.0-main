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
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('ci')->unique();
            $table->string('genero');
            $table->date('fecha_nacimiento');
            $table->string('nacionalidad')->default('Boliviana');
            $table->string('estado_civil');
            $table->string('profesion')->nullable();
            $table->string('telefono');
            $table->string('whatsapp')->nullable();
            $table->string('email');
            $table->string('direccion')->nullable();
            $table->string('ciudad');
            $table->string('numero_contrato')->nullable()->unique();
            $table->string('estado')->default('activo');
            $table->date('fecha_inicio');
            $table->string('rol');
            $table->string('especialidad_abogado')->nullable();
            $table->string('foto')->nullable();
            $table->json('documentos')->nullable();
            $table->text('nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
