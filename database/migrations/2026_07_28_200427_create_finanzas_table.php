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
        Schema::create('finanzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->unique()->constrained('procesos')->restrictOnDelete();
            $table->decimal('costo', 10, 2);
            $table->string('tipo_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finanzas');
    }
};
