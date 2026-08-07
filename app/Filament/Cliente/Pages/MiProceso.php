<?php

namespace App\Filament\Cliente\Pages;

use App\Enums\CategoriaDocumento;
use App\Enums\EstadoPago;
use App\Models\Cliente;
use App\Models\DocumentoSolicitud;
use App\Models\PlanPago;
use App\Models\Proceso;
use App\Models\ProcesoDocumento;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class MiProceso extends Page
{
    protected static string $routePath = '/procesos/{proceso}';

    protected string $view = 'filament.cliente.pages.mi-proceso';

    protected static bool $shouldRegisterNavigation = false;

    public Proceso $proceso;

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'procesos/{proceso}';
    }

    public function mount(Proceso $proceso): void
    {
        abort_unless($proceso->cliente_id === $this->getCliente()->id, 403);

        $this->proceso = $proceso;
    }

    public function getTitle(): string
    {
        return $this->proceso->tipo_proceso;
    }

    public function getCliente(): Cliente
    {
        /** @var Cliente $cliente */
        $cliente = Filament::auth()->user();

        return $cliente;
    }

    public function getProceso(): Proceso
    {
        return $this->proceso->fresh([
            'abogado',
            'finanza.planPagos',
            'documentos.personal',
            'documentos.cliente',
            'solicitudesDocumento.personal',
            'agendas',
        ]);
    }

    // --- Documentos generados (PDF) ---

    protected function documentoAction(string $nombre, string $etiqueta, string $vista, string $archivoBase, \Closure $datos, bool $requiereFinanza = false): Action
    {
        return Action::make($nombre)
            ->label($etiqueta)
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->size('sm')
            ->outlined()
            ->action(function () use ($vista, $archivoBase, $datos, $requiereFinanza) {
                $proceso = $this->getProceso();

                if ($requiereFinanza && ! $proceso->finanza) {
                    Notification::make()->title('Este proceso no tiene finanzas registradas todavía')->warning()->send();

                    return;
                }

                $pdf = Pdf::loadView($vista, $datos($proceso));

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    "{$archivoBase}.pdf",
                );
            });
    }

    public function contratoAction(): Action
    {
        return $this->documentoAction('contrato', 'Contrato de Servicios', 'pdf.contrato', 'contrato-de-servicios', fn (Proceso $proceso) => ['proceso' => $proceso]);
    }

    public function planPagosAction(): Action
    {
        return $this->documentoAction('plan-pagos', 'Plan de Pagos', 'pdf.plan-pagos', 'plan-de-pagos', fn (Proceso $proceso) => ['proceso' => $proceso, 'finanza' => $proceso->finanza], requiereFinanza: true);
    }

    public function fichaProcesoAction(): Action
    {
        return $this->documentoAction('ficha-proceso', 'Ficha del Proceso', 'pdf.ficha-proceso', 'ficha-del-proceso', fn (Proceso $proceso) => ['proceso' => $proceso]);
    }

    // --- Expediente digital ---

    public function subirDocumentoAction(): Action
    {
        return Action::make('subirDocumento')
            ->label('Subir documento')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Select::make('categoria')
                    ->options(CategoriaDocumento::class)
                    ->default(CategoriaDocumento::Otro)
                    ->required()
                    ->native(false),
                FileUpload::make('archivo')
                    ->required()
                    ->disk('public')
                    ->directory('procesos/documentos')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/png',
                        'image/jpeg',
                    ]),
            ])
            ->action(function (array $data, array $arguments): void {
                $documento = $this->proceso->documentos()->create([
                    'nombre' => $data['nombre'],
                    'categoria' => $data['categoria'],
                    'archivo' => $data['archivo'],
                    'origen' => 'cliente',
                    'cliente_id' => $this->getCliente()->id,
                    'solicitud_id' => $arguments['solicitud'] ?? null,
                ]);

                if ($solicitudId = $arguments['solicitud'] ?? null) {
                    DocumentoSolicitud::find($solicitudId)?->marcarCumplida();
                }

                Notification::make()->title('Documento subido')->success()->send();
            });
    }

    public function descargarDocumentoUrl(ProcesoDocumento $documento): string
    {
        return Storage::disk('public')->url($documento->archivo);
    }

    // --- Pagos y QR ---

    public function subirComprobanteAction(): Action
    {
        return Action::make('subirComprobante')
            ->label('Subir comprobante')
            ->icon(Heroicon::OutlinedCamera)
            ->schema([
                FileUpload::make('comprobante')
                    ->label('Comprobante de pago')
                    ->required()
                    ->disk('public')
                    ->directory('pagos/comprobantes')
                    ->image(),
            ])
            ->action(function (array $data, array $arguments): void {
                $planPago = PlanPago::find($arguments['planPago']);

                if (! $planPago || $planPago->finanza->proceso->cliente_id !== $this->getCliente()->id) {
                    abort(403);
                }

                $planPago->registrarPagoCliente($data['comprobante']);

                Notification::make()
                    ->title('Pago registrado')
                    ->body('Tu comprobante fue enviado. Quedará pendiente de confirmación por el equipo de Netley.')
                    ->success()
                    ->send();
            });
    }

    public function qrUrlPara(PlanPago $planPago): string
    {
        return Storage::disk('public')->url($planPago->generarQr());
    }
}
