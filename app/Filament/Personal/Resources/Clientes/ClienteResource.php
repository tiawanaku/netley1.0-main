<?php

namespace App\Filament\Personal\Resources\Clientes;

use App\Filament\Personal\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Personal\Resources\Clientes\Pages\ListClientes;
use App\Models\Cliente;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static UnitEnum|string|null $navigationGroup = 'Cliente Ejecutivo';

    protected static ?string $navigationLabel = 'Cliente Ejecutivo';

    protected static ?string $modelLabel = 'cliente';

    protected static ?string $pluralModelLabel = 'clientes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acceso al Portal Cliente')
                    ->columns(2)
                    ->components([
                        Placeholder::make('nombre')
                            ->label('Nombre')
                            ->content(fn (?Cliente $record): ?string => $record?->nombre_completo),
                        Placeholder::make('usuario')
                            ->label('Usuario')
                            ->content(fn (?Cliente $record): ?string => $record?->usuario),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(['nombre', 'apellidos', 'usuario'])
                    ->description(fn (Cliente $record): string => $record->apellidos),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp'),
                TextColumn::make('usuario')
                    ->label('Usuario')
                    ->searchable(),
                TextColumn::make('consulta.nombre_completo')
                    ->label('Consulta de origen')
                    ->placeholder('Alta directa'),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
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
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
        ];
    }
}
