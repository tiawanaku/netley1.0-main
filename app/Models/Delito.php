<?php

namespace App\Models;

use App\Enums\CategoriaLegal;
use Illuminate\Database\Eloquent\Model;

class Delito extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'area',
        'delito',
    ];

    /**
     * @return array<string, string>
     */
    public static function opcionesPara(CategoriaLegal|string|null $materiaLegal): array
    {
        $materiaLegal = static::normalizarMateria($materiaLegal);

        if (blank($materiaLegal)) {
            return [];
        }

        return static::query()
            ->where('area', $materiaLegal)
            ->orderBy('delito')
            ->pluck('delito', 'delito')
            ->all();
    }

    /**
     * El estado de un Select puede llegar como el enum ya castado (formularios de
     * edición, donde el valor se hidrata desde el modelo) o como string crudo
     * (formularios de creación). Se normaliza siempre al valor string del enum.
     */
    public static function normalizarMateria(CategoriaLegal|string|null $materiaLegal): ?string
    {
        return $materiaLegal instanceof CategoriaLegal ? $materiaLegal->value : $materiaLegal;
    }
}
