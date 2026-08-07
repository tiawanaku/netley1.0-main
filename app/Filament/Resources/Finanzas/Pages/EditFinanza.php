<?php

namespace App\Filament\Resources\Finanzas\Pages;

use App\Filament\Resources\Finanzas\FinanzaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanza extends EditRecord
{
    protected static string $resource = FinanzaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
