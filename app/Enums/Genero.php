<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Genero: string implements HasLabel
{
    case Masculino = 'masculino';
    case Femenino = 'femenino';
    case Otro = 'otro';

    public function getLabel(): string
    {
        return match ($this) {
            self::Masculino => 'Masculino',
            self::Femenino => 'Femenino',
            self::Otro => 'Otro',
        };
    }
}
