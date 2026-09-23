@php
    \Log::info('Contenido de Orden de Trabajo - Diagnóstico Detallado', [
        'cotizacion_id' => $cotizacion->id,
        'orden_trabajo_exists' => $cotizacion->orden_trabajo ? true : false,
        'orden_trabajo_data' => $cotizacion->orden_trabajo ? $cotizacion->orden_trabajo->toArray() : 'No existe',
        'vista_file_path' => __FILE__, // Muestra la ruta exacta del archivo de vista
    ]);
@endphp
<div class="card border shadow-none mb-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0 fw-semibold">
            <i class="fas fa-tools me-2 text-primary"></i> Orden de Trabajo
        </h5>
    </div>
    <div class="card-body">
        @if($cotizacion->orden_trabajo)
            <!-- Mostrar orden existente -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Estado:</label>
                        <span class="badge bg-{{ $cotizacion->orden_trabajo->estado === 'completado' ? 'success' : ($cotizacion->orden_trabajo->estado === 'en_progreso' ? 'primary' : 'warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $cotizacion->orden_trabajo->estado)) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha inicio:</label>
                        <p>{{ $cotizacion->orden_trabajo->fecha_inicio->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha estimada fin:</label>
                        <p>{{ $cotizacion->orden_trabajo->fecha_fin_estimada ? $cotizacion->orden_trabajo->fecha_fin_estimada->format('d/m/Y H:i') : 'No especificada' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Descripción:</label>
                <div class="border p-3 rounded bg-light">
                    {!! nl2br(e($cotizacion->orden_trabajo->descripcion)) !!}
                </div>
            </div>
            
            <div class="text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarOrdenModal">
                    <i class="fas fa-edit me-1"></i> Editar Orden
                </button>
            </div>
            
            @include('admin.ventas.cotizaciones.proceso.orden-trabajo.modals.editar', ['orden' => $cotizacion->orden_trabajo])
        @else
            <!-- Mostrar formulario de creación -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No se ha creado una orden de trabajo para esta cotización.
            </div>
            
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearOrdenModal">
                <i class="fas fa-plus me-1"></i> Crear Orden de Trabajo
            </button>
            
            @include('admin.ventas.cotizaciones.proceso.orden-trabajo.modals.crear')
        @endif
        
        <!-- Historial -->
        <div class="mt-4">
            <h6 class="fw-semibold mb-3">
                <i class="fas fa-history me-2"></i> Historial
            </h6>
            
            @if($cotizacion->orden_trabajo && $cotizacion->orden_trabajo->historial && $cotizacion->orden_trabajo->historial->count() > 0)
                <div class="timeline-container">
                    @foreach($cotizacion->orden_trabajo->historial as $evento)
                        <div class="timeline-item mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $evento->usuario ? $evento->usuario->name : 'Usuario desconocido' }}</strong>
                                <small class="text-muted">{{ $evento->fecha->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="d-flex">
                                <span class="badge bg-secondary me-2">{{ ucfirst($evento->accion) }}</span>
                                <p class="mb-0">{{ $evento->descripcion }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light">
                    No hay historial registrado.
                </div>
            @endif
        </div>
    </div>
</div>