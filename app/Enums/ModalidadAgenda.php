<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ModalidadAgenda: string implements HasLabel
{
    case Presencial = 'presencial';
    case Virtual = 'virtual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Presencial => 'Presencial',
            self::Virtual => 'Virtual',
        };
    }
}
