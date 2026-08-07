<?php

namespace App\Filament\Personal\Pages;

use App\Enums\CategoriaDocumento;
use App\Enums\OrigenDocumento;
use App\Models\Personal;
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

class VerCaso extends Page
{
    protected static string $routePath = '/casos/{proceso}';

    protected string $view = 'filament.personal.pages.ver-caso';

    protected static bool $shouldRegisterNavigation = false;

    public Proceso $proceso;

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'casos/{proceso}';
    }

    public function mount(Proceso $proceso): void
    {
        abort_unless($proceso->abogado_id === $this->getPersonal()->id, 403);

        $this->proceso = $proceso;
    }

    public function getTitle(): string
    {
        return "{$this->proceso->cliente?->nombre_completo} — {$this->proceso->tipo_proceso}";
    }

    public function getPersonal(): Personal
    {
        /** @var Personal $personal */
        $personal = Filament::auth()->user();

        return $personal;
    }

    public function getProceso(): Proceso
    {
        return $this->proceso->fresh([
            'cliente',
            'finanza.planPagos',
            'documentos.personal',
            'documentos.cliente',
        ]);
    }

    public function contratoAction(): Action
    {
        return Action::make('contrato')
            ->label('Descargar contrato')
            ->icon(Heroicon::OutlinedDocumentText)
            ->action(function () {
                $pdf = Pdf::loadView('pdf.contrato', ['proceso' => $this->getProceso()]);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    "contrato-{$this->proceso->id}.pdf",
                );
            });
    }

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
            ->action(function (array $data): void {
                $this->proceso->documentos()->create([
                    'nombre' => $data['nombre'],
                    'categoria' => $data['categoria'],
                    'archivo' => $data['archivo'],
                    'origen' => OrigenDocumento::Staff,
                    'personal_id' => $this->getPersonal()->id,
                ]);

                Notification::make()->title('Documento subido')->success()->send();
            });
    }

    public function descargarDocumentoUrl(ProcesoDocumento $documento): string
    {
        return Storage::disk('public')->url($documento->archivo);
    }
}
