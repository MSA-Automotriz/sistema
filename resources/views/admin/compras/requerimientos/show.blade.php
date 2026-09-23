@extends('admin.layouts.app')

@section('title', 'Detalles del Requerimiento')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-info-circle text-info me-2"></i> Detalle de Requerimiento
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Requerimiento #{{ $requerimiento->id }}
                </h2>
                <div class="d-flex align-items-center mt-2">
                    @if(isset($requerimiento->estado))
                        <span class="badge rounded-pill bg-{{ $requerimiento->estado->color ?? 'secondary' }} px-3 py-2 fw-bold shadow-sm">
                            <i class="fas fa-circle me-1 small"></i> {{ strtoupper($requerimiento->estado->nombre ?? 'SIN ESTADO') }}
                        </span>
                    @else
                        <span class="badge rounded-pill bg-secondary px-3 py-2 fw-bold shadow-sm">
                            SIN ESTADO
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.ordenes.create', ['requerimiento_id' => $requerimiento->id]) }}" class="btn bg-success text-white rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-shopping-cart me-2"></i> Crear OC
                </a>
                <a href="{{ route('admin.compras.requerimientos.edit', $requerimiento) }}" class="btn bg-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-edit me-2"></i> Editar
                </a>
                <a href="{{ route('admin.compras.requerimientos.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row">
    <div class="col-12">
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Información General -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Datos Generales</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Código:</dt>
                                <dd class="col-sm-8">{{ $requerimiento->id }}</dd>

                                <dt class="col-sm-4">Tipo de requerimiento:</dt>
                                <dd class="col-sm-8">{{ strtoupper($requerimiento->tipo ?? 'INVENTARIO') }}</dd>

                                <dt class="col-sm-4">Almacén destino:</dt>
                                <dd class="col-sm-8">{{ $requerimiento->almacen->nombre ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Requerido por:</dt>
                                <dd class="col-sm-8">{{ $requerimiento->user->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Proveedor sugerido:</dt>
                                <dd class="col-sm-8">
                                    @if($requerimiento->proveedor)
                                        {{ $requerimiento->proveedor->nombre_completo }}
                                        @if($requerimiento->proveedor->numero_documento)
                                            ({{ $requerimiento->proveedor->documento_formateado }})
                                        @endif
                                    @else
                                        No especificado
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Comentario:</dt>
                                <dd class="col-sm-8">{{ $requerimiento->comentario ?? 'Sin comentarios' }}</dd>

                                <dt class="col-sm-4">Estado:</dt>
                                <dd class="col-sm-8">
                                    @if(isset($requerimiento->estado))
                                        <span class="badge bg-{{ $requerimiento->estado->color ?? 'secondary' }}">
                                            {{ $requerimiento->estado->nombre ?? 'Sin estado' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin estado</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Ordenes de Compra</h5>
                            @if(method_exists($requerimiento, 'ordenesCompra') && $requerimiento->ordenesCompra && $requerimiento->ordenesCompra->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Orden</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($requerimiento->ordenesCompra as $orden)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.compras.ordenes.show', $orden->id) }}">
                                                            {{ $orden->codigo ?? $orden->id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @php
                                                            $estadoColor = 'secondary';
                                                            $estadoNombre = 'Sin estado';
                                                            
                                                            if (is_object($orden->estado)) {
                                                                $estadoColor = $orden->estado->color ?? 'secondary';
                                                                $estadoNombre = $orden->estado->nombre;
                                                            } elseif (is_string($orden->estado)) {
                                                                $estadoColor = $orden->estado == 'en espera' ? 'warning' : 
                                                                            ($orden->estado == 'aprobada' ? 'success' : 
                                                                            ($orden->estado == 'rechazada' ? 'danger' : 'secondary'));
                                                                $estadoNombre = ucfirst($orden->estado);
                                                            }
                                                        @endphp
                                                        <span class="badge bg-{{ $estadoColor }}">
                                                            {{ $estadoNombre }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No hay órdenes de compra</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detalles del Requerimiento -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Detalle del Requerimiento</h5>
                    <div class="table-responsive">
                        <table class="table table-hover" :class="darkMode ? 'table-dark' : ''">
                            <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                                <tr>
                                    <th class="small text-uppercase">Nro.</th>
                                    <th class="small text-uppercase">Código</th>
                                    <th class="small text-uppercase">Nombre del producto</th>
                                    <th class="small text-uppercase">Tipo</th>
                                    <th class="small text-uppercase">Cantidad requerida</th>
                                    <th class="small text-uppercase">Cantidad en compra</th>
                                    <th class="small text-uppercase">Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($requerimiento->detalles && $requerimiento->detalles->count() > 0)
                                    @foreach($requerimiento->detalles as $index => $detalle)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detalle->codigo ?? 'N/A' }}</td>
                                            <td>{{ $detalle->nombre ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($detalle->tipo_item ?? 'N/A') }}</td>
                                            <td>{{ $detalle->cantidad ?? 0 }}</td>
                                            <td>{{ $detalle->cantidad_compra ?? 0 }}</td>
                                            <td>{{ $detalle->unidad ?? 'UND' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No hay productos en este requerimiento.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($requerimiento, 'historialActualizaciones'))
                <!-- Historial de Actualizaciones -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3" :class="darkMode ? 'text-light' : 'text-dark'">Historial de Actualizaciones</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($requerimiento->historialActualizaciones && $requerimiento->historialActualizaciones->count() > 0)
                                    @foreach($requerimiento->historialActualizaciones as $historial)
                                        <tr>
                                            <td>{{ $historial->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $historial->user->name ?? 'Usuario desconocido' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $historial->estado_color ?? 'secondary' }}">
                                                    {{ $historial->estado_nombre ?? 'Estado desconocido' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">No hay historial de actualizaciones</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

    </div>
</div>
</div>
</div>
@endsection