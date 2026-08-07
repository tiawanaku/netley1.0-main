<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TipoAgenda: string implements HasColor, HasLabel
{
    case Cita = 'cita';
    case Llamada = 'llamada';
    case Reunion = 'reunion';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cita => 'Cita',
            self::Llamada => 'Llamada',
            self::Reunion => 'Reunión',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Cita => 'success',
            self::Llamada => 'info',
            self::Reunion => 'primary',
        };
    }
}
