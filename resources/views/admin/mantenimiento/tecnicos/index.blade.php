@extends('admin.layouts.app')

@section('title', 'Técnicos')

@section('header', 'Gestión de Técnicos')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Panel principal -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Header con botón de acción -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1">
                            <i class="fas fa-user-cog me-2"></i>
                            Total de Técnicos: <span>{{ $tecnicos->total() }}</span>
                        </h2>
                        <p class="text-muted small mb-0">Gestiona la información de los técnicos del taller</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.mantenimiento.tecnicos.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Técnico
                        </a>
                    </div>
                </div>

                <!-- Tabla de técnicos -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Especialidad</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Fecha Ingreso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tecnicos as $tecnico)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $tecnico->codigo }}</strong>
                                </td>
                                <td>{{ $tecnico->user->name }}</td>
                                <td>{{ $tecnico->user->email }}</td>
                                <td>
                                    @if($tecnico->especialidad)
                                        <span class="badge bg-info">{{ $tecnico->especialidad }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $tecnico->telefono ?? '-' }}</td>
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
                                <td>{{ $tecnico->fecha_ingreso ? $tecnico->fecha_ingreso->format('d/m/Y') : '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.mantenimiento.tecnicos.show', $tecnico) }}"
                                           class="btn btn-outline-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.mantenimiento.tecnicos.edit', $tecnico) }}"
                                           class="btn btn-outline-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmarEliminacion({{ $tecnico->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $tecnico->id }}"
                                          action="{{ route('admin.mantenimiento.tecnicos.destroy', $tecnico) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay técnicos registrados</p>
                                    <a href="{{ route('admin.mantenimiento.tecnicos.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>
                                        Agregar Primer Técnico
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $tecnicos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarEliminacion(tecnicoId) {
    if (confirm('¿Está seguro de que desea eliminar este técnico? Esta acción no se puede deshacer.')) {
        document.getElementById('delete-form-' + tecnicoId).submit();
    }
}
</script>
@endpush
@endsection
