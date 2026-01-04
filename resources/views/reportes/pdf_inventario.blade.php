<!DOCTYPE html>
<html>
<head>
    <title>Inventario de Activos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .logo { width: 150px; height: auto; float: left; }
        .info-empresa { text-align: right; float: right; font-size: 10px; color: #777; }
        .titulo { font-size: 18px; font-weight: bold; margin-top: 10px; clear: both; }
        .filtros { background-color: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0d6efd; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { border-bottom: 1px solid #ddd; padding: 6px; font-size: 11px; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 10px; color: white; }
        .bg-disponible { background-color: #198754; } /* Verde */
        .bg-uso { background-color: #0d6efd; } /* Azul */
        .bg-manto { background-color: #ffc107; color: black; } /* Amarillo */
        .bg-baja { background-color: #dc3545; } /* Rojo */

        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: center; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="titulo">REPORTE DE INVENTARIO GENERAL</div>
        <div style="clear: both;"></div>
    </div>

    <div class="filtros">
        <strong>Filtros aplicados:</strong><br>
        Ubicación: {{ $filtros['ubicacion'] }} | 
        Estado: {{ $filtros['estado'] }} | 
        Tipo: {{ $filtros['tipo'] }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Serie</th>
                <th>Equipo</th>
                <th>Marca/Modelo</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Adquisición</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
            <tr>
                <td><strong>{{ $activo->numero_serie }}</strong></td>
                <td>{{ $activo->tipo->nombre ?? '-' }}</td>
                <td>{{ $activo->marca->nombre ?? '-' }} {{ $activo->modelo }}</td>
                <td>{{ $activo->ubicacion->nombre ?? '-' }}</td>
                <td>
                    @php
                        $clase = 'bg-manto';
                        if($activo->estado_id == 1) $clase = 'bg-disponible';
                        if($activo->estado_id == 2) $clase = 'bg-uso';
                        if($activo->estado_id == 6) $clase = 'bg-baja';
                    @endphp
                    <span class="badge {{ $clase }}">
                        {{ $activo->estado->nombre ?? 'N/A' }}
                    </span>
                </td>
                <td>{{ $activo->fecha_adquisicion ? date('d/m/Y', strtotime($activo->fecha_adquisicion)) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 11px;">
        <strong>Total de activos listados:</strong> {{ count($activos) }}
    </div>

    <div class="footer">
        Sistema SIGMA - Generado el {{ date('d/m/Y H:i') }} - Página <span class="page-number"></span>
    </div>
</body>
</html>