<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Constancia de Devolución</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ce1126; padding-bottom: 10px; }
        .header img { height: 60px; margin-bottom: 10px; } 
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; background-color: #f0f0f0; padding: 5px; border-left: 4px solid #ce1126; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; width: 30%; }
        .declaracion { margin: 30px 0; text-align: justify; font-size: 11px; font-style: italic; }
        .firmas { margin-top: 60px; width: 100%; }
        .firma-box { width: 45%; float: left; text-align: center; border-top: 1px solid #333; padding-top: 10px; margin-right: 5%; }
        .firma-box:last-child { margin-right: 0; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: center; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo-ramagas.webp') }}" alt="Grupo Ramagas">
        <p style="font-weight: bold; font-size: 14px; color: #333; margin-top: 5px;">CONSTANCIA DE DEVOLUCIÓN DE ACTIVO</p>
        <p>Folio: {{ $asignacion->id }} | Fecha de Devolución: {{ $asignacion->fecha_devolucion ? $asignacion->fecha_devolucion->format('d/m/Y') : 'Pendiente' }}</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Empleado que Entrega</div>
        <table>
            <tr>
                <th>Nombre Completo</th>
                <td>{{ $asignacion->empleado->nombre }} {{ $asignacion->empleado->apellido_paterno }} {{ $asignacion->empleado->apellido_materno }}</td>
            </tr>
            <tr>
                <th>No. Empleado</th>
                <td>{{ $asignacion->empleado->numero_empleado }}</td>
            </tr>
            <tr>
                <th>Departamento</th>
                <td>{{ $asignacion->empleado->departamento->nombre ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles del Activo Recibido</div>
        <table>
            <tr>
                <th>Tipo / Marca / Modelo</th>
                <td>{{ $asignacion->activo->tipo->nombre ?? 'N/A' }} - {{ $asignacion->activo->marca->nombre ?? 'N/A' }} {{ $asignacion->activo->modelo }}</td>
            </tr>
            <tr>
                <th>Número de Serie</th>
                <td><strong>{{ $asignacion->activo->numero_serie }}</strong></td>
            </tr>
            <tr>
                <th>Estado de Recepción</th>
                <td>{{ $asignacion->estadoDevolucion->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Observaciones de Recepción</th>
                <td>{{ $asignacion->observaciones_devolucion ?? 'Ninguna' }}</td>
            </tr>
        </table>
    </div>

    <div class="declaracion">
        <p>
            Por medio de la presente, el Departamento de Sistemas de <strong>Grupo Ramagas</strong> hace constar que ha recibido 
            el activo tecnológico descrito anteriormente por parte del empleado mencionado. 
            El equipo ha sido inspeccionado y se ha registrado su estado físico y funcional en el sistema. 
            Con esta entrega, el empleado queda liberado de la responsabilidad sobre dicho activo.
        </p>
    </div>

    <div class="firmas">
        <div class="firma-box">
            <br><br>
            <strong>{{ $asignacion->empleado->nombre }} {{ $asignacion->empleado->apellido_paterno }}</strong><br>
            Entregó (Empleado)
        </div>
        <div class="firma-box">
            <br><br>
            <strong>Departamento de Sistemas</strong><br>
            Recibió / Vo.Bo.
        </div>
    </div>

    <div class="footer">
        Documento generado digitalmente por SIGMA (Sistema de Monitoreo de Activos).
    </div>
</body>
</html>