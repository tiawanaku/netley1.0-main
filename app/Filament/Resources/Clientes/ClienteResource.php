<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Models\Cliente;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
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
                Section::make('Datos del cliente')
                    ->columns(2)
                    ->components([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellidos')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        TextInput::make('whatsapp')
                            ->tel()
                            ->maxLength(20),
                    ]),
                Section::make('Acceso al Portal Cliente')
                    ->description('Usuario y contraseña generados automáticamente al crear el Cliente Ejecutivo. La contraseña no puede volver a mostrarse; usa "Restablecer contraseña" si el cliente la perdió.')
                    ->columns(2)
                    ->components([
                        Placeholder::make('usuario')
                            ->label('Usuario')
                            ->content(fn (?Cliente $record): ?string => $record?->usuario),
                        Placeholder::make('consulta_id')
                            ->label('Consulta de origen')
                            ->content(fn (?Cliente $record): string => $record?->consulta?->nombre_completo ?? 'Alta directa (sin consulta previa)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
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
                    ->label('Fecha de conversión')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('restablecerPassword')
                    ->label('Restablecer contraseña')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Se generará una nueva contraseña temporal. La anterior dejará de funcionar.')
                    ->action(function (Cliente $record): void {
                        $password = Str::password(10);
                        $record->update(['password' => $password]);

                        Notification::make()
                            ->title('Contraseña restablecida')
                            ->body("Usuario: {$record->usuario}\nContraseña temporal: {$password}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar.")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
}
