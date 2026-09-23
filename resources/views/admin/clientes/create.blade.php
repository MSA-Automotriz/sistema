@extends('admin.layouts.app')

@section('title', 'Crear Cliente')

@section('header', 'Crear Nuevo Cliente')

@push('styles')
<style>
    .input-group-text {
        background-color: #f8f9fa;
        border-left: 0;
    }
    
    #documento-loading .spinner-border {
        width: 1rem;
        height: 1rem;
    }
    
    .form-text i {
        margin-right: 4px;
    }
    
    .document-status {
        transition: all 0.3s ease;
    }
    
    .input-group input:focus + .input-group-text {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary.validating {
        background-color: #0d6efd;
        border-color: #0d6efd;
        opacity: 0.65;
    }
    
    .form-control:read-only {
        background-color: #e9ecef;
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h5 class="mb-0 fw-semibold">Crear Nuevo Cliente</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.clientes.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <!-- Sección: Documento -->
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-3">Información del Documento</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_cliente" class="form-label small text-muted mb-1">Tipo de Cliente *</label>
                            <select name="tipo_cliente" id="tipo_cliente" class="form-select form-select-sm shadow-sm @error('tipo_cliente') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                <option value="natural" {{ old('tipo_cliente') == 'natural' ? 'selected' : '' }}>Persona Natural (DNI)</option>
                                <option value="juridica" {{ old('tipo_cliente') == 'juridica' ? 'selected' : '' }}>Persona Jurídica (RUC)</option>
                            </select>
                            @error('tipo_cliente') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="documento_identidad" class="form-label small text-muted mb-1">Número de Documento *</label>
                            <div class="input-group">
                                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control form-control-sm shadow-sm @error('documento_identidad') is-invalid @enderror" value="{{ old('documento_identidad') }}" required maxlength="11" autocomplete="off">
                                <div class="input-group-text" id="documento-loading" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                            @error('documento_identidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text text-muted small" id="documento-hint"></div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="validar_documento" class="btn btn-sm btn-primary w-100 shadow-sm">
                                <i class="fas fa-search me-1"></i>Validar Datos
                            </button>
                        </div>

                        <!-- Campos para DNI -->
                        <div class="col-12 dni-fields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label small text-muted mb-1">Apellido Paterno</label>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control form-control-sm shadow-sm @error('apellido_paterno') is-invalid @enderror" value="{{ old('apellido_paterno') }}" readonly>
                                    @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label small text-muted mb-1">Apellido Materno</label>
                                    <input type="text" name="apellido_materno" id="apellido_materno" class="form-control form-control-sm shadow-sm @error('apellido_materno') is-invalid @enderror" value="{{ old('apellido_materno') }}" readonly>
                                    @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="nombres" class="form-label small text-muted mb-1">Nombres</label>
                                    <input type="text" name="nombres" id="nombres" class="form-control form-control-sm shadow-sm @error('nombres') is-invalid @enderror" value="{{ old('nombres') }}" readonly>
                                    @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campo para RUC -->
                        <div class="col-12 ruc-fields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="razon_social" class="form-label small text-muted mb-1">Razón Social</label>
                                    <input type="text" name="razon_social" id="razon_social" class="form-control form-control-sm shadow-sm @error('razon_social') is-invalid @enderror" value="{{ old('razon_social') }}" readonly>
                                    @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Ubicación -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Ubicación</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="departamento" class="form-label small text-muted mb-1">Departamento *</label>
                            <select name="departamento" id="departamento" class="form-select form-select-sm shadow-sm @error('departamento') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento }}" {{ old('departamento') == $departamento ? 'selected' : '' }}>{{ $departamento }}</option>
                                @endforeach
                            </select>
                            @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="provincia" class="form-label small text-muted mb-1">Provincia *</label>
                            <select name="provincia" id="provincia" class="form-select form-select-sm shadow-sm @error('provincia') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                            </select>
                            @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="distrito" class="form-label small text-muted mb-1">Distrito *</label>
                            <select name="distrito" id="distrito" class="form-select form-select-sm shadow-sm @error('distrito') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                            </select>
                            @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="col-md-6 mt-4">
                            <label for="correo" class="form-label small text-muted mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" id="correo" class="form-control form-control-sm shadow-sm @error('correo') is-invalid @enderror" placeholder="Ej: cliente@dominio.com" value="{{ old('correo') }}">
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6 mt-4">
                            <label for="categoria_cliente_id" class="form-label small text-muted mb-1">Categoría Cliente *</label>
                            <select name="categoria_cliente_id" id="categoria_cliente_id" class="form-select form-select-sm shadow-sm @error('categoria_cliente_id') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_cliente_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Canal de Captación -->
                        <div class="col-md-12 mt-4">
                            <label for="canal_captacion_id" class="form-label small text-muted mb-1">Canal de Captación *</label>
                            <div class="btn-group d-flex flex-wrap gap-2" role="group" aria-label="Canal de Captación">
                                @foreach ($canales as $canal)
                                    <input type="radio" class="btn-check" name="canal_captacion_id" id="canal_{{ $canal->id }}" value="{{ $canal->id }}" {{ old('canal_captacion_id') == $canal->id ? 'checked' : '' }} required>
                                    <label class="btn btn-sm btn-outline-primary" for="canal_{{ $canal->id }}">{{ $canal->nombre }}</label>
                                @endforeach
                            </div>
                            @error('canal_captacion_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Sección: Teléfonos -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Teléfonos de Contacto</h6>
                            <div id="telefonos-container">
                                <div class="input-group mb-2 telefono-entry">
                                    <input type="text" name="telefonos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 01234567">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-telefono">Eliminar</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 shadow-sm" id="add-telefono">+ Agregar Teléfono</button>
                        </div>

                        <!-- Sección: Celulares -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Celulares de Contacto</h6>
                            <div id="celulares-container">
                                <div class="input-group mb-2 celular-entry">
                                    <input type="text" name="celulares[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 987654321">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-celular">Eliminar</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 shadow-sm" id="add-celular">+ Agregar Celular</button>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Cliente
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
        document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Validación del formulario
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

    // Mostrar/ocultar campos según tipo de cliente
    function mostrarCamposSegunTipoCliente() {
        const tipoCliente = document.getElementById('tipo_cliente').value;
        const dniFields = document.querySelectorAll('.dni-fields');
        const rucFields = document.querySelectorAll('.ruc-fields');
        const documentoInput = document.getElementById('documento_identidad');
        const documentoHint = document.getElementById('documento-hint');
        
        // Limpiar campos al cambiar tipo
        limpiarCamposDocumento();
        
        if (tipoCliente === 'natural') {
            dniFields.forEach(field => field.style.display = 'block');
            rucFields.forEach(field => field.style.display = 'none');
            documentoInput.maxLength = 8;
            documentoInput.placeholder = 'Ej: 12345678';
            documentoHint.textContent = 'Ingrese 8 dígitos del DNI';
        } else if (tipoCliente === 'juridica') {
            dniFields.forEach(field => field.style.display = 'none');
            rucFields.forEach(field => field.style.display = 'block');
            documentoInput.maxLength = 11;
            documentoInput.placeholder = 'Ej: 20123456789';
            documentoHint.textContent = 'Ingrese 11 dígitos del RUC';
        } else {
            dniFields.forEach(field => field.style.display = 'none');
            rucFields.forEach(field => field.style.display = 'none');
            documentoInput.maxLength = 11;
            documentoInput.placeholder = '';
            documentoHint.textContent = '';
        }
    }

    // Limpiar campos de documento
    function limpiarCamposDocumento() {
        document.getElementById('documento_identidad').value = '';
        document.getElementById('apellido_paterno').value = '';
        document.getElementById('apellido_materno').value = '';
        document.getElementById('nombres').value = '';
        document.getElementById('razon_social').value = '';
    }
    
    const tipoClienteSelect = document.getElementById('tipo_cliente');
    const documentoInput = document.getElementById('documento_identidad');
    
    tipoClienteSelect.addEventListener('change', mostrarCamposSegunTipoCliente);
    
    // Si ya hay un tipo seleccionado al cargar la página, mostrar los campos correspondientes
    if (tipoClienteSelect.value) {
        mostrarCamposSegunTipoCliente();
    }

    // Autocompletado automático mientras se escribe
    let timeoutId = null;
    let isValidating = false;

    // Validación de formato en tiempo real
    documentoInput.addEventListener('input', function(e) {
        const valor = e.target.value.replace(/\D/g, ''); // Solo números
        e.target.value = valor;
        
        const tipoCliente = document.getElementById('tipo_cliente').value;
        const documentoHint = document.getElementById('documento-hint');
        
        // Limpiar timeout anterior
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        // Validar longitud según tipo
        if (tipoCliente === 'natural' && valor.length === 8) {
            documentoHint.innerHTML = '<i class="fas fa-check text-success"></i> DNI válido - Buscando datos...';
            timeoutId = setTimeout(() => autoValidarDocumento(), 800);
        } else if (tipoCliente === 'juridica' && valor.length === 11) {
            documentoHint.innerHTML = '<i class="fas fa-check text-success"></i> RUC válido - Buscando datos...';
            timeoutId = setTimeout(() => autoValidarDocumento(), 800);
        } else if (valor.length > 0) {
            const esperado = tipoCliente === 'natural' ? 8 : 11;
            const tipo = tipoCliente === 'natural' ? 'DNI' : 'RUC';
            documentoHint.innerHTML = `<i class="fas fa-info-circle text-info"></i> ${tipo}: ${valor.length}/${esperado} dígitos`;
        } else {
            documentoHint.textContent = tipoCliente === 'natural' ? 'Ingrese 8 dígitos del DNI' : 'Ingrese 11 dígitos del RUC';
        }
    });

    // Solo permitir números
    documentoInput.addEventListener('keypress', function(e) {
        if (!/\d/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
            e.preventDefault();
        }
    });

    // Validación automática
    function autoValidarDocumento() {
        if (isValidating) return;
        
        const tipoCliente = document.getElementById('tipo_cliente').value;
        const numeroDocumento = documentoInput.value;

        if (!tipoCliente || !numeroDocumento) return;

        const requiredLength = tipoCliente === 'natural' ? 8 : 11;
        if (numeroDocumento.length !== requiredLength) return;

        validarDocumentoAPI(tipoCliente === 'natural' ? 'DNI' : 'RUC', numeroDocumento, true);
    }

    // Función para resetear el formulario
    function resetForm() {
        document.getElementById('documento_identidad').value = '';
        document.getElementById('apellido_paterno').value = '';
        document.getElementById('apellido_materno').value = '';
        document.getElementById('nombres').value = '';
        document.getElementById('razon_social').value = '';
        
        document.getElementById('departamento').value = '';
        document.getElementById('provincia').innerHTML = '<option value="">Seleccione</option>';
        document.getElementById('distrito').innerHTML = '<option value="">Seleccione</option>';
        
        document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'none');
        document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'none');

        // Resetear teléfonos
        const telefonosContainer = document.getElementById('telefonos-container');
        telefonosContainer.innerHTML = `
            <div class="input-group mb-2 telefono-entry">
                <input type="text" name="telefonos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 01234567">
                <button type="button" class="btn btn-outline-danger btn-sm remove-telefono">Eliminar</button>
            </div>`;

        // Resetear celulares
        const celularesContainer = document.getElementById('celulares-container');
        celularesContainer.innerHTML = `
            <div class="input-group mb-2 celular-entry">
                <input type="text" name="celulares[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 987654321">
                <button type="button" class="btn btn-outline-danger btn-sm remove-celular">Eliminar</button>
            </div>`;

        document.querySelector('form.needs-validation').classList.remove('was-validated');
    }

    // Cargar provincias cuando cambia el departamento
    document.getElementById('departamento').addEventListener('change', function () {
        const departamento = this.value;
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');

        // Resetear los selectores de provincia y distrito
        provinciaSelect.innerHTML = '<option value="">Seleccione</option>';
        distritoSelect.innerHTML = '<option value="">Seleccione</option>';

        if (departamento) {
            console.log('Cargando provincias para departamento:', departamento);
            
            // Mostrar spinner o indicador de carga
            provinciaSelect.disabled = true;
            provinciaSelect.innerHTML = '<option value="">Cargando...</option>';
            
            fetch('{{ route('admin.clientes.provincias') }}?departamento=' + encodeURIComponent(departamento))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud: ' + response.status);
                    }
                    return response.json();
                })
                .then(provincias => {
                    console.log('Provincias recibidas:', provincias);
                    
                    // Resetear y habilitar el selector
                    provinciaSelect.disabled = false;
                    provinciaSelect.innerHTML = '<option value="">Seleccione</option>';
                    
                    if (provincias.length === 0) {
                        console.warn('No se encontraron provincias para el departamento seleccionado.');
                    } else {
                        provincias.forEach(provincia => {
                            const option = document.createElement('option');
                            option.value = provincia;
                            option.textContent = provincia;
                            provinciaSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar provincias:', error);
                    provinciaSelect.disabled = false;
                    provinciaSelect.innerHTML = '<option value="">Error al cargar - Intente de nuevo</option>';
                });
        }
    });

    document.getElementById('provincia').addEventListener('change', function () {
        const departamento = document.getElementById('departamento').value;
        const provincia = this.value;
        const distritoSelect = document.getElementById('distrito');

        // Resetear el selector de distrito
        distritoSelect.innerHTML = '<option value="">Seleccione</option>';

        if (departamento && provincia) {
            console.log('Cargando distritos para departamento:', departamento, 'y provincia:', provincia);
            
            // Mostrar spinner o indicador de carga
            distritoSelect.disabled = true;
            distritoSelect.innerHTML = '<option value="">Cargando...</option>';
            
            fetch('{{ route('admin.clientes.distritos') }}?departamento=' + encodeURIComponent(departamento) + '&provincia=' + encodeURIComponent(provincia))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud: ' + response.status);
                    }
                    return response.json();
                })
                .then(distritos => {
                    console.log('Distritos recibidos:', distritos);
                    
                    // Resetear y habilitar el selector
                    distritoSelect.disabled = false;
                    distritoSelect.innerHTML = '<option value="">Seleccione</option>';
                    
                    if (distritos.length === 0) {
                        console.warn('No se encontraron distritos para la provincia seleccionada.');
                    } else {
                        distritos.forEach(distrito => {
                            const option = document.createElement('option');
                            option.value = distrito;
                            option.textContent = distrito;
                            distritoSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar distritos:', error);
                    distritoSelect.disabled = false;
                    distritoSelect.innerHTML = '<option value="">Error al cargar - Intente de nuevo</option>';
                });
        }
    });

    // Función principal de validación de documento
    function validarDocumentoAPI(tipoDocumento, numeroDocumento, esAutocompletado = false) {
        if (isValidating) return;
        isValidating = true;

        const validarBtn = document.getElementById('validar_documento');
        const loadingIndicator = document.getElementById('documento-loading');
        const documentoHint = document.getElementById('documento-hint');

        // Mostrar indicadores de carga
        if (esAutocompletado) {
            loadingIndicator.style.display = 'flex';
            documentoHint.innerHTML = '<i class="fas fa-spinner fa-spin text-primary"></i> Consultando APIs...';
        } else {
            validarBtn.disabled = true;
            validarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Validando...';
        }

        console.log('Validando documento:', { tipo_documento: tipoDocumento, numero_documento: numeroDocumento, auto: esAutocompletado });

        fetch('{{ route('admin.clientes.validar-documento') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                tipo_documento: tipoDocumento,
                numero_documento: numeroDocumento,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de la API:', data);
            
            if (data.success) {
                procesarDatosValidacion(data.data, tipoDocumento, esAutocompletado);
                
                if (esAutocompletado) {
                    documentoHint.innerHTML = `<i class="fas fa-check text-success"></i> Datos encontrados automáticamente`;
                } else {
                    documentoHint.innerHTML = `<i class="fas fa-check text-success"></i> Validación exitosa`;
                }
            } else {
                if (esAutocompletado) {
                    documentoHint.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> ${data.message || 'Datos no encontrados'}`;
                } else {
                    alert(data.message || 'No se encontraron datos para el documento ingresado.');
                }
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            if (esAutocompletado) {
                documentoHint.innerHTML = `<i class="fas fa-times text-danger"></i> Error en la consulta`;
            } else {
                alert('Error al validar el documento. Intente nuevamente.');
            }
        })
        .finally(() => {
            isValidating = false;
            loadingIndicator.style.display = 'none';
            
            if (!esAutocompletado) {
                validarBtn.disabled = false;
                validarBtn.innerHTML = '<i class="fas fa-search me-1"></i>Validar Datos';
            }
        });
    }

    // Procesar datos de validación
    function procesarDatosValidacion(data, tipoDocumento, esAutocompletado) {
        if (tipoDocumento === 'DNI') {
            document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'block');
            document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'none');
            
            document.getElementById('apellido_paterno').value = data.apellido_paterno || '';
            document.getElementById('apellido_materno').value = data.apellido_materno || '';
            document.getElementById('nombres').value = data.nombres || '';
            document.getElementById('razon_social').value = '';
            
            // Hacer campos editables si es autocompletado (por si hay errores en la API)
            if (esAutocompletado) {
                document.getElementById('apellido_paterno').readOnly = false;
                document.getElementById('apellido_materno').readOnly = false;
                document.getElementById('nombres').readOnly = false;
            }
            
        } else if (tipoDocumento === 'RUC') {
            document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'none');
            document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'block');
            
            document.getElementById('razon_social').value = data.nombre_o_razon_social || '';
            document.getElementById('apellido_paterno').value = '';
            document.getElementById('apellido_materno').value = '';
            document.getElementById('nombres').value = '';
            
            // Hacer campo editable si es autocompletado
            if (esAutocompletado) {
                document.getElementById('razon_social').readOnly = false;
            }

            // Procesar ubicación si está disponible
            if (data.departamento) {
                procesarUbicacion(data);
            }
        }
    }

    // Procesar datos de ubicación
    function procesarUbicacion(data) {
        const departamentoSelect = document.getElementById('departamento');
        
        if (data.departamento) {
            departamentoSelect.value = data.departamento;
            const departamentoEvent = new Event('change');
            departamentoSelect.dispatchEvent(departamentoEvent);

            setTimeout(() => {
                if (data.provincia) {
                    const provinciaSelect = document.getElementById('provincia');
                    provinciaSelect.value = data.provincia;
                    const provinciaEvent = new Event('change');
                    provinciaSelect.dispatchEvent(provinciaEvent);

                    setTimeout(() => {
                        if (data.distrito) {
                            document.getElementById('distrito').value = data.distrito;
                        }
                    }, 600);
                }
            }, 600);
        }
    }

    // Event listener para botón manual de validación
    document.getElementById('validar_documento').addEventListener('click', function () {
        const tipoCliente = document.getElementById('tipo_cliente').value;
        const numeroDocumento = document.getElementById('documento_identidad').value;

        if (!tipoCliente || !numeroDocumento) {
            alert('Por favor, seleccione el tipo de cliente y proporcione el número de documento.');
            return;
        }

        const tipoDocumento = tipoCliente === 'natural' ? 'DNI' : 'RUC';
        validarDocumentoAPI(tipoDocumento, numeroDocumento, false);
    });

    // Agregar y eliminar teléfonos dinámicamente
    let telefonoIndex = 0;
    document.getElementById('add-telefono').addEventListener('click', function () {
        telefonoIndex++;
        const container = document.getElementById('telefonos-container');
        const newTelefono = `
            <div class="input-group mb-2 telefono-entry">
                <input type="text" name="telefonos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 01234567">
                <button type="button" class="btn btn-outline-danger btn-sm remove-telefono">Eliminar</button>
            </div>`;
        container.insertAdjacentHTML('beforeend', newTelefono);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-telefono')) {
            if (document.querySelectorAll('.telefono-entry').length > 1) {
                e.target.closest('.telefono-entry').remove();
            }
        }
    });

    // Agregar y eliminar celulares dinámicamente
    let celularIndex = 0;
    document.getElementById('add-celular').addEventListener('click', function () {
        celularIndex++;
        const container = document.getElementById('celulares-container');
        const newCelular = `
            <div class="input-group mb-2 celular-entry">
                <input type="text" name="celulares[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 987654321">
                <button type="button" class="btn btn-outline-danger btn-sm remove-celular">Eliminar</button>
            </div>`;
        container.insertAdjacentHTML('beforeend', newCelular);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-celular')) {
            if (document.querySelectorAll('.celular-entry').length > 1) {
                e.target.closest('.celular-entry').remove();
            }
        }
    });

    // Cargar provincias y distritos al inicio si hay valores preseleccionados
    const departamentoSelect = document.getElementById('departamento');
    const provinciaSelect = document.getElementById('provincia');
    
    if (departamentoSelect.value) {
        const departamentoEvent = new Event('change');
        departamentoSelect.dispatchEvent(departamentoEvent);
    }
    
    if (provinciaSelect.value) {
        const provinciaEvent = new Event('change');
        provinciaSelect.dispatchEvent(provinciaEvent);
    }
});
   </script>
@endpush