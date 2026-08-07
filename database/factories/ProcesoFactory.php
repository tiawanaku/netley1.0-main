<?php

namespace Database\Factories;

use App\Enums\CategoriaLegal;
use App\Models\Cliente;
use App\Models\Proceso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Proceso>
 */
class ProcesoFactory extends Factory
{
    private const TIPOS_POR_MATERIA = [
        'familiar' => ['Divorcio', 'Pensión Alimentaria', 'Régimen de Visitas'],
        'laboral' => ['Despido Injustificado', 'Beneficios Sociales'],
        'civil' => ['Sucesiones', 'Usucapión', 'Cobro de Deuda'],
        'penal' => ['Defensa', 'Querella'],
        'comercial' => ['Constitución de Sociedad', 'Disolución de Sociedad'],
        'administrativo' => ['Impugnación de Resolución'],
        'tributario' => ['Impugnación Tributaria'],
    ];

    public function definition(): array
    {
        $materia = fake()->randomElement(CategoriaLegal::cases());
        $tipos = self::TIPOS_POR_MATERIA[$materia->value];

        return [
            'cliente_id' => Cliente::factory(),
            'materia_legal' => $materia,
            'tipo_proceso' => fake()->randomElement($tipos),
            'tiempo_proceso_meses' => fake()->numberBetween(3, 24),
        ];
    }
}
