<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrigenConsulta: string implements HasLabel
{
    case Referido = 'referido';
    case PaginaWeb = 'pagina_web';
    case ColegioConvenio = 'colegio_convenio';
    case LlamadaDirecta = 'llamada_directa';
    case RedesSociales = 'redes_sociales';
    case Publicidad = 'publicidad';

    public function getLabel(): string
    {
        return match ($this) {
            self::Referido => 'Referido',
            self::PaginaWeb => 'Página Web',
            self::ColegioConvenio => 'Colegio/Convenio',
            self::LlamadaDirecta => 'Llamada Directa',
            self::RedesSociales => 'Redes Sociales',
            self::Publicidad => 'Publicidad',
        };
    }
}
