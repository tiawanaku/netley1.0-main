<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo {{ $recibo->numero }} - Netley</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1f2933;
            margin: 0;
            padding: 24px;
            font-size: 13px;
        }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .subtitle { color: #6b7280; margin: 0 0 16px; font-size: 11px; }
        .estado {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 16px;
        }
        .estado.ok { background: #dcfce7; color: #166534; }
        .estado.anulado { background: #fee2e2; color: #991b1b; }
        .estado.alterado { background: #fef3c7; color: #92400e; }
        .grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .grid td { padding: 4px 8px 4px 0; vertical-align: top; }
        .label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }
        .value { font-size: 13px; }
        .monto { font-size: 20px; font-weight: bold; }
        .qr { text-align: center; margin: 20px 0; }
        .qr img { width: 160px; height: 160px; }
        .footer { margin-top: 16px; font-size: 10px; color: #6b7280; text-align: center; }
        .toolbar { margin-bottom: 20px; }
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
            <a href="{{ $pdfUrl }}">Descargar PDF</a>
        </div>
    @endunless

    <h1>Netley — Recibo {{ $recibo->numero }}</h1>
    <p class="subtitle">Verificación generada el {{ now()->format('d/m/Y H:i') }}</p>

    @if ($recibo->estado->value === 'anulado')
        <div class="estado anulado">⚠ Recibo ANULADO el {{ $recibo->anulado_en?->format('d/m/Y H:i') }}</div>
        <p>Motivo: {{ $recibo->motivo_anulacion }}</p>
    @elseif (! $autentico)
        <div class="estado alterado">⚠ Los datos de este recibo no coinciden con el registro original</div>
    @else
        <div class="estado ok">✅ Recibo verificado — emitido por Netley</div>
    @endif

    <table class="grid">
        <tr>
            <td>
                <span class="label">Número de recibo</span>
                <span class="value">{{ $recibo->numero }}</span>
            </td>
            <td>
                <span class="label">Fecha de pago</span>
                <span class="value">{{ $recibo->fecha_pago->format('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Cliente</span>
                <span class="value">{{ $recibo->cliente->nombre_completo }}</span>
            </td>
            <td>
                <span class="label">Proceso</span>
                <span class="value">{{ $recibo->proceso->tipo_proceso }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Concepto</span>
                <span class="value">{{ $recibo->concepto }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Monto</span>
                <span class="monto">Bs. {{ number_format((float) $recibo->monto, 2) }}</span>
            </td>
            <td>
                <span class="label">Registrado por</span>
                <span class="value">{{ $recibo->registrado_por ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <div class="qr">
        <img src="{{ $qrDataUri }}" alt="QR de verificación">
        <p class="subtitle">Escanea para verificar este recibo en cualquier momento</p>
    </div>

    <p class="footer">Identificador: {{ $recibo->identificador }}</p>
</body>
</html>
