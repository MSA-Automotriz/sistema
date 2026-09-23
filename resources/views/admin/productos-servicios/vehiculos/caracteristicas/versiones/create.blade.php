@extends('admin.layouts.app')

@section('title', 'Nueva Versión')

@section('header', 'Nueva Versión')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Crear Nueva Versión
                        </h2>
                        <p class="text-muted small mb-0">Registra una nueva versión desde aquí</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="marca_id" class="form-label small text-muted mb-1">Marca *</label>
                            <select name="marca_id" 
                                    id="marca_id" 
                                    class="form-select form-control-sm @error('marca_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione una marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('marca_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modelo_id" class="form-label small text-muted mb-1">Modelo *</label>
                            <select name="modelo_id" 
                                    id="modelo_id" 
                                    class="form-select form-control-sm @error('modelo_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione un modelo</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}" {{ old('modelo_id') == $modelo->id ? 'selected' : '' }}>
                                        {{ $modelo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('modelo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label small text-muted mb-1">Nombre de la Versión *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese el nombre de la versión" 
                                   value="{{ old('nombre') }}" 
                                   required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="carroceria" class="form-label small text-muted mb-1">Carrocería *</label>
                            <input type="text" 
                                   name="carroceria" 
                                   id="carroceria" 
                                   class="form-control form-control-sm @error('carroceria') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: Sedán, SUV" 
                                   value="{{ old('carroceria') }}" 
                                   required>
                            @error('carroceria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cilindrada" class="form-label small text-muted mb-1">Cilindrada *</label>
                            <input type="text" 
                                   name="cilindrada" 
                                   id="cilindrada" 
                                   class="form-control form-control-sm @error('cilindrada') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: 2.0L" 
                                   value="{{ old('cilindrada') }}" 
                                   required>
                            @error('cilindrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="transmision" class="form-label small text-muted mb-1">Transmisión *</label>
                            <input type="text" 
                                   name="transmision" 
                                   id="transmision" 
                                   class="form-control form-control-sm @error('transmision') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: Automática, Manual" 
                                   value="{{ old('transmision') }}" 
                                   required>
                            @error('transmision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="traccion" class="form-label small text-muted mb-1">Tracción *</label>
                            <input type="text" 
                                   name="traccion" 
                                   id="traccion" 
                                   class="form-control form-control-sm @error('traccion') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: 4x4, FWD" 
                                   value="{{ old('traccion') }}" 
                                   required>
                            @error('traccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="combustible_id" class="form-label small text-muted mb-1">Combustible *</label>
                            <select name="combustible_id" 
                                    id="combustible_id" 
                                    class="form-select form-control-sm @error('combustible_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione un combustible</option>
                                @foreach ($combustibles as $combustible)
                                    <option value="{{ $combustible->id }}" {{ old('combustible_id') == $combustible->id ? 'selected' : '' }}>
                                        {{ $combustible->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('combustible_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Versión
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
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endpush