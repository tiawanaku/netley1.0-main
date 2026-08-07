<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrigenDocumento: string implements HasLabel
{
    case Staff = 'staff';
    case Cliente = 'cliente';

    public function getLabel(): string
    {
        return match ($this) {
            self::Staff => 'Personal Netley',
            self::Cliente => 'Cliente',
        };
    }
}
