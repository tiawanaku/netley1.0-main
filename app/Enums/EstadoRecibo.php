<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoRecibo: string implements HasColor, HasLabel
{
    case Emitido = 'emitido';
    case Anulado = 'anulado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Emitido => 'Emitido',
            self::Anulado => 'Anulado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Emitido => 'success',
            self::Anulado => 'danger',
        };
    }
}
