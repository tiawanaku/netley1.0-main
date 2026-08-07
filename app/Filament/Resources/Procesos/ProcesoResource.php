<?php

namespace App\Filament\Resources\Procesos;

use App\Enums\CategoriaLegal;
use App\Enums\EstadoProceso;
use App\Enums\RolPersonal;
use App\Filament\Resources\Procesos\Pages\CreateProceso;
use App\Filament\Resources\Procesos\Pages\EditProceso;
use App\Filament\Resources\Procesos\Pages\ListProcesos;
use App\Filament\Resources\Procesos\RelationManagers\DocumentosRelationManager;
use App\Filament\Resources\Procesos\RelationManagers\SolicitudesRelationManager;
use App\Models\Cliente;
use App\Models\Delito;
use App\Models\Personal;
use App\Models\Proceso;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProcesoResource extends Resource
{
    protected static ?string $model = Proceso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $recordTitleAttribute = 'tipo_proceso';

    protected static UnitEnum|string|null $navigationGroup = 'Procesos';

    protected static ?string $navigationLabel = 'Procesos';

    protected static ?string $modelLabel = 'proceso';

    protected static ?string $pluralModelLabel = 'procesos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Cliente $record): string => $record->nombre_completo)
                    ->searchable(['nombre', 'apellidos'])
                    ->preload()
                    ->required(),
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
                TextInput::make('tiempo_proceso_meses')
                    ->label('Tiempo del proceso')
                    ->suffix('meses')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                Select::make('abogado_id')
                    ->label('Abogado responsable')
                    ->relationship(
                        name: 'abogado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('rol', RolPersonal::Abogado)
                            ->when(
                                filled($get('materia_legal')),
                                fn (Builder $query) => $query->where('especialidad_abogado', $get('materia_legal')),
                            ),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                    ->helperText('Se muestran solo los abogados con especialidad en la materia legal seleccionada.')
                    ->searchable(['nombre', 'apellidos'])
                    ->preload(),
                Select::make('estado')
                    ->label('Estado')
                    ->options(EstadoProceso::class)
                    ->default(EstadoProceso::Activo)
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_proceso')
            ->columns([
                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable(['cliente.nombre', 'cliente.apellidos'])
                    ->sortable(),
                TextColumn::make('materia_legal')
                    ->label('Materia legal')
                    ->badge(),
                TextColumn::make('tipo_proceso')
                    ->label('Tipo de proceso')
                    ->searchable(),
                TextColumn::make('tiempo_proceso_meses')
                    ->label('Duración')
                    ->suffix(' meses')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('abogado.nombre_completo')
                    ->label('Abogado')
                    ->searchable(['nombre', 'apellidos'])
                    ->toggleable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Cliente $record): string => $record->nombre_completo)
                    ->searchable(),
                SelectFilter::make('materia_legal')
                    ->label('Materia legal')
                    ->options(CategoriaLegal::class),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoProceso::class),
                SelectFilter::make('abogado_id')
                    ->label('Abogado')
                    ->relationship('abogado', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('contrato')
                    ->label('Contrato')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->action(function (Proceso $record) {
                        $pdf = Pdf::loadView('pdf.contrato', ['proceso' => $record]);

                        return response()->streamDownload(
                            fn () => print ($pdf->output()),
                            "contrato-{$record->id}.pdf",
                        );
                    }),
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
            DocumentosRelationManager::class,
            SolicitudesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcesos::route('/'),
            'create' => CreateProceso::route('/create'),
            'edit' => EditProceso::route('/{record}/edit'),
        ];
    }
}
