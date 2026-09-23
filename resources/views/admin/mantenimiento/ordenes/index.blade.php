<!-- resources/views/admin/mantenimiento/ordenes/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Listado de Órdenes de Trabajo')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Órdenes de Trabajo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <form action="{{ route('admin.mantenimiento.ordenes.index') }}" method="GET" class="d-flex me-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por código o cliente..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="btn-group">
                <a href="{{ route('admin.mantenimiento.citas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Cita
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mantenimiento.ordenes.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="diagnostico" {{ request('estado') === 'diagnostico' ? 'selected' : '' }}>Diagnóstico</option>
                            <option value="espera_aprobacion" {{ request('estado') === 'espera_aprobacion' ? 'selected' : '' }}>Esperando Aprobación</option>
                            <option value="en_progreso" {{ request('estado') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="finalizado" {{ request('estado') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                            <option value="facturado" {{ request('estado') === 'facturado' ? 'selected' : '' }}>Facturado</option>
                            <option value="entregado" {{ request('estado') === 'entregado' ? 'selected' : '' }}>Entregado</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tecnico_id" class="form-label">Técnico</label>
                        <select name="tecnico_id" id="tecnico_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach(\App\Models\User::whereHas('roles', function($q) { $q->where('name', 'tecnico'); })->get() as $tecnico)
                                <option value="{{ $tecnico->id }}" {{ request('tecnico_id') == $tecnico->id ? 'selected' : '' }}>{{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.mantenimiento.ordenes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Listado de Órdenes</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Fecha Ingreso</th>
                        <th>Estado</th>
                        <th>Técnico</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $orden)
                        <tr>
                            <td>{{ $orden->codigo_orden }}</td>
                            <td>
                                @if($orden->cliente->tipo_cliente == 'natural')
                                    {{ $orden->cliente->nombres }} {{ $orden->cliente->apellido_paterno }}
                                @else
                                    {{ $orden->cliente->razon_social }}
                                @endif
                            </td>
                            <td>
                                {{ $orden->vehiculo->marca->nombre ?? 'N/A' }} 
                                {{ $orden->vehiculo->modelo->nombre ?? 'N/A' }} 
                                ({{ $orden->vehiculo->nro_placa }})
                            </td>
                            <td>{{ \Carbon\Carbon::parse($orden->fecha_ingreso)->format('d/m/Y') }}</td>
                            <td>
                                @switch($orden->estado)
                                    @case('diagnostico')
                                        <span class="badge bg-info">Diagnóstico</span>
                                        @break
                                    @case('espera_aprobacion')
                                        <span class="badge bg-warning">Esperando Aprobación</span>
                                        @break
                                    @case('en_progreso')
                                        <span class="badge bg-primary">En Progreso</span>
                                        @break
                                    @case('finalizado')
                                        <span class="badge bg-success">Finalizado</span>
                                        @break
                                    @case('facturado')
                                        <span class="badge bg-secondary">Facturado</span>
                                        @break
                                    @case('entregado')
                                        <span class="badge bg-dark">Entregado</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $orden->estado }}</span>
                                @endswitch
                            </td>
                            <td>{{ $orden->tecnico->name ?? 'Sin asignar' }}</td>
                            <td>S/ {{ number_format($orden->getTotalOrdenAttribute(), 2) }}</td>
                            <td>
                                <div class="btn-group">
                                <a href="{{ route('admin.mantenimiento.ordenes.show', ['orden' => $orden->id]) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.mantenimiento.ordenes.imprimir', ['orden' => $orden->id]) }}" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay órdenes de trabajo registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $ordenes->appends(request()->query())->links() }}
        </div>
    </div>
@endsection