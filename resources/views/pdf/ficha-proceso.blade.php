@extends('pdf.layout')

@section('titulo', 'Ficha del Proceso')

@section('contenido')
    <table class="datos">
        <tr>
            <td class="etiqueta">Cliente</td>
            <td>{{ $proceso->cliente->nombre_completo }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Materia legal</td>
            <td>{{ $proceso->materia_legal->getLabel() }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Tipo de proceso</td>
            <td>{{ $proceso->tipo_proceso }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Duración estimada</td>
            <td>{{ $proceso->tiempo_proceso_meses }} meses</td>
        </tr>
        <tr>
            <td class="etiqueta">Fecha de registro</td>
            <td>{{ $proceso->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    @if ($proceso->finanza)
        <table class="datos">
            <tr>
                <td class="etiqueta" style="font-weight: bold;">Finanzas</td>
                <td></td>
            </tr>
            <tr>
                <td class="etiqueta">Costo total</td>
                <td>Bs. {{ number_format($proceso->finanza->costo, 2) }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Tipo de pago</td>
                <td>{{ $proceso->finanza->tipo_pago->getLabel() }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Cuotas generadas</td>
                <td>{{ $proceso->finanza->planPagos->count() }}</td>
            </tr>
        </table>
    @endif
@endsection
