@extends('pdf.layout')

@section('titulo', 'Plan de Pagos')

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
        <tr>
            <td class="etiqueta">Costo total</td>
            <td>Bs. {{ number_format($finanza->costo, 2) }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Tipo de pago</td>
            <td>{{ $finanza->tipo_pago->getLabel() }}</td>
        </tr>
    </table>

    <table class="cuotas">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($finanza->planPagos as $index => $cuota)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $cuota->fecha->format('d/m/Y') }}</td>
                    <td>Bs. {{ number_format($cuota->monto, 2) }}</td>
                    <td>{{ $cuota->estado->getLabel() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
