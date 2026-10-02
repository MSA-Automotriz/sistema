<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket POS - {{ $venta->codigo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace, 'Segoe UI', Tahoma, sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #000;
            background-color: #fff;
            width: 72mm;
            margin: 0 auto;
            padding: 6px 2px;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        .no-print {
            text-align: center;
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f1f3f5;
            border-radius: 4px;
        }

        .btn-action {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin: 0 4px;
        }

        .btn-print {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-close {
            background-color: #6c757d;
            color: #fff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .divider-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .header-section {
            text-align: center;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .company-sub {
            font-size: 10px;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 12px;
            font-weight: bold;
            margin: 5px 0 2px;
        }

        .info-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 10.5px;
        }

        .info-table td.label {
            width: 32%;
            font-weight: bold;
        }

        .items-table th {
            font-size: 10px;
            font-weight: bold;
            padding: 3px 0;
            border-bottom: 1px dashed #000;
        }

        .items-table td {
            font-size: 10.5px;
            padding: 2px 0;
            vertical-align: top;
        }

        .item-row-detail {
            font-size: 9.5px;
            color: #333;
        }

        .totals-table td {
            padding: 2px 0;
            font-size: 11px;
        }

        .totals-table td.total-label {
            font-weight: bold;
            text-align: right;
            padding-right: 8px;
        }

        .total-highlight {
            font-size: 13px;
            font-weight: bold;
        }

        .footer-section {
            text-align: center;
            font-size: 9.5px;
            margin-top: 8px;
            line-height: 1.3;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                width: 100%;
                padding: 0 2mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Botones de Acción (No se imprimen) -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-action btn-print">🖨️ Imprimir Ticket</button>
        <button onclick="window.close()" class="btn-action btn-close">✖️ Cerrar</button>
    </div>

    <!-- Encabezado de la Empresa -->
    <div class="header-section">
        <div class="company-name">{{ config('app.name', 'MSA AUTOMOTRIZ') }}</div>
        <div class="company-sub">SERVICIOS AUTOMOTRICES & REPUESTOS</div>
        <div class="company-sub">RUC: 20601234567</div>
        <div class="company-sub">Dir: Av. Principal 123 - Taller Central</div>
        <div class="company-sub">Tel: (01) 555-0199 / 999-888-777</div>

        <div class="divider"></div>
        <div class="doc-title">TICKET DE VENTA</div>
        <div class="fw-bold">{{ $venta->codigo }}</div>
    </div>

    <!-- Información General -->
    <table class="info-table">
        <tr>
            <td class="label">Fecha/Hora:</td>
            <td>{{ $venta->fecha ? $venta->fecha->format('d/m/Y H:i:s') : date('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td class="label">Cajero:</td>
            <td>{{ $venta->usuario->name ?? 'Cajero' }}</td>
        </tr>
        <tr>
            <td class="label">Almacén:</td>
            <td>{{ $venta->almacen->nombre ?? 'Principal' }}</td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td class="fw-bold">
                @if($venta->cliente)
                    @if($venta->cliente->tipo_cliente == 'natural')
                        {{ trim(($venta->cliente->nombres ?? '') . ' ' . ($venta->cliente->apellido_paterno ?? '') . ' ' . ($venta->cliente->apellido_materno ?? '')) ?: 'Cliente Varios' }}
                    @else
                        {{ $venta->cliente->razon_social ?? 'Cliente Corporativo' }}
                    @endif
                @else
                    CLIENTE GENERAL
                @endif
            </td>
        </tr>
        @if($venta->cliente && $venta->cliente->documento_identidad)
        <tr>
            <td class="label">Doc. Ident.:</td>
            <td>{{ $venta->cliente->tipo_documento ?? 'DOC' }}: {{ $venta->cliente->documento_identidad }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Pago:</td>
            <td class="text-uppercase">{{ $venta->tipo_pago ?? 'Efectivo' }} ({{ $venta->moneda ?? 'Soles' }})</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Detalle de Productos y Servicios -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">DESCRIPCIÓN</th>
                <th class="text-center" style="width: 15%;">CANT</th>
                <th class="text-right" style="width: 17%;">P.U.</th>
                <th class="text-right" style="width: 18%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $simbolo = ($venta->moneda == 'Dólares' || $venta->moneda == 'USD') ? '$' : 'S/.'; @endphp
            @forelse($venta->detallesPOS as $det)
            <tr>
                <td colspan="4" style="padding-top: 3px;">
                    <span class="fw-bold">{{ $det->parte ? $det->parte->nombre : ($det->descripcion ?? 'Producto') }}</span>
                    @if($det->parte && $det->parte->codigo)
                        <br><span class="item-row-detail">Cod: {{ $det->parte->codigo }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">{{ number_format($det->cantidad, 2) }}</td>
                <td class="text-right">{{ number_format($det->precio_unitario, 2) }}</td>
                <td class="text-right fw-bold">{{ number_format($det->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Sin items registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totales -->
    <table class="totals-table">
        <tr>
            <td class="total-label">SUBTOTAL:</td>
            <td class="text-right" style="width: 35%;">{{ $simbolo }} {{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        @if(isset($venta->descuento) && $venta->descuento > 0)
        <tr>
            <td class="total-label">DESCUENTO:</td>
            <td class="text-right">{{ $simbolo }} -{{ number_format($venta->descuento, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td class="total-label">I.G.V. (18%):</td>
            <td class="text-right">{{ $simbolo }} {{ number_format($venta->igv, 2) }}</td>
        </tr>
        <tr class="divider-double">
            <td class="total-label total-highlight">TOTAL:</td>
            <td class="text-right total-highlight">{{ $simbolo }} {{ number_format($venta->total, 2) }}</td>
        </tr>
        @if($venta->monto_abonado > 0)
        <tr>
            <td class="total-label">ABONADO:</td>
            <td class="text-right">{{ $simbolo }} {{ number_format($venta->monto_abonado, 2) }}</td>
        </tr>
        @endif
        @if($venta->saldo_pendiente > 0)
        <tr>
            <td class="total-label" style="color: #c00;">SALDO PENDIENTE:</td>
            <td class="text-right fw-bold" style="color: #c00;">{{ $simbolo }} {{ number_format($venta->saldo_pendiente, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- Pie de Ticket -->
    <div class="footer-section">
        <div>¡GRACIAS POR SU PREFERENCIA!</div>
        <div style="margin-top: 3px;">Conserve este comprobante para cualquier reclamo o garantía.</div>
        <div style="margin-top: 3px;">Todo cambio o devolución requiere la presentación de este ticket dentro de las 48 horas.</div>
        <div class="divider" style="margin-top: 6px;"></div>
        <div style="font-size: 8.5px; color: #555;">MSA Automotriz - Software de Gestión de Taller</div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            // Auto trigger print dialog
            setTimeout(function() {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
