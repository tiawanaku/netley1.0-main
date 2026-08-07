<?php

namespace Database\Factories;

use App\Enums\TipoPago;
use App\Models\Finanza;
use App\Models\Proceso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Finanza>
 */
class FinanzaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'proceso_id' => Proceso::factory(),
            'costo' => fake()->numberBetween(500, 5000),
            'tipo_pago' => fake()->randomElement(TipoPago::cases()),
        ];
    }
}
