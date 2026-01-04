<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carta Responsiva</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        
        /* Ajuste del Header para el Logo */
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ce1126; padding-bottom: 10px; } /* Rojo Ramagas */
        .header img { height: 60px; margin-bottom: 10px; } 
        .header p { margin: 2px 0 0; font-size: 10px; color: #666; }
        
        .section { margin-bottom: 20px; }
        /* Ajuste del color de borde al rojo de la marca */
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
        
        <p style="font-weight: bold; font-size: 14px; color: #333; margin-top: 5px;">CARTA RESPONSIVA DE ACTIVO TECNOLÓGICO</p>
        <p>Folio: {{ $asignacion->id }} | Fecha: {{ $asignacion->fecha_asignacion->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Empleado Responsable</div>
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
                <th>Puesto</th>
                <td>{{ $asignacion->empleado->puesto->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Departamento / Ubicación</th>
                <td>{{ $asignacion->empleado->departamento->nombre ?? '' }} / {{ $asignacion->empleado->ubicacion->nombre ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalles del Activo Asignado</div>
        <table>
            <tr>
                <th>Tipo de Activo</th>
                <td>{{ $asignacion->activo->tipo->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Marca / Modelo</th>
                <td>{{ $asignacion->activo->marca->nombre ?? 'N/A' }} / {{ $asignacion->activo->modelo }}</td>
            </tr>
            <tr>
                <th>Número de Serie</th>
                <td><strong>{{ $asignacion->activo->numero_serie }}</strong></td>
            </tr>
            <tr>
                <th>Especificaciones Clave</th>
                <td>
                    @php $specs = $asignacion->activo->especificaciones ?? []; @endphp
                    @if(isset($specs['procesador'])) CPU: {{ $specs['procesador'] }}<br> @endif
                    @if(isset($specs['ram'])) RAM: {{ $specs['ram'] }}<br> @endif
                    @if(isset($specs['almacenamiento'])) Disco: {{ $specs['almacenamiento'] }}<br> @endif
                    @if(isset($specs['imei'])) IMEI: {{ $specs['imei'] }} @endif
                </td>
            </tr>
            <tr>
                <th>Condición de Entrega</th>
                <td>{{ $asignacion->estadoEntrega->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Accesorios / Observaciones</th>
                <td>{{ $asignacion->observaciones_entrega ?? 'Ninguna' }}</td>
            </tr>
        </table>
    </div>

    <div class="declaracion">
        <p>
            Por medio de la presente, certifico que he recibido el equipo detallado anteriormente en las condiciones descritas. 
            Me comprometo a hacer un uso responsable y exclusivamente laboral del mismo, así como a reportar cualquier falla 
            o desperfecto al área de Sistemas de inmediato. Entiendo que este equipo es propiedad de <strong>Grupo Ramagas</strong> 
            y deberé devolverlo cuando sea requerido o al finalizar mi relación laboral.
        </p>
    </div>

    <div class="firmas">
        <div class="firma-box">
            <br><br>
            <strong>{{ $asignacion->empleado->nombre }} {{ $asignacion->empleado->apellido_paterno }}</strong><br>
            Firma del Empleado
        </div>
        <div class="firma-box">
            <br><br>
            <strong>Departamento de Sistemas</strong><br>
            Autoriza / Entrega
        </div>
    </div>

    <div class="footer">
        Documento generado digitalmente por SIGMA (Sistema de Monitoreo de Activos) el {{ date('d/m/Y H:i') }}.
    </div>
</body>
</html>