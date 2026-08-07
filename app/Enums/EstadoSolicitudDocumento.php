<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoSolicitudDocumento: string implements HasColor, HasLabel
{
    case Pendiente = 'pendiente';
    case Cumplida = 'cumplida';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Cumplida => 'Cumplida',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::Cumplida => 'success',
        };
    }
}
