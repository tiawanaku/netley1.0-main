<?php

namespace Database\Factories;

use App\Enums\EspecialidadAbogado;
use App\Enums\EstadoCivil;
use App\Enums\EstadoPersonal;
use App\Enums\Genero;
use App\Enums\RolPersonal;
use App\Models\Personal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Personal>
 */
class PersonalFactory extends Factory
{
    public function definition(): array
    {
        $rol = fake()->randomElement(RolPersonal::cases());

        return [
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName().' '.fake()->lastName(),
            'ci' => fake()->unique()->numerify('#######'),
            'genero' => fake()->randomElement(Genero::cases()),
            'fecha_nacimiento' => fake()->dateTimeBetween('-60 years', '-20 years'),
            'nacionalidad' => 'Boliviana',
            'estado_civil' => fake()->randomElement(EstadoCivil::cases()),
            'profesion' => $rol === RolPersonal::Abogado ? 'Abogado' : fake()->jobTitle(),
            'telefono' => fake()->numerify('7#######'),
            'whatsapp' => fake()->numerify('7#######'),
            'email' => fake()->unique()->safeEmail(),
            'direccion' => fake()->streetAddress(),
            'ciudad' => fake()->randomElement(array_keys(Personal::CIUDADES)),
            'numero_contrato' => fake()->unique()->numerify('CT-####'),
            'estado' => fake()->randomElement(EstadoPersonal::cases()),
            'fecha_inicio' => fake()->dateTimeBetween('-5 years', 'now'),
            'rol' => $rol,
            'especialidad_abogado' => $rol === RolPersonal::Abogado
                ? fake()->randomElement([
                    EspecialidadAbogado::Familiar,
                    EspecialidadAbogado::Laboral,
                    EspecialidadAbogado::Civil,
                    EspecialidadAbogado::Penal,
                    EspecialidadAbogado::Comercial,
                ])
                : null,
            'nota' => fake()->boolean(20) ? fake()->sentence() : null,
        ];
    }
}
