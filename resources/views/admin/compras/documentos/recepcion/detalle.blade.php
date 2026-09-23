@extends('admin.layouts.app')
@section('title', 'Detalle de Recepción')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-list-alt text-info me-2"></i> Reporte Detallado
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Recepción #{{ $orden->codigo }}
                </h2>
                <div class="d-flex align-items-center mt-2">
                    @php
                        $estado = $orden->estado_recepcion ?? 'pendiente';
                        $badgeClass = $estado == 'completo' ? 'success' : 
                                    ($estado == 'completo_con_faltantes' ? 'warning' : 
                                    ($estado == 'parcial' ? 'info' : 'secondary'));
                    @endphp
                    <span class="badge rounded-pill bg-{{ $badgeClass }} px-3 py-2 fw-bold shadow-sm">
                        <i class="fas fa-circle me-1 small"></i> {{ $estado == 'completo_con_faltantes' ? 'COMPLETO CON FALTANTES' : strtoupper($estado) }}
                    </span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($orden->estado_recepcion != 'completo' && $orden->estado_recepcion != 'completo_con_faltantes')
                <a href="{{ route('admin.recepcion.show', $orden->id) }}" class="btn bg-primary text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-edit me-2"></i> Continuar Recepción
                </a>
                @endif
                <a href="{{ route('admin.recepcion.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <!-- Info Section -->
    <div class="row mb-4">
        <div class="col-12 col-lg-3 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-boxes text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Items Pedidos</p>
                            <h4 class="mb-0 fw-bold text-primary">{{ $orden->detalles->sum('cantidad_en_compra') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Items Recibidos</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $orden->detalles->sum('cantidad_recibida') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-undo text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Items Devueltos</p>
                            <h4 class="mb-0 fw-bold text-warning">
                                {{ $orden->detalles->sum(function($detalle) { 
                                    return $detalle->devoluciones->sum('cantidad_devuelta'); 
                                }) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-secondary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-clock text-secondary"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Items Pendientes</p>
                            <h4 class="mb-0 fw-bold text-secondary">
                                {{ $orden->detalles->sum('cantidad_en_compra') - $orden->detalles->sum('cantidad_recibida') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-header border-0 pb-0">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-list me-2 text-primary"></i>
                Detalle por Producto
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">Producto</th>
                            <th class="py-3 px-4 text-center">Pedida</th>
                            <th class="py-3 px-4 text-center">Recibida</th>
                            <th class="py-3 px-4 text-center">Devuelta</th>
                            <th class="py-3 px-4 text-center">Pendiente</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-center">Recepciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->detalles as $detalle)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="py-4 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            {{ strtoupper(substr($detalle->nombre_producto, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $detalle->nombre_producto }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-barcode me-1"></i>{{ $detalle->codigo }}
                                        </p>
                                        <span class="badge bg-light text-dark">{{ ucfirst($detalle->tipo_item) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="badge bg-primary px-3 py-2 fs-6">
                                    {{ $detalle->cantidad_en_compra }}
                                </span>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="badge bg-success px-3 py-2 fs-6">
                                    {{ $detalle->cantidad_recibida ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="badge bg-warning px-3 py-2 fs-6">
                                    {{ $detalle->devoluciones->sum('cantidad_devuelta') }}
                                </span>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="badge bg-secondary px-3 py-2 fs-6">
                                    {{ $detalle->cantidad_en_compra - ($detalle->cantidad_recibida ?? 0) }}
                                </span>
                            </td>
                            <td class="text-center py-4 px-4">
                                <span class="badge px-3 py-2 fs-6 
                                    @if($detalle->estado_recepcion == 'completo') bg-success
                                    @elseif($detalle->estado_recepcion == 'completo_con_faltantes') bg-warning
                                    @elseif($detalle->estado_recepcion == 'parcial') bg-info
                                    @else bg-secondary
                                    @endif">
                                    @if($detalle->estado_recepcion == 'completo_con_faltantes')
                                        Completo c/Faltantes
                                    @else
                                        {{ ucfirst($detalle->estado_recepcion ?? 'pendiente') }}
                                    @endif
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($detalle->recepciones->count() > 0)
                                    <button class="btn btn-outline-primary btn-sm" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#recepciones-{{ $detalle->id }}">
                                        <i class="fas fa-eye me-1"></i>
                                        Ver {{ $detalle->recepciones->count() }} recepciones
                                    </button>
                                @else
                                    <span class="text-muted">Sin recepciones</span>
                                @endif
                            </td>
                        </tr>
                        
                        @if($detalle->recepciones->count() > 0)
                        <tr>
                            <td colspan="7" class="p-0" style="border: none;">
                                <div class="collapse" id="recepciones-{{ $detalle->id }}">
                                    <div class="p-4" style="background: #f8f9fa;">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-history me-2"></i>
                                            Historial de Recepciones
                                        </h6>
                                        <div class="row">
                                            @foreach($detalle->recepciones as $recepcion)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-success">
                                                                {{ $recepcion->cantidad_recibida }} items
                                                            </span>
                                                            <small class="text-muted">
                                                                {{ $recepcion->fecha_recepcion->format('d/m/Y') }}
                                                            </small>
                                                        </div>
                                                        <p class="mb-2">
                                                            <strong>Recibido por:</strong> {{ $recepcion->recibidoPor->name }}
                                                        </p>
                                                        @if($recepcion->observaciones)
                                                        <p class="mb-0 small text-muted">
                                                            <strong>Observaciones:</strong> {{ $recepcion->observaciones }}
                                                        </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($detalle->devoluciones->count() > 0)
                                        <h6 class="fw-bold mb-3 mt-4">
                                            <i class="fas fa-undo me-2"></i>
                                            Historial de Devoluciones
                                        </h6>
                                        <div class="row">
                                            @foreach($detalle->devoluciones as $devolucion)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-warning">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-warning">
                                                                {{ $devolucion->cantidad_devuelta }} items devueltos
                                                            </span>
                                                            <small class="text-muted">
                                                                {{ $devolucion->fecha_devolucion->format('d/m/Y') }}
                                                            </small>
                                                        </div>
                                                        <p class="mb-2">
                                                            <strong>Devuelto por:</strong> {{ $devolucion->devueltoPor->name }}
                                                        </p>
                                                        <p class="mb-0 small">
                                                            <strong>Motivo:</strong> {{ $devolucion->motivo }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.recepcion.index') }}" 
           class="btn btn-secondary btn-lg px-4 py-2" 
           style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al Listado
        </a>
        
        @if($orden->estado_recepcion != 'completo' && $orden->estado_recepcion != 'completo_con_faltantes')
        <a href="{{ route('admin.recepcion.show', $orden->id) }}" 
           class="btn btn-primary btn-lg px-4 py-2" 
           style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <i class="fas fa-edit me-2"></i>
            Continuar Recepción
        </a>
        @endif
    </div>
</div>
@endsection