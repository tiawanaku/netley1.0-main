<?php

namespace App\Filament\Resources\Agendas\Pages;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Models\Agenda;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAgenda extends CreateRecord
{
    protected static string $resource = AgendaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        if (
            filled($data['responsable_id'] ?? null)
            && Agenda::hayConflicto((int) $data['responsable_id'], Carbon::parse($data['fecha_inicio']), Carbon::parse($data['fecha_fin']))
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
