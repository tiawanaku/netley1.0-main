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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('cita');
            $table->string('estado')->default('pendiente');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('asunto')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('ubicacion')->nullable();
            $table->unsignedSmallInteger('duracion_minutos')->nullable();
            $table->string('resultado')->nullable();
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->nullOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
