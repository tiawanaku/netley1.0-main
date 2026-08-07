<?php

namespace App\Filament\Personal\Pages;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoAgenda;
use App\Enums\EstadoConsulta;
use App\Enums\EstadoPersonal;
use App\Enums\EstadoProceso;
use App\Enums\ResultadoLlamada;
use App\Enums\RolPersonal;
use App\Enums\TipoAgenda;
use App\Enums\TipoPago;
use App\Filament\Personal\Widgets\MiAgendaCalendarWidget;
use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Delito;
use App\Models\Finanza;
use App\Models\Personal;
use App\Models\Proceso;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class MiAgenda extends Page
{
    protected static string $routePath = '/';

    protected string $view = 'filament.personal.pages.mi-agenda';

    protected static ?string $title = 'Mi Agenda';

    protected static ?string $navigationLabel = 'Mi Agenda';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MiAgendaCalendarWidget::class,
        ];
    }

    public function getPersonal(): Personal
    {
        /** @var Personal $personal */
        $personal = Filament::auth()->user();

        return $personal;
    }

    /**
     * @return Collection<int, Agenda>
     */
    public function getAgendas(): Collection
    {
        return $this->getPersonal()
            ->agendas()
            ->with(['consulta.cliente', 'proceso.cliente'])
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    /**
     * @return Collection<int, Proceso>
     */
    public function getCasos(): Collection
    {
        return $this->getPersonal()
            ->procesos()
            ->with('cliente')
            ->latest()
            ->get();
    }

    protected function agendaDelResponsable(int $agendaId): Agenda
    {
        return Agenda::query()
            ->where('responsable_id', $this->getPersonal()->id)
            ->with('consulta.cliente')
            ->findOrFail($agendaId);
    }

    public function cierreAction(): Action
    {
        return Action::make('cierre')
            ->label('Cierre')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->steps(function (array $arguments): array {
                $agenda = $this->agendaDelResponsable($arguments['agenda']);
                $consulta = $agenda->consulta;
                $puedeConvertir = $agenda->tipo === TipoAgenda::Cita
                    && $consulta
                    && ! $consulta->cliente()->exists();

                return [
                    Step::make('Resultado de la cita')
                        ->schema([
                            Radio::make('tipo_cierre')
                                ->label('¿Cómo terminó la cita?')
                                ->options(array_filter([
                                    'cliente_ejecutivo' => $puedeConvertir ? 'Cliente Ejecutivo' : null,
                                    'solo_consulta' => 'Solo consulta',
                                    'derivar' => 'Derivar (Psicología / Trabajo Social)',
                                    'no_asistio' => 'No asistió / no contesta',
                                    'reagendar' => 'Reagendar',
                                ]))
                                ->required()
                                ->live(),

                            DateTimePicker::make('fecha_inicio')
                                ->label('Nueva fecha y hora')
                                ->helperText('Lunes a viernes, de 08:00 a 17:00, en bloques de 15 minutos.')
                                ->default($agenda->fecha_inicio)
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(15)
                                ->minDate(now())
                                ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'reagendar')
                                ->required(fn (Get $get): bool => $get('tipo_cierre') === 'reagendar')
                                ->rule(fn (): \Closure => Agenda::reglaHorarioLaboral(), fn (Get $get): bool => $get('tipo_cierre') === 'reagendar'),

                            Select::make('especialidad')
                                ->label('Derivar a')
                                ->options([
                                    RolPersonal::Psicologo->value => RolPersonal::Psicologo->getLabel(),
                                    RolPersonal::TrabajadorSocial->value => RolPersonal::TrabajadorSocial->getLabel(),
                                ])
                                ->live()
                                ->native(false)
                                ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'derivar')
                                ->required(fn (Get $get): bool => $get('tipo_cierre') === 'derivar'),
                            Select::make('responsable_id')
                                ->label('Asignar a')
                                ->options(function (Get $get): array {
                                    if (! $get('especialidad')) {
                                        return [];
                                    }

                                    return Personal::query()
                                        ->where('rol', $get('especialidad'))
                                        ->where('estado', EstadoPersonal::Activo)
                                        ->get()
                                        ->mapWithKeys(fn (Personal $personal): array => [$personal->id => $personal->nombre_completo])
                                        ->all();
                                })
                                ->native(false)
                                ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'derivar')
                                ->required(fn (Get $get): bool => $get('tipo_cierre') === 'derivar'),
                            DateTimePicker::make('fecha_derivacion')
                                ->label('Fecha y hora de la derivación')
                                ->helperText('Lunes a viernes, de 08:00 a 17:00, en bloques de 15 minutos.')
                                ->default(now()->addDay()->setTime(9, 0))
                                ->native(false)
                                ->seconds(false)
                                ->minutesStep(15)
                                ->minDate(now())
                                ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'derivar')
                                ->required(fn (Get $get): bool => $get('tipo_cierre') === 'derivar')
                                ->rule(fn (): \Closure => Agenda::reglaHorarioLaboral(), fn (Get $get): bool => $get('tipo_cierre') === 'derivar'),
                            Textarea::make('notas')
                                ->label('Notas para el especialista')
                                ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'derivar'),
                        ]),

                    Step::make('Validación de datos')
                        ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'cliente_ejecutivo')
                        ->schema([
                            TextInput::make('nombre')
                                ->label('Nombres')
                                ->default($consulta?->nombre)
                                ->required(),
                            TextInput::make('apellido_paterno')
                                ->label('Apellido paterno')
                                ->default($consulta?->apellido_paterno)
                                ->required(),
                            TextInput::make('apellido_materno')
                                ->label('Apellido materno')
                                ->default($consulta?->apellido_materno),
                            TextInput::make('ci')
                                ->label('Número de carnet')
                                ->default($consulta?->ci)
                                ->required()
                                ->maxLength(20),
                            TextInput::make('telefono')
                                ->label('Teléfono')
                                ->default($consulta?->telefono)
                                ->required(),
                            TextInput::make('whatsapp')
                                ->label('WhatsApp')
                                ->default($consulta?->whatsapp),
                        ]),

                    Step::make('Datos del proceso')
                        ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'cliente_ejecutivo')
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
                                ->default(fn (): ?int => $this->getPersonal()->esAbogado() ? $this->getPersonal()->id : null)
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
                        ->visible(fn (Get $get): bool => $get('tipo_cierre') === 'cliente_ejecutivo')
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
            })
            ->action(function (array $data, array $arguments) {
                return $this->procesarCierre($this->agendaDelResponsable($arguments['agenda']), $data);
            });
    }

    private function procesarCierre(Agenda $agenda, array $data)
    {
        return match ($data['tipo_cierre']) {
            'solo_consulta' => $this->cerrarSoloConsulta($agenda),
            'no_asistio' => $this->cerrarNoAsistio($agenda),
            'reagendar' => $this->cerrarReagendar($agenda, $data),
            'derivar' => $this->cerrarDerivar($agenda, $data),
            'cliente_ejecutivo' => $this->cerrarClienteEjecutivo($agenda, $data),
        };
    }

    private function cerrarSoloConsulta(Agenda $agenda): void
    {
        $agenda->update(['estado' => EstadoAgenda::Finalizada]);

        Notification::make()
            ->title('Cita cerrada como solo consulta')
            ->success()
            ->send();
    }

    private function cerrarNoAsistio(Agenda $agenda): void
    {
        $agenda->update([
            'estado' => EstadoAgenda::Finalizada,
            'resultado' => ResultadoLlamada::NoContesto,
        ]);

        $agenda->consulta?->update(['estado' => EstadoConsulta::NoAsistio]);

        Notification::make()
            ->title('Cita cerrada: no asistió / no contesta')
            ->success()
            ->send();
    }

    private function cerrarReagendar(Agenda $agenda, array $data): void
    {
        $inicio = Carbon::parse($data['fecha_inicio']);
        $fin = $inicio->copy()->addMinutes(15);

        if ($agenda->responsable_id && Agenda::hayConflicto($agenda->responsable_id, $inicio, $fin, $agenda->id)) {
            Notification::make()
                ->title('Conflicto de horario')
                ->body('Ya tienes una actividad registrada en ese horario.')
                ->danger()
                ->send();

            return;
        }

        $agenda->update([
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'estado' => EstadoAgenda::Reagendada,
        ]);

        $agenda->consulta?->update(['estado' => EstadoConsulta::ReAgendada]);

        Notification::make()
            ->title('Cita reagendada')
            ->success()
            ->send();
    }

    private function cerrarDerivar(Agenda $agenda, array $data): void
    {
        $especialidad = RolPersonal::from($data['especialidad']);

        $agenda->update([
            'estado' => EstadoAgenda::Finalizada,
            'descripcion' => trim(($agenda->descripcion ? $agenda->descripcion."\n" : '')."Derivado a {$especialidad->getLabel()}."),
        ]);

        $fechaInicio = Carbon::parse($data['fecha_derivacion']);

        Agenda::create([
            'tipo' => TipoAgenda::Reunion,
            'estado' => EstadoAgenda::Pendiente,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaInicio->copy()->addMinutes(60),
            'asunto' => "Derivación a {$especialidad->getLabel()}",
            'descripcion' => $data['notas'] ?? null,
            'consulta_id' => $agenda->consulta_id,
            'cliente_id' => $agenda->cliente_id,
            'responsable_id' => $data['responsable_id'],
        ]);

        Notification::make()
            ->title('Cita derivada')
            ->body("Se creó una nueva tarea asignada para {$especialidad->getLabel()}.")
            ->success()
            ->send();
    }

    private function cerrarClienteEjecutivo(Agenda $agenda, array $data)
    {
        $consulta = $agenda->consulta;

        if (! $consulta || $consulta->cliente()->exists()) {
            Notification::make()
                ->title('Esta consulta ya fue convertida en Cliente Ejecutivo')
                ->warning()
                ->send();

            return null;
        }

        $anticipo = (float) ($data['anticipo'] ?? 0);

        $consulta->update([
            'nombre' => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'] ?? null,
            'ci' => $data['ci'],
            'telefono' => $data['telefono'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'pago_inicial_monto' => $anticipo > 0 ? $anticipo : null,
            'pago_inicial_registrado_en' => $anticipo > 0 ? now() : null,
            'atendido_por' => $this->getPersonal()->id,
        ]);

        ['cliente' => $cliente, 'password' => $password] = Cliente::convertirDesdeConsulta($consulta);

        $proceso = Proceso::create([
            'cliente_id' => $cliente->id,
            'materia_legal' => $data['materia_legal'],
            'tipo_proceso' => $data['tipo_proceso'],
            'tiempo_proceso_meses' => $data['tiempo_proceso_meses'],
            'abogado_id' => $data['abogado_id'],
            'estado' => EstadoProceso::Activo,
        ]);

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

        $agenda->update(['estado' => EstadoAgenda::Finalizada]);

        Notification::make()
            ->title('Cliente Ejecutivo y caso creados')
            ->body("Usuario: {$cliente->usuario}\nContraseña temporal: {$password}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar.\n\nSe descargó el contrato: imprímelo y fírmalo junto al cliente, luego súbelo firmado al repositorio de documentos del caso.")
            ->success()
            ->persistent()
            ->send();

        $pdf = Pdf::loadView('pdf.contrato', ['proceso' => $proceso]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            "contrato-{$proceso->id}.pdf",
        );
    }

    public function registrarPagoAction(): Action
    {
        return Action::make('registrarPago')
            ->label('Registrar pago y crear Cliente Ejecutivo')
            ->icon(Heroicon::OutlinedBanknotes)
            ->color('success')
            ->schema([
                TextInput::make('monto')
                    ->label('Monto del pago inicial (Bs.)')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),
            ])
            ->action(function (array $data, array $arguments): void {
                $agenda = $this->agendaDelResponsable($arguments['agenda']);
                $consulta = $agenda->consulta;

                if (! $consulta || $consulta->cliente()->exists()) {
                    Notification::make()
                        ->title('Esta consulta ya fue convertida en Cliente Ejecutivo')
                        ->warning()
                        ->send();

                    return;
                }

                $consulta->update([
                    'pago_inicial_monto' => $data['monto'],
                    'pago_inicial_registrado_en' => now(),
                    'atendido_por' => $this->getPersonal()->id,
                ]);

                ['cliente' => $cliente, 'password' => $password] = Cliente::convertirDesdeConsulta($consulta);

                Notification::make()
                    ->title('Cliente Ejecutivo creado')
                    ->body("Usuario: {$cliente->usuario}\nContraseña temporal: {$password}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar.")
                    ->success()
                    ->persistent()
                    ->send();
            });
    }
}
