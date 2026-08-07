<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoProceso: string implements HasColor, HasLabel
{
    case Activo = 'activo';
    case Cerrado = 'cerrado';
    case Archivado = 'archivado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Cerrado => 'Cerrado',
            self::Archivado => 'Archivado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Activo => 'success',
            self::Cerrado => 'gray',
            self::Archivado => 'warning',
        };
    }
}
