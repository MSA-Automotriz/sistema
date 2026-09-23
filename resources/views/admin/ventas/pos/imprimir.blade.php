{{-- resources/views/admin/ventas/pos/imprimir.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta {{ $venta->codigo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            float: right;
            width: 300px;
            margin-top: 10px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
        }
        
        .footer {
            clear: both;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-completada { background-color: #d4edda; color: #155724; }
        .status-parcial { background-color: #fff3cd; color: #856404; }
        .status-pendiente { background-color: #d1ecf1; color: #0c5460; }
        
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Botón de impresión (no se imprime) -->
    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            ❌ Cerrar
        </button>
    </div>

    <!-- Encabezado -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Mi Empresa') }}</div>
        <div>RUC: 20123456789</div>
        <div>Dirección de la empresa</div>
        <div>Teléfono: (01) 123-4567</div>
        <div class="document-title">VENTA POS</div>
        <div>{{ $venta->codigo }}</div>
    </div>

    <!-- Información de la venta -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell info-label">Fecha:</div>
            <div class="info-cell">{{ $venta->fecha->format('d/m/Y H:i') }}</div>
            <div class="info-cell info-label" style="padding-left: 30px;">Estado:</div>
            <div class="info-cell">
                <span class="status-badge status-{{ strtolower($venta->estado) }}">{{ $venta->estado }}</span>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-cell info-label">Cliente:</div>
            <div class="info-cell">
                @if($venta->cliente)
                    @if($venta->cliente->tipo_cliente == 'natural')
                        {{ trim(($venta->cliente->nombres ?? '') . ' ' . ($venta->cliente->apellido_paterno ?? '') . ' ' . ($venta->cliente->apellido_materno ?? '')) ?: 'Sin nombre' }}
                    @else
                        {{ $venta->cliente->razon_social ?? 'Cliente corporativo' }}
                    @endif
                @else
                    Cliente no encontrado
                @endif
            </div>
            <div class="info-cell info-label" style="padding-left: 30px;">Documento:</div>
            <div class="info-cell">{{ $venta->cliente->documento_identidad ?? 'Sin documento' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-cell info-label">Usuario:</div>
            <div class="info-cell">{{ $venta->usuario->name ?? 'Usuario no encontrado' }}</div>
            <div class="info-cell info-label" style="padding-left: 30px;">Almacén:</div>
            <div class="info-cell">{{ $venta->almacen->nombre ?? 'Almacén no encontrado' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-cell info-label">Moneda:</div>
            <div class="info-cell">{{ $venta->moneda }}</div>
            <div class="info-cell info-label" style="padding-left: 30px;">Tipo de Pago:</div>
            <div class="info-cell">{{ $venta->tipo_pago }}</div>
        </div>
        
        @if($venta->cotizacion)
        <div class="info-row">
            <div class="info-cell info-label">Cotización:</div>
            <div class="info-cell">{{ $venta->cotizacion->codigo }}</div>
            <div class="info-cell" colspan="2"></div>
        </div>
        @endif
    </div>

    <!-- Tabla de items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 40%;">Descripción</th>
                <th style="width: 8%;">Cant.</th>
                <th style="width: 12%;">P. Unit.</th>
                <th style="width: 8%;">Desc.</th>
                <th style="width: 17%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($venta->detallesPOS as $detalle)
            <tr>
                <td>
                    {{ $detalle->parte ? $detalle->parte->codigo : 'N/A' }}
                    <br><small style="color: #666;">{{ ucfirst($detalle->tipo_item) }}</small>
                </td>
                <td>
                    <strong>{{ $detalle->parte ? $detalle->parte->nombre : $detalle->descripcion }}</strong>
                    @if($detalle->descripcion && $detalle->parte && $detalle->descripcion != $detalle->parte->nombre)
                        <br><small style="color: #666;">{{ $detalle->descripcion }}</small>
                    @endif
                </td>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td class="text-right">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-center">
                    @if($detalle->descuento > 0)
                        {{ number_format($detalle->descuento, 1) }}%
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($detalle->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #666;">No hay items registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>IGV (18%):</strong></td>
                <td class="text-right">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->igv, 2) }}</td>
            </tr>
            <tr class="total-final">
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->total, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Abonado:</strong></td>
                <td class="text-right" style="color: #28a745;">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->monto_abonado, 2) }}</td>
            </tr>
            @if($venta->saldo_pendiente > 0)
            <tr>
                <td><strong>Saldo Pendiente:</strong></td>
                <td class="text-right" style="color: #dc3545; font-weight: bold;">{{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->saldo_pendiente, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Observaciones -->
    @if($venta->observaciones)
    <div style="clear: both; margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <strong>Observaciones:</strong><br>
        {{ $venta->observaciones }}
    </div>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
        <p>{{ config('app.name') }} - Sistema de Gestión</p>
        
        @if($venta->saldo_pendiente > 0)
        <div style="margin-top: 15px; padding: 10px; border: 2px solid #dc3545; background-color: #f8d7da; color: #721c24;">
            <strong>ATENCIÓN: Esta venta tiene un saldo pendiente de {{ $venta->moneda == 'Dólares' ? '$' : 'S/.' }} {{ number_format($venta->saldo_pendiente, 2) }}</strong>
        </div>
        @endif
    </div>

    <script>
        // Auto-imprimir al cargar (opcional)
        // window.onload = function() { window.print(); }
        
        // Cerrar ventana después de imprimir
        window.onafterprint = function() {
            // window.close();
        }
    </script>
</body>
</html>