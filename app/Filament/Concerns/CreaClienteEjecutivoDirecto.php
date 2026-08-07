<?php

namespace App\Filament\Concerns;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoPersonal;
use App\Enums\EstadoProceso;
use App\Enums\RolPersonal;
use App\Enums\TipoPago;
use App\Models\Cliente;
use App\Models\Delito;
use App\Models\Finanza;
use App\Models\Personal;
use App\Models\Proceso;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Alta directa de un Cliente Ejecutivo (sin Consulta previa), usada tanto en
 * el panel Admin como en el panel Personal. Crea Cliente + Proceso + Finanza
 * en un solo wizard, replicando los datos que pide el cierre de cita en
 * MiAgenda pero sin depender de una Agenda/Consulta existente.
 */
trait CreaClienteEjecutivoDirecto
{
    use HasWizard;

    protected array $credencialesGeneradas = [];

    protected ?Proceso $procesoCreado = null;

    public function getSteps(): array
    {
        return [
            Step::make('Datos del cliente')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombres')
                        ->required(),
                    TextInput::make('apellido_paterno')
                        ->label('Apellido paterno')
                        ->required(),
                    TextInput::make('apellido_materno')
                        ->label('Apellido materno'),
                    TextInput::make('ci')
                        ->label('Número de carnet')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('telefono')
                        ->label('Teléfono')
                        ->tel()
                        ->required(),
                    TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel(),
                ]),

            Step::make('Datos del proceso')
                ->schema([
                    Select::make('materia_legal')
                        ->label('Materia legal')
                        ->options(CategoriaLegal::class)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set): void {
                            $set('abogado_id', null);
                            $set('tipo_proceso', null);
                        })
                        ->native(false),
                    Select::make('tipo_proceso')
                        ->label('Tipo de proceso / delito')
                        ->helperText('Filtrado según la materia legal. Puedes agregar uno nuevo si no está en la lista.')
                        ->options(fn (Get $get): array => Delito::opcionesPara($get('materia_legal')))
                        ->searchable()
                        ->native(false)
                        ->disabled(fn (Get $get): bool => blank($get('materia_legal')))
                        ->createOptionForm([
                            TextInput::make('delito')
                                ->label('Nuevo tipo de proceso / delito')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(function (array $data, Get $get): string {
                            Delito::firstOrCreate([
                                'area' => Delito::normalizarMateria($get('materia_legal')),
                                'delito' => $data['delito'],
                            ]);

                            return $data['delito'];
                        })
                        ->required(),
                    Select::make('abogado_id')
                        ->label('Asignar caso a')
                        ->helperText('Se muestran solo los abogados con especialidad en la materia legal seleccionada.')
                        ->options(fn (Get $get): array => Personal::query()
                            ->where('estado', EstadoPersonal::Activo)
                            ->where('rol', RolPersonal::Abogado)
                            ->when(
                                filled($get('materia_legal')),
                                fn ($query) => $query->where('especialidad_abogado', $get('materia_legal')),
                            )
                            ->orderBy('nombre')
                            ->get()
                            ->mapWithKeys(fn (Personal $personal): array => [$personal->id => $personal->nombre_completo])
                            ->all())
                        ->searchable()
                        ->native(false)
                        ->required(),
                    TextInput::make('tiempo_proceso_meses')
                        ->label('Tiempo del proceso (meses)')
                        ->numeric()
                        ->minValue(1)
                        ->live()
                        ->required(),
                ]),

