@extends('admin.layouts.app')

@section('title', 'Nuevo Tipo de Cambio')

@push('styles')
<style>
    .form-card {
        border: none;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        border-radius: 0.75rem;
    }
    
    .form-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.75rem 0.75rem 0 0;
    }
    
    .sunat-preview {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        opacity: 0.65;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Nuevo Tipo de Cambio</h1>
            <p class="text-muted mb-0">Registra un nuevo tipo de cambio USD-PEN</p>
        </div>
        <div>
            <a href="{{ route('admin.configuracion.tipos-cambio.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card form-card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i>
                        <h5 class="mb-0">Datos del Tipo de Cambio</h5>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Preview de datos de SUNAT si existen -->
                    @if(request('origen') === 'sunat' && request('fecha'))
                        <div class="sunat-preview">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-download me-2"></i>
                                <strong>Datos obtenidos de SUNAT</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="opacity-75">Fecha:</small>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="opacity-75">Compra:</small>
                                    <div class="fw-semibold">S/ {{ number_format(request('compra', 0), 4) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="opacity-75">Venta:</small>
                                    <div class="fw-semibold">S/ {{ number_format(request('venta', 0), 4) }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.configuracion.tipos-cambio.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Fecha del tipo de cambio -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" 
                                           class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" 
                                           name="fecha" 
                                           value="{{ old('fecha', request('fecha', date('Y-m-d'))) }}" 
                                           required>
                                    <label for="fecha">Fecha del Tipo de Cambio *</label>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Origen -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('origen') is-invalid @enderror" 
                                            id="origen" 
                                            name="origen" 
                                            required>
                                        <option value="">Seleccionar origen</option>
                                        <option value="sunat" {{ old('origen', request('origen')) === 'sunat' ? 'selected' : '' }}>
                                            SUNAT (Oficial)
                                        </option>
                                        <option value="manual" {{ old('origen', request('origen')) === 'manual' ? 'selected' : '' }}>
                                            Manual
                                        </option>
                                    </select>
                                    <label for="origen">Origen del Tipo de Cambio *</label>
                                    @error('origen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipo de cambio compra -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" 
                                           class="form-control @error('compra') is-invalid @enderror" 
                                           id="compra" 
                                           name="compra" 
                                           step="0.0001" 
                                           min="0" 
                                           max="99.9999"
                                           value="{{ old('compra', request('compra')) }}" 
                                           placeholder="0.0000"
                                           required>
                                    <label for="compra">Tipo de Cambio Compra (S/) *</label>
                                    @error('compra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipo de cambio venta -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" 
                                           class="form-control @error('venta') is-invalid @enderror" 
                                           id="venta" 
                                           name="venta" 
                                           step="0.0001" 
                                           min="0" 
                                           max="99.9999"
                                           value="{{ old('venta', request('venta')) }}" 
                                           placeholder="0.0000"
                                           required>
                                    <label for="venta">Tipo de Cambio Venta (S/) *</label>
                                    @error('venta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fecha inicio vigencia -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" 
                                           class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                           id="fecha_inicio" 
                                           name="fecha_inicio" 
                                           value="{{ old('fecha_inicio', date('Y-m-d')) }}" 
                                           required>
                                    <label for="fecha_inicio">Vigente Desde *</label>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fecha fin vigencia -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" 
                                           class="form-control @error('fecha_fin') is-invalid @enderror" 
                                           id="fecha_fin" 
                                           name="fecha_fin" 
                                           value="{{ old('fecha_fin') }}">
                                    <label for="fecha_fin">Vigente Hasta (Opcional)</label>
                                    <div class="form-text">Dejar vacío para vigencia indefinida</div>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estado activo -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="activo" 
                                           name="activo" 
                                           value="1"
                                           {{ old('activo', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <strong>Activar este tipo de cambio</strong>
                                        <div class="form-text">Solo puede haber un tipo de cambio activo por fecha</div>
                                    </label>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              id="observaciones" 
                                              name="observaciones" 
                                              style="height: 100px"
                                              placeholder="Observaciones opcionales">{{ old('observaciones') }}</textarea>
                                    <label for="observaciones">Observaciones (Opcional)</label>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.configuracion.tipos-cambio.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Tipo de Cambio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-llenar fecha de inicio con la fecha del tipo de cambio
    document.getElementById('fecha').addEventListener('change', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        if (!fechaInicio.value) {
            fechaInicio.value = this.value;
        }
    });

    // Validación de fechas
    document.getElementById('fecha_fin').addEventListener('change', function() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        if (fechaInicio && this.value && this.value < fechaInicio) {
            alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            this.value = '';
        }
    });

    // Calcular diferencia entre compra y venta
    function actualizarDiferencia() {
        const compra = parseFloat(document.getElementById('compra').value) || 0;
        const venta = parseFloat(document.getElementById('venta').value) || 0;
        
        if (compra > 0 && venta > 0) {
            const diferencia = venta - compra;
            const porcentaje = ((diferencia / compra) * 100).toFixed(2);
            
            // Mostrar diferencia si es muy alta o muy baja
            if (Math.abs(porcentaje) > 5) {
                console.log(`Diferencia: ${diferencia.toFixed(4)} (${porcentaje}%)`);
            }
        }
    }

    document.getElementById('compra').addEventListener('input', actualizarDiferencia);
    document.getElementById('venta').addEventListener('input', actualizarDiferencia);
</script>
@endpush