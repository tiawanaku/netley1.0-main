<?php

namespace App\Filament\Personal\Widgets;

use App\Enums\EstadoAgenda;
use App\Enums\TipoAgenda;
use App\Models\Agenda;
use App\Models\Personal;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class MiAgendaCalendarWidget extends FullCalendarWidget
{
    protected static bool $isLazy = false;

    public Model | string | null $model = Agenda::class;

    public function getPersonal(): Personal
    {
        /** @var Personal $personal */
        $personal = Filament::auth()->user();

        return $personal;
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

    /**
     * Solo lectura: el abogado no puede arrastrar, redimensionar, crear
     * ni editar actividades desde el calendario, solo visualizarlas.
     */
    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }

    /**
     * No-op: evita que el clic en un evento intente montar la acción "view"
     * por defecto del paquete. El calendario es puramente informativo.
     */
    public function onEventClick(array $event): void
    {
        //
    }

    public function fetchEvents(array $info): array
    {
        return Agenda::query()
            ->where('responsable_id', $this->getPersonal()->id)
            ->whereBetween('fecha_inicio', [$info['start'], $info['end']])
            ->with(['proceso.cliente', 'consulta', 'cliente'])
            ->get()
            ->map(function (Agenda $agenda): array {
                return EventData::make()
                    ->id($agenda->id)
                    ->title($this->tituloPara($agenda))
                    ->start($agenda->fecha_inicio)
                    ->end($agenda->fecha_fin)
                    ->backgroundColor($this->colorPara($agenda))
                    ->borderColor($this->colorPara($agenda))
                    ->toArray();
            })
            ->all();
    }

    protected function tituloPara(Agenda $agenda): string
    {
        if ($agenda->tipo === TipoAgenda::Cita) {
            $cliente = $agenda->proceso?->cliente ?? $agenda->cliente;
            $tipoProceso = $agenda->proceso?->tipo_proceso;

            $titulo = $cliente?->nombre_completo ?? $agenda->consulta?->nombre_completo ?? $agenda->tipo->getLabel();

            if ($tipoProceso) {
                $titulo .= " — {$tipoProceso}";
            }

            return $titulo;
        }

        return $agenda->relacionado_con ?: $agenda->tipo->getLabel();
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
}
