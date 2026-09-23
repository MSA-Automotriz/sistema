<!-- resources/views/admin/mantenimiento/ordenes/print.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Trabajo #{{ $orden->codigo_orden }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .company-header h1 {
            margin: 0;
            color: #333;
            font-size: 24pt;
        }
        
        .company-header p {
            margin: 5px 0;
            color: #666;
        }
        
        .document-title {
            text-align: center;
            margin: 20px 0;
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }
        
        .order-info {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            flex: 0 0 25%;
            font-weight: bold;
        }
        
        .info-value {
            flex: 0 0 75%;
        }
        
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .client-vehicle-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .client-info, .vehicle-info {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-container {
            width: 50%;
            margin-left: auto;
            border: 1px solid #ddd;
            padding: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 14pt;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 50px;
        }
        
        .terms-container {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10pt;
        }
        
        @media print {
            body {
                padding: 0;
                background-color: white;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.close()">Cerrar</button>
    </div>
    
    <div class="company-header">
        <h1>TALLER AUTOMOTRIZ</h1>
        <p>Av. Principal 123, Lima - Perú</p>
        <p>Teléfono: (01) 123-4567 | Email: contacto@tallerautomotriz.com</p>
        <p>RUC: 20123456789</p>
    </div>
    
    <div class="document-title">
        ORDEN DE TRABAJO N° {{ $orden->codigo_orden }}
        @if($orden->factura)
            <div>FACTURA N° {{ $orden->factura->numero_factura }}</div>
        @endif
    </div>
    
    <div class="order-info">
        <div class="grid-container">
            <div>
                <div class="info-row">
                    <div class="info-label">Fecha de Ingreso:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y') }}</div>
                </div>
                @if($orden->fecha_fin_trabajo)
                    <div class="info-row">
                        <div class="info-label">Fecha de Fin:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($orden->fecha_fin_trabajo)->format('d/m/Y') }}</div>
                    </div>
                @endif
            </div>
            <div>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">{{ ucfirst($orden->estado) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Técnico:</div>
                    <div class="info-value">{{ $orden->tecnico->name ?? 'Sin asignar' }}</div>
                </div>
            </div>
            <div>
                @if($orden->factura)
                    <div class="info-row">
                        <div class="info-label">Pago:</div>
                        <div class="info-value">{{ ucfirst($orden->factura->estado_pago) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Método:</div>
                        <div class="info-value">{{ ucfirst($orden->factura->metodo_pago) }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="client-vehicle-container">
        <div class="client-info">
            <div class="section-title">CLIENTE</div>
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">
                    @if($orden->cliente->tipo_cliente == 'persona')
                        {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }} {{ $orden->cliente->apellido_materno }}
                    @else
                        {{ $orden->cliente->razon_social }}
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Documento:</div>
                <div class="info-value">
                    {{ $orden->cliente->tipo_cliente == 'persona' ? 'DNI: ' : 'RUC: ' }}
                    {{ $orden->cliente->documento_identidad }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">
                    {{ $orden->cliente->departamento }}, {{ $orden->cliente->provincia }}, {{ $orden->cliente->distrito }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Contacto:</div>
                <div class="info-value">
                    {{ $orden->cliente->correo ?? 'No registrado' }}
                    @if($orden->cliente->telefonos->count() > 0)
                        <br>{{ $orden->cliente->telefonos->first()->numero }}
                    @endif
                </div>
            </div>
        </div>
        
        <div class="vehicle-info">
            <div class="section-title">VEHÍCULO</div>
            <div class="info-row">
                <div class="info-label">Marca/Modelo:</div>
                <div class="info-value">
                    {{ $orden->vehiculo->marca->nombre ?? 'N/A' }} 
                    {{ $orden->vehiculo->modelo->nombre ?? 'N/A' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Año:</div>
                <div class="info-value">{{ $orden->vehiculo->anio ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Placa:</div>
                <div class="info-value">{{ $orden->vehiculo->nro_placa }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Color:</div>
                <div class="info-value">{{ $orden->vehiculo->color }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kilometraje:</div>
                <div class="info-value">
                    {{ number_format($orden->kilometraje_ingreso, 0, '.', ',') }} km (Ingreso)
                    @if($orden->kilometraje_salida)
                        <br>{{ number_format($orden->kilometraje_salida, 0, '.', ',') }} km (Salida)
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="section-title">DIAGNÓSTICO Y TRABAJOS REALIZADOS</div>
    <p>{{ $orden->descripcion_problema ?? 'Problema reportado por el cliente: No especificado' }}</p>
    
    @if($orden->diagnostico)
        <p><strong>Diagnóstico:</strong> {{ $orden->diagnostico }}</p>
    @endif
    
    @if($orden->recomendaciones)
        <p><strong>Recomendaciones:</strong> {{ $orden->recomendaciones }}</p>
    @endif
    
    <!-- Repuestos -->
    <div class="section-title">REPUESTOS UTILIZADOS</div>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orden->detallesRepuestos as $detalle)
                <tr>
                    <td>{{ $detalle->parte->codigo ?? 'N/A' }}</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay repuestos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Servicios -->
    <div class="section-title">SERVICIOS REALIZADOS</div>
    <table>
        <thead>
            <tr>
                <th>Servicio</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orden->detallesServicios as $detalle)
                <tr>
                    <td>{{ $detalle->descripcion }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay servicios registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Totales -->
    <div class="totals-container">
        <div class="total-row">
            <span>Subtotal Repuestos:</span>
            <span>S/ {{ number_format($orden->getTotalRepuestosAttribute(), 2) }}</span>
        </div>
        <div class="total-row">
            <span>Subtotal Servicios:</span>
            <span>S/ {{ number_format($orden->getTotalServiciosAttribute(), 2) }}</span>
        </div>
        
        @if($orden->factura)
            <div class="total-row">
                <span>Subtotal:</span>
                <span>S/ {{ number_format($orden->factura->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>IGV (18%):</span>
                <span>S/ {{ number_format($orden->factura->impuestos, 2) }}</span>
            </div>
        @endif
        
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($orden->factura ? $orden->factura->total : $orden->getTotalOrdenAttribute(), 2) }}</span>
        </div>
    </div>
    
    <!-- Garantía -->
    @if($orden->factura && $orden->factura->dias_garantia > 0)
        <div style="margin-top: 20px;">
            <strong>Garantía:</strong> Los trabajos realizados tienen una garantía de {{ $orden->factura->dias_garantia }} días a partir de la fecha de entrega.
        </div>
    @endif
    
    <!-- Próxima revisión -->
    @if($orden->fecha_proxima_revision)
        <div style="margin-top: 10px;">
            <strong>Próxima revisión recomendada:</strong> {{ \Carbon\Carbon::parse($orden->fecha_proxima_revision)->format('d/m/Y') }}
        </div>
    @endif
    
    <!-- Firmas -->
    <div class="signature-container">
        <div class="signature-box">
            <div class="signature-line">Firma del Técnico</div>
            <div>{{ $orden->tecnico->name ?? 'Técnico Responsable' }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Firma del Cliente</div>
            <div>
                @if($orden->cliente->tipo_cliente == 'persona')
                    {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }}
                @else
                    {{ $orden->cliente->razon_social }}
                @endif
            </div>
        </div>
    </div>
    
    <!-- Términos y condiciones -->
    <div class="terms-container">
        <p><strong>TÉRMINOS Y CONDICIONES:</strong></p>
        <ol>
            <li>El cliente declara haber recibido el vehículo a su entera satisfacción y en buen estado de funcionamiento.</li>
            <li>La garantía cubre únicamente defectos en la mano de obra y los repuestos instalados, no cubre daños causados por uso inadecuado o intervención de terceros.</li>
            <li>Los repuestos reemplazados serán entregados al cliente, salvo indicación expresa en contrario.</li>
            <li>La empresa no se responsabiliza por objetos personales dejados en el vehículo.</li>
        </ol>
    </div>
</body>
</html>