<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización #{{ $cotizacion->codigo }}</title>
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
            margin-bottom: 15px;
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
            padding: 10px;
            text-align: center;
            background-color: #f8fafc;
        }
        .doc-box h2 {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-box .code {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            margin: 4px 0 0 0;
        }
        .section-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .section-header {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #334155;
            padding: 5px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        .section-content {
            padding: 8px;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 25%;
        }
        .value {
            color: #0f172a;
            width: 75%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0f172a;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 11px;
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
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }
        .notes-box {
            border: 1px dashed #94a3b8;
            background-color: #fafafa;
            border-radius: 4px;
            padding: 8px 10px;
            margin-top: 15px;
            font-size: 9px;
            color: #475569;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 0 40px;
        }
        .signature-line {
            border-top: 1px solid #475569;
            padding-top: 5px;
            font-size: 10px;
            color: #334155;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-sol { background-color: #dbeafe; color: #1e40af; }
        .badge-usd { background-color: #dcfce7; color: #166534; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <h1 class="company-name">MSA AUTOMOTRIZ</h1>
                <p class="company-subtitle">Servicio Técnico Especializado y Venta de Repuestos</p>
                <p class="company-subtitle">RUC: 20600000000 | Tel: (01) 234-5678 | Email: ventas@msa-automotriz.com</p>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <div class="doc-box">
                    <h2>COTIZACIÓN</h2>
                    <div class="code">{{ $cotizacion->codigo }}</div>
                    <div style="font-size: 9px; color: #64748b; margin-top: 3px;">
                        Fecha: {{ $cotizacion->created_at ? $cotizacion->created_at->format('d/m/Y') : date('d/m/Y') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Cliente y Datos Generales -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                <table class="section-box">
                    <tr><td class="section-header">Datos del Cliente</td></tr>
                    <tr>
                        <td class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td class="label">Cliente:</td>
                                    <td class="value">
                                        @if($cotizacion->cliente)
                                            @if($cotizacion->cliente->tipo_cliente === 'natural')
                                                {{ trim(($cotizacion->cliente->nombres ?? '') . ' ' . ($cotizacion->cliente->apellido_paterno ?? '') . ' ' . ($cotizacion->cliente->apellido_materno ?? '')) }}
                                            @else
                                                {{ $cotizacion->cliente->razon_social ?? 'Sin razón social' }}
                                            @endif
                                        @else
                                            Cliente General
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">{{ ($cotizacion->cliente && $cotizacion->cliente->tipo_cliente === 'juridica') ? 'RUC' : 'DNI/Doc' }}:</td>
                                    <td class="value">{{ $cotizacion->cliente->documento_identidad ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Teléfono:</td>
                                    <td class="value">{{ $cotizacion->cliente->telefonos->first()->numero ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Dirección:</td>
                                    <td class="value">{{ $cotizacion->cliente->direccion ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                <table class="section-box">
                    <tr><td class="section-header">Condiciones Comerciales</td></tr>
                    <tr>
                        <td class="section-content">
                            <table class="info-table">
                                <tr>
                                    <td class="label">Moneda:</td>
                                    <td class="value">
                                        <span class="badge {{ $cotizacion->moneda === 'Dolares' || $cotizacion->moneda === 'USD' ? 'badge-usd' : 'badge-sol' }}">
                                            {{ $cotizacion->moneda ?? 'Soles' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Forma de Pago:</td>
                                    <td class="value">{{ $cotizacion->forma_pago ?? 'Contado' }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Validez hasta:</td>
                                    <td class="value">
                                        {{ $cotizacion->fecha_validez ? \Carbon\Carbon::parse($cotizacion->fecha_validez)->format('d/m/Y') : '15 días' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Asesor/Vendedor:</td>
                                    <td class="value">{{ $cotizacion->usuario->name ?? 'Asesor Comercial' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Tabla de Ítems -->
    @php
        $simbolo = ($cotizacion->moneda === 'Dolares' || $cotizacion->moneda === 'USD') ? 'US$ ' : 'S/ ';
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 15%;" class="text-left">Código</th>
                <th style="width: 45%;" class="text-left">Descripción / Concepto</th>
                <th style="width: 10%;" class="text-center">Cant.</th>
                <th style="width: 12%;" class="text-right">P. Unit.</th>
                <th style="width: 13%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->detalles as $idx => $det)
                @php
                    $desc = 'Ítem';
                    $codigo = '-';
                    if ($det->repuesto) {
                        $desc = $det->repuesto->nombre;
                        $codigo = $det->repuesto->codigo ?? '-';
                    } elseif ($det->servicio) {
                        $desc = $det->servicio->nombre;
                        $codigo = 'SERV-' . $det->servicio->id;
                    } elseif ($det->vehiculo) {
                        $desc = ($det->vehiculo->marca->nombre ?? '') . ' ' . ($det->vehiculo->modelo->nombre ?? '') . ' ' . ($det->vehiculo->version->nombre ?? '');
                        $codigo = $det->vehiculo->codigo ?? '-';
                    }
                    $precioUnit = (float)($det->precio_unitario ?? 0);
                    $cant = (float)($det->cantidad ?? 1);
                    $subtotalItem = (float)($det->total ?? ($precioUnit * $cant));
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-left font-mono">{{ $codigo }}</td>
                    <td class="text-left">{{ $desc }}</td>
                    <td class="text-center">{{ number_format($cant, 0) }}</td>
                    <td class="text-right">{{ $simbolo }}{{ number_format($precioUnit, 2) }}</td>
                    <td class="text-right font-bold">{{ $simbolo }}{{ number_format($subtotalItem, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px; color: #94a3b8;">
                        No se registraron ítems en esta cotización.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totales -->
    @php
        $calcSubtotal = (float)($cotizacion->subtotal ?? 0);
        $calcTotal = (float)($cotizacion->total ?? 0);
        if ($calcTotal == 0 && $cotizacion->detalles->count() > 0) {
            $calcTotal = $cotizacion->detalles->sum(function($d) {
                return (float)($d->total ?? ($d->precio_unitario * $d->cantidad));
            });
            $calcSubtotal = $calcTotal / 1.18;
        }
        $calcIgv = (float)($cotizacion->impuestos ?? ($calcTotal - $calcSubtotal));
    @endphp

    <table class="totals-table">
        <tr>
            <td class="total-label">Subtotal:</td>
            <td class="total-amount">{{ $simbolo }}{{ number_format($calcSubtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="total-label">I.G.V. (18%):</td>
            <td class="total-amount">{{ $simbolo }}{{ number_format($calcIgv, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td class="total-label" style="font-size: 12px; color: #0f172a;">TOTAL:</td>
            <td class="total-amount" style="font-size: 12px; color: #0f172a;">{{ $simbolo }}{{ number_format($calcTotal, 2) }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- Notas y Términos -->
    <div class="notes-box">
        <strong>Términos y Condiciones:</strong>
        <ul style="margin: 4px 0 0 15px; padding: 0;">
            <li>Los precios incluyen I.G.V. si se emite factura/boleta.</li>
            <li>Cotización sujeta a disponibilidad de stock al momento de la confirmación de la orden.</li>
            <li>Los tiempos de entrega o servicio se coordinan con el asesor tras la aprobación formal.</li>
            <li>Cuentas bancarias: BCP Soles: 191-0000000-0-00 / BBVA Soles: 0011-0000-0000000000</li>
        </ul>
    </div>

    <!-- Firmas -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    MSA AUTOMOTRIZ<br>
                    <span style="font-size: 9px; font-weight: normal; color: #64748b;">Asesor Responsable</span>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">
                    ACEPTADO POR EL CLIENTE<br>
                    <span style="font-size: 9px; font-weight: normal; color: #64748b;">Firma / Razón Social / DNI</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
