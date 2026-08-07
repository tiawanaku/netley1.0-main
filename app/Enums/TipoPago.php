<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipoPago: string implements HasLabel
{
    case Semanal = 'semanal';
    case Mensual = 'mensual';
    case AlContado = 'al_contado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Semanal => 'Semanal',
            self::Mensual => 'Mensual',
            self::AlContado => 'Al Contado',
        };
    }
}
