<?php

namespace App\Filament\Resources\Agendas;

use App\Enums\EstadoAgenda;
use App\Enums\ModalidadAgenda;
use App\Enums\ResultadoLlamada;
use App\Enums\TipoAgenda;
use App\Filament\Resources\Agendas\Pages\CreateAgenda;
use App\Filament\Resources\Agendas\Pages\EditAgenda;
use App\Filament\Resources\Agendas\Pages\ListAgendas;
use App\Filament\Resources\Agendas\RelationManagers\HistorialRelationManager;
use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\Personal;
use App\Models\Proceso;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static UnitEnum|string|null $navigationGroup = 'Agenda';

    protected static ?string $navigationLabel = 'Actividades';

    protected static ?string $modelLabel = 'actividad';

    protected static ?string $pluralModelLabel = 'actividades';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(static::formComponents());
    }

    public static function formComponents(): array
    {
        return [
                Section::make('Información general')
                    ->columns(2)
                    ->components([
                        Select::make('tipo')
                            ->label('Tipo')
                            ->options(TipoAgenda::class)
                            ->required()
                            ->live()
                            ->native(false),
                        Select::make('estado')
                            ->label('Estado')
                            ->options(EstadoAgenda::class)
                            ->default(EstadoAgenda::Pendiente)
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Vinculación')
                    ->columns(2)
                    ->visible(fn (Get $get): bool => filled($get('tipo')))
                    ->components([
                        Radio::make('origen_cita')
                            ->label('Origen')
                            ->options([
                                'consulta' => 'Consulta (aún no es cliente)',
                                'proceso' => 'Proceso / Caso',
                            ])
                            ->default('consulta')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Radio $component, ?Agenda $record): void {
                                if ($record) {
                                    $component->state($record->proceso_id ? 'proceso' : 'consulta');
                                }
                            })
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Cita->value),
                        Select::make('proceso_id')
                            ->label('Proceso / Caso')
                            ->relationship('proceso', 'tipo_proceso')
                            ->getOptionLabelFromRecordUsing(fn (Proceso $record): string => $record->resumen)
                            ->searchable(['tipo_proceso'])
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Cita->value && $get('origen_cita') === 'proceso')
                            ->required(fn (Get $get): bool => $get('tipo') === TipoAgenda::Cita->value && $get('origen_cita') === 'proceso'),
                        Radio::make('origen_llamada')
                            ->label('Origen')
                            ->options([
                                'consulta' => 'Consulta',
                                'cliente' => 'Cliente Ejecutivo',
                            ])
                            ->default('consulta')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Radio $component, ?Agenda $record): void {
                                if ($record) {
                                    $component->state($record->consulta_id ? 'consulta' : 'cliente');
                                }
                            })
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Llamada->value),
                        Select::make('consulta_id')
                            ->label('Consulta')
                            ->relationship('consulta', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn (Consulta $record): string => $record->nombre_completo)
                            ->searchable(['nombre', 'apellido_paterno', 'apellido_materno'])
                            ->preload()
                            ->visible(fn (Get $get): bool => ($get('tipo') === TipoAgenda::Llamada->value && $get('origen_llamada') === 'consulta')
                                || ($get('tipo') === TipoAgenda::Cita->value && $get('origen_cita') === 'consulta'))
                            ->required(fn (Get $get): bool => ($get('tipo') === TipoAgenda::Llamada->value && $get('origen_llamada') === 'consulta')
                                || ($get('tipo') === TipoAgenda::Cita->value && $get('origen_cita') === 'consulta')),
                        Select::make('cliente_id')
                            ->label('Cliente Ejecutivo')
                            ->relationship('cliente', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn (Cliente $record): string => $record->nombre_completo)
                            ->searchable(['nombre', 'apellidos'])
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Llamada->value && $get('origen_llamada') === 'cliente')
                            ->required(fn (Get $get): bool => $get('tipo') === TipoAgenda::Llamada->value && $get('origen_llamada') === 'cliente'),
                        Select::make('participantes')
                            ->label('Participantes')
                            ->relationship('participantes', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                            ->searchable(['nombre', 'apellidos'])
                            ->multiple()
                            ->preload()
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Reunion->value),
                    ]),
                Section::make('Horario')
                    ->columns(2)
                    ->visible(fn (Get $get): bool => filled($get('tipo')))
                    ->components([
                        Select::make('responsable_id')
                            ->label('Profesional responsable')
                            ->relationship('responsable', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                            ->searchable(['nombre', 'apellidos'])
                            ->preload()
                            ->visible(fn (Get $get): bool => in_array($get('tipo'), [TipoAgenda::Cita->value, TipoAgenda::Llamada->value]))
                            ->required(fn (Get $get): bool => in_array($get('tipo'), [TipoAgenda::Cita->value, TipoAgenda::Llamada->value])),
                        TextInput::make('duracion_minutos')
                            ->label('Duración (minutos)')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Llamada->value),
                        DateTimePicker::make('fecha_inicio')
                            ->label('Fecha y hora de inicio')
                            ->required()
                            ->live(),
                        DateTimePicker::make('fecha_fin')
                            ->label('Fecha y hora de fin')
                            ->required()
                            ->after('fecha_inicio'),
                        Select::make('modalidad')
                            ->label('Modalidad')
                            ->options(ModalidadAgenda::class)
                            ->native(false)
                            ->visible(fn (Get $get): bool => in_array($get('tipo'), [TipoAgenda::Cita->value, TipoAgenda::Reunion->value])),
                        Select::make('ubicacion')
                            ->label('Lugar')
                            ->options(Agenda::UBICACIONES)
                            ->native(false)
                            ->visible(fn (Get $get): bool => in_array($get('tipo'), [TipoAgenda::Cita->value, TipoAgenda::Reunion->value])),
                    ]),
                Section::make('Detalle adicional')
                    ->visible(fn (Get $get): bool => filled($get('tipo')))
                    ->components([
                        TextInput::make('asunto')
                            ->label(fn (Get $get): string => $get('tipo') === TipoAgenda::Llamada->value ? 'Motivo' : 'Asunto')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => in_array($get('tipo'), [TipoAgenda::Llamada->value, TipoAgenda::Reunion->value]))
                            ->required(fn (Get $get): bool => $get('tipo') === TipoAgenda::Reunion->value),
                        Select::make('resultado')
                            ->label('Resultado')
                            ->options(ResultadoLlamada::class)
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('tipo') === TipoAgenda::Llamada->value),
                        Textarea::make('descripcion')
                            ->label(fn (Get $get): string => $get('tipo') === TipoAgenda::Llamada->value ? 'Observaciones' : 'Descripción')
                            ->columnSpanFull(),
                    ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('relacionado_con')
                    ->label('Relacionado con'),
                TextColumn::make('responsable.nombre_completo')
                    ->label('Responsable')
                    ->searchable(['nombre', 'apellidos']),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('modalidad')
                    ->label('Modalidad')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(TipoAgenda::class),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoAgenda::class),
                SelectFilter::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'nombre'),
                Filter::make('fecha_inicio')
                    ->schema([
                        DateTimePicker::make('desde'),
                        DateTimePicker::make('hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($query, $desde) => $query->where('fecha_inicio', '>=', $desde))
                            ->when($data['hasta'] ?? null, fn ($query, $hasta) => $query->where('fecha_inicio', '<=', $hasta));
                    }),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->recordActions([
                Action::make('reagendar')
                    ->label('Reagendar')
                    ->icon(Heroicon::OutlinedClock)
                    ->color('warning')
                    ->schema(fn (Agenda $record) => [
                        DateTimePicker::make('fecha_inicio')
                            ->label('Nueva fecha y hora de inicio')
                            ->default($record->fecha_inicio)
                            ->required(),
                        DateTimePicker::make('fecha_fin')
                            ->label('Nueva fecha y hora de fin')
                            ->default($record->fecha_fin)
                            ->required()
                            ->after('fecha_inicio'),
                    ])
                    ->action(function (Agenda $record, array $data): void {
                        $inicio = Carbon::parse($data['fecha_inicio']);
                        $fin = Carbon::parse($data['fecha_fin']);

                        if ($record->responsable_id && Agenda::hayConflicto($record->responsable_id, $inicio, $fin, $record->id)) {
                            Notification::make()
                                ->title('Conflicto de horario')
                                ->body('El profesional ya tiene una actividad registrada en ese horario.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'fecha_inicio' => $inicio,
                            'fecha_fin' => $fin,
                            'estado' => EstadoAgenda::Reagendada,
                        ]);

                        Notification::make()
                            ->title('Agenda reagendada')
                            ->success()
                            ->send();
                    }),
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Agenda $record): bool => $record->estado !== EstadoAgenda::Cancelada)
                    ->requiresConfirmation()
                    ->action(fn (Agenda $record) => $record->update(['estado' => EstadoAgenda::Cancelada])),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HistorialRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgendas::route('/'),
            'create' => CreateAgenda::route('/create'),
            'edit' => EditAgenda::route('/{record}/edit'),
        ];
    }
}