            Step::make('Finanzas')
                ->schema([
                    TextInput::make('costo')
                        ->label('Iguala profesional (Bs.)')
                        ->numeric()
                        ->minValue(0.01)
                        ->live(onBlur: true)
                        ->required(),
                    Select::make('tipo_pago')
                        ->label('Tipo de pago')
                        ->options(function (Get $get): array {
                            $opciones = [
                                TipoPago::Semanal->value => TipoPago::Semanal->getLabel(),
                                TipoPago::Mensual->value => TipoPago::Mensual->getLabel(),
                            ];

                            $costo = (float) ($get('costo') ?? 0);
                            $anticipo = (float) ($get('anticipo') ?? 0);

                            if ($costo > 0 && abs($costo - $anticipo) < 0.01) {
                                $opciones[TipoPago::AlContado->value] = TipoPago::AlContado->getLabel();
                            }

                            return $opciones;
                        })
                        ->required()
                        ->live()
                        ->native(false),
                    TextInput::make('anticipo')
                        ->label('Anticipo / primer pago recibido (Bs.)')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('cuotas')
                        ->label('Número de cuotas (opcional, genera el plan de pagos ahora)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(function (Get $get): ?int {
                            $meses = (float) ($get('tiempo_proceso_meses') ?? 0);

                            if ($meses <= 0) {
                                return null;
                            }

                            return $get('tipo_pago') === TipoPago::Semanal->value
                                ? (int) round($meses * 52 / 12)
                                : (int) $meses;
                        })
                        ->visible(fn (Get $get): bool => $get('tipo_pago') !== TipoPago::AlContado->value)
                        ->dehydrated(fn (Get $get): bool => $get('tipo_pago') !== TipoPago::AlContado->value),
                    DatePicker::make('fecha_inicio_pagos')
                        ->label('Fecha de la primera cuota')
                        ->default(now())
                        ->visible(fn (Get $get): bool => $get('tipo_pago') !== TipoPago::AlContado->value)
                        ->dehydrated(fn (Get $get): bool => $get('tipo_pago') !== TipoPago::AlContado->value),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            ['cliente' => $cliente, 'password' => $password] = Cliente::crearDirecto([
                'nombre' => $data['nombre'],
                'apellidos' => trim("{$data['apellido_paterno']} {$data['apellido_materno']}"),
                'ci' => $data['ci'],
                'telefono' => $data['telefono'],
                'whatsapp' => $data['whatsapp'] ?? null,
            ]);

            $proceso = Proceso::create([
                'cliente_id' => $cliente->id,
                'materia_legal' => $data['materia_legal'],
                'tipo_proceso' => $data['tipo_proceso'],
                'tiempo_proceso_meses' => $data['tiempo_proceso_meses'],
                'abogado_id' => $data['abogado_id'],
                'estado' => EstadoProceso::Activo,
            ]);

            $anticipo = (float) ($data['anticipo'] ?? 0);

            $finanza = Finanza::create([
                'proceso_id' => $proceso->id,
                'costo' => $data['costo'],
                'tipo_pago' => $data['tipo_pago'],
                'anticipo' => $anticipo,
                'anticipo_registrado_en' => $anticipo > 0 ? now() : null,
            ]);

            if (! empty($data['cuotas'])) {
                $finanza->generarPlanPagos((int) $data['cuotas'], $data['fecha_inicio_pagos'] ?? now());
            }

            $this->credencialesGeneradas = [
                'usuario' => $cliente->usuario,
                'password' => $password,
            ];

            $this->procesoCreado = $proceso;

            return $cliente;
        });
    }

    protected function getCreatedNotification(): ?Notification
    {
        if (empty($this->credencialesGeneradas)) {
            return null;
        }

        return Notification::make()
            ->title('Cliente Ejecutivo y caso creados')
            ->body("Usuario: {$this->credencialesGeneradas['usuario']}\nContraseña temporal: {$this->credencialesGeneradas['password']}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar.\n\nSe descargó el contrato: imprímelo y fírmalo junto al cliente, luego súbelo firmado al repositorio de documentos del caso.")
            ->success()
            ->persistent();
    }

    protected function getSubmitFormLivewireMethodName(): string
    {
        return 'crearYGenerarContrato';
    }

    /**
     * Llama al flujo normal de creación (que ya guarda, notifica y redirige)
     * y además descarga el contrato de servicios del caso recién creado.
     */
    public function crearYGenerarContrato(): ?StreamedResponse
    {
        $this->create();

        if (! $this->procesoCreado) {
            return null;
        }

        $pdf = Pdf::loadView('pdf.contrato', ['proceso' => $this->procesoCreado]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            "contrato-{$this->procesoCreado->id}.pdf",
        );
    }
}
