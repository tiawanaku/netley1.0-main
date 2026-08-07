<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delitos', function (Blueprint $table) {
            $table->id();
            $table->string('area', 50);
            $table->string('delito', 255);
            $table->index(['area', 'delito']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delitos');
    }
};
