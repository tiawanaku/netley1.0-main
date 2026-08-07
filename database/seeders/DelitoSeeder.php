<?php

namespace Database\Seeders;

use App\Models\Delito;
use Illuminate\Database\Seeder;

class DelitoSeeder extends Seeder
{
    /**
     * Mapea el área tal como aparece en delitos.txt al valor de App\Enums\CategoriaLegal.
     *
     * @var array<string, string>
     */
    private const AREA_A_MATERIA = [
        'CIVIL' => 'civil',
        'FAMILIA' => 'familiar',
        'LABORAL' => 'laboral',
        'PENAL' => 'penal',
    ];

    public function run(): void
    {
        $contenido = file_get_contents(database_path('seeders/data/delitos.txt'));

        preg_match_all(
            "/\(\d+,\s*'([^']+)',\s*'([^']+)'\)/u",
            $contenido,
            $coincidencias,
            PREG_SET_ORDER,
        );

        $filas = collect($coincidencias)
            ->map(fn (array $m): array => [
                'area' => self::AREA_A_MATERIA[$m[1]] ?? strtolower($m[1]),
                'delito' => trim($m[2]),
            ])
            ->unique(fn (array $fila): string => $fila['area'].'|'.$fila['delito'])
            ->values();

        Delito::query()->delete();

        $filas->chunk(200)->each(fn ($chunk) => Delito::insert($chunk->all()));
    }
}
