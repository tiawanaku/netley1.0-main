<?php

namespace App\Filament\Resources\Personals\Pages;

use App\Filament\Resources\Personals\PersonalResource;
use App\Models\Personal;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonal extends CreateRecord
{
    protected static string $resource = PersonalResource::class;

    protected function afterCreate(): void
    {
        /** @var Personal $record */
        $record = $this->record;

        if (blank($record->plainPassword)) {
            return;
        }

        Notification::make()
            ->title('Personal registrado — acceso al portal generado')
            ->body("Usuario: {$record->usuario}\nContraseña temporal: {$record->plainPassword}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar. Se le pedirá cambiarla en su primer inicio de sesión.")
            ->success()
            ->persistent()
            ->send();
    }
}
