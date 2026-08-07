<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RolPersonal: string implements HasColor, HasLabel
{
    case Administrador = 'administrador';
    case Abogado = 'abogado';
    case Asistente = 'asistente';
    case Secretaria = 'secretaria';
    case Contador = 'contador';
    case Psicologo = 'psicologo';
    case TrabajadorSocial = 'trabajador_social';

    public function getLabel(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Abogado => 'Abogado',
            self::Asistente => 'Asistente',
            self::Secretaria => 'Secretaria',
            self::Contador => 'Contador',
            self::Psicologo => 'Psicólogo',
            self::TrabajadorSocial => 'Trabajador Social',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Administrador => 'danger',
            self::Abogado => 'primary',
            self::Asistente => 'info',
            self::Secretaria => 'warning',
            self::Contador => 'gray',
            self::Psicologo => 'info',
            self::TrabajadorSocial => 'warning',
        };
    }
}
