@extends('admin.layouts.app')

@section('title', 'Editar Tipo de Cambio')

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
            <h1 class="h3 mb-1">Editar Tipo de Cambio</h1>
            <p class="text-muted mb-0">Modifica los datos del tipo de cambio USD-PEN</p>
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
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Información del Tipo de Cambio
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.configuracion.tipos-cambio.update', $tipoCambio) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Fecha del tipo de cambio -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" 
                                           class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" 
                                           name="fecha" 
                                           value="{{ old('fecha', $tipoCambio->fecha) }}"
                                           required>
                                    <label for="fecha">Fecha del Tipo de Cambio</label>
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
                                        <option value="sunat" {{ old('origen', $tipoCambio->origen) == 'sunat' ? 'selected' : '' }}>SUNAT</option>
                                        <option value="manual" {{ old('origen', $tipoCambio->origen) == 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                    <label for="origen">Origen</label>
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
                                           value="{{ old('compra', $tipoCambio->compra) }}"
                                           step="0.0001" 
                                           min="0" 
                                           max="99.9999"
                                           placeholder="3.7500"
                                           required>
                                    <label for="compra">Tipo de Cambio Compra (S/)</label>
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
                                           value="{{ old('venta', $tipoCambio->venta) }}"
                                           step="0.0001" 
                                           min="0" 
                                           max="99.9999"
                                           placeholder="3.7800"
                                           required>
                                    <label for="venta">Tipo de Cambio Venta (S/)</label>
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
                                           value="{{ old('fecha_inicio', $tipoCambio->fecha_inicio) }}"
                                           required>
                                    <label for="fecha_inicio">Fecha Inicio Vigencia</label>
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
                                           value="{{ old('fecha_fin', $tipoCambio->fecha_fin) }}">
                                    <label for="fecha_fin">Fecha Fin Vigencia (Opcional)</label>
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
                                           {{ old('activo', $tipoCambio->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">
                                        <strong>Tipo de Cambio Activo</strong>
                                        <small class="d-block text-muted">Marcar si este tipo de cambio está actualmente en uso</small>
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
                                              placeholder="Observaciones adicionales...">{{ old('observaciones', $tipoCambio->observaciones) }}</textarea>
                                    <label for="observaciones">Observaciones</label>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.configuracion.tipos-cambio.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Actualizar Tipo de Cambio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection