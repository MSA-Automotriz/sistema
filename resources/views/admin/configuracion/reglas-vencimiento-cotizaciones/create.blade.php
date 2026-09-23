@extends('admin.layouts.app')

@section('title', 'Crear Regla de Vencimiento')

@section('header', 'Crear Regla de Vencimiento')

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
                    <h1 class="h3 mb-1 text-dark fw-semibold">Nueva Regla de Vencimiento</h1>
                    <p class="text-muted mb-0">Configure una nueva regla para gestionar vencimientos automáticos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-plus me-2 text-primary"></i> Información de la Regla
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracion.reglas-vencimiento-cotizaciones.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label fw-medium">Nombre de la Regla <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" 
                                       placeholder="Ej: Vendedores Junior, Cotizaciones Estándar">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label fw-medium">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                         id="descripcion" name="descripcion" rows="3" 
                                         placeholder="Descripción detallada de cuándo se aplica esta regla">{{ old('descripcion') }}</textarea>
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
                                       id="dias_vencimiento" name="dias_vencimiento" value="{{ old('dias_vencimiento', 7) }}" 
                                       min="1" max="365">
                                <div class="form-text">Días sin actividad antes de que la cotización venza</div>
                                @error('dias_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dias_alerta" class="form-label fw-medium">Días para Alerta</label>
                                <input type="number" class="form-control @error('dias_alerta') is-invalid @enderror" 
                                       id="dias_alerta" name="dias_alerta" value="{{ old('dias_alerta', 2) }}" 
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
                                            {{ old('estado_vencido_id') == $estado->id ? 'selected' : '' }}>
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
                                           name="permite_reasignacion" {{ old('permite_reasignacion', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="permite_reasignacion">
                                        Permite Reasignación
                                    </label>
                                </div>
                                <div class="form-text">Si otros asesores pueden tomar cotizaciones vencidas</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="requiere_aprobacion" 
                                           name="requiere_aprobacion" {{ old('requiere_aprobacion') ? 'checked' : '' }}>
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
                                           name="notificar_vencimiento" {{ old('notificar_vencimiento', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="notificar_vencimiento">
                                        Notificar Vencimiento
                                    </label>
                                </div>
                                <div class="form-text">Si enviar notificaciones cuando las cotizaciones venzan</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" 
                                           name="activo" {{ old('activo', true) ? 'checked' : '' }}>
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
                                @foreach($usuarios->chunk(3) as $usuarioChunk)
                                @foreach($usuarioChunk as $usuario)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="usuario_{{ $usuario->id }}" 
                                               name="usuarios_seleccionados[]" 
                                               value="{{ $usuario->id }}"
                                               {{ is_array(old('usuarios_seleccionados')) && in_array($usuario->id, old('usuarios_seleccionados')) ? 'checked' : '' }}>
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
                                <i class="fas fa-save me-1"></i> Crear Regla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">¿Cómo funciona?</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Las cotizaciones se marcan automáticamente como vencidas</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Se envían alertas antes del vencimiento</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Otros asesores pueden tomar cotizaciones vencidas</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Se actualiza el estado automáticamente</small>
                        </li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="fw-semibold text-warning">Ejemplo</h6>
                    <div class="bg-light p-3 rounded">
                        <small>
                            <strong>Regla:</strong> Vendedores Junior<br>
                            <strong>Vencimiento:</strong> 7 días<br>
                            <strong>Alerta:</strong> 2 días antes<br>
                            <strong>Resultado:</strong> A los 5 días se envía alerta, a los 7 días la cotización vence y puede ser reasignada.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection