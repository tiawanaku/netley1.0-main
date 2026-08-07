<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Consulta;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        $nombre = fake()->firstName();
        $apellidos = fake()->lastName().' '.fake()->lastName();

        return [
            'consulta_id' => Consulta::factory(),
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'telefono' => fake()->numerify('7#######'),
            'whatsapp' => fake()->numerify('7#######'),
            'usuario' => Str::of("{$nombre} {$apellidos}")->ascii()->lower()->slug('.'),
            'password' => Str::password(10),
        ];
    }
}
