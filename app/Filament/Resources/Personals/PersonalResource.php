<?php

namespace App\Filament\Resources\Personals;

use App\Enums\EspecialidadAbogado;
use App\Enums\EstadoCivil;
use App\Enums\EstadoPersonal;
use App\Enums\Genero;
use App\Enums\RolPersonal;
use App\Filament\Resources\Personals\Pages\CreatePersonal;
use App\Filament\Resources\Personals\Pages\EditPersonal;
use App\Filament\Resources\Personals\Pages\ListPersonals;
use App\Models\Personal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PersonalResource extends Resource
{
    protected static ?string $model = Personal::class;

    protected static ?string $slug = 'personal';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static UnitEnum|string|null $navigationGroup = 'Personas';

    protected static ?string $navigationLabel = 'Personal';

    protected static ?string $modelLabel = 'personal';

    protected static ?string $pluralModelLabel = 'personal';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->columns(2)
                    ->components([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('apellidos')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('ci')
                            ->label('CI')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Select::make('genero')
                            ->label('Género')
                            ->options(Genero::class)
                            ->required()
                            ->native(false),
                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de nacimiento')
                            ->required()
                            ->maxDate(now()),
                        TextInput::make('nacionalidad')
                            ->required()
                            ->default('Boliviana')
                            ->maxLength(255),
                        Select::make('estado_civil')
                            ->label('Estado civil')
                            ->options(EstadoCivil::class)
                            ->required()
                            ->native(false),
                        TextInput::make('profesion')
                            ->label('Profesión')
                            ->maxLength(255),
                    ]),
                Section::make('Contacto')
                    ->columns(2)
                    ->components([
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->unique(ignoreRecord: true)
                            ->helperText('7 u 8 dígitos, empieza con 2-7. Se usará como usuario de acceso al portal.')
                            ->maxLength(20),
                        TextInput::make('whatsapp')
                            ->tel()
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->maxLength(20),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('ciudad')
                            ->label('Ciudad')
                            ->options(Personal::CIUDADES)
                            ->required()
                            ->native(false),
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('Datos Netley')
                    ->columns(2)
                    ->components([
                        TextInput::make('numero_contrato')
                            ->label('Número de contrato')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('estado')
                            ->label('Estado')
                            ->options(EstadoPersonal::class)
                            ->default(EstadoPersonal::Activo)
                            ->required()
                            ->native(false),
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required(),
                        Select::make('rol')
                            ->label('Rol')
                            ->options(RolPersonal::class)
                            ->required()
                            ->live()
                            ->native(false),
                        Select::make('especialidad_abogado')
                            ->label('Especialidad de abogado')
                            ->options(EspecialidadAbogado::class)
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('rol') === RolPersonal::Abogado)
                            ->columnSpanFull(),
                        FileUpload::make('foto')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('personal/fotos'),
                        Textarea::make('nota')
                            ->columnSpanFull(),
                    ]),
                Section::make('Documentos')
                    ->components([
                        FileUpload::make('documentos')
                            ->label('Documentos')
                            ->multiple()
                            ->disk('public')
                            ->directory('personal/documentos')
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ]),
                Section::make('Acceso al Portal de Personal')
                    ->description('El usuario y la contraseña temporal se generan automáticamente al registrar al personal. Usa "Restablecer contraseña" en el listado para generar una nueva.')
                    ->visible(fn (?Personal $record): bool => filled($record?->usuario))
                    ->components([
                        Placeholder::make('usuario')
                            ->label('Usuario (teléfono)')
                            ->content(fn (?Personal $record): ?string => $record?->usuario),
                        Placeholder::make('must_change_password')
                            ->label('Debe cambiar contraseña')
                            ->content(fn (?Personal $record): string => $record?->must_change_password ? 'Sí, pendiente' : 'No'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(['nombre', 'apellidos', 'ci'])
                    ->description(fn (Personal $record): string => $record->apellidos),
                TextColumn::make('ci')
                    ->label('CI')
                    ->searchable(),
                TextColumn::make('rol')
                    ->label('Rol')
                    ->badge(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->formatStateUsing(fn (string $state): string => Personal::CIUDADES[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Fecha de inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('rol')
                    ->label('Rol')
                    ->options(RolPersonal::class),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(EstadoPersonal::class),
                SelectFilter::make('ciudad')
                    ->label('Ciudad')
                    ->options(Personal::CIUDADES),
            ])
            ->defaultSort('nombre')
            ->recordActions([
                Action::make('restablecerPassword')
                    ->label('Restablecer contraseña')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Se generará una nueva contraseña temporal para el Portal de Personal y se obligará a cambiarla en el próximo inicio de sesión.')
                    ->action(function (Personal $record): void {
                        $password = $record->generarAccesoPortal();

                        Notification::make()
                            ->title('Contraseña restablecida')
                            ->body("Usuario: {$record->usuario}\nContraseña temporal: {$password}\n\nGuarda esta contraseña ahora, no se podrá volver a mostrar.")
                            ->success()
                            ->persistent()
                            ->send();
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPersonals::route('/'),
            'create' => CreatePersonal::route('/create'),
            'edit' => EditPersonal::route('/{record}/edit'),
        ];
    }
}
