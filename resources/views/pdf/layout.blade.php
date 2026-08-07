<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('titulo')</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .encabezado {
            border-bottom: 2px solid #d97706;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .encabezado .marca {
            font-size: 20px;
            font-weight: bold;
            color: #d97706;
        }
        .encabezado .titulo {
            font-size: 16px;
            font-weight: bold;
            margin-top: 6px;
        }
        .encabezado .fecha {
            font-size: 10px;
            color: #6b7280;
        }
        table.datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.datos td {
            padding: 4px 6px;
            vertical-align: top;
        }
        table.datos td.etiqueta {
            width: 160px;
            color: #6b7280;
        }
        table.cuotas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.cuotas th, table.cuotas td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        table.cuotas th {
            background-color: #f3f4f6;
        }
        .firmas {
            margin-top: 60px;
            width: 100%;
        }
        .firmas td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }
        .firmas .linea {
            border-top: 1px solid #1f2937;
            width: 80%;
            margin: 0 auto;
            padding-top: 4px;
        }
        .pie {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
    @yield('estilos')
</head>
<body>
    <div class="encabezado">
        <div class="marca">Netley — Gestión Legal</div>
        <div class="titulo">@yield('titulo')</div>
        <div class="fecha">Generado el {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    @yield('contenido')

    <div class="pie">Documento generado automáticamente por Netley.</div>
</body>
</html>
