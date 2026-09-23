@extends('admin.layouts.app')

@section('title', 'Nuevo Proveedor')

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-plus text-info me-2"></i> Crear Proveedor
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 text-shadow-sm d-flex align-items-center">
                    Nuevo Proveedor
                </h2>
                <p class="text-white-50 mb-0">Ingresa los datos para registrar un proveedor.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.compras.proveedores.index') }}" class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                    <i class="fas fa-arrow-left text-primary me-2"></i> Volver a Listado
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative" style="top: -3.5rem; z-index: 10;">
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card dashboard-card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.compras.proveedores.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <!-- Sección: Documento -->
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-3">Información del Documento</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_documento" class="form-label small text-muted mb-1">Tipo de Documento *</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select form-select-sm shadow-sm @error('tipo_documento') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                <option value="DNI" {{ old('tipo_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="RUC" {{ old('tipo_documento') == 'RUC' ? 'selected' : '' }}>RUC</option>
                            </select>
                            @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="numero_documento" class="form-label small text-muted mb-1">Número de Documento *</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control form-control-sm shadow-sm @error('numero_documento') is-invalid @enderror" value="{{ old('numero_documento') }}" required>
                            @error('numero_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="validar_documento" class="btn btn-sm btn-primary w-100 shadow-sm">Validar Datos</button>
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
                        <div class="col-md-6">
                            <label for="direccion" class="form-label small text-muted mb-1">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control form-control-sm shadow-sm @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
                            @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="departamento" class="form-label small text-muted mb-1">Departamento *</label>
                            <select name="departamento" id="departamento" class="form-select form-select-sm shadow-sm @error('departamento') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento }}" {{ old('departamento') == $departamento ? 'selected' : '' }}>{{ $departamento }}</option>
                                @endforeach
                            </select>
                            @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="provincia" class="form-label small text-muted mb-1">Provincia *</label>
                            <select name="provincia" id="provincia" class="form-select form-select-sm shadow-sm @error('provincia') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                            </select>
                            @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="distrito" class="form-label small text-muted mb-1">Distrito *</label>
                            <select name="distrito" id="distrito" class="form-select form-select-sm shadow-sm @error('distrito') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                            </select>
                            @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Sección: Detalles del Proveedor -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Detalles del Proveedor</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="categoria_proveedor_id" class="form-label small text-muted mb-1">Categoría Proveedor *</label>
                            <select name="categoria_proveedor_id" id="categoria_proveedor_id" class="form-select form-select-sm shadow-sm @error('categoria_proveedor_id') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_proveedor_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre_categoria_proveedor }}</option>
                                @endforeach
                            </select>
                            @error('categoria_proveedor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="cubre_garantias" class="form-label small text-muted mb-1">¿Cubre Garantías? *</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="cubre_garantias" id="cubre_garantias" value="Sí" {{ old('cubre_garantias') == 'Sí' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="cubre_garantias" id="cubre_garantias_label">
                                    {{ old('cubre_garantias') == 'Sí' ? 'Sí' : 'No' }}
                                </label>
                            </div>
                            @error('cubre_garantias') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="es_aseguradora" class="form-label small text-muted mb-1">¿Es Aseguradora? *</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="es_aseguradora" id="es_aseguradora" value="Sí" {{ old('es_aseguradora') == 'Sí' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="es_aseguradora" id="es_aseguradora_label">
                                    {{ old('es_aseguradora') == 'Sí' ? 'Sí' : 'No' }}
                                </label>
                            </div>
                            @error('es_aseguradora') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Sección: Correos -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Correos de Contacto</h6>
                            <div id="correos-container">
                                <div class="input-group mb-2 correo-entry">
                                    <input type="email" name="correos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: proveedor@dominio.com" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-correo">Eliminar</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 shadow-sm" id="add-correo">+ Agregar Correo</button>
                        </div>

                        <!-- Sección: Contactos -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Contactos</h6>
                            <div id="contactos-container">
                                <div class="row mb-2 contacto-entry">
                                    <div class="col-md-6">
                                        <input type="text" name="contactos[0][nombre]" class="form-control form-control-sm shadow-sm" placeholder="Nombre del contacto" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="contactos[0][telefono]" class="form-control form-control-sm shadow-sm" placeholder="Teléfono" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-contacto w-100">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 shadow-sm" id="add-contacto">+ Agregar Contacto</button>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.compras.proveedores.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
    <style>
        .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
        .form-check-label {
            margin-left: 0.5em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            'use strict';

            // Función para actualizar las etiquetas de los switches
            function updateSwitchLabels() {
                const switches = document.querySelectorAll('.form-check-input');
                console.log('Switches encontrados:', switches.length); // Depuración
                switches.forEach(switchInput => {
                    const label = switchInput.nextElementSibling;
                    console.log(`Switch ${switchInput.id}: checked=${switchInput.checked}`); // Depuración
                    label.textContent = switchInput.checked ? 'Sí' : 'No';
                });
            }

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

            // Actualizar etiquetas de los switches al cargar la página
            updateSwitchLabels();

            // Función para resetear el formulario
            function resetForm() {
                document.getElementById('numero_documento').value = '';
                document.getElementById('apellido_paterno').value = '';
                document.getElementById('apellido_materno').value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('razon_social').value = '';
                document.getElementById('direccion').value = '';
                document.getElementById('departamento').value = '';
                document.getElementById('provincia').innerHTML = '<option value="">Seleccione</option>';
                document.getElementById('distrito').innerHTML = '<option value="">Seleccione</option>';
                document.getElementById('categoria_proveedor_id').value = '';
                
                // Resetear los switches
                const cubreGarantiasSwitch = document.getElementById('cubre_garantias');
                const esAseguradoraSwitch = document.getElementById('es_aseguradora');
                cubreGarantiasSwitch.checked = false;
                esAseguradoraSwitch.checked = false;
                updateSwitchLabels();

                document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'none');
                document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'none');

                const correosContainer = document.getElementById('correos-container');
                correosContainer.innerHTML = `
                    <div class="input-group mb-2 correo-entry">
                        <input type="email" name="correos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: proveedor@dominio.com" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-correo">Eliminar</button>
                    </div>`;
                correoIndex = 1;

                const contactosContainer = document.getElementById('contactos-container');
                contactosContainer.innerHTML = `
                    <div class="row mb-2 contacto-entry">
                        <div class="col-md-6">
                            <input type="text" name="contactos[0][nombre]" class="form-control form-control-sm shadow-sm" placeholder="Nombre del contacto" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="contactos[0][telefono]" class="form-control form-control-sm shadow-sm" placeholder="Teléfono" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-contacto w-100">Eliminar</button>
                        </div>
                    </div>`;
                contactoIndex = 1;

                document.querySelector('form.needs-validation').classList.remove('was-validated');
            }

            document.getElementById('tipo_documento').addEventListener('change', function () {
                resetForm();
            });

            document.getElementById('validar_documento').addEventListener('click', function () {
                const tipoDocumento = document.getElementById('tipo_documento').value;
                const numeroDocumento = document.getElementById('numero_documento').value;

                if (!tipoDocumento || !numeroDocumento) {
                    alert('Por favor, seleccione el tipo de documento y proporcione el número.');
                    return;
                }

                console.log('Enviando solicitud para validar documento:', { tipo_documento: tipoDocumento, numero_documento: numeroDocumento });

                fetch('{{ route('admin.compras.proveedores.validar.documento') }}', {
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
                .then(response => {
                    console.log('Estado de la respuesta:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    if (data.success) {
                        if (tipoDocumento === 'DNI') {
                            document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'block');
                            document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'none');
                            document.getElementById('apellido_paterno').value = data.data.apellido_paterno || '';
                            document.getElementById('apellido_materno').value = data.data.apellido_materno || '';
                            document.getElementById('nombres').value = data.data.nombres || '';
                            document.getElementById('razon_social').value = '';
                        } else if (tipoDocumento === 'RUC') {
                            document.querySelectorAll('.dni-fields').forEach(field => field.style.display = 'none');
                            document.querySelectorAll('.ruc-fields').forEach(field => field.style.display = 'block');
                            document.getElementById('razon_social').value = data.data.nombre_o_razon_social || '';
                            document.getElementById('apellido_paterno').value = '';
                            document.getElementById('apellido_materno').value = '';
                            document.getElementById('nombres').value = '';

                            document.getElementById('direccion').value = data.data.direccion || '';
                            document.getElementById('departamento').value = data.data.departamento || '';
                            
                            const departamentoEvent = new Event('change');
                            document.getElementById('departamento').dispatchEvent(departamentoEvent);

                            setTimeout(() => {
                                document.getElementById('provincia').value = data.data.provincia || '';
                                const provinciaEvent = new Event('change');
                                document.getElementById('provincia').dispatchEvent(provinciaEvent);

                                setTimeout(() => {
                                    document.getElementById('distrito').value = data.data.distrito || '';
                                }, 500);
                            }, 500);
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('Error al validar el documento.');
                });
            });

            // Cargar provincias y distritos dinámicamente
            document.getElementById('departamento').addEventListener('change', function () {
                const departamento = this.value;
                const provinciaSelect = document.getElementById('provincia');
                const distritoSelect = document.getElementById('distrito');

                // Resetear los selectores de provincia y distrito
                provinciaSelect.innerHTML = '<option value="">Seleccione</option>';
                distritoSelect.innerHTML = '<option value="">Seleccione</option>';

                if (departamento) {
                    console.log('Cargando provincias para departamento:', departamento);
                    fetch('{{ route('admin.compras.proveedores.provincias') }}?departamento=' + encodeURIComponent(departamento))
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la solicitud: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(provincias => {
                            console.log('Provincias recibidas:', provincias);
                            if (provincias.length === 0) {
                                alert('No se encontraron provincias para el departamento seleccionado.');
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
                            alert('Error al cargar las provincias. Por favor, intenta de nuevo.');
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
                    fetch('{{ route('admin.compras.proveedores.distritos') }}?departamento=' + encodeURIComponent(departamento) + '&provincia=' + encodeURIComponent(provincia))
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la solicitud: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(distritos => {
                            console.log('Distritos recibidos:', distritos);
                            if (distritos.length === 0) {
                                alert('No se encontraron distritos para la provincia seleccionada.');
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
                            alert('Error al cargar los distritos. Por favor, intenta de nuevo.');
                        });
                }
            });

            // Agregar y eliminar correos dinámicamente
            let correoIndex = 1;
            document.getElementById('add-correo').addEventListener('click', function () {
                const container = document.getElementById('correos-container');
                const newCorreo = `
                    <div class="input-group mb-2 correo-entry">
                        <input type="email" name="correos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: proveedor@dominio.com" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-correo">Eliminar</button>
                    </div>`;
                container.insertAdjacentHTML('beforeend', newCorreo);
                correoIndex++;
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-correo')) {
                    if (document.querySelectorAll('.correo-entry').length > 1) {
                        e.target.closest('.correo-entry').remove();
                    }
                }
            });

            // Agregar y eliminar contactos dinámicamente
            let contactoIndex = 1;
            document.getElementById('add-contacto').addEventListener('click', function () {
                const container = document.getElementById('contactos-container');
                const newContacto = `
                    <div class="row mb-2 contacto-entry">
                        <div class="col-md-6">
                            <input type="text" name="contactos[${contactoIndex}][nombre]" class="form-control form-control-sm shadow-sm" placeholder="Nombre del contacto" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="contactos[${contactoIndex}][telefono]" class="form-control form-control-sm shadow-sm" placeholder="Teléfono" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-contacto w-100">Eliminar</button>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', newContacto);
                contactoIndex++;
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-contacto')) {
                    if (document.querySelectorAll('.contacto-entry').length > 1) {
                        e.target.closest('.contacto-entry').remove();
                    }
                }
            });

            // Actualizar etiquetas de los switches en tiempo real
            document.querySelectorAll('.form-check-input').forEach(switchInput => {
                switchInput.addEventListener('change', function () {
                    const label = this.nextElementSibling;
                    console.log(`Switch ${this.id} cambiado: checked=${this.checked}`); // Depuración
                    label.textContent = this.checked ? 'Sí' : 'No';
                });
            });
        });
    </script>
@endpush