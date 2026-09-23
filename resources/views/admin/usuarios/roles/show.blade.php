@extends('admin.layouts.app')

@section('title', 'Detalles del Rol')

@section('header', 'Detalles del Rol')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.usuarios.roles.index') }}" 
                   class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark fw-semibold">{{ $rol->name }}</h1>
                    <p class="text-muted mb-0">Información detallada del rol</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-info me-2 text-primary"></i> Información del Rol
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">ID</label>
                            <div class="form-control-plaintext">{{ $rol->id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Nombre</label>
                            <div class="form-control-plaintext">{{ $rol->name }}</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium text-muted">Guard</label>
                            <div class="form-control-plaintext">
                                <span class="badge bg-secondary">{{ $rol->guard_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Creado</label>
                            <div class="form-control-plaintext">{{ $rol->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Actualizado</label>
                            <div class="form-control-plaintext">{{ $rol->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-info"></i> Información Adicional
                    </h5>
                </div>
                <div class="card-body">
                    @if($rol->description)
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Descripción</label>
                            <div class="form-control-plaintext">{{ $rol->description }}</div>
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Este es un rol básico del sistema. Para gestión avanzada de permisos, 
                        considera implementar la integración completa con Spatie Permission.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-info text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-users me-2"></i> Usuarios con este Rol
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-medium">Total de usuarios:</span>
                        <span class="badge bg-primary rounded-pill fs-6">
                            {{ $rol->users ? $rol->users->count() : 0 }}
                        </span>
                    </div>
                    
                    @if($rol->users && $rol->users->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($rol->users->take(5) as $user)
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($rol->users->count() > 5)
                            <div class="list-group-item px-0 border-0">
                                <small class="text-muted">
                                    Y {{ $rol->users->count() - 5 }} usuario(s) más...
                                </small>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash text-muted mb-2"></i>
                            <p class="text-muted mb-0">Sin usuarios asignados</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-secondary text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-cog me-2"></i> Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.usuarios.roles.edit', $rol) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar Rol
                        </a>
                        
                        @if(!$rol->users || $rol->users->count() == 0)
                        <form action="{{ route('admin.usuarios.roles.destroy', $rol) }}" 
                              method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i> Eliminar Rol
                            </button>
                        </form>
                        @else
                        <button class="btn btn-danger" disabled title="No se puede eliminar un rol con usuarios asignados">
                            <i class="fas fa-trash me-2"></i> Eliminar Rol
                        </button>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            No se puede eliminar porque tiene usuarios asignados
                        </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection