<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrigenRecibo: string implements HasLabel
{
    case Cuota = 'cuota';
    case Anticipo = 'anticipo';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cuota => 'Cuota',
            self::Anticipo => 'Anticipo',
        };
    }
}
