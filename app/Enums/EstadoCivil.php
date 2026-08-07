<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EstadoCivil: string implements HasLabel
{
    case Soltero = 'soltero';
    case Casado = 'casado';
    case Divorciado = 'divorciado';
    case Viudo = 'viudo';
    case UnionLibre = 'union_libre';

    public function getLabel(): string
    {
        return match ($this) {
            self::Soltero => 'Soltero(a)',
            self::Casado => 'Casado(a)',
            self::Divorciado => 'Divorciado(a)',
            self::Viudo => 'Viudo(a)',
            self::UnionLibre => 'Unión Libre',
        };
    }
}
