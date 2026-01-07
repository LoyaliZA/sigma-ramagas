<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carta Responsiva</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        
        /* Encabezado */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ce1126; padding-bottom: 10px; }
        .header img { height: 50px; margin-bottom: 5px; } 
        .header h1 { margin: 2px 0; font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        
        /* Secciones */
        .section { margin-bottom: 15px; }
        .section-title { 
            font-weight: bold; 
            background-color: #f0f0f0; 
            padding: 5px 10px; 
            border-left: 4px solid #ce1126; 
            margin-bottom: 8px; 
            font-size: 11px;
        }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f8f9fa; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        
        /* Texto Legal */
        .declaracion { 
            margin: 20px 0; 
            text-align: justify; 
            font-size: 10px; 
            font-style: italic; 
            padding: 10px;
            background-color: #fafafa;
            border: 1px solid #eee;
        }
        
        /* Firmas */
        .firmas-container { margin-top: 50px; width: 100%; }
        .firma-box { 
            width: 40%; 
            display: inline-block; 
            text-align: center; 
            border-top: 1px solid #333; 
            padding-top: 5px; 
            margin: 0 4%; 
        }

        .footer { 
            position: fixed; bottom: 0; left: 0; right: 0; 
            font-size: 8px; text-align: center; color: #999; 
            border-top: 1px solid #ddd; padding-top: 5px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo-ramagas.webp') }}" alt="Grupo Ramagas">
        <h1>Carta Responsiva de Activos</h1>
        <p>
            <strong>Folio Lote:</strong> {{ substr($asignaciones->first()->lote_id, 0, 8) }} | 
            <strong>Fecha:</strong> {{ $asignaciones->first()->fecha_asignacion->format('d/m/Y') }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Responsable</div>
        <table>
            <tr>
                <th width="20%">Nombre:</th>
                <td width="30%">{{ $empleado->nombre }} {{ $empleado->apellido_paterno }} {{ $empleado->apellido_materno }}</td>
                <th width="15%">No. Empleado:</th>
                <td width="35%">{{ $empleado->numero_empleado }}</td>
            </tr>
            <tr>
                <th>Puesto:</th>
                <td>{{ $empleado->puesto->nombre ?? 'N/A' }}</td>
                <th>Departamento:</th>
                <td>{{ $empleado->departamento->nombre ?? '' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Equipos Asignados</div>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th width="15%">Tipo</th>
                    <th width="25%">Marca / Modelo</th>
                    <th width="20%">Serie / Código</th>
                    <th width="15%">Estado</th>
                    <th width="25%">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asignaciones as $item)
                <tr>
                    <td>{{ $item->activo->tipo->nombre ?? 'Activo' }}</td>
                    <td>
                        {{ $item->activo->marca->nombre ?? '' }}<br>
                        {{ $item->activo->modelo }}
                    </td>
                    <td>
                        <strong>{{ $item->activo->numero_serie }}</strong><br>
                        <span style="color:#666; font-size:9px;">{{ $item->activo->codigo_interno ?? '' }}</span>
                    </td>
                    <td>{{ $item->estadoEntrega->nombre ?? 'N/A' }}</td>
                    <td>{{ $item->observaciones_entrega ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="declaracion">
        <p>
            Recibo de conformidad los equipos y accesorios descritos, propiedad de <strong>Grupo Ramagas</strong>.
            Me comprometo a utilizarlos exclusivamente para mis labores, cuidarlos diligentemente y reportar cualquier incidencia al área de Sistemas.
            Entiendo que deberé devolverlos cuando la empresa lo solicite.
        </p>
    </div>

    <div class="firmas-container">
        <div class="firma-box">
            <br><br><br>
            <strong>{{ $empleado->nombre }} {{ $empleado->apellido_paterno }}</strong><br>
            Firma del Empleado
        </div>
        <div class="firma-box">
            <br><br><br>
            <strong>Departamento de Sistemas</strong><br>
            Entrega
        </div>
    </div>

    <div class="footer">
        Documento generado por SIGMA el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>