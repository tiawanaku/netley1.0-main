<?php

namespace App\Filament\Resources\Consultas\Pages;

use App\Enums\EstadoConsulta;
use App\Filament\Resources\Consultas\ConsultaResource;
use App\Models\Consulta;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListConsultas extends ListRecords
{
    protected static string $resource = ConsultaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'todas' => Tab::make('Todas'),
        ];

        foreach (EstadoConsulta::cases() as $estado) {
            $tabs[$estado->value] = Tab::make($estado->getLabel())
                ->query(fn (Builder $query) => $query->where('estado', $estado))
                ->badge(fn (): int => Consulta::query()->where('estado', $estado)->count())
                ->badgeColor($estado->getColor());
        }

        return $tabs;
    }
}
