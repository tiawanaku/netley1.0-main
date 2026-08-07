<?php

namespace App\Filament\Personal\Pages;

use App\Models\Personal;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Schema;

class CambiarPasswordObligatorio extends Page
{
    protected static string $routePath = '/cambiar-password-obligatorio';

    protected string $view = 'filament.personal.pages.cambiar-password-obligatorio';

    protected static ?string $title = 'Cambio de contraseña obligatorio';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('password')
                    ->label('Nueva contraseña')
                    ->password()
                    ->revealable()
                    ->required()
                    ->minLength(8)
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->label('Confirmar contraseña')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function guardarAction(): Action
    {
        return Action::make('guardar')
            ->label('Cambiar contraseña e ingresar')
            ->action(function (): void {
                $data = $this->form->getState();

                /** @var Personal $personal */
                $personal = Filament::auth()->user();
                $personal->password = $data['password'];
                $personal->must_change_password = false;
                $personal->save();

                Notification::make()
                    ->title('Contraseña actualizada')
                    ->success()
                    ->send();

                $this->redirect(Filament::getPanel('personal')->getUrl());
            });
    }
}
