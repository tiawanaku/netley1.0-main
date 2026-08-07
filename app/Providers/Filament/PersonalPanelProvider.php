<?php

namespace App\Providers\Filament;

use App\Filament\Personal\Auth\EditProfile;
use App\Filament\Personal\Auth\Login;
use App\Http\Middleware\EnsurePersonalPasswordChanged;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class PersonalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('personal')
            ->path('staff')
            ->brandName('Netley — Portal de Personal')
            ->authGuard('personal')
            ->login(Login::class)
            ->profile(EditProfile::class, isSimple: false)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->plugin(FilamentFullCalendarPlugin::make())
            ->discoverResources(in: app_path('Filament/Personal/Resources'), for: 'App\Filament\Personal\Resources')
            ->discoverPages(in: app_path('Filament/Personal/Pages'), for: 'App\Filament\Personal\Pages')
            ->discoverWidgets(in: app_path('Filament/Personal/Widgets'), for: 'App\Filament\Personal\Widgets')
            ->pages([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsurePersonalPasswordChanged::class,
            ]);
    }
}
