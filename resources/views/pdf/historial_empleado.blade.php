<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial de Activos - {{ $empleado->numero_empleado }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ce1126; padding-bottom: 10px; }
        .header img { height: 55px; margin-bottom: 5px; }
        .header p { margin: 0; font-size: 10px; color: #666; }
        
        .section-title { 
            font-weight: bold; background-color: #f0f0f0; 
            padding: 5px; border-left: 4px solid #ce1126; margin-bottom: 10px; font-size: 12px;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }

        .status-active { color: #155724; font-weight: bold; }
        .status-returned { color: #856404; font-style: italic; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: center; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo-ramagas.webp') }}" alt="Grupo Ramagas">
        <h2 style="margin: 5px 0; color: #333;">HISTORIAL DE ACTIVOS ASIGNADOS</h2>
        <p>Fecha de Emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="section-title">INFORMACIÓN DEL EMPLEADO</div>
    <table style="margin-bottom: 20px;">
        <tr>
            <th width="20%">No. Empleado</th>
            <td width="30%">{{ $empleado->numero_empleado }}</td>
            <th width="20%">Estatus</th>
            <td width="30%">{{ $empleado->estatus }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td colspan="3">{{ $empleado->nombre_completo }}</td>
        </tr>
        <tr>
            <th>Puesto</th>
            <td>{{ $empleado->puesto->nombre ?? 'N/A' }}</td>
            <th>Departamento</th>
            <td>{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Ubicación</th>
            <td colspan="3">{{ $empleado->ubicacion->nombre ?? 'N/A' }}</td>
        </tr>
        @if($empleado->estatus == 'Baja')
        <tr>
            <th>Fecha Baja</th>
            <td>{{ \Carbon\Carbon::parse($empleado->fecha_baja)->format('d/m/Y') }}</td>
            <th>Motivo</th>
            <td>{{ $empleado->motivo_baja }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">RELACIÓN DE EQUIPOS Y ACTIVOS</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Código / Serie</th>
                <th width="20%">Tipo / Marca</th>
                <th width="25%">Modelo</th>
                <th width="15%">Fecha Asig.</th>
                <th width="15%">Fecha Dev.</th>
                <th width="10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleado->asignaciones as $asig)
            <tr>
                <td>
                    <div style="font-weight:bold;">{{ $asig->activo->numero_serie }}</div>
                </td>
                <td>
                    {{ $asig->activo->tipo->nombre }}<br>
                    <small>{{ $asig->activo->marca->nombre }}</small>
                </td>
                <td>{{ $asig->activo->modelo }}</td>
                <td>{{ \Carbon\Carbon::parse($asig->fecha_asignacion)->format('d/m/Y') }}</td>
                <td>
                    {{ $asig->fecha_devolucion ? \Carbon\Carbon::parse($asig->fecha_devolucion)->format('d/m/Y') : '-' }}
                </td>
                <td>
                    @if($asig->fecha_devolucion)
                        <span class="status-returned">Devuelto</span>
                    @else
                        <span class="status-active">ACTIVO</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px; color: #777;">
                    No se encontraron registros de asignaciones para este empleado.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        SIGMA - Sistema de Gestión de Activos | Grupo Ramagas S.A de C.V.
    </div>
</body>
</html>