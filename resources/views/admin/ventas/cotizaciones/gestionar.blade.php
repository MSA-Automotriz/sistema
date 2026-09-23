@extends('admin.layouts.app')

@section('title', 'Gestionar Cotización')

@php
if (!function_exists('getBootstrapColorHex')) {
    function getBootstrapColorHex($color) {
        $colorMap = [
            'primary' => '0d6efd',
            'secondary' => '6c757d',
            'success' => '198754',
            'danger' => 'dc3545',
            'warning' => 'ffc107',
            'info' => '0dcaf0',
            'light' => 'f8f9fa',
            'dark' => '212529'
        ];
        return $colorMap[$color] ?? '6c757d'; 
    }
}
@endphp

@section('header', 'Gestionar Cotización')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-eye text-info me-2"></i> Detalles de Cotización
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6">Cotización #{{ $cotizacion->codigo }}</h2>
                <div class="text-white-50 mb-0 d-flex align-items-center">
                    <i class="far fa-calendar-alt me-1"></i> {{ $cotizacion->created_at->format('d M, Y H:i') }}
                    <span class="mx-2">|</span>
                    <i class="far fa-user me-1"></i> {{ $cotizacion->usuario ? $cotizacion->usuario->name : 'No asignado' }}
                </div>
            </div>
            <div>
                <div class="d-flex flex-column align-items-end">
                    <small class="text-white-50 mb-1 fw-semibold text-uppercase tracking-wider">Estado Actual</small>
                    <span class="badge bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center cursor-pointer transition hover:scale-105" id="btnEditarCotizacion" style="border: 1px solid rgba(255,255,255,0.8);">
                        <span class="d-inline-block rounded-circle me-2" style="width: 8px; height: 8px; background-color: #{{ getBootstrapColorHex($cotizacion->estado->color ?? 'secondary') }};"></span>
                        {{ $cotizacion->estado->nombre ?? 'Sin estado' }}
                        <i class="bi bi-pencil-square ms-2 text-primary opacity-75"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <!-- Offcanvas/Sidebar para Estados (se movió dentro de layout o states) -->
    @include('admin.ventas.cotizaciones.estados', ['cotizacion' => $cotizacion])

    <!-- Sección de Requerimiento de Compra -->
    @if($cotizacion->estado && $cotizacion->estado->nombre === 'Cerrado Ganado')
        <div class="row mt-3">
            <div class="col-12">
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom-0 pb-0 bg-transparent">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-lg me-3">
                                <i class="fas fa-shopping-cart text-warning"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Requerimiento de Compra</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($cotizacion->requerimientoCompra)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Requerimiento de compra generado exitosamente.
                                <a href="{{ route('admin.compras.requerimientos.show', $cotizacion->requerimientoCompra->id) }}" 
                                   class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-eye me-1"></i> Ver Requerimiento #{{ $cotizacion->requerimientoCompra->id }}
                                </a>
                            </div>
                            
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizacion->requerimientoCompra->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $detalle->tipo_item == 'vehiculo' ? 'info' : ($detalle->tipo_item == 'parte' ? 'secondary' : 'warning') }}">
                                                        {{ $detalle->getTipoFormateadoAttribute() }}
                                                    </span>
                                                </td>
                                                <td>{{ $detalle->getNombreAttribute() }}</td>
                                                <td>{{ $detalle->cantidad }}</td>
                                                <td>
                                                    @if($cotizacion->requerimientoCompra->estado)
                                                        <span class="badge" style="background-color: {{ $cotizacion->requerimientoCompra->estado->color ?? '#6c757d' }}">
                                                            {{ $cotizacion->requerimientoCompra->estado->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-3">La cotización está en estado <strong>Cerrado Ganado</strong>. 
                               Es posible generar un requerimiento de compra para los ítems de esta cotización.</p>
                            
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generarRequerimientoModal">
                                <i class="fas fa-plus-circle me-1"></i> Generar Requerimiento de Compra
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row mt-3">
            <div class="col-12">
                <div class="card dashboard-card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom-0 pb-0 bg-transparent">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 p-2 rounded-lg me-3">
                                <i class="fas fa-shopping-cart text-secondary"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-muted">Requerimiento de Compra</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            El requerimiento de compra solo puede generarse cuando la cotización esté en estado <strong>Cerrado Ganado</strong>.
                            <br>
                            Estado actual: <strong>{{ $cotizacion->estado->nombre ?? 'No definido' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mt-4">
        <!-- Panel izquierdo - Información del cliente -->
        <div class="col-lg-3 mb-4">
            <!-- Tarjeta Cliente -->
            <div class="card dashboard-card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            Datos del Cliente
                        </h6>
                    </div>
                </div>
                
                <div class="card-body px-4">
                    <h4 class="fw-bold text-center mb-4 text-dark">
                        @if($cotizacion->cliente->tipo_cliente === 'natural')
                            {{ $cotizacion->cliente->nombres }} {{ $cotizacion->cliente->apellido_paterno }}
                        @else
                            {{ $cotizacion->cliente->razon_social }}
                        @endif
                    </h4>
                    
                    <div class="client-details">
                        <!-- DNI/RUC -->
                        <div class="detail-item d-flex align-items-start mb-3">
                            <div class="detail-icon pt-1 me-3">
                                <i class="fas fa-id-card fa-lg text-primary"></i>
                            </div>
                            <div class="detail-text">
                                <span class="text-muted small text-uppercase fw-semibold">{{ $cotizacion->cliente->tipo_cliente === 'natural' ? 'DNI' : 'RUC' }}</span>
                                <p class="mb-0 fw-medium text-dark">{{ $cotizacion->cliente->documento_identidad }}</p>
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
                                    @if($cotizacion->cliente->telefonos->first()->numero ?? false)
                                        <a href="https://wa.me/+51{{ preg_replace('/[^0-9]/', '', $cotizacion->cliente->telefonos->first()->numero) }}" 
                                        class="whatsapp-link d-inline-flex align-items-center" 
                                        target="_blank">
                                            <i class="fab fa-whatsapp me-2 fs-5"></i>
                                            {{ $cotizacion->cliente->telefonos->first()->numero }}
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
                                    @if($cotizacion->cliente->correo ?? false)
                                        <a href="mailto:{{ $cotizacion->cliente->correo }}" 
                                        class="email-link d-inline-flex align-items-center" 
                                        target="_blank">
                                            <i class="fas fa-at me-2"></i>
                                            {{ $cotizacion->cliente->correo }}
                                        </a>
                                    @else
                                        <span class="text-secondary">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta Vehículo -->
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-2 rounded-lg me-3">
                            <i class="fas fa-car text-info"></i>
                        </div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            Detalles Vehículo
                        </h6>
                    </div>
                </div>
                <div class="card-body px-3 py-2">
                    @if($cotizacion->detalles && $cotizacion->detalles->isNotEmpty())
                        @php 
                            $detalle = $cotizacion->detalles->first();
                            $vehiculo = $detalle->vehiculo; 
                        @endphp
                        @if($vehiculo)
                            <h6 class="fw-bold text-dark mb-1 fs-6">
                                {{ $vehiculo->marca->nombre ?? '' }} | 
                                {{ $vehiculo->modelo->nombre ?? '' }} | 
                                {{ $detalle->color->nombre ?? 'N/A' }} | 
                                {{ $vehiculo->version->nombre ?? 'Versión no especificada' }}
                            </h6>

                            <div class="row row-cols-1 g-2 mb-3">
                                <div class="col">
                                    <div class="bg-light rounded-2 p-2 small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Cantidad:</span>
                                            <span class="fw-bold">{{ $detalle->cantidad }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">P. Unitario:</span>
                                            <span class="fw-bold text-success">S/ {{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="bg-light rounded-2 p-2 small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Subtotal:</span>
                                            <span class="fw-bold">S/ {{ number_format($detalle->subtotal, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total:</span>
                                            <span class="fw-bold text-success">S/ {{ number_format($detalle->total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning text-center py-1 mb-2 small">
                                Información del vehículo no disponible
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning text-center py-1 mb-2 small">
                            No hay vehículos en esta cotización
                        </div>
                    @endif

                    <div class="d-flex align-items-center bg-light rounded-2 p-2">
                        <div class="detail-icon me-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Tiempo estimado</span>
                            <p class="mb-0 fw-bold text-primary">1 Mes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel central y derecho - Sistema de pestañas -->
        <div class="col-lg-9">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom p-0 rounded-top-4">
                    <ul class="nav nav-tabs" id="cotizacionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-3 py-3" id="gestion-tab" data-bs-toggle="tab" data-bs-target="#gestion" 
                                type="button" role="tab" aria-controls="gestion" aria-selected="true">
                                Gestión
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" 
                                type="button" role="tab" aria-controls="pagos" aria-selected="false">
                                Pagos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="comprobantes-tab" data-bs-toggle="tab" data-bs-target="#comprobantes" 
                                type="button" role="tab" aria-controls="comprobantes" aria-selected="false">
                                Factura/Boleta
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="nota-pedido-tab" data-bs-toggle="tab" data-bs-target="#nota-pedido" 
                                type="button" role="tab" aria-controls="nota-pedido" aria-selected="false">
                                Nota de Pedido
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="orden-trabajo-tab" data-bs-toggle="tab" data-bs-target="#orden-trabajo" 
                                type="button" role="tab" aria-controls="orden-trabajo" aria-selected="false">
                                Orden de Trabajo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="acta-entrega-tab" data-bs-toggle="tab" data-bs-target="#acta-entrega" 
                                type="button" role="tab" aria-controls="acta-entrega" aria-selected="false">
                                Acta de Entrega
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="sunarp-tab" data-bs-toggle="tab" data-bs-target="#sunarp" 
                                type="button" role="tab" aria-controls="sunarp" aria-selected="false">
                                SUNARP
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="placa-tab" data-bs-toggle="tab" data-bs-target="#placa" 
                                type="button" role="tab" aria-controls="placa" aria-selected="false">
                                Placa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-3 py-3" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" 
                                type="button" role="tab" aria-controls="documentos" aria-selected="false">
                                Documentos
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0" id="cotizacionTabsContent">
                        <!-- Pestaña Gestión -->
                        <div class="tab-pane fade show active" id="gestion" role="tabpanel" aria-labelledby="gestion-tab">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body border-bottom pt-3 pb-2 bg-white rounded-top shadow-sm">
                                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="fas fa-tasks me-2 text-primary opacity-75"></i> Gestión de Cotización
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            @include('admin.ventas.cotizaciones.proceso.gestion-form', ['cotizacion' => $cotizacion])
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body border-bottom pt-3 pb-2 bg-white rounded-top shadow-sm d-flex align-items-center justify-content-between">
                                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="fas fa-history me-2 text-primary opacity-75"></i> Histórico de Seguimientos
                                            </h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $cotizacion->seguimientos->count() }}</span>
                                        </div>
                                        <div class="card-body p-0">
                                            @include('admin.ventas.cotizaciones.proceso.seguimientos', ['cotizacion' => $cotizacion])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Pagos -->
                        <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
                            @include('admin.ventas.cotizaciones.proceso.pagos.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Comprobantes -->
                        <div class="tab-pane fade" id="comprobantes" role="tabpanel" aria-labelledby="comprobantes-tab">
                            @include('admin.ventas.cotizaciones.proceso.comprobantes.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Nota de Pedido -->
                        <div class="tab-pane fade" id="nota-pedido" role="tabpanel" aria-labelledby="nota-pedido-tab">
                            @include('admin.ventas.cotizaciones.proceso.nota-pedido.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Orden de Trabajo -->
                        <div class="tab-pane fade" id="orden-trabajo" role="tabpanel" aria-labelledby="orden-trabajo-tab">
                            @include('admin.ventas.cotizaciones.proceso.orden-trabajo.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Acta de Entrega -->
                        <div class="tab-pane fade" id="acta-entrega" role="tabpanel" aria-labelledby="acta-entrega-tab">
                            @include('admin.ventas.cotizaciones.proceso.acta-entrega.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña SUNARP -->
                        <div class="tab-pane fade" id="sunarp" role="tabpanel" aria-labelledby="sunarp-tab">
                            @include('admin.ventas.cotizaciones.proceso.sunarp.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Placa -->
                        <div class="tab-pane fade" id="placa" role="tabpanel" aria-labelledby="placa-tab">
                            @include('admin.ventas.cotizaciones.proceso.placa.index', ['cotizacion' => $cotizacion])
                        </div>

                        <!-- Pestaña Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                            @include('admin.ventas.cotizaciones.proceso.documentos.index', ['cotizacion' => $cotizacion])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Generar Requerimiento -->
<div class="modal fade" id="generarRequerimientoModal" tabindex="-1" aria-labelledby="generarRequerimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.ventas.cotizaciones.generar-requerimiento', $cotizacion->id) }}" method="POST" id="formGenerarRequerimiento">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="generarRequerimientoModalLabel">
                        <i class="fas fa-shopping-cart me-2"></i> Generar Requerimiento de Compra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Se generará un requerimiento de compra con los ítems seleccionados de la cotización.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                            <label class="form-check-label" for="selectAll">
                                                <small>Todo</small>
                                            </label>
                                        </div>
                                    </th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input item-check" type="checkbox" 
                                                       name="items[]" value="{{ $detalle->id }}" 
                                                       id="item{{ $detalle->id }}" checked>
                                            </div>
                                        </td>
                                        <td>
                                            @if($detalle->vehiculo_catalogo_id)
                                                <span class="badge bg-info">Vehículo</span>
                                            @elseif($detalle->repuesto_id)
                                                <span class="badge bg-secondary">Repuesto</span>
                                            @elseif($detalle->servicio_id)
                                                <span class="badge bg-warning text-dark">Servicio</span>
                                            @else
                                                <span class="badge bg-primary">Otro</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($detalle->vehiculo_catalogo_id && $detalle->vehiculoCatalogo)
                                                {{ $detalle->vehiculoCatalogo->marca ? $detalle->vehiculoCatalogo->marca->nombre : '' }} 
                                                {{ $detalle->vehiculoCatalogo->modelo ? $detalle->vehiculoCatalogo->modelo->nombre : '' }} 
                                                {{ $detalle->vehiculoCatalogo->version ? $detalle->vehiculoCatalogo->version->nombre : '' }}
                                                @if($detalle->color)
                                                    <span class="badge" style="background-color: {{ $detalle->color->hexadecimal ?? '#6c757d' }}">
                                                        {{ $detalle->color->nombre }}
                                                    </span>
                                                @endif
                                            @elseif($detalle->repuesto_id && $detalle->repuesto)
                                                <strong>{{ $detalle->repuesto->codigo ?? '' }}</strong> - {{ $detalle->repuesto->nombre ?? '' }}
                                            @elseif($detalle->servicio_id && $detalle->servicio)
                                                {{ $detalle->servicio->nombre ?? '' }}
                                            @else
                                                Detalle no especificado
                                            @endif
                                        </td>
                                        <td>{{ $detalle->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="prioridad" class="form-label fw-semibold">Prioridad</label>
                            <select class="form-select" id="prioridad" name="prioridad" required>
                                <option value="Normal" selected>Normal</option>
                                <option value="Alta">Alta</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="estado_id" class="form-label fw-semibold">Estado Inicial</label>
                            <select class="form-select" id="estado_id" name="estado_id">
                                @php
                                    $estados = collect([]);
                                    $estadoPendiente = null;
                                    
                                    try {
                                        // Intentamos obtener estados de requerimientos
                                        if (class_exists('App\Models\EstadoRequerimiento')) {
                                            $estados = App\Models\EstadoRequerimiento::all();
                                        } elseif (class_exists('App\Models\Estado')) {
                                            $estados = App\Models\Estado::all();
                                        }
                                        
                                        // Buscar estado "Pendiente"
                                        $estadoPendiente = $estados->first(function ($estado) {
                                            return in_array(strtolower($estado->nombre), ['pendiente', 'nuevo', 'creado']);
                                        });
                                        
                                        // Si no encontramos, usar el primero
                                        if (!$estadoPendiente && $estados->isNotEmpty()) {
                                            $estadoPendiente = $estados->first();
                                        }
                                    } catch(\Exception $e) {
                                        \Log::error('Error al cargar estados para requerimiento: ' . $e->getMessage());
                                    }
                                @endphp
                                
                                @forelse($estados as $estado)
                                    <option value="{{ $estado->id }}" 
                                            {{ $estadoPendiente && $estadoPendiente->id == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @empty
                                    <option value="">No hay estados disponibles</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 mt-3">
                        <label for="comentario" class="form-label fw-semibold">Comentarios adicionales</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3"
                                  placeholder="Ingrese comentarios o detalles adicionales para este requerimiento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGenerarRequerimiento">
                        <i class="fas fa-shopping-cart me-1"></i> Generar Requerimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    body {
        background-color: #f7f7f7;
    }

    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Estilos para pestañas */
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

    /* Estilos para avatares y elementos gráficos */
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
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

    /* Estilos para enlaces de contacto */
    .whatsapp-link {
        color: #25D366;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .whatsapp-link:hover {
        color: #128C7E;
        transform: translateX(5px);
    }

    .email-link {
        color: #007bff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .email-link:hover {
        color: #0056b3;
        transform: translateX(5px);
    }

    /* Estilos para detalles del cliente */
    .detail-text {
        border-left: 2px solid #e9ecef;
        padding-left: 1rem;
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

    /* Estilos para contenido de pestañas */
    .tab-pane {
        padding: 20px;
        min-height: 300px;
    }

    .timeline-container {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* Estilos para vehículo compacto */
    .avatar-circle-sm {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .vehicle-image-sm {
        border: 1px solid #dee2e6;
        padding: 2px;
    }

    .fs-6 {
        font-size: 0.9rem !important;
    }

    .detail-item {
        margin-bottom: 0.5rem !important;
    }

    /* Estilos responsivos */
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
            flex-shrink: 0;
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

    /* Mejorar apariencia del modal */
    .modal-header.bg-primary {
        border-bottom: none;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Estilos para formularios */
    .form-label.fw-semibold {
        font-weight: 600 !important;
        margin-bottom: 0.5rem;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Mejorar badges */
    .badge {
        font-size: 0.75em;
    }

    /* Transiciones suaves */
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar pestañas de Bootstrap
    const tabTriggerList = document.querySelectorAll('#cotizacionTabs button[data-bs-toggle="tab"]');
    const tabList = [...tabTriggerList].map(tabTriggerEl => new bootstrap.Tab(tabTriggerEl));

    // Lista de IDs de pestañas válidas
    const validTabs = [
        '#gestion', '#pagos', '#comprobantes', '#nota-pedido',
        '#orden-trabajo', '#acta-entrega', '#sunarp', '#placa', '#documentos'
    ];

    // Guardar pestaña activa al cambiar
    tabTriggerList.forEach(tabTrigger => {
        tabTrigger.addEventListener('shown.bs.tab', function (event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            if (validTabs.includes(targetTab)) {
                localStorage.setItem('lastActiveTab', targetTab);
                console.log('Pestaña guardada:', targetTab);
            }
        });
    });

    // Restaurar última pestaña activa
    const lastTab = localStorage.getItem('lastActiveTab');
    if (lastTab && validTabs.includes(lastTab)) {
        const tabToActivate = document.querySelector(`button[data-bs-target="${lastTab}"]`);
        if (tabToActivate) {
            const tab = new bootstrap.Tab(tabToActivate);
            tab.show();
        }
    }

    // Inicializar tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Funcionalidad del modal de requerimiento
    initModalRequerimiento();
});

function initModalRequerimiento() {
    // Toggle para seleccionar/deseleccionar todos los items
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-check');

    if (selectAllCheckbox && itemCheckboxes.length > 0) {
        // Evento para seleccionar/deseleccionar todos
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSubmitButton();
        });
        
        // Actualizar "selectAll" si se cambia algún item individual
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedItems = document.querySelectorAll('.item-check:checked').length;
                const totalItems = itemCheckboxes.length;
                
                selectAllCheckbox.checked = checkedItems === totalItems;
                selectAllCheckbox.indeterminate = checkedItems > 0 && checkedItems < totalItems;
                
                updateSubmitButton();
            });
        });
    }

    // Validar formulario antes del envío
    const form = document.getElementById('formGenerarRequerimiento');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedItems = document.querySelectorAll('.item-check:checked');
            
            if (checkedItems.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un ítem para generar el requerimiento.');
                return false;
            }
            
            // Mostrar loading en el botón
            const submitBtn = document.getElementById('btnGenerarRequerimiento');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generando...';
                submitBtn.disabled = true;
            }
        });
    }
}

function updateSubmitButton() {
    const checkedItems = document.querySelectorAll('.item-check:checked');
    const submitBtn = document.getElementById('btnGenerarRequerimiento');
    
    if (submitBtn) {
        if (checkedItems.length === 0) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-shopping-cart me-1"></i> Seleccione ítems';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="fas fa-shopping-cart me-1"></i> Generar Requerimiento (${checkedItems.length})`;
        }
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    // Si tienes toastr configurado
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        // Fallback con alert
        alert(message);
    }
}

// Función para reiniciar pestañas (útil para debug)
function resetTabs() {
    localStorage.removeItem('lastActiveTab');
    const firstTab = document.querySelector('#cotizacionTabs button[data-bs-toggle="tab"]');
    if (firstTab) {
        const tab = new bootstrap.Tab(firstTab);
        tab.show();
    }
}

// Debug: Mostrar información de pestañas en consola
console.log('Pestañas disponibles:', document.querySelectorAll('#cotizacionTabs button[data-bs-toggle="tab"]').length);
console.log('Pestaña activa guardada:', localStorage.getItem('lastActiveTab'));
</script>
@endpush