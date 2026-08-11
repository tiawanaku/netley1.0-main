<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe de consulta - {{ $consulta->nombre_completo }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1f2933;
            margin: 0;
            padding: 24px;
            font-size: 13px;
        }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 {
            font-size: 14px;
            margin: 24px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
        }
        .subtitle { color: #6b7280; margin: 0 0 20px; }
        .grid {
            width: 100%;
            border-collapse: collapse;
        }
        .grid td {
            padding: 4px 8px 4px 0;
            vertical-align: top;
            width: 50%;
        }
        .label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }
        .value { font-size: 13px; }
        .respuesta {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 14px;
        }
        .respuesta .contenido {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            white-space: pre-wrap;
        }
        .metrica {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 8px;
            background: #f3f4f6;
            border-radius: 4px;
            font-size: 11px;
            color: #374151;
        }
        .toolbar {
            margin-bottom: 20px;
        }
        .toolbar a, .toolbar button {
            display: inline-block;
            padding: 8px 16px;
            margin-right: 8px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            color: #1f2933;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    @unless ($paraPdf ?? false)
        <div class="toolbar no-print">
            <button onclick="window.print()">Imprimir</button>
            <a href="{{ route('consultas.informe.pdf', $consulta) }}">Descargar PDF</a>
        </div>
    @endunless

    <h1>Informe de consulta</h1>
    <p class="subtitle">Generado el {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Datos del consultante</h2>
    <table class="grid">
        <tr>
            <td>
                <span class="label">Nombre</span>
                <span class="value">{{ $consulta->nombre_completo }}</span>
            </td>
            <td>
                <span class="label">Teléfono</span>
                <span class="value">{{ $consulta->telefono }}{{ $consulta->whatsapp ? ' / WhatsApp: '.$consulta->whatsapp : '' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Email</span>
                <span class="value">{{ $consulta->email ?: '—' }}</span>
            </td>
            <td>
                <span class="label">Ciudad</span>
                <span class="value">{{ \App\Models\Consulta::CIUDADES[$consulta->ciudad] ?? $consulta->ciudad }}</span>
            </td>
        </tr>
    </table>

    <h2>Detalle de la consulta</h2>
    <table class="grid">
        <tr>
            <td>
                <span class="label">Tipo de proceso</span>
                <span class="value">{{ $consulta->tipo_proceso?->getLabel() ?? '—' }}</span>
            </td>
            <td>
                <span class="label">Origen</span>
                <span class="value">{{ $consulta->origen?->getLabel() ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Forma de ingreso</span>
                <span class="value">{{ $consulta->forma_ingreso?->getLabel() ?? '—' }}</span>
            </td>
            <td>
                <span class="label">Fecha de registro</span>
                <span class="value">{{ $consulta->created_at?->format('d/m/Y H:i') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Descripción de la consulta</span>
                <span class="value">{{ $consulta->descripcion ?: '—' }}</span>
            </td>
        </tr>
    </table>

    <h2>Respuestas</h2>

    @forelse ($respuestas as $respuesta)
        <div class="respuesta">
            <table class="grid">
                <tr>
                    <td>
                        <span class="label">Consulta tomada por</span>
                        <span class="value">{{ $respuesta['tomada_por'] }}</span>
                    </td>
                    <td>
                        <span class="label">Fecha de la cita</span>
                        <span class="value">{{ $respuesta['fecha_cita'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Respondido por</span>
                        <span class="value">{{ $respuesta['respondido_por'] }}</span>
                    </td>
                    <td>
                        <span class="label">Fecha de la respuesta</span>
                        <span class="value">{{ $respuesta['fecha_respuesta'] }}</span>
                    </td>
                </tr>
            </table>
            <span class="metrica">Tiempo de respuesta: {{ $respuesta['tiempo_respuesta'] }}</span>
            <div class="contenido">{{ $respuesta['contenido'] }}</div>
        </div>
    @empty
        <p>No hay respuestas registradas todavía.</p>
    @endforelse
</body>
</html>
