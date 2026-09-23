<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Pedido #{{ $nota_pedido->codigo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #0d6efd;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .row {
            display: flex;
            margin-bottom: 10px;
        }
        .col {
            flex: 1;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 10px;
        }
        .subtotal-section {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .subtotal-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }
        .observaciones {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>NOTA DE PEDIDO</h1>
            <p>Código: {{ $nota_pedido->codigo }}</p>
            <p>Fecha de emisión: {{ $nota_pedido->fecha_emision->format('d/m/Y') }}</p>
        </div>
        
        <div class="info-section">
            <div class="row">
                <div class="col">
                    <p><span class="label">Cliente:</span> 
                        @if($cliente->tipo_cliente === 'natural')
                            {{ $cliente->nombres }} {{ $cliente->apellido_paterno }} {{ $cliente->apellido_materno }}
                        @else
                            {{ $cliente->razon_social }}
                        @endif
                    </p>
                    <p><span class="label">{{ $cliente->tipo_cliente === 'natural' ? 'DNI' : 'RUC' }}:</span> {{ $cliente->documento_identidad }}</p>
                    <p><span class="label">Dirección:</span> {{ $cliente->direccion ?? 'No especificada' }}</p>
                </div>
                <div class="col">
                    <p><span class="label">Teléfono:</span> {{ $cliente->telefonos->first()->numero ?? 'No especificado' }}</p>
                    <p><span class="label">Email:</span> {{ $cliente->correo ?? 'No especificado' }}</p>
                    <p><span class="label">Estado:</span> {{ $nota_pedido->estado }}</p>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Descripción</th>
                    <th width="10%">Tipo</th>
                    <th width="10%" class="text-center">Cantidad</th>
                    <th width="15%" class="text-right">Precio Unit.</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if($items->count() > 0)
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $item->descripcion }}
                            @if($item->detalles)
                            <br><small>{{ $item->detalles }}</small>
                            @endif
                        </td>
                        <td>{{ $item->tipo }}</td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-right">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($item->precio_unitario, 2) }}
                        </td>
                        <td class="text-right">
                            {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                            {{ number_format($item->cantidad * $item->precio_unitario, 2) }}
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">No hay items en la nota de pedido</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="subtotal-section">
            <div class="subtotal-row">
                <span>Subtotal:</span>
                <span>
                    {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                    {{ number_format($items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }), 2) }}
                </span>
            </div>
            <div class="subtotal-row">
                <span>IGV (18%):</span>
                <span>
                    {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                    {{ number_format($items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }) * 0.18, 2) }}
                </span>
            </div>
            <div class="total-row">
                <span>TOTAL:</span>
                <span>
                    {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                    {{ number_format($items->sum(function($item) { return $item->cantidad * $item->precio_unitario; }) * 1.18, 2) }}
                </span>
            </div>
        </div>
        
        @if($nota_pedido->observaciones)
        <div class="observaciones">
            <h4>Observaciones:</h4>
            <p>{{ $nota_pedido->observaciones }}</p>
        </div>
        @endif
        
        <div class="footer">
            <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
            <p>Esta nota de pedido está asociada a la cotización #{{ $cotizacion->codigo }}</p>
        </div>
    </div>
</body>
</html>