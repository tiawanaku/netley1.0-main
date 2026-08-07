<?php

namespace App\Http\Middleware;

use App\Filament\Personal\Pages\CambiarPasswordObligatorio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePersonalPasswordChanged
{
    /**
     * RN-PER-02: mientras el Personal no cambie su contraseña temporal,
     * solo puede ver la pantalla de cambio de contraseña obligatorio.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $personal = auth('personal')->user();

        if (
            $personal
            && $personal->must_change_password
            && ! $request->routeIs('filament.personal.pages.cambiar-password-obligatorio')
            && ! $request->routeIs('filament.personal.auth.logout')
        ) {
            return redirect(CambiarPasswordObligatorio::getUrl());
        }

        return $next($request);
    }
}
