<?php

namespace App\Filament\Cliente\Pages;

use App\Enums\EstadoAgenda;
use App\Enums\EstadoPago;
use App\Enums\EstadoProceso;
use App\Enums\TipoAgenda;
use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Personal;
use App\Models\PlanPago;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected static string $routePath = '/';

    protected string $view = 'filament.cliente.pages.dashboard';

    protected static ?string $navigationLabel = 'Mi Panel';

    protected static ?string $title = 'Mi Panel';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public function getCliente(): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = Filament::auth()->user();

        return $cliente->load(['procesos.abogado', 'procesos.agendas', 'procesos.finanza.planPagos']);
    }

    public function getAbogadoPatrocinante(): ?Personal
    {
        $procesos = $this->getCliente()->procesos;

        return $procesos->firstWhere('estado', EstadoProceso::Activo)?->abogado
            ?? $procesos->first(fn ($proceso) => $proceso->abogado)?->abogado;
    }

    public function getProcesosActivosCount(): int
    {
        return $this->getCliente()->procesos
            ->where('estado', EstadoProceso::Activo)
            ->count();
    }

    public function getProximaCita(): ?Agenda
    {
        $procesoIds = $this->getCliente()->procesos->pluck('id');

        return Agenda::query()
            ->where('tipo', TipoAgenda::Cita)
            ->where('estado', '!=', EstadoAgenda::Cancelada)
            ->where('fecha_inicio', '>=', now())
            ->whereIn('proceso_id', $procesoIds)
            ->orderBy('fecha_inicio')
            ->first();
    }

    public function getProximoPago(): ?PlanPago
    {
        return PlanPago::query()
            ->whereIn('finanza_id', $this->getCliente()->procesos->pluck('finanza.id')->filter())
            ->whereNotIn('estado', [EstadoPago::Pagado])
            ->orderBy('fecha')
            ->first();
    }

    public function getNotificacionesRecientes()
    {
        return $this->getCliente()->notifications()->latest()->limit(5)->get();
    }

    public function fichaClienteAction(): Action
    {
        return Action::make('ficha-cliente')
            ->label('Ficha del Cliente')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->size('sm')
            ->outlined()
            ->action(function () {
                $cliente = $this->getCliente();
                $pdf = Pdf::loadView('pdf.ficha-cliente', ['cliente' => $cliente]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'ficha-del-cliente.pdf',
                );
            });
    }
}
