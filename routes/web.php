<?php

use App\Http\Controllers\ConsultaInformeController;
use App\Http\Controllers\ReciboVerificacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/consultas/{consulta}/informe', [ConsultaInformeController::class, 'show'])
        ->name('consultas.informe');
    Route::get('/consultas/{consulta}/informe/pdf', [ConsultaInformeController::class, 'pdf'])
        ->name('consultas.informe.pdf');
});

// Verificación pública de recibos (NET-002): sin autenticación a propósito
// (se accede escaneando el QR impreso), protegida por firma de Laravel
// (URL::signedRoute) y limitada por throttle contra fuerza bruta.
Route::middleware(['signed', 'throttle:30,1'])->group(function () {
    Route::get('/verificar/recibo/{recibo:identificador}', [ReciboVerificacionController::class, 'show'])
        ->name('recibos.verificar');
    Route::get('/verificar/recibo/{recibo:identificador}/pdf', [ReciboVerificacionController::class, 'pdf'])
        ->name('recibos.verificar.pdf');
});
