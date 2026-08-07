<?php

namespace Database\Seeders;

use App\Models\Finanza;
use App\Models\Proceso;
use Illuminate\Database\Seeder;

class FinanzaSeeder extends Seeder
{
    public function run(): void
    {
        Proceso::doesntHave('finanza')->get()->each(function (Proceso $proceso) {
            $finanza = Finanza::factory()->create(['proceso_id' => $proceso->id]);

            $maximo = $finanza->maxCuotasPermitidas();
            $cuotas = min($maximo, fake()->numberBetween(1, min($maximo, 12)));

            $finanza->generarPlanPagos($cuotas, now()->subMonths(1));
        });
    }
}
