@extends('admin.layouts.app')

@section('title', 'Planes de Mantenimiento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-wrench mr-2"></i>
                        Planes de Mantenimiento
                    </h3>
                    <a href="{{ route('admin.planes-mantenimiento.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Plan
                    </a>
                </div>
                
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('admin.planes-mantenimiento.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="buscar" class="form-control" placeholder="Buscar planes..." 
                                       value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="modelo" class="form-control">
                                    <option value="">Todos los modelos</option>
                                    @foreach($modelosVehiculo as $modeloVehiculo)
                                        <option value="{{ $modeloVehiculo }}" {{ request('modelo') == $modeloVehiculo ? 'selected' : '' }}>
                                            {{ $modeloVehiculo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="ano" class="form-control">
                                    <option value="">Todos los años</option>
                                    @foreach($anos as $ano)
                                        <option value="{{ $ano }}" {{ request('ano') == $ano ? 'selected' : '' }}>
                                            {{ $ano }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>
                                        Activos
                                    </option>
                                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>
                                        Inactivos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary mr-2">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('admin.planes-mantenimiento.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de planes -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Vehículo</th>
                                    <th>Intervalo</th>
                                    <th>Componentes</th>
                                    <th>Estado</th>
                                    <th>Creado</th>
                                    <th width="200">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($planes as $plan)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $plan->nombre }}</div>
                                        @if($plan->descripcion)
                                            <small class="text-muted">{{ Str::limit($plan->descripcion, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $plan->modelo_vehiculo }} {{ $plan->ano_modelo }}</div>
                                        <small class="text-muted">{{ $plan->tipo_transmision }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="padding: 5px 10px; background-color: #17a2b8; color: #000;">
                                            {{ number_format($plan->intervalo_base) }} km
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary" style="padding: 5px 10px; background-color: #17a2b8; color: #000;">
                                            {{ $plan->componentesPlan->count() }} items
                                        </span>
                                    </td>
                                    <td>
                                        @if($plan->activo)
                                            <span class="badge badge-success" style="padding: 5px 10px; background-color: #17a2b8; color: #000;">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $plan->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $plan->usuario->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.planes-mantenimiento.show', $plan) }}" 
                                               class="btn btn-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.planes-mantenimiento.edit', $plan) }}" 
                                               class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.planes-mantenimiento.duplicate', $plan) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary" title="Duplicar">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.planes-mantenimiento.toggle-status', $plan) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-{{ $plan->activo ? 'outline-danger' : 'outline-success' }}" 
                                                        title="{{ $plan->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas fa-{{ $plan->activo ? 'times' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.planes-mantenimiento.destroy', $plan) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" 
                                                        onclick="return confirm('¿Está seguro de eliminar este plan?')"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                                            <div class="text-muted">No se encontraron planes de mantenimiento</div>
                                            <a href="{{ route('admin.planes-mantenimiento.create') }}" 
                                               class="btn btn-primary mt-2">
                                                <i class="fas fa-plus mr-1"></i>
                                                Crear primer plan
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($planes->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $planes->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection