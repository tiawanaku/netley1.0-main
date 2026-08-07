<?php

namespace App\Filament\Resources\Agendas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistorialRelationManager extends RelationManager
{
    protected static string $relationship = 'historial';

    protected static ?string $title = 'Historial';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('accion')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->default('Sistema'),
                TextColumn::make('accion')
                    ->label('Acción')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
