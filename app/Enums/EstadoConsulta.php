<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EstadoConsulta: string implements HasColor, HasLabel
{
    case Nueva = 'nueva';
    case Agendada = 'agendada';
    case ReAgendada = 're_agendada';
    case ClienteEjecutivo = 'cliente_ejecutivo';
    case NoAsistio = 'no_asistio';
    case Descartada = 'descartada';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nueva => 'Nueva',
            self::Agendada => 'Agendada',
            self::ReAgendada => 'Re Agendada',
            self::ClienteEjecutivo => 'Cliente Ejecutivo',
            self::NoAsistio => 'No Asistió',
            self::Descartada => 'Descartada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Nueva => 'info',
            self::Agendada => 'warning',
            self::ReAgendada => 'primary',
            self::ClienteEjecutivo => 'success',
            self::NoAsistio => 'danger',
            self::Descartada => 'gray',
        };
    }
}
