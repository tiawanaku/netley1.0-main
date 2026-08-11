<?php

namespace App\Filament\Personal\Resources\Consultas;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoAgenda;
use App\Enums\EstadoConsulta;
use App\Enums\FormaIngreso;
use App\Enums\ModalidadAgenda;
use App\Enums\OrigenConsulta;
use App\Enums\TipoAgenda;
use App\Filament\Personal\Resources\Consultas\Pages\CreateConsulta;
use App\Filament\Personal\Resources\Consultas\Pages\ListConsultas;
use App\Models\Agenda;
use App\Models\Consulta;
use App\Models\Personal;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsultaResource extends Resource
{
    protected static ?string $model = Consulta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?string $navigationLabel = 'Consultas';

    protected static ?string $modelLabel = 'consulta';

    protected static ?string $pluralModelLabel = 'consultas';

    /**
     * RN: en el Portal de Personal, cada usuario solo puede ver las consultas
     * sin asignación (sin ninguna cita agendada todavía) y las que ya le fueron
     * asignadas a él mediante una cita (Agenda.responsable_id). El resto —
     * asignadas a otro profesional, vencidas o no— quedan fuera de su alcance.
     */
    public static function getEloquentQuery(): Builder
    {
        $personalId = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->where(function (Builder $query) use ($personalId): void {
                $query->whereDoesntHave('agendas')
                    ->orWhereHas('agendas', fn (Builder $q) => $q->where('responsable_id', $personalId));
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del contacto')
                    ->description('Información de la persona que realiza la consulta.')
                    ->columns(2)
                    ->components([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellido_paterno')
                            ->label('Apellido paterno')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellido_materno')
                            ->label('Apellido materno')
                            ->maxLength(255),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->helperText('7 u 8 dígitos, empieza con 2-7.')
                            ->maxLength(20),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Select::make('ciudad')
                            ->label('Ciudad')
                            ->options(Consulta::CIUDADES)
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Detalle de la consulta')
                    ->columns(2)
                    ->components([
                        Select::make('tipo_proceso')
                            ->label('Tipo de proceso')
                            ->options(CategoriaLegal::class)
                            ->required()
                            ->native(false),
                        Select::make('origen')
                            ->label('Origen')
                            ->options(OrigenConsulta::class)
                            ->required()
                            ->native(false),
                        Select::make('forma_ingreso')
                            ->label('Forma de ingreso')
                            ->options(FormaIngreso::class)
                            ->native(false),
                        TextInput::make('colegio_otros')
                            ->label('Colegio/Otros')
                            ->maxLength(255),
                        Textarea::make('descripcion')
                            ->label('Consulta')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno'])
                    ->sortable(['nombre']),
                TextColumn::make('telefono')
                    ->label('Contacto')
                    ->searchable(['telefono', 'email'])
                    ->description(fn (Consulta $record): ?string => $record->email),
                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->formatStateUsing(fn (string $state): string => Consulta::CIUDADES[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('tipo_proceso')
                    ->label('Tipo de Proceso')
                    ->badge(),
                TextColumn::make('origen')
                    ->label('Origen'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Registrada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('ciudad')
                    ->label('Ciudad')
                    ->options(Consulta::CIUDADES),
                SelectFilter::make('tipo_proceso')
                    ->label('Tipo de proceso')
                    ->options(CategoriaLegal::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('agendarCita')
                    ->label('Agendar cita')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->color('gray')
                    ->visible(fn (Consulta $record): bool => $record->estado !== EstadoConsulta::ClienteEjecutivo)
                    ->schema([
                        Select::make('responsable_id')
                            ->label('Profesional asignado')
                            ->helperText('Abogado, psicólogo u otro personal que atenderá la cita.')
                            ->options(fn (): array => Personal::query()
                                ->orderBy('nombre')
                                ->get()
                                ->mapWithKeys(fn (Personal $personal): array => [$personal->id => $personal->nombre_completo])
                                ->toArray())
                            ->searchable()
                            ->required(),
                        DateTimePicker::make('fecha_inicio')
                            ->label('Fecha y hora')
                            ->helperText('Lunes a viernes, de 08:00 a 17:00, en bloques de 15 minutos.')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->minutesStep(15)
                            ->minDate(now())
                            ->rule(fn (): \Closure => Agenda::reglaHorarioLaboral()),
                        Select::make('modalidad')
                            ->label('Modalidad')
                            ->options(ModalidadAgenda::class)
                            ->native(false)
                            ->required(),
                        Select::make('ubicacion')
                            ->label('Lugar')
                            ->options(Agenda::UBICACIONES)
                            ->native(false),
                        Textarea::make('descripcion')
                            ->label('Motivo / observaciones')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Consulta $record, array $data): void {
                        $inicio = Carbon::parse($data['fecha_inicio']);
                        $fin = $inicio->copy()->addMinutes(15);

                        if (Agenda::hayConflicto((int) $data['responsable_id'], $inicio, $fin)) {
                            Notification::make()
                                ->title('Conflicto de horario')
                                ->body('El profesional seleccionado ya tiene una actividad en ese horario.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Agenda::create([
                            'tipo' => TipoAgenda::Cita,
                            'estado' => EstadoAgenda::Pendiente,
                            'consulta_id' => $record->id,
                            'responsable_id' => $data['responsable_id'],
                            'fecha_inicio' => $inicio,
                            'fecha_fin' => $fin,
                            'modalidad' => $data['modalidad'],
                            'ubicacion' => $data['ubicacion'] ?? null,
                            'descripcion' => $data['descripcion'] ?? null,
                        ]);

                        if ($record->estado === EstadoConsulta::Nueva) {
                            $record->update(['estado' => EstadoConsulta::Agendada]);
                        }

                        Notification::make()
                            ->title('Cita agendada')
                            ->success()
                            ->send();
                    }),
                Action::make('darRespuesta')
                    ->label('Dar respuesta')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('gray')
                    ->visible(fn (Consulta $record): bool => $record->agendas()->where('tipo', TipoAgenda::Cita)->exists())
                    ->schema([
                        Textarea::make('respuesta')
                            ->label('Respuesta')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Consulta $record, array $data): void {
                        $agenda = $record->agendas()
                            ->where('tipo', TipoAgenda::Cita)
                            ->latest('fecha_inicio')
                            ->first();

                        $agenda->answers()->create([
                            'respuesta' => $data['respuesta'],
                            'personal_id' => Filament::auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Respuesta guardada')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsultas::route('/'),
            'create' => CreateConsulta::route('/create'),
        ];
    }
}
