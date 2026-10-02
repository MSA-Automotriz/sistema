<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Trabajo #{{ $orden->codigo_orden ?? $orden->id }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .company-subtitle {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .doc-box {
            border: 2px solid #0f172a;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
            background-color: #f8fafc;
        }
        .doc-box h2 {
            margin: 0;
            font-size: 13px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-box .code {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            margin: 3px 0 0 0;
        }
        .section-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .section-header {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #334155;
            padding: 4px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        .section-content {
            padding: 6px 8px;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 30%;
        }
        .value {
            color: #0f172a;
            width: 70%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .totals-table td {
            padding: 3px 6px;
            font-size: 10px;
        }
        .totals-table .total-label {
            font-weight: 600;
            color: #334155;
            text-align: right;
        }
        .totals-table .total-amount {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .totals-table .grand-total {
            background-color: #f1f5f9;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 11px;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }
        .signature-line {
            border-top: 1px solid #475569;
            padding-top: 4px;
            font-size: 9px;
            color: #334155;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <h1 class="company-name">MSA AUTOMOTRIZ</h1>
                <p class="company-subtitle">Taller de Mantenimiento Mecánico y Diagnóstico Electrónico</p>
                <p class="company-subtitle">RUC: 20600000000 | Taller Principal | Tel: (01) 234-5678</p>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="doc-box">
                    <h2>ORDEN DE TRABAJO</h2>
                    <div class="code">{{ $orden->codigo_orden ?? ('OT-' . str_pad($orden->id, 6, '0', STR_PAD_LEFT)) }}</div>
                    <div style="font-size: 9px; color: #64748b; margin-top: 3px;">
                        Estado: <strong style="text-transform: uppercase; color: #2563eb;">{{ $orden->estado ?? 'En Proceso' }}</strong>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Datos del Cliente y Vehículo -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <table class="section-box">
                    <tr><td class="section-header">Datos del Cliente</td></tr>
                    <tr>
                        <td class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td class="label">Cliente:</td>
                                    <td class="value">
                                        @if($orden->cliente)
                                            @if($orden->cliente->tipo_cliente === 'natural')
                                                {{ trim(($orden->cliente->nombres ?? '') . ' ' . ($orden->cliente->apellido_paterno ?? '') . ' ' . ($orden->cliente->apellido_materno ?? '')) }}
                                            @else
                                                {{ $orden->cliente->razon_social ?? 'Cliente corporativo' }}
                                            @endif
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Doc. Identidad:</td>
                                    <td class="value">{{ $orden->cliente->documento_identidad ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teléfono:</td>
                                    <td class="value">{{ $orden->cliente->telefonos->first()->numero ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Técnico Resp.:</td>
                                    <td class="value">{{ $orden->tecnico->name ?? 'Por asignar' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <table class="section-box">
                    <tr><td class="section-header">Datos del Vehículo</td></tr>
                    <tr>
                        <td class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td class="label">Placa:</td>
                                    <td class="value"><strong style="font-size: 12px; color: #2563eb;">{{ $orden->vehiculo->placa ?? 'SIN PLACA' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="label">Marca / Modelo:</td>
                                    <td class="value">{{ $orden->vehiculo->marca ?? 'N/A' }} {{ $orden->vehiculo->modelo ?? '' }} ({{ $orden->vehiculo->anio ?? '-' }})</td>
                                </tr>
                                <tr>
                                    <td class="label">Km Ingreso:</td>
                                    <td class="value">{{ $orden->kilometraje_ingreso ? number_format($orden->kilometraje_ingreso) . ' km' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Fecha Ingreso:</td>
                                    <td class="value">{{ $orden->fecha_ingreso ? $orden->fecha_ingreso->format('d/m/Y H:i') : ($orden->created_at ? $orden->created_at->format('d/m/Y') : date('d/m/Y')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Diagnóstico y Problema Reportado -->
    @if($orden->descripcion_problema || $orden->diagnostico)
    <table class="section-box" style="margin-bottom: 10px;">
        <tr><td class="section-header">Diagnóstico y Motivo de Ingreso</td></tr>
        <tr>
            <td class="section-content">
                @if($orden->descripcion_problema)
                    <p style="margin: 2px 0;"><strong>Falla / Motivo:</strong> {{ $orden->descripcion_problema }}</p>
                @endif
                @if($orden->diagnostico)
                    <p style="margin: 2px 0;"><strong>Diagnóstico Técnico:</strong> {{ $orden->diagnostico }}</p>
                @endif
            </td>
        </tr>
    </table>
    @endif

    <!-- Tabla de Servicios / Mano de Obra -->
    <div style="font-weight: bold; font-size: 10px; margin-top: 6px; margin-bottom: 2px; color: #0f172a;">1. SERVICIOS Y MANO DE OBRA REALIZADA</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 55%;" class="text-left">Servicio / Actividad</th>
                <th style="width: 10%;" class="text-center">Cant.</th>
                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotalServicios = 0; @endphp
            @forelse($orden->detallesServicios as $i => $ds)
                @php
                    $monto = (float)($ds->cantidad * $ds->precio_unitario);
                    $subtotalServicios += $monto;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $ds->servicio->nombre ?? ($ds->descripcion ?? 'Servicio') }}</td>
                    <td class="text-center">{{ number_format($ds->cantidad, 0) }}</td>
                    <td class="text-right">S/ {{ number_format($ds->precio_unitario, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($monto, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center" style="color: #94a3b8;">No se registraron servicios.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabla de Repuestos Utilizados -->
    <div style="font-weight: bold; font-size: 10px; margin-top: 6px; margin-bottom: 2px; color: #0f172a;">2. REPUESTOS E INSUMOS UTILIZADOS</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 15%;" class="text-left">Código</th>
                <th style="width: 40%;" class="text-left">Descripción del Repuesto</th>
                <th style="width: 10%;" class="text-center">Cant.</th>
                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotalRepuestos = 0; @endphp
            @forelse($orden->detallesRepuestos as $j => $dr)
                @php
                    $montoR = (float)($dr->cantidad * $dr->precio_unitario);
                    $subtotalRepuestos += $montoR;
                @endphp
                <tr>
                    <td class="text-center">{{ $j + 1 }}</td>
                    <td class="font-mono">{{ $dr->parte->codigo ?? '-' }}</td>
                    <td>{{ $dr->parte->nombre ?? 'Repuesto' }}</td>
                    <td class="text-center">{{ number_format($dr->cantidad, 0) }}</td>
                    <td class="text-right">S/ {{ number_format($dr->precio_unitario, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($montoR, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center" style="color: #94a3b8;">No se registraron repuestos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totales -->
    @php
        $totalGeneral = $subtotalServicios + $subtotalRepuestos;
    @endphp

    <table class="totals-table">
        <tr>
            <td class="total-label">Total Servicios:</td>
            <td class="total-amount">S/ {{ number_format($subtotalServicios, 2) }}</td>
        </tr>
        <tr>
            <td class="total-label">Total Repuestos:</td>
            <td class="total-amount">S/ {{ number_format($subtotalRepuestos, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td class="total-label" style="color: #0f172a;">TOTAL ORDEN:</td>
            <td class="total-amount" style="color: #0f172a;">S/ {{ number_format($totalGeneral, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    @if($orden->recomendaciones)
    <div style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px; font-size: 9px; margin-top: 5px;">
        <strong>Recomendaciones para el Cliente / Próxima Revisión:</strong> {{ $orden->recomendaciones }}
    </div>
    @endif

    <!-- Firmas -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    MECÁNICO / JEFE DE TALLER<br>
                    <span style="font-size: 8px; font-weight: normal; color: #64748b;">MSA Automotriz</span>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">
                    CONFORMIDAD DEL CLIENTE<br>
                    <span style="font-size: 8px; font-weight: normal; color: #64748b;">Firma / DNI / Fecha de Recepción</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
