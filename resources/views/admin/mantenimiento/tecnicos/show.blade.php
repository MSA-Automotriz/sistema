@extends('admin.layouts.app')

@section('title', 'Detalles del Técnico')

@section('header', 'Detalles del Técnico')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Botones de acción -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.mantenimiento.tecnicos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Volver al Listado
            </a>
            <div>
                <a href="{{ route('admin.mantenimiento.tecnicos.edit', $tecnico) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>
                    Editar
                </a>
            </div>
        </div>

        <!-- Información del Técnico -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-cog me-2"></i>
                    {{ $tecnico->user->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <!-- Información de Usuario -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2"></i>Información Personal
                        </h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th width="40%">Código:</th>
                                    <td><strong class="text-primary">{{ $tecnico->codigo }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $tecnico->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $tecnico->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono:</th>
                                    <td>{{ $tecnico->telefono ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tel. Emergencia:</th>
                                    <td>{{ $tecnico->telefono_emergencia ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @switch($tecnico->estado)
                                            @case('activo')
                                                <span class="badge bg-success">Activo</span>
                                                @break
                                            @case('inactivo')
                                                <span class="badge bg-secondary">Inactivo</span>
                                                @break
                                            @case('vacaciones')
                                                <span class="badge bg-warning">Vacaciones</span>
                                                @break
                                            @case('licencia')
                                                <span class="badge bg-info">Licencia</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Información Profesional -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tools me-2"></i>Información Profesional
                        </h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th width="40%">Especialidad:</th>
                                    <td>
                                        @if($tecnico->especialidad)
                                            <span class="badge bg-info">{{ $tecnico->especialidad }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cédula Profesional:</th>
                                    <td>{{ $tecnico->cedula_profesional ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Ingreso:</th>
                                    <td>{{ $tecnico->fecha_ingreso ? $tecnico->fecha_ingreso->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Antigüedad:</th>
                                    <td>
                                        @if($tecnico->fecha_ingreso)
                                            {{ $tecnico->fecha_ingreso->diffForHumans() }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Registro:</th>
                                    <td>{{ $tecnico->created_at->format('d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Certificaciones -->
                    @if($tecnico->certificaciones)
                    <div class="col-md-6 mt-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-certificate me-2"></i>Certificaciones
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0 text-muted small">{{ $tecnico->certificaciones }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Habilidades -->
                    @if($tecnico->habilidades)
                    <div class="col-md-6 mt-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-star me-2"></i>Habilidades
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0 text-muted small">{{ $tecnico->habilidades }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notas -->
                    @if($tecnico->notas)
                    <div class="col-12 mt-3">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Notas Adicionales
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0 text-muted small">{{ $tecnico->notas }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Órdenes de Trabajo Recientes -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Órdenes de Trabajo Recientes
                </h5>
            </div>
            <div class="card-body p-4">
                @if($tecnico->ordenesTrabajoMantenimiento->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Estado</th>
                                <th>Fecha Ingreso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tecnico->ordenesTrabajoMantenimiento as $orden)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $orden->codigo_orden }}</strong>
                                </td>
                                <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                                <td>
                                    @if($orden->vehiculo)
                                        {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}
                                        <br>
                                        <small class="text-muted">{{ $orden->vehiculo->placa }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @switch($orden->estado)
                                        @case('pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                            @break
                                        @case('en_progreso')
                                            <span class="badge bg-info">En Progreso</span>
                                            @break
                                        @case('completado')
                                            <span class="badge bg-success">Completado</span>
                                            @break
                                        @case('cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $orden->estado }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $orden->fecha_ingreso ? $orden->fecha_ingreso->format('d/m/Y') : '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.mantenimiento.ordenes.show', $orden) }}"
                                       class="btn btn-sm btn-outline-info"
                                       title="Ver orden">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay órdenes de trabajo asignadas a este técnico</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
