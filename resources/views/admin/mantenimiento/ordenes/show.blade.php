@extends('admin.layouts.app')

@section('title', 'Detalle de Orden de Trabajo')

@push('styles')
<style>
    body {
        background-color: #f7f7f7;
    }
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .nav-tabs {
        border-bottom: none;
    }
    .nav-tabs .nav-link {
        border: none;
        font-weight: 500;
        color: #6c757d;
        border-radius: 0;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: transparent;
        background-color: rgba(13, 110, 253, 0.05);
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background-color: #fff;
    }
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    .detail-icon {
        width: 36px;
        min-width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    .detail-text {
        flex-grow: 1;
        border-left: 2px solid #e9ecef;
        padding-left: 1rem;
    }
    .timeline-container {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        height: 100%;
        width: 2px;
        background-color: #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-badge {
        position: absolute;
        left: -30px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        color: white;
        top: 0;
        z-index: 1;
    }
    .timeline-panel {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .timeline-title {
        margin-top: 0;
        font-weight: bold;
    }
    .timeline-date {
        font-size: 0.9em;
        color: #6c757d;
    }
    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .hover-shadow-lg {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-shadow-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .tab-pane {
        padding: 20px;
    }
    @media (max-width: 991.98px) {
        .col-lg-3.mb-4 .card {
            margin-bottom: 1rem;
        }
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
        }
        .nav-tabs::-webkit-scrollbar {
            height: 4px;
        }
        .nav-tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .nav-tabs::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="d-flex justify-content-between flex-wrap bg-light flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="">
            <span class="color-secondary">Orden de Trabajo</span> <br>
            <div class="d-flex">
                <h1 style="font-size: 20pt;font-weight: 700;">{{ $orden->codigo_orden }}</h1>
                @switch($orden->estado)
                        @case('diagnostico')
                            <span class="badge bg-info fs-6 me-3">Diagnóstico</span>
                            @break
                        @case('espera_aprobacion')
                            <span class="badge bg-warning fs-6 me-3">Esperando Aprobación</span>
                            @break
                        @case('en_progreso')
                            <span class="badge bg-primary fs-6 me-3">En Progreso</span>
                            @break
                        @case('finalizado')
                            <span class="badge bg-success fs-6 me-3">Finalizado</span>
                            @break
                        @case('facturado')
                            <span class="badge bg-secondary fs-6 me-3">Facturado</span>
                            @break
                        @case('entregado')
                            <span class="badge bg-dark fs-6 me-3">Entregado</span>
                            @break
                        @default
                            <span class="badge bg-secondary fs-6 me-3">{{ $orden->estado }}</span>
                    @endswitch
            </div>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <!--a href="{{ route('admin.mantenimiento.ordenes.index') }}" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a-->
                <form action="{{ route('admin.mantenimiento.ordenes.update', ['orden' => $orden->id]) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    @method('PUT')
                    
                    <select name="estado" id="estado" class="form-select me-2" style="width: auto;">
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ $orden->estado == $state ? 'selected' : '' }}>{{ ucfirst($state) }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i>
                    </button>
                </form>
                <div class="btn">
                    <!-- Acciones según estado -->
                    @switch($orden->estado)
                        @case('diagnostico')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#diagnosticoModal">
                                <i class="fas fa-clipboard-check"></i> Registrar Diagnóstico
                            </button>
                            @break
                        @case('espera_aprobacion')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#aprobacionModal">
                                <i class="fas fa-thumbs-up"></i> Registrar Aprobación del Cliente
                            </button>
                            @break
                        @case('en_progreso')
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#finalizarTrabajoModal">
                                <i class="fas fa-check-circle"></i> Finalizar Trabajo
                            </button>
                            @break
                        @case('finalizado')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generarFacturaModal">
                                <i class="fas fa-file-invoice-dollar"></i> Generar Factura
                            </button>
                            @break
                        @case('facturado')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrarPagoModal">
                                <i class="fas fa-money-bill-wave"></i> Registrar Pago
                            </button>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <!-- Estado Actual de la Orden -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">

            </div>
        </div>
    </div>

    <div class="row">
        <!-- Panel izquierdo - Información del cliente y vehículo -->
        <div class="col-lg-3 mb-4">
            <!-- Datos del Cliente -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden hover-shadow-lg transition-all mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold fs-5 d-flex align-items-center">
                        <i class="fas fa-user-circle me-2 fs-4"></i> 
                        Datos del Cliente
                    </h5>
                </div>
                
                <div class="card-body px-4">
                    <h4 class="fw-bold text-center mb-4 text-dark">
                        @if($orden->cliente->tipo_cliente == 'natural')
                            {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }} {{ $orden->cliente->apellido_materno }}
                        @else
                            {{ $orden->cliente->razon_social }}
                        @endif
                    </h4>
                    
                    <div class="client-details">
                        <!-- DNI/RUC -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-id-card fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">{{ $orden->cliente->tipo_cliente == 'persona' ? 'RUC' : 'DNI' }}</span>
                                <p class="mb-0 fw-medium text-dark">{{ $orden->cliente->documento_identidad }}</p>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-phone-alt fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">Teléfono</span>
                                <p class="mb-0 fw-medium">
                                    @if($orden->cliente->telefonos && $orden->cliente->telefonos->first())
                                        <a href="https://wa.me/+51{{ preg_replace('/[^0-9]/', '', $orden->cliente->telefonos->first()->numero) }}" 
                                        class="text-success d-inline-flex align-items-center" 
                                        target="_blank">
                                            <i class="fab fa-whatsapp me-2 fs-5"></i>
                                            {{ $orden->cliente->telefonos->first()->numero }}
                                        </a>
                                    @else
                                        <span class="text-secondary">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-envelope fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">Email</span>
                                <p class="mb-0 fw-medium">
                                    @if($orden->cliente->correo)
                                        <a href="mailto:{{ $orden->cliente->correo }}" 
                                        class="text-primary d-inline-flex align-items-center" 
                                        target="_blank">
                                            <i class="fas fa-at me-2"></i>
                                            {{ $orden->cliente->correo }}
                                        </a>
                                    @else
                                        <span class="text-secondary">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <!--div class="detail-item d-flex align-items-start">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-map-marker-alt fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">Dirección</span>
                                <p class="mb-0 fw-medium text-dark">
                                    {{ $orden->cliente->direccion }}, 
                                    {{ $orden->cliente->distrito }}, 
                                    {{ $orden->cliente->provincia }}, 
                                    {{ $orden->cliente->departamento }}
                                </p>
                            </div>
                        </div-->
                    </div>
                </div>
            </div>
            
            <!-- Datos del Vehículo -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden hover-shadow-lg transition-all">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold fs-5 d-flex align-items-center">
                        <i class="fas fa-car me-2 fs-4"></i> 
                        Datos del Vehículo
                    </h5>
                </div>
                
                <div class="card-body px-4">
                    <h5 class="fw-bold text-center mb-3 text-dark">
                        {{ $orden->vehiculo->marca->nombre ?? 'N/A' }} 
                        {{ $orden->vehiculo->modelo->nombre ?? 'N/A' }}
                    </h5>
                    
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="row row-cols-2 g-2">
                            <div class="col">
                                <div class="detail-item">
                                    <span class="text-muted small text-uppercase fw-semibold">Placa</span>
                                    <p class="mb-0 fw-bold text-dark">{{ $orden->vehiculo->nro_placa }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="detail-item">
                                    <span class="text-muted small text-uppercase fw-semibold">Año</span>
                                    <p class="mb-0 fw-bold text-dark">{{ $orden->vehiculo->anio ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="detail-item">
                                    <span class="text-muted small text-uppercase fw-semibold">Color</span>
                                    <p class="mb-0 fw-bold text-dark">{{ $orden->vehiculo->color }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="detail-item">
                                    <span class="text-muted small text-uppercase fw-semibold">Combustible</span>
                                    <p class="mb-0 fw-bold text-dark">{{ $orden->vehiculo->combustible->nombre ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vehicle-details">
                        <!-- Motor -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-cogs fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">N° Motor</span>
                                <p class="mb-0 fw-medium text-dark">{{ $orden->vehiculo->motor }}</p>
                            </div>
                        </div>

                        <!-- Chasis -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-barcode fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">N° Chasis</span>
                                <p class="mb-0 fw-medium text-dark">{{ $orden->vehiculo->serie_vim }}</p>
                            </div>
                        </div>

                        <!-- Kilometraje -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-tachometer-alt fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">Kilometraje</span>
                                <p class="mb-0 fw-medium text-dark">
                                    @if($orden->kilometraje_ingreso)
                                        {{ number_format($orden->kilometraje_ingreso, 0, '.', ',') }} km (Ingreso)<br>
                                    @endif
                                    
                                    @if($orden->kilometraje_salida)
                                        {{ number_format($orden->kilometraje_salida, 0, '.', ',') }} km (Salida)
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles del servicio -->
                    <div class="mt-4">
                        <h6 class="fw-bold border-bottom pb-2">Detalles del Servicio</h6>
                        <p class="mb-2"><span class="fw-bold">Problema:</span> {{ $orden->descripcion_problema ?? 'No especificado' }}</p>
                        
                        @if($orden->diagnostico)
                            <p class="mb-2"><span class="fw-bold">Diagnóstico:</span> {{ $orden->diagnostico }}</p>
                        @endif
                        
                        @if($orden->recomendaciones)
                            <p class="mb-2"><span class="fw-bold">Recomendaciones:</span> {{ $orden->recomendaciones }}</p>
                        @endif
                        
                        @if($orden->fecha_proxima_revision)
                            <p class="mb-0"><span class="fw-bold">Próxima revisión:</span> {{ \Carbon\Carbon::parse($orden->fecha_proxima_revision)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho - Sistema de pestañas -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs" id="ordenTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-3 py-3" id="linea-tiempo-tab" data-bs-toggle="tab" data-bs-target="#linea-tiempo" 
                                type="button" role="tab" aria-controls="linea-tiempo" aria-selected="true">
                                Línea de Tiempo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="seguimiento-tab" data-bs-toggle="tab" data-bs-target="#seguimiento" 
                                type="button" role="tab" aria-controls="seguimiento" aria-selected="false">
                                Seguimiento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="repuestos-servicios-tab" data-bs-toggle="tab" data-bs-target="#repuestos-servicios" 
                                type="button" role="tab" aria-controls="repuestos-servicios" aria-selected="false">
                                Repuestos y Servicios
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" 
                                type="button" role="tab" aria-controls="pagos" aria-selected="false">
                                Pagos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="factura-tab" data-bs-toggle="tab" data-bs-target="#factura" 
                                type="button" role="tab" aria-controls="factura" aria-selected="false">
                                Factura/Boleta
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0" id="ordenTabsContent">
                        <!-- Pestaña Línea de Tiempo -->
                        <div class="tab-pane fade show active" id="linea-tiempo" role="tabpanel" aria-labelledby="linea-tiempo-tab">
                            <div class="timeline-container p-3">
                                <div class="timeline">
                                    @if($orden->fecha_ingreso)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-info">
                                                <i class="fas fa-car"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Ingreso del Vehículo</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y H:i') }}</div>
                                                <p>Vehículo ingresado al taller para diagnóstico con {{ number_format($orden->kilometraje_ingreso, 0, '.', ',') }} km.</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($orden->fecha_diagnostico)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-primary">
                                                <i class="fas fa-stethoscope"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Diagnóstico Realizado</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_diagnostico)->format('d/m/Y H:i') }}</div>
                                                <p>{{ $orden->diagnostico }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($orden->fecha_aprobacion_cliente)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-success">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Aprobación del Cliente</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_aprobacion_cliente)->format('d/m/Y H:i') }}</div>
                                                <p>Método de aprobación: {{ ucfirst($orden->metodo_aprobacion) }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($orden->fecha_inicio_trabajo)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-warning">
                                                <i class="fas fa-wrench"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Inicio de Trabajos</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_inicio_trabajo)->format('d/m/Y H:i') }}</div>
                                                <p>Técnico asignado: {{ $orden->tecnico->name ?? 'No especificado' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($orden->fecha_fin_trabajo)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-info">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Finalización de Trabajos</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_fin_trabajo)->format('d/m/Y H:i') }}</div>
                                                @if($orden->fecha_proxima_revision)
                                                    <p>Próxima revisión recomendada: {{ \Carbon\Carbon::parse($orden->fecha_proxima_revision)->format('d/m/Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($orden->factura)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-secondary">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Facturación</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->factura->fecha_emision)->format('d/m/Y') }}</div>
                                                <p>Factura #{{ $orden->factura->numero_factura }} por S/ {{ number_format($orden->factura->total, 2) }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($orden->factura->estado_pago === 'pagado')
                                            <div class="timeline-item">
                                                <div class="timeline-badge bg-success">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </div>
                                                <div class="timeline-panel">
                                                    <div class="timeline-title">Pago Recibido</div>
                                                    <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/Y') }}</div>
                                                    <p>Método de pago: {{ ucfirst($orden->factura->metodo_pago) }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($orden->fecha_entrega)
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-dark">
                                                <i class="fas fa-flag-checkered"></i>
                                            </div>
                                            <div class="timeline-panel">
                                                <div class="timeline-title">Entrega del Vehículo</div>
                                                <div class="timeline-date">{{ \Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/Y H:i') }}</div>
                                                <p>Kilometraje de salida: {{ number_format($orden->kilometraje_salida, 0, '.', ',') }} km</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Seguimiento (nueva implementación) -->
                        <div class="tab-pane fade" id="seguimiento" role="tabpanel" aria-labelledby="seguimiento-tab">
                            @include('admin.mantenimiento.ordenes.seguimiento.index', ['orden' => $orden])
                        </div>
                        
                        <!-- Pestaña Repuestos y Servicios -->
                        <div class="tab-pane fade" id="repuestos-servicios" role="tabpanel" aria-labelledby="repuestos-servicios-tab">
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Repuestos y Servicios</h5>
                                    @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                        <div>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#agregarRepuestoModal">
                                                <i class="fas fa-plus"></i> Agregar Repuesto
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#agregarServicioModal">
                                                <i class="fas fa-plus"></i> Agregar Servicio
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Repuestos -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Repuestos</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Código</th>
                                                        <th>Descripción</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-end">Precio Unit.</th>
                                                        <th class="text-end">Subtotal</th>
                                                        @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                            <th class="text-center">Acciones</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($orden->detallesRepuestos as $detalle)
                                                        <tr>
                                                            <td>{{ $detalle->parte->codigo ?? 'N/A' }}</td>
                                                            <td>{{ $detalle->descripcion }}</td>
                                                            <td class="text-center">{{ $detalle->cantidad }}</td>
                                                            <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                            <td class="text-end">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                                            @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                                <td class="text-center">
                                                                    <form action="{{ route('admin.mantenimiento.ordenes.eliminar-repuesto', $detalle) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este repuesto?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ $orden->estado != 'facturado' && $orden->estado != 'entregado' ? '6' : '5' }}" class="text-center">No hay repuestos registrados</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr class="fw-bold">
                                                        <td colspan="{{ $orden->estado != 'facturado' && $orden->estado != 'entregado' ? '4' : '3' }}" class="text-end">Total Repuestos:</td>
                                                        <td class="text-end">S/ {{ number_format($orden->getTotalRepuestosAttribute(), 2) }}</td>
                                                        @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                            <td></td>
                                                        @endif
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Servicios -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Servicios</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Servicio</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-end">Precio Unit.</th>
                                                        <th class="text-end">Subtotal</th>
                                                        @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                            <th class="text-center">Acciones</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($orden->detallesServicios as $detalle)
                                                        <tr>
                                                            <td>{{ $detalle->descripcion }}</td>
                                                            <td class="text-center">{{ $detalle->cantidad }}</td>
                                                            <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                            <td class="text-end">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                                            @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                                <td class="text-center">
                                                                    <form action="{{ route('admin.mantenimiento.ordenes.eliminar-servicio', $detalle) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este servicio?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="{{ $orden->estado != 'facturado' && $orden->estado != 'entregado' ? '5' : '4' }}" class="text-center">No hay servicios registrados</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr class="fw-bold">
                                                        <td colspan="{{ $orden->estado != 'facturado' && $orden->estado != 'entregado' ? '3' : '2' }}" class="text-end">Total Servicios:</td>
                                                        <td class="text-end">S/ {{ number_format($orden->getTotalServiciosAttribute(), 2) }}</td>
                                                        @if($orden->estado != 'facturado' && $orden->estado != 'entregado')
                                                            <td></td>
                                                        @endif
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Total General -->
                                <div class="card shadow-sm border-0 bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-6">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Subtotal Repuestos:</span>
                                                    <span class="fw-medium">S/ {{ number_format($orden->getTotalRepuestosAttribute(), 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Subtotal Servicios:</span>
                                                    <span class="fw-medium">S/ {{ number_format($orden->getTotalServiciosAttribute(), 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fw-bold">
                                                    <span>TOTAL:</span>
                                                    <span class="fs-5 text-primary">S/ {{ number_format($orden->getTotalOrdenAttribute(), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Pagos -->
                        <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
                            <div class="p-3">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Información de Pago</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($orden->factura && $orden->factura->estado_pago === 'pagado')
                                            <div class="alert alert-success">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-check-circle me-2 fs-3"></i>
                                                    <h5 class="mb-0">Pago completado</h5>
                                                </div>
                                                <p class="mb-0">El pago de esta orden de trabajo ha sido registrado correctamente.</p>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                    <p><span class="fw-bold">Fecha de pago:</span> {{ \Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/Y') }}</p>
                                                    <p><span class="fw-bold">Método de pago:</span> {{ ucfirst($orden->factura->metodo_pago) }}</p>
                                                    <p><span class="fw-bold">Monto pagado:</span> S/ {{ number_format($orden->factura->total, 2) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="fw-bold">Número de factura:</span> {{ $orden->factura->numero_factura }}</p>
                                                    <p><span class="fw-bold">Días de garantía:</span> {{ $orden->factura->dias_garantia }}</p>
                                                    @if($orden->factura->notas)
                                                        <p><span class="fw-bold">Notas:</span> {{ $orden->factura->notas }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($orden->factura)
                                            <div class="alert alert-warning">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-exclamation-triangle me-2 fs-3"></i>
                                                    <h5 class="mb-0">Pago pendiente</h5>
                                                </div>
                                                <p class="mb-0">Esta orden tiene una factura generada pero el pago aún no ha sido registrado.</p>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                    <p><span class="fw-bold">Fecha de emisión:</span> {{ \Carbon\Carbon::parse($orden->factura->fecha_emision)->format('d/m/Y') }}</p>
                                                    <p><span class="fw-bold">Número de factura:</span> {{ $orden->factura->numero_factura }}</p>
                                                    <p><span class="fw-bold">Total a pagar:</span> S/ {{ number_format($orden->factura->total, 2) }}</p>
                                                </div>
                                                <div class="col-md-6 d-flex align-items-center justify-content-center">
                                                    @if($orden->estado === 'facturado')
                                                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#registrarPagoModal">
                                                            <i class="fas fa-money-bill-wave me-2"></i> Registrar Pago
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-info-circle me-2 fs-3"></i>
                                                    <h5 class="mb-0">Factura no generada</h5>
                                                </div>
                                                <p class="mb-0">Aún no se ha generado una factura para esta orden de trabajo.</p>
                                            </div>
                                            
                                            <div class="text-center mt-4">
                                                @if($orden->estado === 'finalizado')
                                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#generarFacturaModal">
                                                        <i class="fas fa-file-invoice-dollar me-2"></i> Generar Factura
                                                    </button>
                                                @else
                                                    <p class="text-muted">La factura se podrá generar cuando la orden esté en estado "Finalizado".</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Factura/Boleta -->
                        <div class="tab-pane fade" id="factura" role="tabpanel" aria-labelledby="factura-tab">
                            <div class="p-3">
                                @if($orden->factura)
                                    <div class="card shadow-sm mb-4 border-0">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="mb-0">
                                                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                                    Factura #{{ $orden->factura->numero_factura }}
                                                </h5>
                                                <div>
                                                    <span class="badge {{ $orden->factura->estado_pago === 'pagado' ? 'bg-success' : 'bg-warning' }} fs-6">
                                                        {{ ucfirst($orden->factura->estado_pago) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="text-muted">Cliente:</label>
                                                        <p class="fw-medium">
                                                            @if($orden->cliente->tipo_cliente == 'persona')
                                                                {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }} {{ $orden->cliente->apellido_materno }}
                                                            @else
                                                                {{ $orden->cliente->razon_social }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="text-muted">{{ $orden->cliente->tipo_cliente == 'persona' ? 'DNI' : 'RUC' }}:</label>
                                                        <p class="fw-medium">{{ $orden->cliente->documento_identidad }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="text-muted">Dirección:</label>
                                                        <p class="fw-medium">{{ $orden->cliente->direccion }}, {{ $orden->cliente->distrito }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="text-muted">Fecha de emisión:</label>
                                                        <p class="fw-medium">{{ \Carbon\Carbon::parse($orden->factura->fecha_emision)->format('d/m/Y') }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="text-muted">Método de pago:</label>
                                                        <p class="fw-medium">{{ ucfirst($orden->factura->metodo_pago) }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="text-muted">Días de garantía:</label>
                                                        <p class="fw-medium">{{ $orden->factura->dias_garantia }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Descripción</th>
                                                            <th class="text-center">Cantidad</th>
                                                            <th class="text-end">Precio Unit.</th>
                                                            <th class="text-end">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Repuestos -->
                                                        @foreach($orden->detallesRepuestos as $detalle)
                                                            <tr>
                                                                <td>{{ $detalle->descripcion }} (Repuesto)</td>
                                                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                                                <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                                <td class="text-end">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        
                                                        <!-- Servicios -->
                                                        @foreach($orden->detallesServicios as $detalle)
                                                            <tr>
                                                                <td>{{ $detalle->descripcion }} (Servicio)</td>
                                                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                                                <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                                <td class="text-end">S/ {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-5 offset-md-7">
                                                    <div class="bg-light p-3 rounded-3">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Subtotal:</span>
                                                            <span class="fw-medium">S/ {{ number_format($orden->factura->subtotal, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>IGV (18%):</span>
                                                            <span class="fw-medium">S/ {{ number_format($orden->factura->impuestos, 2) }}</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between fs-5 fw-bold">
                                                            <span>TOTAL:</span>
                                                            <span class="text-primary">S/ {{ number_format($orden->factura->total, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center mt-4">
                                                <button class="btn btn-primary" onclick="window.print()">
                                                    <i class="fas fa-print me-2"></i> Imprimir Factura
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="mb-4">
                                            <i class="fas fa-file-invoice text-muted" style="font-size: 5rem;"></i>
                                        </div>
                                        <h4 class="text-muted mb-3">No se ha generado factura para esta orden</h4>
                                        
                                        @if($orden->estado === 'finalizado')
                                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#generarFacturaModal">
                                                <i class="fas fa-file-invoice-dollar me-2"></i> Generar Factura
                                            </button>
                                        @else
                                            <p class="text-muted">La factura se podrá generar cuando la orden esté en estado "Finalizado".</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

                        <!-- Modal para registrar diagnóstico -->
                        @if($orden->estado === 'diagnostico')
                            <div class="modal fade" id="diagnosticoModal" tabindex="-1" aria-labelledby="diagnosticoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.mantenimiento.ordenes.registrar-diagnostico', ['orden' => $orden->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="diagnosticoModalLabel">Registrar Diagnóstico</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if(!$orden->kilometraje_ingreso)
                                                    <div class="mb-3">
                                                        <label for="kilometraje_ingreso" class="form-label">Kilometraje de Ingreso</label>
                                                        <input type="number" name="kilometraje_ingreso" id="kilometraje_ingreso" class="form-control" min="0" value="{{ $orden->vehiculo->kilometraje }}" required>
                                                    </div>
                                                @endif
                                                
                                                <div class="mb-3">
                                                    <label for="diagnostico" class="form-label">Diagnóstico Detallado</label>
                                                    <textarea name="diagnostico" id="diagnostico" class="form-control" rows="6" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Diagnóstico</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Modal para registrar aprobación del cliente -->
                        @if($orden->estado === 'espera_aprobacion')
                            <div class="modal fade" id="aprobacionModal" tabindex="-1" aria-labelledby="aprobacionModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.mantenimiento.ordenes.registrar-aprobacion', ['orden' => $orden->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="aprobacionModalLabel">Registrar Aprobación del Cliente</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="metodo_aprobacion" class="form-label">Método de Aprobación</label>
                                                    <select name="metodo_aprobacion" id="metodo_aprobacion" class="form-select" required>
                                                        <option value="presencial">Presencial</option>
                                                        <option value="telefono">Vía Telefónica</option>
                                                        <option value="email">Vía Email</option>
                                                        <option value="whatsapp">WhatsApp</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success">Registrar Aprobación</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Modal para finalizar trabajo -->
                        @if($orden->estado === 'en_progreso')
                            <div class="modal fade" id="finalizarTrabajoModal" tabindex="-1" aria-labelledby="finalizarTrabajoModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.mantenimiento.ordenes.finalizar-trabajo', ['orden' => $orden->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="finalizarTrabajoModalLabel">Finalizar Trabajo</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="kilometraje_salida" class="form-label">Kilometraje de Salida</label>
                                                    <input type="number" name="kilometraje_salida" id="kilometraje_salida" class="form-control" min="{{ $orden->kilometraje_ingreso }}" value="{{ $orden->kilometraje_ingreso }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="recomendaciones" class="form-label">Recomendaciones</label>
                                                    <textarea name="recomendaciones" id="recomendaciones" class="form-control" rows="3"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="fecha_proxima_revision" class="form-label">Fecha Recomendada para Próxima Revisión</label>
                                                    <input type="date" name="fecha_proxima_revision" id="fecha_proxima_revision" class="form-control" min="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-info">Finalizar Trabajo</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Modal para generar factura -->
                        @if($orden->estado === 'finalizado')
                            <div class="modal fade" id="generarFacturaModal" tabindex="-1" aria-labelledby="generarFacturaModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.mantenimiento.ordenes.generar-factura', ['orden' => $orden->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="generarFacturaModalLabel">Generar Factura</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="numero_factura" class="form-label">Número de Factura</label>
                                                    <input type="text" name="numero_factura" id="numero_factura" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                                    <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                                        <option value="efectivo">Efectivo</option>
                                                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                                        <option value="transferencia">Transferencia Bancaria</option>
                                                        <option value="yape">Yape</option>
                                                        <option value="plin">Plin</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="dias_garantia" class="form-label">Días de Garantía</label>
                                                    <input type="number" name="dias_garantia" id="dias_garantia" class="form-control" min="0" value="30" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notas" class="form-label">Notas Adicionales</label>
                                                    <textarea name="notas" id="notas" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Generar Factura</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Modal para registrar pago -->
@if($orden->estado === 'facturado')
    <div class="modal fade" id="registrarPagoModal" tabindex="-1" aria-labelledby="registrarPagoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.mantenimiento.ordenes.registrar-pago', ['orden' => $orden->id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="registrarPagoModalLabel">Registrar Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6>Detalles de la Factura:</h6>
                            <p>Factura #{{ $orden->factura->numero_factura }}</p>
                            <p class="mb-0">Total a pagar: S/ {{ number_format($orden->factura->total, 2) }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Modal para agregar repuesto -->
<div class="modal fade" id="agregarRepuestoModal" tabindex="-1" aria-labelledby="agregarRepuestoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mantenimiento.ordenes.agregar-repuesto', ['orden' => $orden->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarRepuestoModalLabel">Agregar Repuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="parte_id" class="form-label">Repuesto</label>
                        <select name="parte_id" id="parte_id" class="form-select" required>
                            <option value="">Seleccione un repuesto</option>
                            @foreach($partes as $parte)
                                <option value="{{ $parte->id }}" data-precio="{{ $parte->precio_venta }}">
                                    {{ $parte->codigo }} - {{ $parte->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" name="precio_unitario" id="precio_unitario" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar Repuesto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar servicio -->
<div class="modal fade" id="agregarServicioModal" tabindex="-1" aria-labelledby="agregarServicioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mantenimiento.ordenes.agregar-servicio', ['orden' => $orden->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarServicioModalLabel">Agregar Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="servicio_id" class="form-label">Servicio</label>
                        <select name="servicio_id" id="servicio_id" class="form-select" required>
                            <option value="">Seleccione un servicio</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}">
                                    {{ $servicio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad_servicio" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" name="precio_unitario" id="precio_unitario_servicio" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar Servicio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar cambio de repuesto para autocompletar precio
        const parteSelect = document.getElementById('parte_id');
        const precioInput = document.getElementById('precio_unitario');
        
        if (parteSelect && precioInput) {
            parteSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.precio) {
                    precioInput.value = selectedOption.dataset.precio;
                } else {
                    precioInput.value = '';
                }
            });
        }
        
        // Manejar cambio de servicio para autocompletar precio
        const servicioSelect = document.getElementById('servicio_id');
        const precioServicioInput = document.getElementById('precio_unitario_servicio');
        
        if (servicioSelect && precioServicioInput) {
            servicioSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.precio) {
                    precioServicioInput.value = selectedOption.dataset.precio;
                } else {
                    precioServicioInput.value = '';
                }
            });
        }
        
        // Inicializar y guardar pestañas activas
        const validTabs = ['#linea-tiempo', '#repuestos-servicios', '#pagos', '#factura'];
        
        // Guardar pestaña activa al cambiar
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('data-bs-target');
            localStorage.setItem('lastActiveOrderTab', targetTab);
            console.log('Pestaña activada:', targetTab);
        });

        // Restaurar última pestaña activa
        let lastTab = localStorage.getItem('lastActiveOrderTab');
        if (lastTab && validTabs.includes(lastTab)) {
            $('button[data-bs-target="' + lastTab + '"]').tab('show');
        } else {
            // Por defecto, activar la primera pestaña
            $('button[data-bs-toggle="tab"]').first().tab('show');
        }
    });
</script>
@endpush