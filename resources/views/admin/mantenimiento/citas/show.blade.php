<!-- resources/views/admin/mantenimiento/citas/show.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Detalle de Cita de Mantenimiento')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Cita #{{ $cita->id }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.mantenimiento.citas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.mantenimiento.citas.edit', $cita) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                
                @if($cita->estado === 'pendiente')
                    <!-- Botón que activa el modal -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmarCitaModal">
                        <i class="fas fa-check"></i> Confirmar Cita
                    </button>
                @endif
                
                @if(!$cita->ordenTrabajo)
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarCitaModal">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Estado de la cita -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Estado de la Cita</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Estado Actual:</h5>
                            @switch($cita->estado)
    @case('pendiente')
        <span class="badge bg-warning">Pendiente</span>
        @break
    @case('confirmada')
        <span class="badge bg-primary">Confirmada</span>
        @break
    @case('en_progreso')
        <span class="badge bg-info">En Progreso</span>
        @break
    @case('completada')
        <span class="badge bg-success">Completada</span>
        @break
    @case('cancelada')
        <span class="badge bg-danger">Cancelada</span>
        @break
    @case('diagnostico')
        <span class="badge bg-secondary">Diagnóstico</span>
        @break
    @case('presupuestado')
        <span class="badge bg-info">Presupuestado</span>
        @break
    @case('en_reparacion')
        <span class="badge bg-primary">En Reparación</span>
        @break
    @case('finalizado')
        <span class="badge bg-success">Finalizado</span>
        @break
    @default
        <span class="badge bg-secondary">{{ $cita->estado }}</span>
@endswitch
                        </div>
                        <div class="col-md-4">
                            <h5>Fecha y Hora:</h5>
                            <p>{{ \Carbon\Carbon::parse($cita->fecha_hora_cita)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Técnico Asignado:</h5>
                            <p>{{ $cita->tecnico->name ?? 'Sin asignar' }}</p>
                        </div>
                    </div>
                    
                    @if($cita->ordenTrabajo)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-tools me-2"></i> Esta cita tiene una <strong>Orden de Trabajo</strong> asociada.
                            <a href="{{ route('admin.mantenimiento.ordenes.show', $cita->ordenTrabajo) }}" class="btn btn-sm btn-info ms-2">
                                Ver Orden de Trabajo #{{ $cita->ordenTrabajo->id }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Información del Cliente -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> Datos del Cliente</h5>
                </div>
                <div class="card-body">
                    <h6 class="border-bottom pb-2">Información Personal</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nombre:</div>
                        <div class="col-md-8">
                            @if($cita->cliente->tipo_cliente == 'persona')
                                {{ $cita->cliente->nombres }} {{ $cita->cliente->apellido_paterno }} {{ $cita->cliente->apellido_materno }}
                            @else
                                {{ $cita->cliente->razon_social }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Documento:</div>
                        <div class="col-md-8">
                            {{ $cita->cliente->tipo_cliente == 'persona' ? 'DNI: ' : 'RUC: ' }}
                            {{ $cita->cliente->documento_identidad }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Ubicación:</div>
                        <div class="col-md-8">
                            {{ $cita->cliente->departamento }}, {{ $cita->cliente->provincia }}, {{ $cita->cliente->distrito }}
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mt-4">Contacto</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Correo:</div>
                        <div class="col-md-8">{{ $cita->cliente->correo ?? 'No registrado' }}</div>
                    </div>
                    
                    @if($cita->cliente->telefonos->count() > 0)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Teléfonos:</div>
                            <div class="col-md-8">
                                <ul class="list-unstyled">
                                    @foreach($cita->cliente->telefonos as $telefono)
                                        <li>{{ ucfirst($telefono->tipo) }}: {{ $telefono->numero }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Información del Vehículo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i> Datos del Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Marca y Modelo:</div>
                        <div class="col-md-8">
                            {{ $cita->vehiculo->marca->nombre ?? 'N/A' }} 
                            {{ $cita->vehiculo->modelo->nombre ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Año:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->anio ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Placa:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->nro_placa }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Color:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->color }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">N° Motor:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->motor }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">N° Chasis:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->serie_vim }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Kilometraje:</div>
                        <div class="col-md-8">{{ number_format($cita->vehiculo->kilometraje, 0, '.', ',') }} km</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Combustible:</div>
                        <div class="col-md-8">{{ $cita->vehiculo->combustible->nombre ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detalles de la Cita -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Detalles de la Cita</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Motivo de visita:</div>
                        <div class="col-md-9">{{ $cita->motivo_visita }}</div>
                    </div>
                    
                    @if($cita->descripcion_problema)
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Descripción del problema:</div>
                            <div class="col-md-9">{{ $cita->descripcion_problema }}</div>
                        </div>
                    @endif
                    
                    @if($cita->notas_adicionales)
                        <div class="row mb-3">
                            <div class="col-md-3 fw-bold">Observaciones:</div>
                            <div class="col-md-9">{{ $cita->notas_adicionales }}</div>
                        </div>
                    @endif
                    
                    <!-- Aquí se podrían mostrar los servicios solicitados si existe una tabla para ello -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registro de Adelanto -->
    @if($cita->estado === 'pendiente' || $cita->estado === 'confirmada')
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Registrar Adelanto</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.mantenimiento.citas.adelanto', $cita) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="monto" class="form-label">Monto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input type="number" name="monto" id="monto" class="form-control" min="0" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                        <option value="transferencia">Transferencia Bancaria</option>
                                        <option value="yape">Yape</option>
                                        <option value="plin">Plin</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Registrar Adelanto
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para confirmar cita -->
    @if($cita->estado === 'pendiente')
<!-- Modal para confirmar cita -->
<div class="modal fade" id="confirmarCitaModal" tabindex="-1" aria-labelledby="confirmarCitaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mantenimiento.citas.confirmar', $cita) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarCitaModalLabel">Confirmar Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal -->
                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label">Técnico Asignado</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select" required>
                            <option value="">Seleccione un técnico</option>
                            @foreach(\App\Models\User::whereHas('roles', function($query) {
                                $query->where('name', 'tecnico');
                            })->get() as $tecnico)
                                <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="box" class="form-label">Box Asignado</label>
                        <input type="text" name="box" id="box" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="kilometraje" class="form-label">Kilometraje Actual</label>
                        <div class="input-group">
                            <input type="number" name="kilometraje" id="kilometraje" class="form-control" 
                                value="{{ $cita->vehiculo->kilometraje ?? 0 }}" min="{{ $cita->vehiculo->kilometraje ?? 0 }}">
                            <span class="input-group-text">km</span>
                        </div>
                        <small class="form-text text-muted">Último kilometraje registrado: {{ number_format($cita->vehiculo->kilometraje ?? 0, 0, '.', ',') }} km</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar y Crear Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endif

    <!-- Modal para eliminar cita -->
    @if(!$cita->ordenTrabajo)
        <div class="modal fade" id="eliminarCitaModal" tabindex="-1" aria-labelledby="eliminarCitaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.mantenimiento.citas.destroy', $cita) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="eliminarCitaModalLabel">Eliminar Cita</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Está seguro de eliminar esta cita de mantenimiento?</p>
                            <p class="text-danger">Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection