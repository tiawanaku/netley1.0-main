<?php

namespace App\Filament\Pages;

use App\Models\Proceso;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Documentos extends Page
{
    protected string $view = 'filament.pages.documentos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Documentos';

    protected static ?string $navigationLabel = 'Documentos';

    protected static ?string $title = 'Generador de Documentos';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('proceso_id')
                    ->label('Proceso')
                    ->options(fn (): array => Proceso::with('cliente')
                        ->get()
                        ->mapWithKeys(fn (Proceso $proceso): array => [$proceso->id => $proceso->resumen])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->helperText('Selecciona el caso para el que quieres generar documentos.'),
            ])
            ->statePath('data');
    }

    protected function getProceso(): ?Proceso
    {
        $procesoId = $this->data['proceso_id'] ?? null;

        return $procesoId ? Proceso::with(['cliente.procesos', 'finanza.planPagos'])->find($procesoId) : null;
    }

    protected function documentoAction(string $nombre, string $etiqueta, string $vista, string $archivo, Closure $datos, bool $requiereFinanza = false): Action
    {
        return Action::make($nombre)
            ->label($etiqueta)
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->disabled(fn (): bool => blank($this->getProceso()))
            ->action(function () use ($vista, $archivo, $datos, $requiereFinanza) {
                $proceso = $this->getProceso();

                if (! $proceso) {
                    Notification::make()->title('Selecciona un proceso primero')->warning()->send();

                    return;
                }

                if ($requiereFinanza && ! $proceso->finanza) {
                    Notification::make()->title('Este proceso no tiene finanzas registradas todavía')->warning()->send();

                    return;
                }

                $pdf = Pdf::loadView($vista, $datos($proceso));

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    $archivo,
                );
            });
    }

    public function contratoAction(): Action
    {
        return $this->documentoAction(
            'contrato',
            'Contrato de Servicios',
            'pdf.contrato',
            'contrato-de-servicios.pdf',
            fn (Proceso $proceso) => ['proceso' => $proceso],
        );
    }

    public function planPagosAction(): Action
    {
        return $this->documentoAction(
            'plan-pagos',
            'Plan de Pagos',
            'pdf.plan-pagos',
            'plan-de-pagos.pdf',
            fn (Proceso $proceso) => ['proceso' => $proceso, 'finanza' => $proceso->finanza],
            requiereFinanza: true,
        );
    }

    public function aceptacionAction(): Action
    {
        return $this->documentoAction(
            'aceptacion',
            'Aceptación del Cliente',
            'pdf.aceptacion-cliente',
            'aceptacion-del-cliente.pdf',
            fn (Proceso $proceso) => ['proceso' => $proceso],
        );
    }

    public function fichaClienteAction(): Action
    {
        return $this->documentoAction(
            'ficha-cliente',
            'Ficha del Cliente',
            'pdf.ficha-cliente',
            'ficha-del-cliente.pdf',
            fn (Proceso $proceso) => ['cliente' => $proceso->cliente],
        );
    }

    public function fichaProcesoAction(): Action
    {
        return $this->documentoAction(
            'ficha-proceso',
            'Ficha del Proceso',
            'pdf.ficha-proceso',
            'ficha-del-proceso.pdf',
            fn (Proceso $proceso) => ['proceso' => $proceso],
        );
    }
}
