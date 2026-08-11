<?php

namespace App\Filament\Resources\Procesos\Pages;

use App\Filament\Resources\Procesos\ProcesoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProcesos extends ListRecords
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'pendiente' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->pendientes()),
            'cerrado' => Tab::make('Cerrados')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->cerrados()),
        ];
    }
}
