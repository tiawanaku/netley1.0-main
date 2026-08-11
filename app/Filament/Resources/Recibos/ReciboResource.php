<?php

namespace App\Filament\Resources\Recibos;

use App\Enums\EstadoRecibo;
use App\Enums\OrigenRecibo;
use App\Filament\Resources\Recibos\Pages\ListRecibos;
use App\Models\Cliente;
use App\Models\Personal;
use App\Models\Proceso;
use App\Models\Recibo;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use UnitEnum;

class ReciboResource extends Resource
{
    protected static ?string $model = Recibo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $recordTitleAttribute = 'numero';

    protected static UnitEnum|string|null $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Recibos';

    protected static ?string $modelLabel = 'recibo';

    protected static ?string $pluralModelLabel = 'recibos';

    // Los recibos solo se emiten automáticamente al confirmar un pago
    // (PlanPago::confirmarPago / Finanza::confirmarAnticipo). No existe
    // formulario de creación ni edición manual — ver Resources/Recibos.

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('numero')->label('Número de recibo'),
            TextEntry::make('estado')->label('Estado')->badge(),
            TextEntry::make('origen_tipo')->label('Origen')->badge(),
            TextEntry::make('fecha_pago')->label('Fecha de pago')->date('d/m/Y'),
            TextEntry::make('cliente.nombre_completo')->label('Cliente'),
            TextEntry::make('proceso.tipo_proceso')->label('Proceso'),
            TextEntry::make('concepto')->label('Concepto'),
            TextEntry::make('monto')->label('Monto')->money('BOB'),
            TextEntry::make('registrado_por')->label('Registrado por'),
            TextEntry::make('created_at')->label('Emitido el')->dateTime('d/m/Y H:i'),
            TextEntry::make('anulado_en')->label('Anulado el')->dateTime('d/m/Y H:i')->visible(fn (Recibo $record): bool => $record->estado === EstadoRecibo::Anulado),
            TextEntry::make('anulado_por')->label('Anulado por')->visible(fn (Recibo $record): bool => $record->estado === EstadoRecibo::Anulado),
            TextEntry::make('motivo_anulacion')->label('Motivo de anulación')->visible(fn (Recibo $record): bool => $record->estado === EstadoRecibo::Anulado)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Número')
                    ->badge()
                    ->searchable(),
                TextColumn::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable(['nombre', 'apellidos']),
                TextColumn::make('proceso.tipo_proceso')
                    ->label('Proceso'),
                TextColumn::make('concepto')
                    ->label('Concepto'),
                TextColumn::make('origen_tipo')
                    ->label('Origen')
                    ->badge(),
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('registrado_por')
                    ->label('Registrado por'),
                TextColumn::make('created_at')
                    ->label('Emitido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoRecibo::class),
                SelectFilter::make('origen_tipo')
                    ->label('Origen')
                    ->options(OrigenRecibo::class),
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Cliente $record): string => $record->nombre_completo)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('proceso_id')
                    ->label('Proceso')
                    ->relationship('proceso', 'tipo_proceso')
                    ->getOptionLabelFromRecordUsing(fn (Proceso $record): string => $record->resumen)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('registrado_por_personal_id')
                    ->label('Registrado por (personal)')
                    ->relationship('registradoPorPersonal', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('registrado_por_user_id')
                    ->label('Registrado por (admin)')
                    ->relationship('registradoPorUser', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name)
                    ->searchable()
                    ->preload(),
                Filter::make('fecha_pago')
                    ->schema([
                        DatePicker::make('desde'),
                        DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha_pago', '>=', $fecha))
                            ->when($data['hasta'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('fecha_pago', '<=', $fecha));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                Action::make('descargarPdf')
                    ->label('Descargar PDF')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->url(fn (Recibo $record): string => URL::signedRoute('recibos.verificar.pdf', ['recibo' => $record->identificador]))
                    ->openUrlInNewTab(),
                Action::make('anular')
                    ->label('Anular')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Recibo $record): bool => $record->estado === EstadoRecibo::Emitido)
                    ->requiresConfirmation()
                    ->modalDescription('Esta acción no revierte el pago asociado. El número de recibo queda anulado permanentemente y no se reutiliza.')
                    ->schema([
                        Textarea::make('motivo')
                            ->label('Motivo de la anulación')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (Recibo $record, array $data): void {
                        $record->anular($data['motivo']);

                        Notification::make()
                            ->title('Recibo anulado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                //
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
            'index' => ListRecibos::route('/'),
        ];
    }
}
