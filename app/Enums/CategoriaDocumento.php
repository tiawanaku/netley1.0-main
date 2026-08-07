<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CategoriaDocumento: string implements HasColor, HasLabel
{
    case Contrato = 'contrato';
    case Poder = 'poder';
    case Memorial = 'memorial';
    case Resolucion = 'resolucion';
    case Demanda = 'demanda';
    case Fotografia = 'fotografia';
    case Prueba = 'prueba';
    case Otro = 'otro';

    public function getLabel(): string
    {
        return match ($this) {
            self::Contrato => 'Contrato',
            self::Poder => 'Poder',
            self::Memorial => 'Memorial',
            self::Resolucion => 'Resolución',
            self::Demanda => 'Demanda',
            self::Fotografia => 'Fotografía',
            self::Prueba => 'Prueba',
            self::Otro => 'Otro',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Contrato => 'success',
            self::Poder => 'info',
            self::Memorial => 'primary',
            self::Resolucion => 'warning',
            self::Demanda => 'danger',
            self::Fotografia => 'gray',
            self::Prueba => 'gray',
            self::Otro => 'gray',
        };
    }
}
