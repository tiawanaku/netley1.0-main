<?php

namespace App\Filament\Cliente\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mis datos')
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
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->helperText('7 u 8 dígitos, empieza con 2-7.')
                            ->maxLength(20),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->rule('regex:/^[2-7][0-9]{6,7}$/')
                            ->maxLength(20),
                    ]),
                Section::make('Cambiar contraseña')
                    ->columns(2)
                    ->components([
                        $this->getCurrentPasswordFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }
}
