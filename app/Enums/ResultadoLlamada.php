<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ResultadoLlamada: string implements HasColor, HasLabel
{
    case NoContesto = 'no_contesto';
    case Contesto = 'contesto';
    case Reagendar = 'reagendar';
    case Interesado = 'interesado';
    case NoInteresado = 'no_interesado';

    public function getLabel(): string
    {
        return match ($this) {
            self::NoContesto => 'No contestó',
            self::Contesto => 'Contestó',
            self::Reagendar => 'Reagendar',
            self::Interesado => 'Interesado',
            self::NoInteresado => 'No interesado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NoContesto => 'gray',
            self::Contesto => 'info',
            self::Reagendar => 'warning',
            self::Interesado => 'success',
            self::NoInteresado => 'danger',
        };
    }
}
