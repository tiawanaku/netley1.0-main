<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recibo_correlativos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->timestamps();
        });

        // Fila única que sostiene el contador. Se bloquea con lockForUpdate()
        // dentro de una transacción corta para asignar el siguiente número.
        DB::table('recibo_correlativos')->insert([
            'ultimo_numero' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibo_correlativos');
    }
};
