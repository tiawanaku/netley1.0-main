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
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        DB::statement('ALTER TABLE agendas MODIFY created_by BIGINT UNSIGNED NULL');

        Schema::table('agendas', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        DB::statement('ALTER TABLE agendas MODIFY created_by BIGINT UNSIGNED NOT NULL');

        Schema::table('agendas', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
