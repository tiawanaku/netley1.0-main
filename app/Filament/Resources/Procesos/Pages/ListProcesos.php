<?php

namespace App\Filament\Resources\Procesos\Pages;

use App\Filament\Resources\Procesos\ProcesoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcesos extends ListRecords
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
