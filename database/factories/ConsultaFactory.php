<?php

namespace Database\Factories;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoConsulta;
use App\Enums\FormaIngreso;
use App\Enums\OrigenConsulta;
use App\Models\Consulta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consulta>
 */
class ConsultaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido_paterno' => fake()->lastName(),
            'apellido_materno' => fake()->lastName(),
            'telefono' => fake()->numerify('7#######'),
            'ciudad' => fake()->randomElement(array_keys(Consulta::CIUDADES)),
            'email' => fake()->safeEmail(),
            'tipo_proceso' => fake()->randomElement(CategoriaLegal::cases()),
            'descripcion' => fake()->sentence(),
            'origen' => fake()->randomElement(OrigenConsulta::cases()),
            'forma_ingreso' => fake()->randomElement(FormaIngreso::cases()),
            'colegio_otros' => fake()->boolean(30) ? fake()->company() : null,
            'estado' => fake()->randomElement(EstadoConsulta::cases()),
        ];
    }
}
