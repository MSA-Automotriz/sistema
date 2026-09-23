@extends('admin.layouts.app')

@section('title', 'Editar Año de Modelo')

@section('header', 'Editar Año de Modelo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Editar Año de Modelo: {{ $anioModelo->anio }}
                        </h2>
                        <p class="text-muted small mb-0">Actualiza los datos del año de modelo desde aquí</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.update', $anioModelo) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
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
                                    <option value="{{ $marca->id }}" {{ old('marca_id', $anioModelo->marca_id) == $marca->id ? 'selected' : '' }}>
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
                                    <option value="{{ $modelo->id }}" {{ old('modelo_id', $anioModelo->modelo_id) == $modelo->id ? 'selected' : '' }}>
                                        {{ $modelo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('modelo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="version_id" class="form-label small text-muted mb-1">Versión *</label>
                            <select name="version_id" 
                                    id="version_id" 
                                    class="form-select form-control-sm @error('version_id') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione una versión</option>
                                @foreach ($versiones as $version)
                                    <option value="{{ $version->id }}" {{ old('version_id', $anioModelo->version_id) == $version->id ? 'selected' : '' }}>
                                        {{ $version->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('version_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="anio" class="form-label small text-muted mb-1">Año *</label>
                            <input type="number" 
                                   name="anio" 
                                   id="anio" 
                                   class="form-control form-control-sm @error('anio') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: 2023" 
                                   value="{{ old('anio', $anioModelo->anio) }}" 
                                   min="1900" 
                                   max="{{ date('Y') + 1 }}" 
                                   required>
                            @error('anio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="precio" class="form-label small text-muted mb-1">Precio *</label>
                            <input type="number" 
                                   name="precio" 
                                   id="precio" 
                                   class="form-control form-control-sm @error('precio') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: 25000.00" 
                                   value="{{ old('precio', $anioModelo->precio) }}" 
                                   step="0.01" 
                                   min="0" 
                                   required>
                            @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="moneda" class="form-label small text-muted mb-1">Moneda *</label>
                            <select name="moneda" 
                                    id="moneda" 
                                    class="form-select form-control-sm @error('moneda') is-invalid @enderror" 
                                    :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                    required>
                                <option value="">Seleccione una moneda</option>
                                <option value="SOL" {{ old('moneda', $anioModelo->moneda) == 'SOL' ? 'selected' : '' }}>SOL</option>
                                <option value="USD" {{ old('moneda', $anioModelo->moneda) == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                            @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Año de Modelo
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