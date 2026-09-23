@extends('admin.layouts.app')
@section('title', 'Recepcionar Orden')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-truck-loading text-info me-2"></i> Recepción en Proceso
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
                            <i class="fas fa-building text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Proveedor</p>
                            <h6 class="mb-0 fw-bold">{{ $orden->proveedor->nombre ?? 'Sin proveedor' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 mb-3">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-calendar text-info"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Fecha</p>
                            <h6 class="mb-0 fw-bold">{{ $orden->created_at->format('d/m/Y') }}</h6>
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
                            <i class="fas fa-warehouse text-success"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Almacén</p>
                            <h6 class="mb-0 fw-bold">{{ $orden->almacen->nombre ?? 'Sin almacén' }}</h6>
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
                            <i class="fas fa-boxes text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-1 small">Total Items</p>
                            <h6 class="mb-0 fw-bold">{{ $orden->detalles->sum('cantidad_en_compra') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Errores encontrados:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <form action="{{ route('admin.recepcion.store', $orden->id) }}" method="POST" id="recepcionForm">
            @csrf
            <input type="hidden" name="tipo_recepcion" id="tipoRecepcion" value="normal">
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-4">
                                    <i class="fas fa-cube me-2 text-primary"></i>Producto
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-shopping-cart me-2 text-primary"></i>Pedida
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-check me-2 text-primary"></i>Recibida
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-plus-circle me-2 text-primary"></i>A Recibir
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-clock me-2 text-primary"></i>Pendiente
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-chart-line me-2 text-primary"></i>Estado
                                </th>
                                <th class="py-3 px-4">
                                    <i class="fas fa-comment me-2 text-primary"></i>Observaciones
                                </th>
                                <th class="py-3 px-4 text-center">
                                    <i class="fas fa-undo me-2 text-primary"></i>Acciones
                                </th>
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
                                    @if($detalle->estado_recepcion != 'completo' && $detalle->estado_recepcion != 'completo_con_faltantes')
                                    <input type="number" 
                                           name="recepciones[{{ $detalle->id }}][cantidad_recibida]" 
                                           class="form-control text-center fw-bold cantidad-recibir" 
                                           min="0" 
                                           max="{{ $detalle->cantidad_en_compra - ($detalle->cantidad_recibida ?? 0) }}"
                                           value="0"
                                           data-detalle-id="{{ $detalle->id }}"
                                           data-cantidad-pedida="{{ $detalle->cantidad_en_compra }}"
                                           data-cantidad-anterior="{{ $detalle->cantidad_recibida ?? 0 }}"
                                           style="width: 100px; border-radius: 10px;"
                                           placeholder="0">
                                    @else
                                    <span class="text-muted">Completado</span>
                                    @endif
                                </td>
                                <td class="text-center py-4 px-4">
                                    <span id="pendiente-{{ $detalle->id }}" class="badge bg-warning px-3 py-2 fs-6">
                                        {{ $detalle->cantidad_en_compra - ($detalle->cantidad_recibida ?? 0) }}
                                    </span>
                                </td>
                                <td class="text-center py-4 px-4">
                                    <span id="estado-{{ $detalle->id }}" class="badge px-3 py-2 fs-6 
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
                                    @if($detalle->estado_recepcion != 'completo' && $detalle->estado_recepcion != 'completo_con_faltantes')
                                    <textarea name="recepciones[{{ $detalle->id }}][observaciones]" 
                                              class="form-control" 
                                              rows="2" 
                                              style="border-radius: 8px; resize: vertical;"
                                              placeholder="Notas sobre la recepción..."></textarea>
                                    @else
                                    <span class="text-muted small">Item completado</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($detalle->cantidad_recibida > 0)
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="mostrarModalDevolucion({{ $detalle->id }}, '{{ $detalle->nombre_producto }}', {{ $detalle->cantidad_recibida }})"
                                            title="Devolver items">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-light p-4" style="border-radius: 0 0 15px 15px;">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_recepcion" class="form-label fw-bold">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Fecha de Recepción
                        </label>
                        <input type="date" 
                               name="fecha_recepcion" 
                               id="fecha_recepcion" 
                               class="form-control" 
                               value="{{ date('Y-m-d') }}" 
                               style="border-radius: 10px;"
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="observaciones_generales" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-2 text-primary"></i>
                            Observaciones Generales
                        </label>
                        <textarea name="observaciones_generales" 
                                  id="observaciones_generales" 
                                  class="form-control" 
                                  rows="3" 
                                  style="border-radius: 10px; resize: vertical;"
                                  placeholder="Comentarios generales sobre la recepción..."></textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="{{ route('admin.recepcion.index') }}" 
                       class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Listado
                    </a>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        @if($orden->estado_recepcion != 'completo' && $orden->estado_recepcion != 'completo_con_faltantes')
                        <button type="button" 
                                class="btn btn-warning rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0" 
                                onclick="completarConFaltantes()">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Completar con Faltantes
                        </button>
                        @endif
                        
                        <button type="submit" 
                                class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                            <i class="fas fa-save me-2"></i>
                            Registrar Recepción
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Devolución -->
<div class="modal fade" id="modalDevolucion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-undo me-2 text-danger"></i>
                    Devolver Items
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.recepcion.devolver', $orden->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="detalle_id" id="detalleDevolucionId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Producto:</label>
                        <p id="productoDevolucionNombre" class="text-muted"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cantidadDevolver" class="form-label fw-bold">Cantidad a Devolver:</label>
                        <input type="number" 
                               name="cantidad_devolver" 
                               id="cantidadDevolver" 
                               class="form-control" 
                               min="1" 
                               style="border-radius: 10px;" 
                               required>
                        <small class="text-muted">Máximo: <span id="maxDevolucion"></span> items</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivoDevolucion" class="form-label fw-bold">Motivo de Devolución:</label>
                        <textarea name="motivo" 
                                  id="motivoDevolucion" 
                                  class="form-control" 
                                  rows="3" 
                                  style="border-radius: 10px;" 
                                  placeholder="Describe el motivo de la devolución..." 
                                  required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fechaDevolucion" class="form-label fw-bold">Fecha de Devolución:</label>
                        <input type="date" 
                               name="fecha_devolucion" 
                               id="fechaDevolucion" 
                               class="form-control" 
                               value="{{ date('Y-m-d') }}" 
                               style="border-radius: 10px;" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo me-2"></i>
                        Registrar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.cantidad-recibir');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const detalleId = this.dataset.detalleId;
            const cantidadPedida = parseInt(this.dataset.cantidadPedida);
            const cantidadAnterior = parseInt(this.dataset.cantidadAnterior);
            let cantidadRecibir = parseInt(this.value) || 0;
            
            // Validar que no exceda la cantidad pendiente
            const maxPermitido = cantidadPedida - cantidadAnterior;
            if (cantidadRecibir > maxPermitido) {
                this.value = maxPermitido;
                cantidadRecibir = maxPermitido;
                this.style.borderColor = '#dc3545';
                setTimeout(() => {
                    this.style.borderColor = '#ced4da';
                }, 1000);
            }
            
            const totalRecibido = cantidadAnterior + cantidadRecibir;
            const pendiente = cantidadPedida - totalRecibido;
            
            // Actualizar cantidad pendiente
            const pendienteElement = document.getElementById(`pendiente-${detalleId}`);
            pendienteElement.textContent = pendiente;
            
            // Actualizar estado visual
            const estadoSpan = document.getElementById(`estado-${detalleId}`);
            estadoSpan.classList.remove('bg-success', 'bg-info', 'bg-secondary');
            
            if (totalRecibido >= cantidadPedida) {
                estadoSpan.textContent = 'Completo';
                estadoSpan.classList.add('bg-success');
            } else if (totalRecibido > 0) {
                estadoSpan.textContent = 'Parcial';
                estadoSpan.classList.add('bg-info');
            } else {
                estadoSpan.textContent = 'Pendiente';
                estadoSpan.classList.add('bg-secondary');
            }
            
            // Efecto visual en el input
            if (cantidadRecibir > 0) {
                this.style.borderColor = '#198754';
                this.style.backgroundColor = 'rgba(25, 135, 84, 0.05)';
            } else {
                this.style.borderColor = '#ced4da';
                this.style.backgroundColor = 'white';
            }
        });
    });

    // Validación antes de envío
    document.getElementById('recepcionForm').addEventListener('submit', function(e) {
        const inputs = document.querySelectorAll('.cantidad-recibir');
        let hayRecepciones = false;
        
        inputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                hayRecepciones = true;
            }
        });
        
        if (!hayRecepciones && document.getElementById('tipoRecepcion').value === 'normal') {
            e.preventDefault();
            
            // Crear alerta usando Bootstrap
            const alertHTML = `
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Debe ingresar al menos una cantidad a recibir mayor a 0 o completar con faltantes.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insertar alerta
            const container = document.querySelector('.container-fluid');
            const firstCard = container.querySelector('.card');
            firstCard.insertAdjacentHTML('beforebegin', alertHTML);
            
            // Scroll suave hacia arriba
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            return false;
        }
        
        // Mostrar indicador de carga
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
        submitBtn.disabled = true;
    });
});

// Función para completar con faltantes
function completarConFaltantes() {
    if (confirm('¿Está seguro de marcar esta orden como completa con items faltantes? Esta acción no se puede deshacer.')) {
        const motivo = prompt('Ingrese el motivo de los items faltantes:');
        if (motivo && motivo.trim()) {
            document.getElementById('tipoRecepcion').value = 'completa_con_faltantes';
            
            // Crear input hidden para el motivo
            const motivoInput = document.createElement('input');
            motivoInput.type = 'hidden';
            motivoInput.name = 'motivo_faltantes';
            motivoInput.value = motivo.trim();
            document.getElementById('recepcionForm').appendChild(motivoInput);
            
            document.getElementById('recepcionForm').submit();
        }
    }
}

// Función para mostrar modal de devolución
function mostrarModalDevolucion(detalleId, nombreProducto, cantidadRecibida) {
    document.getElementById('detalleDevolucionId').value = detalleId;
    document.getElementById('productoDevolucionNombre').textContent = nombreProducto;
    document.getElementById('maxDevolucion').textContent = cantidadRecibida;
    document.getElementById('cantidadDevolver').max = cantidadRecibida;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalDevolucion'));
    modal.show();
}
</script>
@endpush
@endsection