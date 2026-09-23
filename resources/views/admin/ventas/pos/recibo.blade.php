<!-- resources/views/admin/ventas/pos/recibo.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Venta #{{ $venta->codigo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .header img {
            max-width: 60mm;
            height: auto;
        }
        .info {
            margin-bottom: 5mm;
        }
        .info p {
            margin: 2pt 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        .table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 2pt;
            font-size: 9pt;
        }
        .table td {
            padding: 2pt;
            font-size: 9pt;
            border-bottom: 1px dotted #ccc;
        }
        .totals {
            width: 100%;
            text-align: right;
        }
        .totals td {
            padding: 2pt;
        }
        .totals .value {
            font-weight: bold;
            width: 25mm;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 8pt;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5mm 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <h3 style="margin: 2mm 0;">COMPROBANTE DE VENTA</h3>
            <p style="margin: 0;">{{ config('app.name') }}</p>
            <p style="margin: 0;">{{ $venta->almacen->direccion ?? 'Dirección no especificada' }}</p>
            <p style="margin: 0;">Teléfono: {{ $venta->almacen->telefono ?? 'No especificado' }}</p>
        </div>
        
        <div class="info">
            <p><strong>Venta #:</strong> {{ $venta->codigo }}</p>
            <p><strong>Fecha:</strong> {{ $venta->fecha->format('d/m/Y H:i') }}</p>
            <p><strong>Cliente:</strong> {{ $venta->cliente->tipo_cliente == 'natural' ? 
                        $venta->cliente->nombres . ' ' . $venta->cliente->apellido_paterno . ' ' . $venta->cliente->apellido_materno :
                        $venta->cliente->razon_social }}</p>
            <p><strong>{{ $venta->cliente->tipo_cliente == 'natural' ? 'DNI' : 'RUC' }}:</strong> {{ $venta->cliente->documento_identidad }}</p>
            <p><strong>Vendedor:</strong> {{ $venta->usuario->name }}</p>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th style="text-align: right;">Cant.</th>
                    <th style="text-align: right;">P.Unit</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->nombre_item }}</td>
                    <td style="text-align: right;">{{ number_format($detalle->cantidad, 2) }}</td>
                    <td style="text-align: right;">{{ $venta->moneda == 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td style="text-align: right;">{{ $venta->moneda == 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($detalle->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <table class="totals">
            <tr>
                <td>Subtotal:</td>
                <td class="value">{{ $venta->moneda == 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($venta->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>IGV (18%):</td>
                <td class="value">{{ $venta->moneda == 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($venta->igv, 2) }}</td>
            </tr>
            <tr>
                <td><strong>TOTAL:</strong></td>
                <td class="value">{{ $venta->moneda == 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <div class="footer">
            <p><strong>FORMA DE PAGO:</strong> {{ $venta->tipo_pago }}</p>
            <p>¡Gracias por su compra!</p>
            @if($venta->observaciones)
            <p>Observaciones: {{ $venta->observaciones }}</p>
            @endif
            <p>{{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>