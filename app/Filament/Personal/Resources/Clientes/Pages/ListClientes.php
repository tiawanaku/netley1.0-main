<?php

namespace App\Filament\Personal\Resources\Clientes\Pages;

use App\Filament\Personal\Resources\Clientes\ClienteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
}
