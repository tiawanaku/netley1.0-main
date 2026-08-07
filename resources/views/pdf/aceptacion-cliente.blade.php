@extends('pdf.layout')

@section('titulo', 'Aceptación del Cliente')

@section('contenido')
    <table class="datos">
        <tr>
            <td class="etiqueta">Cliente</td>
            <td>{{ $proceso->cliente->nombre_completo }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Proceso</td>
            <td>{{ $proceso->materia_legal->getLabel() }} — {{ $proceso->tipo_proceso }}</td>
        </tr>
    </table>

    <p>
        Yo, <strong>{{ $proceso->cliente->nombre_completo }}</strong>, declaro haber sido informado(a)
        de los alcances, tiempos estimados y costos del servicio legal descrito en este documento, y
        acepto las condiciones acordadas con Netley — Gestión Legal para el proceso de
        {{ $proceso->materia_legal->getLabel() }} ({{ $proceso->tipo_proceso }}).
    </p>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea">Firma del Cliente</div>
            </td>
            <td>
                <div class="linea">Fecha</div>
            </td>
        </tr>
    </table>
@endsection
