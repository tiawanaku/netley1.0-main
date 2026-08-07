<?php

namespace App\Filament\Resources\Agendas\Pages;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Models\Agenda;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgenda extends EditRecord
{
    protected static string $resource = AgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (
            filled($data['responsable_id'] ?? null)
            && Agenda::hayConflicto((int) $data['responsable_id'], Carbon::parse($data['fecha_inicio']), Carbon::parse($data['fecha_fin']), $this->getRecord()->getKey())
        ) {
            Notification::make()
                ->title('Conflicto de horario')
                ->body('El profesional seleccionado ya tiene una actividad registrada en ese horario.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
