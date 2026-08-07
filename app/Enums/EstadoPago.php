<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoPago: string implements HasColor, HasLabel
{
    case Pendiente = 'pendiente';
    case PendienteConfirmacion = 'pendiente_confirmacion';
    case Pagado = 'pagado';
    case Vencido = 'vencido';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::PendienteConfirmacion => 'Pendiente de confirmación',
            self::Pagado => 'Pagado',
            self::Vencido => 'Vencido',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendiente => 'warning',
            self::PendienteConfirmacion => 'info',
            self::Pagado => 'success',
            self::Vencido => 'danger',
        };
    }
}
