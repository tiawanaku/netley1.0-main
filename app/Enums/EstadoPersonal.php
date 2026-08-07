<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoPersonal: string implements HasColor, HasLabel
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case Suspendido = 'suspendido';

    public function getLabel(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Inactivo => 'Inactivo',
            self::Suspendido => 'Suspendido',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Activo => 'success',
            self::Inactivo => 'gray',
            self::Suspendido => 'danger',
        };
    }
}
