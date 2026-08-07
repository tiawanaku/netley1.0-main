<?php

namespace App\Filament\Widgets;

use App\Enums\EstadoAgenda;
use App\Enums\TipoAgenda;
use App\Filament\Resources\Agendas\AgendaResource;
use App\Models\Agenda;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AgendaCalendarWidget extends FullCalendarWidget
{
    protected static bool $isLazy = false;

    public Model|string|null $model = Agenda::class;

    public ?int $responsableId = null;

    /**
     * AG-14: filtro por Personal desde la barra de filtros de la página del calendario.
     */
    #[On('agenda-filtro-personal')]
    public function actualizarFiltroPersonal(?int $responsableId): void
    {
        $this->responsableId = $responsableId;
        $this->refreshRecords();
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ];
    }

    public function fetchEvents(array $info): array
    {
        return Agenda::query()
            ->whereBetween('fecha_inicio', [$info['start'], $info['end']])
            ->when($this->responsableId, fn ($query) => $query->where('responsable_id', $this->responsableId))
            ->with(['proceso.cliente', 'consulta', 'cliente', 'responsable'])
            ->get()
            ->map(function (Agenda $agenda): array {
                $titulo = $this->tituloPara($agenda);

                return EventData::make()
                    ->id($agenda->id)
                    ->title($titulo)
                    ->start($agenda->fecha_inicio)
                    ->end($agenda->fecha_fin)
                    ->backgroundColor($this->colorPara($agenda))
                    ->borderColor($this->colorPara($agenda))
                    ->toArray();
            })
            ->all();
    }

    /**
     * AG-13/AG-15: para citas se muestra el id del cliente y el tipo de
     * proceso/delito en vez del nombre, más el personal asignado.
     */
    protected function tituloPara(Agenda $agenda): string
    {
        if ($agenda->tipo === TipoAgenda::Cita) {
            $cliente = $agenda->proceso?->cliente ?? $agenda->cliente;
            $tipoProceso = $agenda->proceso?->tipo_proceso;

            $titulo = $cliente ? "Cliente #{$cliente->id}" : $agenda->tipo->getLabel();

            if ($tipoProceso) {
                $titulo .= " — {$tipoProceso}";
            }
        } else {
            $titulo = $agenda->relacionado_con ?: $agenda->tipo->getLabel();
        }

        if ($agenda->responsable) {
            $titulo .= " — {$agenda->responsable->nombre_completo}";
        }

        return $titulo;
    }

    protected function colorPara(Agenda $agenda): string
    {
        return match (true) {
            $agenda->estado === EstadoAgenda::Cancelada => '#ef4444',
            $agenda->estado === EstadoAgenda::Reagendada => '#eab308',
            $agenda->tipo === TipoAgenda::Cita => '#22c55e',
            $agenda->tipo === TipoAgenda::Llamada => '#3b82f6',
            $agenda->tipo === TipoAgenda::Reunion => '#a855f7',
            default => '#6b7280',
        };
    }

    public function getFormSchema(): array
    {
        return AgendaResource::formComponents();
    }

    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by'] = auth()->id();

                    return $data;
                }),
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        return $this->reagendarDesdeCalendario((int) $event['id'], $event['start'], $event['end'] ?? $event['start']);
    }

    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        return $this->reagendarDesdeCalendario((int) $event['id'], $event['start'], $event['end'] ?? $event['start']);
    }

    protected function reagendarDesdeCalendario(int $agendaId, string $inicio, string $fin): bool
    {
        $agenda = Agenda::find($agendaId);

        if (! $agenda) {
            return true;
        }

        $nuevoInicio = Carbon::parse($inicio);
        $nuevoFin = Carbon::parse($fin);

        if ($agenda->responsable_id && Agenda::hayConflicto($agenda->responsable_id, $nuevoInicio, $nuevoFin, $agenda->id)) {
            Notification::make()
                ->title('Conflicto de horario')
                ->body('El profesional ya tiene una actividad registrada en ese horario. El cambio fue revertido.')
                ->danger()
                ->send();

            return true;
        }

        $agenda->update([
            'fecha_inicio' => $nuevoInicio,
            'fecha_fin' => $nuevoFin,
            'estado' => EstadoAgenda::Reagendada,
        ]);

        return false;
    }
}
