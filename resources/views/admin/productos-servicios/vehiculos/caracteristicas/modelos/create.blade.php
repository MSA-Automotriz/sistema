@extends('admin.layouts.app')

@section('title', 'Nuevo Modelo')

@section('header', 'Nuevo Modelo')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Crear Nuevo Modelo
                        </h2>
                        <p class="text-muted small mb-0">Registra un nuevo modelo desde aquí</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.store') }}" class="needs-validation" novalidate enctype="multipart/form-data">
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
                            <label for="nombre" class="form-label small text-muted mb-1">Nombre del Modelo *</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control form-control-sm @error('nombre') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ingrese el nombre del modelo" 
                                   value="{{ old('nombre') }}" 
                                   required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="duracion_garantia" class="form-label small text-muted mb-1">Duración de la Garantía (Kilometraje)</label>
                            <input type="text" 
                                   name="duracion_garantia" 
                                   id="duracion_garantia" 
                                   class="form-control form-control-sm @error('duracion_garantia') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   placeholder="Ej: 100,000 km" 
                                   value="{{ old('duracion_garantia') }}">
                            @error('duracion_garantia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cantidad_anos" class="form-label small text-muted mb-1">Cantidad de Años *</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="cantidad_anos" 
                                       id="cantidad_anos" 
                                       class="form-control form-control-sm @error('cantidad_anos') is-invalid @enderror" 
                                       :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                       placeholder="Ingrese la cantidad de años" 
                                       value="{{ old('cantidad_anos') }}" 
                                       min="1" 
                                       required>
                                <span class="input-group-text">años</span>
                            </div>
                            @error('cantidad_anos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ficha_tecnica" class="form-label small text-muted mb-1">Ficha Técnica (PDF)</label>
                            <input type="file" 
                                   name="ficha_tecnica" 
                                   id="ficha_tecnica" 
                                   class="form-control form-control-sm @error('ficha_tecnica') is-invalid @enderror" 
                                   :class="darkMode ? 'bg-dark-subtle text-light border-secondary' : ''" 
                                   accept="application/pdf">
                            @error('ficha_tecnica') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Modelo
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