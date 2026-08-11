<?php

namespace App\Filament\Resources\Finanzas\RelationManagers;

use App\Enums\EstadoPago;
use App\Models\PlanPago;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PlanPagosRelationManager extends RelationManager
{
    protected static string $relationship = 'planPagos';

    protected static ?string $title = 'Plan de Pagos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('monto')
                    ->numeric()
                    ->prefix('Bs.')
                    ->minValue(0)
                    ->required(),
                Select::make('estado')
                    ->options(EstadoPago::class)
                    ->default(EstadoPago::Pendiente)
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fecha')
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('BOB'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('pagado_en')
                    ->label('Pagado el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoPago::class),
            ])
            ->defaultSort('fecha')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('verComprobante')
                    ->label('Ver comprobante')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->color('gray')
                    ->visible(fn (PlanPago $record): bool => filled($record->comprobante))
                    ->url(fn (PlanPago $record): string => Storage::disk('public')->url($record->comprobante))
                    ->openUrlInNewTab(),
                Action::make('confirmarPago')
                    ->label('Confirmar pago')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (PlanPago $record): bool => $record->estado === EstadoPago::PendienteConfirmacion)
                    ->requiresConfirmation()
                    ->modalDescription('El cliente registró este pago desde el portal. Confirma que el comprobante es válido. Se emitirá automáticamente un recibo verificable.')
                    ->action(function (PlanPago $record): void {
                        $record->confirmarPago();

                        Notification::make()
                            ->title('Pago confirmado')
                            ->body('Se emitió el recibo ' . $record->refresh()->recibo?->numero)
                            ->success()
                            ->send();
                    }),
                Action::make('descargarRecibo')
                    ->label('Ver recibo')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->color('gray')
                    ->visible(fn (PlanPago $record): bool => $record->recibo()->exists())
                    ->url(fn (PlanPago $record): string => URL::signedRoute('recibos.verificar', ['recibo' => $record->recibo->identificador]))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->visible(fn (PlanPago $record): bool => ! $record->recibo()->exists()),
                DeleteAction::make()
                    ->visible(fn (PlanPago $record): bool => ! $record->recibo()->exists()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
