<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proceso;
use Illuminate\Database\Seeder;

class ProcesoSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::all()->each(function (Cliente $cliente) {
            Proceso::factory()
                ->count(fake()->numberBetween(1, 2))
                ->create(['cliente_id' => $cliente->id]);
        });
    }
}
