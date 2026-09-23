{{-- resources/views/admin/ventas/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detalle de Venta #' . $venta->codigo)

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-shopping-cart text-info me-2"></i> Detalles de Venta
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 d-flex align-items-center">
                    Venta #{{ $venta->codigo }}
                    @switch($venta->estado)
                        @case('Completada')
                            <span class="badge bg-success bg-opacity-25 border border-success border-opacity-50 ms-3 fs-6 rounded-pill px-3 py-2"><span class="d-inline-block rounded-circle bg-success me-1" style="width: 8px; height: 8px;"></span> Completada</span>
                            @break
                        @case('Parcial')
                            <span class="badge bg-warning bg-opacity-25 border border-warning border-opacity-50 text-warning ms-3 fs-6 rounded-pill px-3 py-2"><span class="d-inline-block rounded-circle bg-warning me-1" style="width: 8px; height: 8px;"></span> Parcial</span>
                            @break
                        @case('Cancelada')
                            <span class="badge bg-danger bg-opacity-25 border border-danger border-opacity-50 ms-3 fs-6 rounded-pill px-3 py-2"><span class="d-inline-block rounded-circle bg-danger me-1" style="width: 8px; height: 8px;"></span> Cancelada</span>
                            @break
                        @default
                            <span class="badge bg-secondary bg-opacity-25 border border-secondary border-opacity-50 ms-3 fs-6 rounded-pill px-3 py-2"><span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 8px; height: 8px;"></span> {{ $venta->estado }}</span>
                    @endswitch
                </h2>
                <div class="text-white-50 mt-2 d-flex align-items-center">
                    <i class="far fa-calendar-alt me-1"></i> {{ $venta->fecha->format('d M, Y H:i') }}
                    <span class="mx-2">|</span>
                    <i class="far fa-user me-1"></i> {{ $venta->usuario->name ?? 'Sistema' }}
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($venta->saldo_pendiente > 0 && $venta->estado != 'Cancelada')
                    <button type="button" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105" onclick="abrirModalPago({{ $venta->id }}, {{ $venta->saldo_pendiente }})">
                        <i class="fas fa-credit-card me-2 text-success"></i> Registrar Pago
                    </button>
                @endif
                
                @if($venta->estado != 'Cancelada')
                    <button type="button" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105" onclick="abrirModalAnular({{ $venta->id }})">
                        <i class="fas fa-ban me-2 text-danger"></i> Anular
                    </button>
                @endif
                
                <a href="#" onclick="window.print()" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105">
                    <i class="fas fa-print me-2 text-info"></i> Imprimir
                </a>
                
                <a href="{{ route('admin.ventas.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-arrow-left me-2 text-primary"></i> Volver a Ventas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="row">
        {{-- Información Principal --}}
        <div class="col-xl-8 col-lg-7">
            {{-- Información de la Venta --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Información de la Venta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Código:</strong></td>
                                    <td>{{ $venta->codigo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha:</strong></td>
                                    <td>{{ $venta->fecha->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Usuario:</strong></td>
                                    <td>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $venta->usuario->name ?? 'Usuario no encontrado' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Almacén:</strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $venta->almacen->nombre ?? 'Almacén no encontrado' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($venta->cotizacion)
                                <tr>
                                    <td><strong>Cotización:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.ventas.cotizaciones.show', $venta->cotizacion->id) }}" 
                                           target="_blank">
                                            {{ $venta->cotizacion->codigo }}
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Moneda:</strong></td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $venta->moneda }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Pago:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $venta->tipo_pago == 'Contado' ? 'success' : 'warning' }}">
                                            {{ $venta->tipo_pago }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @switch($venta->estado)
                                            @case('Completada')
                                                <span class="badge badge-success">{{ $venta->estado }}</span>
                                                @break
                                            @case('Parcial')
                                                <span class="badge badge-warning">{{ $venta->estado }}</span>
                                                @break
                                            @case('Cancelada')
                                                <span class="badge badge-danger">{{ $venta->estado }}</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $venta->estado }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>% Pagado:</strong></td>
                                    <td>
                                        @php
                                            $porcentaje = $estadisticasVenta['porcentaje_abonado'];
                                        @endphp
                                        <div class="progress" style="height: 25px; width: 200px;">
                                            <div class="progress-bar 
                                                @if($porcentaje == 100) bg-success 
                                                @elseif($porcentaje >= 50) bg-warning 
                                                @else bg-danger @endif" 
                                                role="progressbar" 
                                                style="width: {{ $porcentaje }}%">
                                                {{ number_format($porcentaje, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($venta->observaciones)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="font-weight-bold">Observaciones:</h6>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($venta->observaciones)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Detalles de la Venta --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-2"></i>Detalle de Items
                        <span class="badge badge-secondary ml-2">{{ $estadisticasVenta['items_unicos'] }} items únicos</span>
                        <span class="badge badge-info ml-1">{{ $estadisticasVenta['total_items'] }} unidades totales</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($detalles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Item</th>
                                        <th width="10%">Cantidad</th>
                                        <th width="15%">Precio Unit.</th>
                                        <th width="10%">Descuento</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="15%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalles as $index => $detalle)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>
                                                            @if($detalle->parte)
                                                                {{ $detalle->parte->nombre }}
                                                            @else
                                                                {{ $detalle->descripcion ?? 'Item sin descripción' }}
                                                            @endif
                                                        </strong>
                                                        @if($detalle->parte && $detalle->parte->codigo)
                                                            <br><small class="text-muted">
                                                                Código: {{ $detalle->parte->codigo }}
                                                            </small>
                                                        @endif
                                                        @if($detalle->tipo_item)
                                                            <br><span class="badge badge-outline-primary badge-sm">
                                                                {{ ucfirst($detalle->tipo_item) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">
                                                    {{ number_format($detalle->cantidad) }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($detalle->precio_unitario, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @if($detalle->descuento > 0)
                                                    <span class="badge badge-warning">
                                                        {{ number_format($detalle->descuento, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($detalle->subtotal, 2) }}
                                            </td>
                                            <td class="text-right">
                                                <strong>
                                                    {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                    {{ number_format($detalle->total, 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                                {{ number_format($venta->total, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No hay items en esta venta</h5>
                            <p class="text-muted">No se encontraron detalles para esta venta.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Derecho --}}
        <div class="col-xl-4 col-lg-5">
            {{-- Información del Cliente --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>Información del Cliente
                    </h6>
                </div>
                <div class="card-body">
                    @if($venta->cliente)
                        <div class="text-center mb-3">
                            <div class="avatar avatar-xl mb-2">
                                <i class="fas fa-user-circle fa-4x text-gray-400"></i>
                            </div>
                            <h5 class="mb-1">{{ $clienteData['nombre'] }}</h5>
                            <p class="text-muted mb-0">{{ $clienteData['documento'] }}</p>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-2">
                                    <strong>Tipo Cliente:</strong><br>
                                    <span class="badge badge-info">
                                        {{ ucfirst($venta->cliente->tipo_cliente ?? 'natural') }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-2">
                                    <strong>Tipo Documento:</strong><br>
                                    {{ $venta->cliente->tipo_documento ?? 'DNI' }}
                                </p>
                            </div>
                        </div>
                        
                        @if($clienteData['telefono'] != 'Sin teléfono')
                        <p class="mb-2">
                            <strong>Teléfono:</strong><br>
                            <a href="tel:{{ $clienteData['telefono'] }}" class="text-decoration-none">
                                <i class="fas fa-phone mr-1"></i>{{ $clienteData['telefono'] }}
                            </a>
                        </p>
                        @endif
                        
                        @if($clienteData['email'] != 'Sin email')
                        <p class="mb-2">
                            <strong>Email:</strong><br>
                            <a href="mailto:{{ $clienteData['email'] }}" class="text-decoration-none">
                                <i class="fas fa-envelope mr-1"></i>{{ $clienteData['email'] }}
                            </a>
                        </p>
                        @endif
                        
                        @if($clienteData['direccion'] != 'Sin dirección')
                        <p class="mb-0">
                            <strong>Dirección:</strong><br>
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $clienteData['direccion'] }}
                        </p>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Cliente no encontrado</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen Financiero --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator mr-2"></i>Resumen Financiero
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success mb-1">
                                    {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                    {{ number_format($venta->monto_abonado, 2) }}
                                </h4>
                                <p class="text-muted mb-0">Abonado</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-{{ $venta->saldo_pendiente > 0 ? 'danger' : 'success' }} mb-1">
                                    {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                    {{ number_format($venta->saldo_pendiente, 2) }}
                                </h4>
                                <p class="text-muted mb-0">Pendiente</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="height: 20px;">
                        @php
                            $porcentaje = $estadisticasVenta['porcentaje_abonado'];
                        @endphp
                        <div class="progress-bar 
                            @if($porcentaje == 100) bg-success 
                            @elseif($porcentaje >= 50) bg-warning 
                            @else bg-danger @endif" 
                            role="progressbar" 
                            style="width: {{ $porcentaje }}%">
                            {{ number_format($porcentaje, 1) }}% Pagado
                        </div>
                    </div>
                    
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-right">
                                {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                {{ number_format($venta->subtotal, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>IGV:</strong></td>
                            <td class="text-right">
                                {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                {{ number_format($venta->igv, 2) }}
                            </td>
                        </tr>
                        @if($estadisticasVenta['descuento_total'] > 0)
                        <tr class="text-success">
                            <td><strong>Descuentos:</strong></td>
                            <td class="text-right">
                                -{{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong>TOTAL:</strong></td>
                            <td class="text-right">
                                <strong>
                                    {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                    {{ number_format($venta->total, 2) }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                    
                    @if($venta->saldo_pendiente > 0 && $venta->estado != 'Cancelada')
                        <button type="button" 
                                class="btn btn-success btn-block mt-3" 
                                onclick="abrirModalPago({{ $venta->id }}, {{ $venta->saldo_pendiente }})">
                            <i class="fas fa-credit-card mr-1"></i>
                            Registrar Pago
                        </button>
                    @endif
                </div>
            </div>

            {{-- Historial de Pagos --}}
            @if($historialPagos->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Historial de Pagos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($historialPagos as $pago)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">
                                        {{ $venta->moneda == 'Soles' ? 'S/' : ' 
                                                {{ number_format($venta->subtotal, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>IGV (18%):</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($venta->igv, 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if($estadisticasVenta['descuento_total'] > 0)
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Descuentos:</strong></td>
                                        <td class="text-right">
                                            <strong class="text-success">
                                                -{{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                                                {{ number_format($estadisticasVenta['descuento_total'], 2) }}
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    <tr class="bg-primary text-white">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong>
                                                {{ $venta->moneda == 'Soles' ? 'S/' : '$' }} 
                 }} 
                                        {{ number_format($pago->monto, 2) }}
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <small>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                    @if($pago->referencia)
                                        <p class="text-muted mb-1">
                                            <small>
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $pago->referencia }}
                                            </small>
                                        </p>
                                    @endif
                                    @if($pago->comentario)
                                        <p class="text-muted mb-1">
                                            <small>{{ $pago->comentario }}</small>
                                        </p>
                                    @endif
                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $pago->usuario_nombre ?? 'Sistema' }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Acciones Rápidas --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools mr-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($venta->cotizacion)
                        <a href="{{ route('admin.ventas.cotizaciones.show', $venta->cotizacion->id) }}" 
                           class="btn btn-outline-info btn-sm" 
                           target="_blank">
                            <i class="fas fa-file-alt mr-1"></i>Ver Cotización Original
                        </a>
                        @endif
                        
                        <button onclick="window.print()" 
                                class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-print mr-1"></i>Imprimir Comprobante
                        </button>
                        
                        @if($venta->estado != 'Cancelada')
                        <button type="button" 
                                class="btn btn-outline-warning btn-sm" 
                                onclick="abrirModalAnular({{ $venta->id }})">
                            <i class="fas fa-ban mr-1"></i>Anular Venta
                        </button>
                        @endif
                        
                        <a href="{{ route('admin.ventas.index') }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Volver a Ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para registrar pago --}}
<div class="modal fade" id="modalPago" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formPago" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card mr-2"></i>Registrar Pago
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="monto">Monto a Pagar *</label>
                        <input type="number" 
                               class="form-control" 
                               id="monto" 
                               name="monto" 
                               step="0.01" 
                               min="0.01" 
                               required>
                        <small class="text-muted">Saldo pendiente: <span id="saldoPendiente"></span></small>
                    </div>
                    <div class="form-group">
                        <label for="referencia">Referencia</label>
                        <input type="text" 
                               class="form-control" 
                               id="referencia" 
                               name="referencia" 
                               placeholder="Nº de operación, banco, etc.">
                    </div>
                    <div class="form-group">
                        <label for="comentario">Comentario</label>
                        <textarea class="form-control" 
                                  id="comentario" 
                                  name="comentario" 
                                  rows="3" 
                                  placeholder="Observaciones del pago..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i>Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal para anular venta --}}
<div class="modal fade" id="modalAnular" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formAnular" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-ban mr-2"></i>Anular Venta
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Esta acción no se puede deshacer. La venta será marcada como cancelada.
                    </div>
                    <div class="form-group">
                        <label for="motivo">Motivo de la anulación *</label>
                        <textarea class="form-control" 
                                  id="motivo" 
                                  name="motivo" 
                                  rows="3" 
                                  required 
                                  placeholder="Describe el motivo por el cual se anula esta venta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban mr-1"></i>Anular Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 15px;
    height: calc(100% - 5px);
    width: 2px;
    background-color: #e3e6f0;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #1cc88a;
}

@media print {
    .btn, .modal, .sidebar, .navbar {
        display: none !important;
    }
    
    .container-fluid {
        width: 100% !important;
        max-width: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .col-xl-8 {
        width: 100% !important;
    }
    
    .col-xl-4 {
        display: none !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
function abrirModalPago(ventaId, saldoPendiente) {
    document.getElementById('formPago').action = `/admin/ventas/${ventaId}/pago`;
    document.getElementById('monto').max = saldoPendiente;
    document.getElementById('monto').value = saldoPendiente;
    document.getElementById('saldoPendiente').textContent = `S/ ${saldoPendiente.toFixed(2)}`;
    $('#modalPago').modal('show');
}

function abrirModalAnular(ventaId) {
    document.getElementById('formAnular').action = `/admin/ventas/${ventaId}/anular`;
    document.getElementById('motivo').value = '';
    $('#modalAnular').modal('show');
}

// Confirmar antes de anular
document.getElementById('formAnular').addEventListener('submit', function(e) {
    if (!confirm('¿Estás seguro de que deseas anular esta venta? Esta acción no se puede deshacer.')) {
        e.preventDefault();
    }
});

// Validar monto del pago
document.getElementById('monto').addEventListener('input', function() {
    const max = parseFloat(this.max);
    const value = parseFloat(this.value);
    
    if (value > max) {
        this.setCustomValidity('El monto no puede ser mayor al saldo pendiente');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsection 