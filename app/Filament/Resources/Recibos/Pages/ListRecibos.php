<?php

namespace App\Filament\Resources\Recibos\Pages;

use App\Filament\Resources\Recibos\ReciboResource;
use Filament\Resources\Pages\ListRecords;

class ListRecibos extends ListRecords
{
    protected static string $resource = ReciboResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
