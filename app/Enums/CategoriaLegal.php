<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoriaLegal: string implements HasLabel
{
    case Familiar = 'familiar';
    case Laboral = 'laboral';
    case Civil = 'civil';
    case Penal = 'penal';
    case Comercial = 'comercial';
    case Administrativo = 'administrativo';
    case Tributario = 'tributario';

    public function getLabel(): string
    {
        return match ($this) {
            self::Familiar => 'Familiar',
            self::Laboral => 'Laboral',
            self::Civil => 'Civil',
            self::Penal => 'Penal',
            self::Comercial => 'Comercial',
            self::Administrativo => 'Administrativo',
            self::Tributario => 'Tributario',
        };
    }
}
