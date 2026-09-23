@extends('admin.layouts.app')

@section('title', 'Detalles de Cotización')

@section('header')
@endsection

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-file-invoice-dollar text-info me-2"></i> Resumen Comercial
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 d-flex align-items-center flex-wrap">
                    Cotización #{{ $cotizacion->codigo }}
                    <span class="badge bg-{{ $cotizacion->estado->color ?? 'secondary' }} bg-opacity-50 text-white border border-white border-opacity-25 rounded-pill fs-6 ms-0 ms-md-3 mt-2 mt-md-0 py-2 px-3 fw-medium backdrop-blur">
                        <span class="d-inline-block rounded-circle bg-white me-2" style="width: 8px; height: 8px;"></span>{{ $cotizacion->estado->nombre ?? 'Sin estado' }}
                    </span>
                </h2>
                <p class="text-white-50 mb-0 mt-2">
                    <i class="far fa-calendar-alt me-1"></i> {{ $cotizacion->created_at->format('d M, Y H:i') }}
                    <span class="mx-2">|</span>
                    <i class="far fa-user me-1"></i> Asesor: <span class="text-white">{{ $cotizacion->usuario ? $cotizacion->usuario->name : 'No asignado' }}</span>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0">
                <a href="{{ route('admin.ventas.cotizaciones.index') }}" class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105">
                    <i class="fas fa-arrow-left me-2"></i> Volver a Cotizaciones
                </a>
                <a href="{{ route('admin.ventas.cotizaciones.edit', $cotizacion) }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105" style="border: 1px solid rgba(255,255,255,0.8);">
                    <i class="fas fa-edit me-2 text-primary"></i> Editar Cotización
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
    <div class="row">
        <!-- Panel izquierdo - Información de cliente y detalles -->
        <div class="col-lg-8 mb-4">
            <!-- Tarjeta de información del cliente -->
            <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span class="avatar-circle bg-primary text-white">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-0 fw-semibold">
                                @if($cotizacion->cliente->tipo_cliente === 'natural')
                                    {{ $cotizacion->cliente->nombres }} {{ $cotizacion->cliente->apellido_paterno }}
                                @else
                                    {{ $cotizacion->cliente->razon_social }}
                                @endif
                            </h5>
                            <p class="text-muted mb-0 small">
                                {{ $cotizacion->cliente->tipo_cliente === 'natural' ? 'DNI' : 'RUC' }}: 
                                {{ $cotizacion->cliente->documento_identidad }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-phone-alt text-primary"></i>
                                </div>
                                <div class="contact-info-text">
                                    <h6 class="mb-0">Teléfono</h6>
                                    <p class="mb-0">{{ $cotizacion->cliente->telefono ?? 'No especificado' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div class="contact-info-text">
                                    <h6 class="mb-0">Email</h6>
                                    <p class="mb-0">{{ $cotizacion->cliente->email ?? 'No especificado' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div class="contact-info-text">
                                    <h6 class="mb-0">Dirección</h6>
                                    <p class="mb-0">{{ $cotizacion->cliente->direccion ?? 'No especificada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de cotización -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-cog me-2 text-primary"></i> Configuración Principal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-warehouse text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Almacén</h6>
                                </div>
                                <p class="mb-0 ps-4">{{ $cotizacion->almacen->nombre ?? 'No especificado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-tag text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Condición</h6>
                                </div>
                                <p class="mb-0 ps-4">{{ $cotizacion->condicion }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-bullhorn text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Canal</h6>
                                </div>
                                <p class="mb-0 ps-4">{{ $cotizacion->canal }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-coins text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Moneda</h6>
                                </div>
                                <p class="mb-0 ps-4">{{ $cotizacion->moneda }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-credit-card text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Forma de Pago</h6>
                                </div>
                                <p class="mb-0 ps-4">{{ $cotizacion->forma_pago }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="config-info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                        <i class="fas fa-calendar-check text-primary"></i>
                                    </span>
                                    <h6 class="mb-0 fw-semibold">Validez</h6>
                                </div>
                                <p class="mb-0 ps-4">
                                    {{ $cotizacion->fecha_validez ? $cotizacion->fecha_validez->format('d/m/Y') : 'No especificada' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($cotizacion->datos_adicionales)
                    <div class="mt-4">
                        <div class="alert alert-light mb-0">
                            <div class="d-flex align-items-center mb-2">
                                <span class="config-icon bg-primary-subtle rounded p-1 me-2">
                                    <i class="fas fa-clipboard-list text-primary"></i>
                                </span>
                                <h6 class="mb-0 fw-semibold">Notas Adicionales</h6>
                            </div>
                            <p class="mb-0 ps-4">{{ $cotizacion->datos_adicionales }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Detalle de ítems -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="fas fa-box me-2 text-primary"></i> Detalle de Ítems
                        </h5>
                        <span class="badge bg-primary rounded-pill">
                            {{ $cotizacion->detalles ? $cotizacion->detalles->count() : 0 }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-3">Tipo</th>
                                    <th class="border-0">Descripción</th>
                                    <th class="border-0 text-end">Cantidad</th>
                                    <th class="border-0 text-end">P. Unitario</th>
                                    <th class="border-0 text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cotizacion->detalles as $detalle)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-{{ $detalle->tipo === 'repuestos' ? 'info' : ($detalle->tipo === 'vehiculos' ? 'primary' : 'success') }} rounded-pill">
                                            {{ ucfirst($detalle->tipo ?? 'Sin tipo') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $detalle->descripcion ?? 'Sin descripción' }}</span>
                                            @if($detalle->unidad)
                                            <small class="text-muted">Unidad: {{ $detalle->unidad }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $detalle->cantidad ?? 0 }}</td>
                                    <td class="text-end">
                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                        {{ number_format($detalle->precio_unitario ?? 0, 2) }}
                                    </td>
                                    <td class="text-end pe-3 fw-semibold">
                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                        {{ number_format(($detalle->cantidad ?? 0) * ($detalle->precio_unitario ?? 0), 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTkgMkgxN1YxSDE1VjJIN1YxSDVWMkgzQzEuOSAyIDEgMi45IDEgNFYyMEMyIDIxLjEgMi45IDIyIDQgMjJIMjBDMjEuMSAyMiAyMiAyMS4xIDIyIDIwVjRDMjIgMi45IDIxLjEgMiAyMCAySDE5Wk0xOSA0SDE5LjVWNkgxNi41VjRIMTkuNVpNOCA0SDkuNVY2SDYuNVY0SDkuNVpNMjAgMjBINC41QzQuMiAyMCA0IDIxIDQgMTlWOEgyMFYxOUMyMCAyMC4xIDE5LjkgMjAgMjAgMjBaIiBmaWxsPSIjOTk5Ii8+PC9zdmc+" width="64" height="64" class="d-block mx-auto mb-2 opacity-50" alt="Empty box">
                                        <p>No hay ítems en esta cotización</p>
                                    </td>
                                </tr>
                                @endforelse
                                
                                <!-- Totales -->
                                <tr class="bg-light">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="text-end fw-semibold border-0">Subtotal:</td>
                                    <td class="text-end pe-3 fw-semibold border-0">
                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                        {{ number_format($cotizacion->subtotal ?? 0, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="text-end fw-semibold border-0">IGV (18%):</td>
                                    <td class="text-end pe-3 fw-semibold border-0">
                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                        {{ number_format($cotizacion->impuestos ?? 0, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="text-end fw-bold border-0 text-primary">TOTAL:</td>
                                    <td class="text-end pe-3 fw-bold border-0 text-primary">
                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}
                                        {{ number_format($cotizacion->total ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho - Seguimientos y acciones -->
        <div class="col-lg-4">
            <!-- Tarjeta de acciones -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-cogs me-2 text-primary"></i> Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.ventas.cotizaciones.index') }}" 
                           class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                        
                        <a href="{{ route('admin.ventas.cotizaciones.edit', $cotizacion) }}" 
                           class="btn btn-outline-info d-flex align-items-center justify-content-center">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        
                        @if($cotizacion->estado && $cotizacion->estado->nombre !== 'Convertida')
                        <form action="{{ route('admin.ventas.cotizaciones.cambiar-estado', $cotizacion) }}" method="POST">
                            @csrf
                            <input type="hidden" name="estado_id" value="{{ $estadoConvertidaId }}">
                            <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle me-2"></i> Marcar como Convertida
                            </button>
                        </form>
                        @endif
                        
                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Imprimir
                        </button>
                        
                        <a href="{{ route('admin.ventas.cotizaciones.gestionar', $cotizacion) }}" 
                           class="btn btn-primary d-flex align-items-center justify-content-center">
                            <i class="fas fa-tasks me-2"></i> Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de seguimientos -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="fas fa-history me-2 text-primary"></i> Seguimientos
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalSeguimiento">
                            <i class="fas fa-plus me-1"></i> Nuevo
                        </button>
                    </div>
                </div>
                <div class="card-body timeline-container p-0">
                    @if($cotizacion->seguimientos && $cotizacion->seguimientos->count() > 0)
                    <div class="modern-timeline p-3">
                        @foreach($cotizacion->seguimientos->sortByDesc('fecha_seguimiento') as $seguimiento)
                        <div class="modern-timeline-item mb-4">
                            <div class="modern-timeline-badge 
                                @if($seguimiento->tipo === 'nota') bg-warning
                                @elseif($seguimiento->tipo === 'llamada') bg-success
                                @elseif($seguimiento->tipo === 'reunion') bg-primary
                                @elseif($seguimiento->tipo === 'email') bg-info
                                @else bg-secondary
                                @endif">
                                @if($seguimiento->tipo === 'nota')
                                <i class="fas fa-sticky-note"></i>
                                @elseif($seguimiento->tipo === 'llamada')
                                <i class="fas fa-phone-alt"></i>
                                @elseif($seguimiento->tipo === 'reunion')
                                <i class="fas fa-handshake"></i>
                                @elseif($seguimiento->tipo === 'email')
                                <i class="fas fa-envelope"></i>
                                @else
                                <i class="fas fa-comment"></i>
                                @endif
                            </div>
                            <div class="modern-timeline-panel">
                                <div class="modern-timeline-header">
                                    <span class="badge 
                                        @if($seguimiento->tipo === 'nota') bg-warning-subtle text-warning
                                        @elseif($seguimiento->tipo === 'llamada') bg-success-subtle text-success
                                        @elseif($seguimiento->tipo === 'reunion') bg-primary-subtle text-primary
                                        @elseif($seguimiento->tipo === 'email') bg-info-subtle text-info
                                        @else bg-secondary-subtle text-secondary
                                        @endif rounded-pill text-capitalize">
                                        {{ $seguimiento->tipo }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        {{ $seguimiento->fecha_seguimiento ? $seguimiento->fecha_seguimiento->format('d M, Y H:i') : '' }}
                                    </small>
                                </div>
                                <div class="modern-timeline-body mt-2">
                                    <p class="mb-1">{{ $seguimiento->contenido }}</p>
                                    <footer class="blockquote-footer mt-1 mb-0">
                                        <cite title="Usuario">{{ $seguimiento->usuario?->name ?? 'Usuario no especificado' }}</cite>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTYgMkg4QzcuNDQ3NzIgMiA3IDIuNDQ3NzIgNyAzVjE2QzcgMTYuNTUyMyA3LjQ0NzcyIDE3IDggMTdIMjBDMjAuNTUyMyAxNyAyMSAxNi41NTIzIDIxIDE2VjdDMjEgNi40NDc3MiAyMC41NTIzIDYgMjAgNkgxN1YzQzE3IDIuNDQ3NzIgMTYuNTUyMyAyIDE2IDJaIiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0xNiA2LjAxMDM3TDIxIDYuMDEwMzciIHN0cm9rZT0iIzk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHBhdGggZD0iTTMgOEgxN1YyMUgzVjhaIiBmaWxsPSIjRTdFN0U3IiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==" 
                             width="80" height="80" alt="No hay seguimientos" class="opacity-50 mb-3">
                        <p class="text-muted mb-0">No hay seguimientos registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSeguimientoLabel">
                    <i class="fas fa-plus-circle me-2"></i> Agregar Seguimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.ventas.cotizaciones.seguimiento.agregar', $cotizacion) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo" class="form-label fw-medium">Tipo de seguimiento</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoNota" value="nota" checked>
                                <label class="form-check-label" for="tipoNota">
                                    <i class="fas fa-sticky-note text-warning me-1"></i> Nota
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoLlamada" value="llamada">
                                <label class="form-check-label" for="tipoLlamada">
                                    <i class="fas fa-phone-alt text-success me-1"></i> Llamada
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoReunion" value="reunion">
                                <label class="form-check-label" for="tipoReunion">
                                    <i class="fas fa-handshake text-primary me-1"></i> Reunión
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoEmail" value="email">
                                <label class="form-check-label" for="tipoEmail">
                                    <i class="fas fa-envelope text-info me-1"></i> Email
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoOtro" value="otro">
                                <label class="form-check-label" for="tipoOtro">
                                    <i class="fas fa-comment text-secondary me-1"></i> Otro
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contenido" class="form-label fw-medium">Detalle del seguimiento</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="4" placeholder="Escriba los detalles del seguimiento..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_seguimiento" class="form-label fw-medium">Fecha y hora</label>
                        <input type="datetime-local" class="form-control" id="fecha_seguimiento" name="fecha_seguimiento" 
                               value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="recordatorio" name="recordatorio">
                        <label class="form-check-label" for="recordatorio">
                            Crear recordatorio para seguimiento posterior
                        </label>
                    </div>
                    
                    <div class="mt-3 d-none" id="divRecordatorio">
                        <label for="fecha_recordatorio" class="form-label fw-medium">Fecha de recordatorio</label>
                        <input type="datetime-local" class="form-control" id="fecha_recordatorio" name="fecha_recordatorio" 
                               value="{{ now()->addDays(3)->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Seguimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos generales */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .config-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Estilos para la información de contacto */
    .contact-info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .contact-info-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #f0f9ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .contact-info-text h6 {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .contact-info-text p {
        font-weight: 500;
    }

    /* Timeline moderna para seguimientos */
    .timeline-container {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    
    .timeline-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .timeline-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .timeline-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .modern-timeline {
        position: relative;
        padding-left: 40px;
    }
    
    .modern-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 19px;
        width: 2px;
        background: #e9ecef;
    }
    
    .modern-timeline-item {
        position: relative;
    }
    
    .modern-timeline-badge {
        position: absolute;
        top: 0;
        left: -40px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 2px solid white;
        z-index: 1;
    }
    
    .modern-timeline-panel {
        background: white;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
        margin-left: 15px;
        position: relative;
    }
    
    .modern-timeline-panel::before {
        content: '';
        position: absolute;
        top: 16px;
        left: -8px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid white;
        z-index: 1;
    }
    
    .modern-timeline-header {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .modern-timeline-body {
        font-size: 0.95rem;
    }

    /* Estilos para impresión */
    @media print {
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header, .btn, .modal, .timeline-container {
            display: none !important;
        }
        
        .modern-timeline::before {
            display: none !important;
        }
        
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
        }
        
        .col-lg-8 {
            width: 100% !important;
        }
        
        .col-lg-4 {
            display: none !important;
        }
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .avatar-circle {
            width: 36px;
            height: 36px;
        }
        
        .modern-timeline {
            padding-left: 30px;
        }
        
        .modern-timeline::before {
            left: 14px;
        }
        
        .modern-timeline-badge {
            width: 28px;
            height: 28px;
            left: -30px;
            font-size: 0.8rem;
        }
        
        .modern-timeline-panel {
            margin-left: 5px;
            padding: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar datepicker para fecha de seguimiento
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#fecha_seguimiento", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            defaultDate: new Date()
        });
        
        flatpickr("#fecha_recordatorio", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            defaultDate: new Date(new Date().getTime() + (3 * 24 * 60 * 60 * 1000)) // +3 días
        });
    }
    
    // Mostrar/ocultar campo de fecha de recordatorio
    $('#recordatorio').change(function() {
        if($(this).is(':checked')) {
            $('#divRecordatorio').removeClass('d-none');
        } else {
            $('#divRecordatorio').addClass('d-none');
        }
    });
    
    // Inicializar tooltips
    var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush