@extends('admin.layouts.app')

@section('title', 'Tipos de Cambio')

@push('styles')
<style>
    .tipo-cambio-actual {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }
    
    .dashboard-card {
        border: none;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        border-radius: 0.5rem;
    }
    
    .badge-origen-sunat {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }
    
    .badge-origen-manual {
        background: linear-gradient(45deg, #6c757d, #495057);
        color: white;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Tipos de Cambio</h1>
            <p class="text-muted mb-0">Administra los tipos de cambio USD-PEN con integración SUNAT</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" id="obtenerSunatBtn">
                <i class="fas fa-download me-1"></i>Obtener de SUNAT
            </button>
            <a href="{{ route('admin.configuracion.tipos-cambio.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Nuevo Tipo de Cambio
            </a>
        </div>
    </div>

    <!-- Tipo de cambio actual -->
    @if($tipoCambioActual)
        <div class="card tipo-cambio-actual mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-star me-2"></i>Tipo de Cambio Actual
                        </h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h5 mb-1">{{ $tipoCambioActual->fecha->format('d/m/Y') }}</div>
                                    <small class="opacity-75">Fecha</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-1">S/ {{ number_format($tipoCambioActual->compra, 4) }}</div>
                                    <small class="opacity-75">Compra</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h4 mb-1">S/ {{ number_format($tipoCambioActual->venta, 4) }}</div>
                                    <small class="opacity-75">Venta</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h6 mb-1">{{ $tipoCambioActual->origen_texto }}</div>
                                    <small class="opacity-75">Origen</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <div class="d-flex flex-column align-items-lg-end">
                            <div class="badge bg-light text-dark mb-2">
                                <i class="fas fa-calendar-check me-1"></i>
                                Vigente desde {{ $tipoCambioActual->fecha_inicio->format('d/m/Y') }}
                            </div>
                            @if($tipoCambioActual->fecha_fin)
                                <div class="badge bg-warning">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Hasta {{ $tipoCambioActual->fecha_fin->format('d/m/Y') }}
                                </div>
                            @else
                                <div class="badge bg-success">
                                    <i class="fas fa-infinity me-1"></i>
                                    Sin fecha límite
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No hay ningún tipo de cambio activo. 
            <a href="{{ route('admin.configuracion.tipos-cambio.create') }}" class="alert-link">Crear uno nuevo</a>
        </div>
    @endif

    <!-- Historial de tipos de cambio -->
    <div class="card dashboard-card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-history text-primary me-2"></i>
                    <h5 class="mb-0">Historial de Tipos de Cambio</h5>
                </div>
                <span class="badge bg-primary">{{ $tiposCambio->total() }} registros</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($tiposCambio->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="100">Fecha</th>
                                <th>Compra</th>
                                <th>Venta</th>
                                <th>Vigencia</th>
                                <th>Origen</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th width="150" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tiposCambio as $tipo)
                                <tr>
                                    <td class="fw-semibold">{{ $tipo->fecha->format('d/m/Y') }}</td>
                                    <td>S/ {{ number_format($tipo->compra, 4) }}</td>
                                    <td>S/ {{ number_format($tipo->venta, 4) }}</td>
                                    <td>
                                        <div class="small">
                                            <div><strong>Desde:</strong> {{ $tipo->fecha_inicio->format('d/m/Y') }}</div>
                                            @if($tipo->fecha_fin)
                                                <div><strong>Hasta:</strong> {{ $tipo->fecha_fin->format('d/m/Y') }}</div>
                                            @else
                                                <div class="text-muted">Sin fecha límite</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tipo->origen === 'sunat' ? 'badge-origen-sunat' : 'badge-origen-manual' }}">
                                            {{ $tipo->origen_texto }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tipo->activo)
                                            @if($tipo->es_vigente)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-warning">Programado</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $tipo->usuario->name }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.configuracion.tipos-cambio.show', $tipo) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.configuracion.tipos-cambio.edit', $tipo) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm {{ $tipo->activo ? 'btn-outline-warning' : 'btn-outline-success' }} toggle-activo-btn" 
                                                    data-id="{{ $tipo->id }}"
                                                    data-activo="{{ $tipo->activo ? 'true' : 'false' }}"
                                                    title="{{ $tipo->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $tipo->activo ? 'toggle-off' : 'toggle-on' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-id="{{ $tipo->id }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($tiposCambio->hasPages())
                    <div class="card-footer">
                        {{ $tiposCambio->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay tipos de cambio registrados</h5>
                    <p class="text-muted mb-3">Comienza creando tu primer tipo de cambio</p>
                    <a href="{{ route('admin.configuracion.tipos-cambio.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Crear Tipo de Cambio
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para obtener de SUNAT -->
    <div class="modal fade" id="sunatModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-download me-2"></i>Obtener Tipo de Cambio de SUNAT
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="sunatForm">
                        <div class="mb-3">
                            <label for="fechaSunat" class="form-label">Fecha del tipo de cambio</label>
                            <input type="date" class="form-control" id="fechaSunat" name="fecha" 
                                   max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                            <div class="form-text">Solo se pueden obtener tipos de cambio de fechas pasadas o actuales</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="obtenerBtn">
                        <i class="fas fa-download me-1"></i>Obtener
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este tipo de cambio?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Obtener tipo de cambio de SUNAT
    document.getElementById('obtenerSunatBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('sunatModal'));
        modal.show();
    });

    document.getElementById('obtenerBtn').addEventListener('click', function() {
        const fecha = document.getElementById('fechaSunat').value;
        
        if (!fecha) {
            alert('Por favor selecciona una fecha');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Obteniendo...';
        btn.disabled = true;

        fetch('{{ route('admin.configuracion.tipos-cambio.sunat') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ fecha: fecha })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redireccionar a crear con datos prellenados
                const params = new URLSearchParams(data.data);
                window.location.href = '{{ route('admin.configuracion.tipos-cambio.create') }}?' + params.toString();
            } else {
                alert(data.message || 'Error al obtener el tipo de cambio');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al conectar con el servicio');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    // Toggle activo/inactivo
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-activo-btn')) {
            const btn = e.target.closest('.toggle-activo-btn');
            const id = btn.dataset.id;
            const isActive = btn.dataset.activo === 'true';
            
            fetch(`{{ route('admin.configuracion.tipos-cambio.index') }}/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al cambiar el estado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error interno del sistema');
            });
        }
    });

    // Eliminar tipo de cambio
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;
            
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ route('admin.configuracion.tipos-cambio.index') }}/${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    });
</script>
@endpush