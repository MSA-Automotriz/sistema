@extends('admin.layouts.app')

@section('title', 'Editar Regla de Vencimiento')

@section('header', 'Editar Regla de Vencimiento')

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.index') }}" 
                   class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-1 text-dark fw-semibold">Editar Regla: {{ $regla->nombre }}</h1>
                    <p class="text-muted mb-0">Modifique la configuración de la regla de vencimiento</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-edit me-2 text-primary"></i> Información de la Regla
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.update', $regla) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label fw-medium">Nombre de la Regla <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre', $regla->nombre) }}" 
                                       placeholder="Ej: Vendedores Junior, Cotizaciones Estándar">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label fw-medium">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                         id="descripcion" name="descripcion" rows="3" 
                                         placeholder="Descripción detallada de cuándo se aplica esta regla">{{ old('descripcion', $regla->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-semibold mb-3 text-primary">
                            <i class="fas fa-clock me-2"></i> Configuración de Tiempos
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dias_vencimiento" class="form-label fw-medium">Días para Vencimiento <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('dias_vencimiento') is-invalid @enderror" 
                                       id="dias_vencimiento" name="dias_vencimiento" value="{{ old('dias_vencimiento', $regla->dias_vencimiento) }}" 
                                       min="1" max="365">
                                <div class="form-text">Días sin actividad antes de que la cotización venza</div>
                                @error('dias_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dias_alerta" class="form-label fw-medium">Días para Alerta</label>
                                <input type="number" class="form-control @error('dias_alerta') is-invalid @enderror" 
                                       id="dias_alerta" name="dias_alerta" value="{{ old('dias_alerta', $regla->dias_alerta) }}" 
                                       min="0" max="365">
                                <div class="form-text">Días antes del vencimiento para enviar alerta (0 = sin alerta)</div>
                                @error('dias_alerta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-semibold mb-3 text-primary">
                            <i class="fas fa-cog me-2"></i> Configuración de Comportamiento
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estado_vencido_id" class="form-label fw-medium">Estado al Vencer <span class="text-danger">*</span></label>
                                <select class="form-select @error('estado_vencido_id') is-invalid @enderror" 
                                        id="estado_vencido_id" name="estado_vencido_id">
                                    <option value="">Seleccionar estado...</option>
                                    @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" 
                                            {{ old('estado_vencido_id', $regla->estado_vencido_id) == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Estado al que cambiará la cotización cuando venza</div>
                                @error('estado_vencido_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="permite_reasignacion" 
                                           name="permite_reasignacion" {{ old('permite_reasignacion', $regla->permite_reasignacion) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="permite_reasignacion">
                                        Permite Reasignación
                                    </label>
                                </div>
                                <div class="form-text">Si otros asesores pueden tomar cotizaciones vencidas</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="requiere_aprobacion" 
                                           name="requiere_aprobacion" {{ old('requiere_aprobacion', $regla->requiere_aprobacion) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="requiere_aprobacion">
                                        Requiere Aprobación
                                    </label>
                                </div>
                                <div class="form-text">Si la reasignación requiere aprobación</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notificar_vencimiento" 
                                           name="notificar_vencimiento" {{ old('notificar_vencimiento', $regla->notificar_vencimiento) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="notificar_vencimiento">
                                        Notificar Vencimiento
                                    </label>
                                </div>
                                <div class="form-text">Si enviar notificaciones cuando las cotizaciones venzan</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" 
                                           name="activo" {{ old('activo', $regla->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="activo">
                                        Regla Activa
                                    </label>
                                </div>
                                <div class="form-text">Si esta regla está activa y se aplica</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-semibold mb-3 text-primary">
                            <i class="fas fa-users me-2"></i> Aplicar a Usuarios Específicos (Opcional)
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Usuarios</label>
                            <div class="form-text mb-2">Si no selecciona ninguno, se aplicará a todos los usuarios</div>
                            <div class="row">
                                @php
                                    $usuariosSeleccionados = old('usuarios_seleccionados', $regla->condiciones['usuarios'] ?? []);
                                @endphp
                                @foreach($usuarios->chunk(3) as $usuarioChunk)
                                @foreach($usuarioChunk as $usuario)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="usuario_{{ $usuario->id }}" 
                                               name="usuarios_seleccionados[]" 
                                               value="{{ $usuario->id }}"
                                               {{ in_array($usuario->id, $usuariosSeleccionados) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="usuario_{{ $usuario->id }}">
                                            {{ $usuario->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.index') }}" 
                               class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar Regla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-info text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2"></i> Estadísticas de Uso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-medium">Cotizaciones usando esta regla:</span>
                        <span class="badge bg-primary rounded-pill fs-6">
                            {{ $regla->cotizaciones()->count() }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-medium">Estado actual:</span>
                        @if($regla->activo)
                            <span class="badge bg-success rounded-pill">Activa</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">Inactiva</span>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium">Creada:</span>
                        <span class="text-muted">{{ $regla->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    @if($regla->cotizaciones()->count() > 0)
                    <hr>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Esta regla está siendo utilizada. Los cambios afectarán las cotizaciones existentes.</small>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold text-warning">⚠️ Importante</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Los cambios se aplicarán a cotizaciones futuras inmediatamente</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Las cotizaciones existentes mantendrán sus fechas calculadas</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-exclamation text-warning me-2"></i>
                            <small>Desactivar la regla no afecta cotizaciones en curso</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection