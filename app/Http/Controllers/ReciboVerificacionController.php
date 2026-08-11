<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ReciboVerificacionController extends Controller
{
    public function show(Recibo $recibo): View
    {
        return view('recibos.recibo', [
            'recibo' => $recibo,
            'autentico' => $recibo->esAutentico(),
            'qrDataUri' => $this->qrDataUri($recibo),
            'pdfUrl' => URL::signedRoute('recibos.verificar.pdf', ['recibo' => $recibo->identificador]),
        ]);
    }

    public function pdf(Recibo $recibo): Response
    {
        $pdf = Pdf::loadView('recibos.recibo', [
            'recibo' => $recibo,
            'autentico' => $recibo->esAutentico(),
            'qrDataUri' => $this->qrDataUri($recibo),
            'paraPdf' => true,
        ])->setPaper([0, 0, 288, 700]); // formato angosto tipo ticket/recibo

        return $pdf->download("{$recibo->numero}.pdf");
    }

    protected function qrDataUri(Recibo $recibo): string
    {
        $url = URL::signedRoute('recibos.verificar', ['recibo' => $recibo->identificador]);

        $result = (new Builder(
            writer: new PngWriter(),
            data: $url,
            size: 260,
            margin: 8,
        ))->build();

        return $result->getDataUri();
    }
}
