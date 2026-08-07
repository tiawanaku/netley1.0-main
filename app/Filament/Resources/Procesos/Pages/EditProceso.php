<?php

namespace App\Filament\Resources\Procesos\Pages;

use App\Filament\Resources\Procesos\ProcesoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProceso extends EditRecord
{
    protected static string $resource = ProcesoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
