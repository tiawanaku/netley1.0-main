<?php

namespace App\Filament\Resources\Procesos\RelationManagers;

use App\Enums\EstadoSolicitudDocumento;
use App\Models\DocumentoSolicitud;
use App\Models\Personal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SolicitudesRelationManager extends RelationManager
{
    protected static string $relationship = 'solicitudesDocumento';

    protected static ?string $title = 'Solicitudes de documentos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('personal_id')
                    ->label('Solicitado por')
                    ->relationship('personal', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn (Personal $record): string => $record->nombre_completo)
                    ->searchable(['nombre', 'apellidos'])
                    ->preload()
                    ->required(),
                Textarea::make('descripcion')
                    ->label('¿Qué documento se solicita?')
                    ->required()
                    ->columnSpanFull(),
                Select::make('estado')
                    ->options(EstadoSolicitudDocumento::class)
                    ->default(EstadoSolicitudDocumento::Pendiente)
                    ->required()
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                TextColumn::make('descripcion')
                    ->limit(60),
                TextColumn::make('personal.nombre_completo')
                    ->label('Solicitado por'),
                TextColumn::make('estado')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options(EstadoSolicitudDocumento::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('marcarCumplida')
                    ->label('Marcar cumplida')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (DocumentoSolicitud $record): bool => $record->estado !== EstadoSolicitudDocumento::Cumplida)
                    ->requiresConfirmation()
                    ->action(fn (DocumentoSolicitud $record) => $record->marcarCumplida()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
