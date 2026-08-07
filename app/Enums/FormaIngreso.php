<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FormaIngreso: string implements HasLabel
{
    case Presencial = 'presencial';
    case Virtual = 'virtual';
    case Telefonica = 'telefonica';

    public function getLabel(): string
    {
        return match ($this) {
            self::Presencial => 'Presencial',
            self::Virtual => 'Virtual',
            self::Telefonica => 'Telefónica',
        };
    }
}
