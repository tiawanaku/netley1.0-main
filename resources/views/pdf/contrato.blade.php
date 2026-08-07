@extends('pdf.layout')

@section('titulo', 'Contrato de Prestación de Servicios — Iguala Profesional')

@php
    $finanza = $proceso->finanza;
    $abogado = $proceso->abogado;
    $cliente = $proceso->cliente;
    $tratamientoAbogado = $abogado?->genero?->value === 'femenino' ? 'Dra.' : 'Dr.';
    $costo = (float) ($finanza?->costo ?? 0);
    $anticipo = (float) ($finanza?->anticipo ?? 0);
    $saldo = max($costo - $anticipo, 0);
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
    $hoy = now();
@endphp

@section('estilos')
    <style>
        @page {
            margin: 12mm 14mm 10mm;
        }
        body {
            font-size: 9.5px;
            line-height: 1.3;
        }
        .encabezado {
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .encabezado .marca {
            font-size: 15px;
        }
        .encabezado .titulo {
            font-size: 12px;
            margin-top: 3px;
        }
        table.datos {
            margin-bottom: 8px;
        }
        table.datos td {
            padding: 2px 6px;
        }
        p {
            margin: 0 0 5px;
            text-align: justify;
        }
        .firmas {
            margin-top: 30px;
        }
        .firmas td {
            padding-top: 24px;
        }
        .firmas .nombre-firma {
            margin-top: 4px;
            font-size: 9px;
        }
    </style>
@endsection

@section('contenido')
    <table class="datos">
        <tr>
            <td class="etiqueta">Cliente</td>
            <td>{{ $cliente?->nombre_completo }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Abogado</td>
            <td>{{ $abogado?->nombre_completo ?? 'Por asignar' }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Materia / proceso</td>
            <td>{{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}</td>
        </tr>
        @if ($finanza)
            <tr>
                <td class="etiqueta">Honorarios</td>
                <td>Bs. {{ number_format($costo, 2) }} ({{ $finanza->tipo_pago->getLabel() }})</td>
            </tr>
        @endif
    </table>

    <p style="text-align: center;"><strong>CONTRATO DE PRESTACIÓN DE SERVICIOS — IGUALA PROFESIONAL</strong></p>

    <p>
        Conste por el presente documento, que a solo reconocimiento de firmas y rúbricas podrá ser elevado
        a instrumento público, la Iguala Profesional que celebran las partes al tenor de las siguientes cláusulas:
    </p>

    <p>
        <strong>PRIMERA.- (PARTES).</strong> Son partes contratantes: la empresa <strong>Netley S.R.L.</strong>,
        representada por {{ $tratamientoAbogado }} {{ $abogado?->nombre_completo ?? '_______________' }},
        con C.I. {{ $abogado?->ci ?? '_______________' }}, mayor de edad, hábil por derecho, quien en adelante
        se denominará <strong>EL ABOGADO</strong>; y por otra parte, {{ $cliente?->nombre_completo ?? '_______________' }},
        con C.I. {{ $cliente?->ci ?? '_______________' }}, mayor de edad y hábil por derecho, quien en adelante
        se denominará <strong>EL CLIENTE</strong>.
    </p>

    <p>
        <strong>SEGUNDA.- (OBJETO).</strong> EL CLIENTE, en forma libre y voluntaria y sin que medie vicio alguno
        del consentimiento, contrata los servicios profesionales del ABOGADO para:
        <strong>{{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}</strong>.
    </p>

    <p>
        <strong>TERCERA.- (OBLIGACIONES DE LAS PARTES).</strong> Son obligaciones de las partes las siguientes:
    </p>
    <p>
        <u>EL CLIENTE se obliga a:</u> proporcionar al ABOGADO toda la información y documentación necesaria
        para el adecuado patrocinio legal del proceso, bajo su responsabilidad; presentar la documentación y
        pruebas en el plazo solicitado por el ABOGADO; mantener en reserva la información proporcionada por el
        ABOGADO; cancelar puntualmente los honorarios profesionales acordados; y correr con los gastos
        emergentes del proceso (notificaciones, fotocopias, transporte, valores judiciales y viáticos si
        corresponde).
    </p>
    <p>
        <u>EL ABOGADO se obliga a:</u> realizar el servicio encomendado con patrocinio profesional, idóneo y
        ético; mantener reserva sobre la información proporcionada por EL CLIENTE; mantener informado al
        CLIENTE sobre el avance del proceso verbalmente y por escrito cuando sea requerido; asesorar
        técnicamente al CLIENTE hasta la conclusión del contrato; y finalizar el servicio encomendado en un
        plazo estimado de <strong>{{ $proceso->tiempo_proceso_meses }} meses</strong>, salvo demora por causa
        a) del cliente, b) del órgano judicial, o c) fuerza mayor.
    </p>

    <p>
        <strong>CUARTA.- (HONORARIOS PROFESIONALES Y FORMA DE PAGO).</strong>
        @if ($finanza)
            EL CLIENTE se obliga a cancelar a favor del ABOGADO la suma de <strong>Bs. {{ number_format($costo, 2) }}</strong>
            por el servicio descrito en la cláusula segunda,
            @if ($saldo <= 0)
                monto que se cancela <strong>AL CONTADO</strong>, en su totalidad, a la firma del presente documento.
            @else
                monto que será cubierto de la siguiente manera: Bs. {{ number_format($anticipo, 2) }} a la firma
                del presente documento, y el saldo de Bs. {{ number_format($saldo, 2) }} mediante pagos de forma
                {{ strtolower($finanza->tipo_pago->getLabel()) }}, debiendo quedar cancelado indispensablemente
                antes de la conclusión del proceso.
            @endif
            Se establece que la única forma de acreditar el pago de honorarios profesionales es a través de
            recibos emitidos por Netley S.R.L., mismos que constituyen prueba plena de cancelación.
        @else
            Los honorarios y la forma de pago se encuentran pendientes de registro.
        @endif
    </p>

    <p>
        <strong>QUINTA.- (CONCLUSIÓN EXTRAORDINARIA).</strong> Se deja expresamente establecido que los
        honorarios profesionales descritos en la cláusula cuarta deberán, sin excepción, ser cancelados a
        favor de Netley S.R.L. si EL CLIENTE: a) por decisión propia desiste o abandona el caso, b) decide
        transar o conciliar, o c) decide contratar los servicios de otro abogado.
    </p>

    <p>
        <strong>SEXTA.- (CONFORMIDAD).</strong> Ambas partes, EL ABOGADO y EL CLIENTE, manifiestan su entera
        conformidad con todas y cada una de las cláusulas precedentes y se obligan a su fiel cumplimiento,
        a los {{ $hoy->day }} días del mes de {{ $meses[$hoy->month] }} del año {{ $hoy->year }}.
    </p>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea">Firma del Cliente</div>
                <div class="nombre-firma">
                    {{ $cliente?->nombre_completo ?? '_______________' }}
                    @if ($cliente?->ci)
                        <br>C.I. {{ $cliente->ci }}
                    @endif
                </div>
            </td>
            <td>
                <div class="linea">Firma del Abogado / Netley</div>
                <div class="nombre-firma">
                    {{ $abogado?->nombre_completo ?? '_______________' }}
                    @if ($abogado?->ci)
                        <br>C.I. {{ $abogado->ci }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
@endsection
