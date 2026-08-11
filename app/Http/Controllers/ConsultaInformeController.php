<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class ConsultaInformeController extends Controller
{
    public function show(Consulta $consulta): View
    {
        return view('consultas.informe', [
            'consulta' => $consulta,
            'respuestas' => $consulta->respuestas(),
        ]);
    }

    public function pdf(Consulta $consulta): Response
    {
        $pdf = Pdf::loadView('consultas.informe', [
            'consulta' => $consulta,
            'respuestas' => $consulta->respuestas(),
            'paraPdf' => true,
        ])->setPaper('letter');

        return $pdf->download("informe-consulta-{$consulta->id}.pdf");
    }
}
