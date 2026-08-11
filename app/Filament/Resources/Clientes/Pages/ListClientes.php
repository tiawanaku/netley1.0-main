<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Cliente Ejecutivo'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'pendiente' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->conCasosPendientes()),
            'cerrado' => Tab::make('Cerrados')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->conCasosCerrados()),
        ];
    }
}
