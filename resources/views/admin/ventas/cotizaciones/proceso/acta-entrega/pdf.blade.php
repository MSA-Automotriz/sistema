<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Entrega #{{ $acta->codigo }}</title>
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
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .signature-box {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .checklist-section {
            margin: 20px 0;
        }
        .checklist-group {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .checklist-header {
            background-color: #f5f5f5;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .checklist-items {
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 8px;
        }
        .checklist-item {
            display: flex;
            align-items: center;
        }
        .checklist-item i {
            margin-right: 8px;
            font-size: 14px;
        }
        .fa-check-square {
            color: #28a745;
        }
        .fa-square {
            color: #dc3545;
        }
        .observaciones {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        /* Simulando Font Awesome para los checkboxes */
        .check-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 1px solid #ddd;
            margin-right: 5px;
            position: relative;
            background: #fff;
        }
        .check-icon.checked:after {
            content: "✓";
            position: absolute;
            top: -2px;
            left: 2px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ACTA DE ENTREGA DE VEHÍCULO</h1>
            <p>Código: {{ $acta->codigo }}</p>
            <p>Fecha de entrega: {{ $acta->fecha_entrega->format('d/m/Y') }}</p>
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
                    <p><span class="label">Persona que entrega:</span> {{ $acta->persona_entrega }}</p>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="row">
                <div class="col">
                    <p><span class="label">Vehículo:</span> {{ $acta->vehiculo_detalle }}</p>
                    <p><span class="label">Placa:</span> {{ $acta->placa ?? 'Sin placa asignada' }}</p>
                </div>
                <div class="col">
                    <p><span class="label">Kilometraje:</span> {{ number_format($acta->kilometraje) }} km</p>
                    <p><span class="label">Nivel de combustible:</span> 
                        @switch($acta->nivel_combustible)
                            @case(0)
                                Vacío
                                @break
                            @case(25)
                                1/4
                                @break
                            @case(50)
                                1/2
                                @break
                            @case(75)
                                3/4
                                @break
                            @case(100)
                                Lleno
                                @break
                            @default
                                No especificado
                        @endswitch
                    </p>
                </div>
            </div>
        </div>
        
        <div class="checklist-section">
            <h3>CHECKLIST DE ENTREGA</h3>
            
            <div class="checklist-group">
                <div class="checklist-header">Documentación</div>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_manual ? 'checked' : '' }}"></span>
                        Manual del propietario
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_garantia ? 'checked' : '' }}"></span>
                        Libreta de garantía
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_tarjeta ? 'checked' : '' }}"></span>
                        Tarjeta de propiedad
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_soat ? 'checked' : '' }}"></span>
                        SOAT vigente
                    </div>
                </div>
            </div>
            
            <div class="checklist-group">
                <div class="checklist-header">Accesorios</div>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_llave ? 'checked' : '' }}"></span>
                        Juego de llaves
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_gata ? 'checked' : '' }}"></span>
                        Gata hidráulica
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_rueda ? 'checked' : '' }}"></span>
                        Rueda de repuesto
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_herramientas ? 'checked' : '' }}"></span>
                        Kit de herramientas
                    </div>
                </div>
            </div>
            
            <div class="checklist-group">
                <div class="checklist-header">Estado Exterior</div>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_carroceria ? 'checked' : '' }}"></span>
                        Carrocería sin golpes
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_pintura ? 'checked' : '' }}"></span>
                        Pintura en buen estado
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_lunas ? 'checked' : '' }}"></span>
                        Lunas y parabrisas sin daños
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_llantas ? 'checked' : '' }}"></span>
                        Llantas en buen estado
                    </div>
                </div>
            </div>
            
            <div class="checklist-group">
                <div class="checklist-header">Estado Interior</div>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_asientos ? 'checked' : '' }}"></span>
                        Asientos en buen estado
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_tablero ? 'checked' : '' }}"></span>
                        Tablero sin daños
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_radio ? 'checked' : '' }}"></span>
                        Radio/sistema multimedia
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_climatizacion ? 'checked' : '' }}"></span>
                        Aire acondicionado/calefacción
                    </div>
                </div>
            </div>
            
            <div class="checklist-group">
                <div class="checklist-header">Funcionamiento</div>
                <div class="checklist-items">
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_motor ? 'checked' : '' }}"></span>
                        Motor en buen estado
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_luces ? 'checked' : '' }}"></span>
                        Sistema de luces completo
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_frenos ? 'checked' : '' }}"></span>
                        Sistema de frenos
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_direccion ? 'checked' : '' }}"></span>
                        Dirección
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_bateria ? 'checked' : '' }}"></span>
                        Batería
                    </div>
                    <div class="checklist-item">
                        <span class="check-icon {{ $acta->check_arranque ? 'checked' : '' }}"></span>
                        Arranque
                    </div>
                </div>
            </div>
        </div>
        
        @if($acta->observaciones)
        <div class="observaciones">
            <h4>Observaciones:</h4>
            <p>{{ $acta->observaciones }}</p>
        </div>
        @endif
        
        <div class="footer">
            <p>Por la presente, dejo constancia de haber recibido el vehículo detallado anteriormente, verificando que se encuentra en buen estado y con todos los accesorios y documentación mencionados en este documento.</p>
            
            <div class="signature-box">
                <div class="signature">
                    <p>{{ $acta->persona_entrega }}</p>
                    <p>PERSONA QUE ENTREGA</p>
                </div>
                <div class="signature">
                    <p>{{ $cliente->tipo_cliente === 'natural' ? $cliente->nombres . ' ' . $cliente->apellido_paterno : $cliente->razon_social }}</p>
                    <p>CLIENTE</p>
                </div>
            </div>
            
            <p class="text-center" style="font-size: 10px; margin-top: 30px;">
                Documento generado el {{ date('d/m/Y H:i:s') }} - Acta asociada a la cotización #{{ $cotizacion->codigo }}
            </p>
        </div>
    </div>
</body>
</html>