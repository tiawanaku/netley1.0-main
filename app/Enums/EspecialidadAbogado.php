<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EspecialidadAbogado: string implements HasLabel
{
    case Civil = 'civil';
    case Penal = 'penal';
    case Familiar = 'familiar';
    case Laboral = 'laboral';
    case Comercial = 'comercial';
    case Administrativo = 'administrativo';
    case Tributario = 'tributario';
    case Constitucional = 'constitucional';
    case Ambiental = 'ambiental';
    case Minero = 'minero';
    case Migratorio = 'migratorio';
    case PropiedadIntelectual = 'propiedad_intelectual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Civil => 'Civil',
            self::Penal => 'Penal',
            self::Familiar => 'Familiar',
            self::Laboral => 'Laboral',
            self::Comercial => 'Comercial y Empresarial',
            self::Administrativo => 'Administrativo',
            self::Tributario => 'Tributario',
            self::Constitucional => 'Constitucional',
            self::Ambiental => 'Ambiental',
            self::Minero => 'Minero',
            self::Migratorio => 'Migratorio',
            self::PropiedadIntelectual => 'Propiedad Intelectual',
        };
    }
}
