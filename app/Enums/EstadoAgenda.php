<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoAgenda: string implements HasColor, HasLabel
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case Reagendada = 'reagendada';
    case Cancelada = 'cancelada';
    case Finalizada = 'finalizada';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Confirmada => 'Confirmada',
            self::Reagendada => 'Reagendada',
            self::Cancelada => 'Cancelada',
            self::Finalizada => 'Finalizada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendiente => 'gray',
            self::Confirmada => 'success',
            self::Reagendada => 'warning',
            self::Cancelada => 'danger',
            self::Finalizada => 'info',
        };
    }
}
