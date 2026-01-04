<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bajas de Activos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #b91c1c; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #b91c1c; }
        .header p { margin: 5px 0 0; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #1f2937; font-weight: bold; text-align: left; padding: 10px; border-bottom: 1px solid #ddd; font-size: 11px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #eee; vertical-align: top; }
        
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; display: inline-block; }
        .bg-baja { background-color: #ef4444; } /* Rojo */
        
        .meta { font-size: 10px; color: #888; text-align: right; margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; }
        
        .empty { text-align: center; padding: 50px; color: #999; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Bajas Definitivas</h1>
        <p>Listado histórico de activos retirados del inventario</p>
        <p style="font-size: 10px; margin-top: 5px;">Generado el: {{ date('d/m/Y h:i A') }}</p>
    </div>

    @if($activos->count() > 0)
    <table>
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th width="20%">Activo</th>
                <th width="15%">Fecha Baja</th>
                <th width="15%">Motivo</th>
                <th width="35%">Justificación / Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
            <tr>
                <td>
                    <strong>{{ $activo->codigo_interno }}</strong><br>
                    <span style="color: #666; font-size: 10px;">SN: {{ $activo->numero_serie }}</span>
                </td>
                <td>
                    {{ $activo->tipo->nombre ?? '' }}<br>
                    <span style="color: #666;">{{ $activo->marca->nombre ?? '' }} {{ $activo->modelo }}</span>
                </td>
                <td>
                    {{ $activo->fecha_baja ? \Carbon\Carbon::parse($activo->fecha_baja)->format('d/m/Y') : 'N/A' }}
                </td>
                <td>
                    <span class="badge bg-baja">
                        {{ $activo->motivoBaja->nombre ?? 'General' }}
                    </span>
                </td>
                <td>
                    {{-- Limpiamos el texto para quitar los corchetes de sistema si es necesario --}}
                    {{ Str::limit($activo->observaciones, 150) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="empty">
            No hay registros de activos dados de baja en el sistema.
        </div>
    @endif

    <div class="meta">
        Documento generado automáticamente por el sistema SIGMA v1.0
    </div>

</body>
</html>