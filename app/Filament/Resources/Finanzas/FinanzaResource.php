<?php

namespace App\Filament\Resources\Finanzas;

use App\Enums\TipoPago;
use App\Filament\Resources\Finanzas\Pages\CreateFinanza;
use App\Filament\Resources\Finanzas\Pages\EditFinanza;
use App\Filament\Resources\Finanzas\Pages\ListFinanzas;
use App\Filament\Resources\Finanzas\RelationManagers\PlanPagosRelationManager;
use App\Models\Finanza;
use App\Models\Proceso;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class FinanzaResource extends Resource
{
    protected static ?string $model = Finanza::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Finanzas';

    protected static ?string $modelLabel = 'finanza';

    protected static ?string $pluralModelLabel = 'finanzas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('proceso_id')
                    ->label('Proceso')
                    ->relationship('proceso', 'tipo_proceso')
                    ->getOptionLabelFromRecordUsing(fn (Proceso $record): string => $record->resumen)
                    ->searchable(['tipo_proceso'])
                    ->preload()
                    ->required(),
                TextInput::make('costo')
                    ->label('Costo')
                    ->numeric()
                    ->prefix('Bs.')
                    ->minValue(0)
                    ->required(),
                Select::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->options(TipoPago::class)
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proceso.resumen')
                    ->label('Proceso')
                    ->searchable(['proceso.tipo_proceso']),
                TextColumn::make('costo')
                    ->label('Costo')
                    ->money('BOB')
                    ->sortable(),
                TextColumn::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->badge(),
                TextColumn::make('plan_pagos_count')
                    ->label('Cuotas generadas')
                    ->counts('planPagos'),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->options(TipoPago::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('generarPlanPagos')
                    ->label('Generar Plan de Pagos')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->color('success')
                    ->schema(fn (Finanza $record) => [
                        TextInput::make('cuotas')
                            ->label('Número de cuotas')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue($record->maxCuotasPermitidas())
                            ->helperText("Máximo permitido: {$record->maxCuotasPermitidas()} cuotas ({$record->tipo_pago->getLabel()}) según el tiempo del proceso.")
                            ->required(),
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de la primera cuota')
                            ->default(now())
                            ->required(),
                    ])
                    ->modalDescription(fn (Finanza $record): string => "Costo total: Bs. {$record->costo}. Se generarán cuotas de monto igual (la última ajusta el redondeo).")
                    ->action(function (Finanza $record, array $data): void {
                        $record->generarPlanPagos((int) $data['cuotas'], $data['fecha_inicio']);

                        Notification::make()
                            ->title('Plan de pagos generado')
                            ->body("Se generaron {$data['cuotas']} cuotas para este proceso.")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PlanPagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanzas::route('/'),
            'create' => CreateFinanza::route('/create'),
            'edit' => EditFinanza::route('/{record}/edit'),
        ];
    }
}
