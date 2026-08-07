<?php

namespace App\Filament\Resources\Finanzas\Pages;

use App\Filament\Resources\Finanzas\FinanzaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanzas extends ListRecords
{
    protected static string $resource = FinanzaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
