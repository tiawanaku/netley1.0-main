@extends('pdf.layout')

@section('titulo', 'Ficha del Cliente')

@section('contenido')
    <table class="datos">
        <tr>
            <td class="etiqueta">Nombre completo</td>
            <td>{{ $cliente->nombre_completo }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Usuario del portal</td>
            <td>{{ $cliente->usuario }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Teléfono</td>
            <td>{{ $cliente->telefono }}</td>
        </tr>
        <tr>
            <td class="etiqueta">WhatsApp</td>
            <td>{{ $cliente->whatsapp ?: '—' }}</td>
        </tr>
        <tr>
            <td class="etiqueta">Cliente desde</td>
            <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="datos">
        <tr>
            <td class="etiqueta" style="font-weight: bold;">Procesos asociados</td>
            <td></td>
        </tr>
        @forelse ($cliente->procesos as $proceso)
            <tr>
                <td class="etiqueta">{{ $proceso->materia_legal->getLabel() }}</td>
                <td>{{ $proceso->tipo_proceso }} — {{ $proceso->tiempo_proceso_meses }} meses</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">Sin procesos registrados.</td>
            </tr>
        @endforelse
    </table>
@endsection
