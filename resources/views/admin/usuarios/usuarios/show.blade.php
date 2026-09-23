@extends('admin.layouts.app')

@section('title', 'Detalles del Usuario')

@section('header', 'Detalles del Usuario')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.usuarios.usuarios.index') }}" 
                   class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark fw-semibold">{{ $usuario->name }}</h1>
                    <p class="text-muted mb-0">{{ $usuario->email }}</p>
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
                        <i class="fas fa-user me-2 text-primary"></i> Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">ID</label>
                            <div class="form-control-plaintext">{{ $usuario->id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Nombre</label>
                            <div class="form-control-plaintext">{{ $usuario->name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Email</label>
                            <div class="form-control-plaintext">{{ $usuario->email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Email Verificado</label>
                            <div class="form-control-plaintext">
                                @if($usuario->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Verificado
                                    </span>
                                    <small class="text-muted ms-2">{{ $usuario->email_verified_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>No verificado
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Creado</label>
                            <div class="form-control-plaintext">{{ $usuario->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-muted">Última Actualización</label>
                            <div class="form-control-plaintext">{{ $usuario->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-user-tag me-2 text-success"></i> Roles Asignados
                        <span class="badge bg-success ms-2">{{ $usuario->roles ? $usuario->roles->count() : 0 }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($usuario->roles && $usuario->roles->count() > 0)
                        <div class="row">
                            @foreach($usuario->roles as $rol)
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1 fw-semibold">{{ $rol->name }}</h6>
                                                @if($rol->description)
                                                <small class="text-muted">{{ $rol->description }}</small>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <a href="{{ route('admin.usuarios.roles.show', $rol) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-warning" style="font-size: 48px;"></i>
                            <h6 class="mt-3 text-muted">Sin Roles Asignados</h6>
                            <p class="text-muted">Este usuario no tiene roles específicos asignados.</p>
                            <a href="{{ route('admin.usuarios.usuarios.edit', $usuario) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>Asignar Roles
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-info text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2"></i> Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-medium">Estado de cuenta:</span>
                        @if($usuario->email_verified_at)
                            <span class="badge bg-success rounded-pill">Activa</span>
                        @else
                            <span class="badge bg-warning rounded-pill">Pendiente</span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-medium">Roles asignados:</span>
                        <span class="badge bg-primary rounded-pill fs-6">
                            {{ $usuario->roles ? $usuario->roles->count() : 0 }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Usuario actual:</span>
                        @if($usuario->id === auth()->id())
                            <span class="badge bg-success rounded-pill">Sí</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">No</span>
                        @endif
                    </div>
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
                        <a href="{{ route('admin.usuarios.usuarios.edit', $usuario) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar Usuario
                        </a>
                        
                        @if($usuario->id !== auth()->id())
                        <form action="{{ route('admin.usuarios.usuarios.destroy', $usuario) }}" 
                              method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i> Eliminar Usuario
                            </button>
                        </form>
                        @else
                        <button class="btn btn-danger" disabled title="No puedes eliminar tu propio usuario">
                            <i class="fas fa-trash me-2"></i> Eliminar Usuario
                        </button>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            No puedes eliminar tu propio usuario
                        </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection