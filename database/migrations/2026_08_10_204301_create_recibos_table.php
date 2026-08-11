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
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->uuid('identificador')->unique();
            $table->string('hash_verificacion', 64);
            $table->string('origen_tipo');

            $table->foreignId('plan_pago_id')->nullable()->unique()->constrained('plan_pagos')->restrictOnDelete();
            $table->foreignId('finanza_id')->nullable()->unique()->constrained('finanzas')->restrictOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('proceso_id')->constrained('procesos')->restrictOnDelete();

            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 3)->default('BOB');
            $table->date('fecha_pago');
            $table->string('estado')->default('emitido');

            $table->foreignId('registrado_por_personal_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->foreignId('registrado_por_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('anulado_en')->nullable();
            $table->foreignId('anulado_por_personal_id')->nullable()->constrained('personal')->nullOnDelete();
            $table->foreignId('anulado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
