<!-- resources/views/admin/mantenimiento/citas/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Listado de Citas de Mantenimiento')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Citas de Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.mantenimiento.citas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Cita
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Citas</h3>
            <div class="card-tools">
                <form action="{{ route('admin.mantenimiento.citas.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Buscar...">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Fecha y Hora</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Técnico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                        <tr>
                            <td>{{ $cita->id }}</td>
                            <td>
                                @if($cita->cliente->tipo_cliente == 'persona')
                                    {{ $cita->cliente->nombres }} {{ $cita->cliente->apellido_paterno }} {{ $cita->cliente->apellido_materno }}
                                @else
                                    {{ $cita->cliente->razon_social }}
                                @endif
                            </td>
                            <td>
                                {{ $cita->vehiculo->marca->nombre ?? 'N/A' }} 
                                {{ $cita->vehiculo->modelo->nombre ?? 'N/A' }} 
                                ({{ $cita->vehiculo->nro_placa ?? 'N/A' }})
                            </td>
                            <td>{{ \Carbon\Carbon::parse($cita->fecha_hora_cita)->format('d/m/Y H:i') }}</td>
                            <td>{{ $cita->motivo_visita }}</td>
                            <td>
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
                                    @default
                                        <span class="badge bg-secondary">{{ $cita->estado }}</span>
                                @endswitch
                            </td>
                            <td>{{ $cita->tecnico->name ?? 'Sin asignar' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.mantenimiento.citas.show', $cita) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.mantenimiento.citas.edit', $cita) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($cita->estado === 'pendiente')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#confirmarCitaModal{{ $cita->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if(!$cita->ordenTrabajo)
                                        <form action="{{ route('admin.mantenimiento.citas.destroy', $cita) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta cita?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Modal para confirmar cita -->
                        @if($cita->estado === 'pendiente')
                            <div class="modal fade" id="confirmarCitaModal{{ $cita->id }}" tabindex="-1" aria-labelledby="confirmarCitaModalLabel{{ $cita->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.mantenimiento.citas.confirmar', $cita) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmarCitaModalLabel{{ $cita->id }}">Confirmar Cita</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
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
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay citas registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $citas->links() }}
        </div>
    </div>
@endsection