@extends('admin.layouts.app')

@section('title', 'Editar Cliente')

@section('header', 'Editar Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h5 class="mb-0 fw-semibold">Editar Cliente</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <!-- Sección: Documento -->
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-3">Información del Documento</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_cliente" class="form-label small text-muted mb-1">Tipo de Cliente *</label>
                            <select name="tipo_cliente" id="tipo_cliente" class="form-select form-select-sm shadow-sm @error('tipo_cliente') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                <option value="natural" {{ $cliente->tipo_cliente == 'natural' ? 'selected' : '' }}>Persona Natural (DNI)</option>
                                <option value="juridica" {{ $cliente->tipo_cliente == 'juridica' ? 'selected' : '' }}>Persona Jurídica (RUC)</option>
                            </select>
                            @error('tipo_cliente') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="documento_identidad" class="form-label small text-muted mb-1">Número de Documento *</label>
                            <input type="text" name="documento_identidad" id="documento_identidad" class="form-control form-control-sm shadow-sm @error('documento_identidad') is-invalid @enderror" value="{{ $cliente->documento_identidad }}" required>
                            @error('documento_identidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="validar_documento" class="btn btn-sm btn-primary w-100 shadow-sm">Validar Datos</button>
                        </div>

                        <!-- Campos para DNI -->
                        <div class="col-12 dni-fields" style="{{ $cliente->tipo_cliente == 'natural' ? '' : 'display: none;' }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label small text-muted mb-1">Apellido Paterno</label>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control form-control-sm shadow-sm @error('apellido_paterno') is-invalid @enderror" value="{{ $cliente->apellido_paterno }}" readonly>
                                    @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label small text-muted mb-1">Apellido Materno</label>
                                    <input type="text" name="apellido_materno" id="apellido_materno" class="form-control form-control-sm shadow-sm @error('apellido_materno') is-invalid @enderror" value="{{ $cliente->apellido_materno }}" readonly>
                                    @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="nombres" class="form-label small text-muted mb-1">Nombres</label>
                                    <input type="text" name="nombres" id="nombres" class="form-control form-control-sm shadow-sm @error('nombres') is-invalid @enderror" value="{{ $cliente->nombres }}" readonly>
                                    @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campo para RUC -->
                        <div class="col-12 ruc-fields" style="{{ $cliente->tipo_cliente == 'juridica' ? '' : 'display: none;' }}">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="razon_social" class="form-label small text-muted mb-1">Razón Social</label>
                                    <input type="text" name="razon_social" id="razon_social" class="form-control form-control-sm shadow-sm @error('razon_social') is-invalid @enderror" value="{{ $cliente->razon_social }}" readonly>
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
                                    <option value="{{ $departamento }}" {{ $cliente->departamento == $departamento ? 'selected' : '' }}>{{ $departamento }}</option>
                                @endforeach
                            </select>
                            @error('departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="provincia" class="form-label small text-muted mb-1">Provincia *</label>
                            <select name="provincia" id="provincia" class="form-select form-select-sm shadow-sm @error('provincia') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($provincias as $provincia)
                                    <option value="{{ $provincia }}" {{ $cliente->provincia == $provincia ? 'selected' : '' }}>{{ $provincia }}</option>
                                @endforeach
                            </select>
                            @error('provincia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="distrito" class="form-label small text-muted mb-1">Distrito *</label>
                            <select name="distrito" id="distrito" class="form-select form-select-sm shadow-sm @error('distrito') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($distritos as $distrito)
                                    <option value="{{ $distrito }}" {{ $cliente->distrito == $distrito ? 'selected' : '' }}>{{ $distrito }}</option>
                                @endforeach
                            </select>
                            @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="col-md-6 mt-4">
                            <label for="correo" class="form-label small text-muted mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" id="correo" class="form-control form-control-sm shadow-sm @error('correo') is-invalid @enderror" placeholder="Ej: cliente@dominio.com" value="{{ $cliente->correo }}">
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6 mt-4">
                            <label for="categoria_cliente_id" class="form-label small text-muted mb-1">Categoría Cliente *</label>
                            <select name="categoria_cliente_id" id="categoria_cliente_id" class="form-select form-select-sm shadow-sm @error('categoria_cliente_id') is-invalid @enderror" required>
                                <option value="">Seleccione</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ $cliente->categoria_cliente_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Canal de Captación -->
                        <div class="col-md-12 mt-4">
                            <label for="canal_captacion_id" class="form-label small text-muted mb-1">Canal de Captación *</label>
                            <div class="btn-group d-flex flex-wrap gap-2" role="group" aria-label="Canal de Captación">
                                @foreach ($canales as $canal)
                                    <input type="radio" class="btn-check" name="canal_captacion_id" id="canal_{{ $canal->id }}" value="{{ $canal->id }}" {{ $cliente->canal_captacion_id == $canal->id ? 'checked' : '' }} required>
                                    <label class="btn btn-sm btn-outline-primary" for="canal_{{ $canal->id }}">{{ $canal->nombre }}</label>
                                @endforeach
                            </div>
                            @error('canal_captacion_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Sección: Teléfonos -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Teléfonos de Contacto</h6>
                            <div id="telefonos-container">
                                @if($cliente->telefonos->where('tipo', 'telefono')->count() > 0)
                                    @foreach($cliente->telefonos->where('tipo', 'telefono') as $telefono)
                                        <div class="input-group mb-2 telefono-entry">
                                            <input type="text" name="telefonos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 01234567" value="{{ $telefono->numero }}">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-telefono">Eliminar</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 telefono-entry">
                                        <input type="text" name="telefonos[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 01234567">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-telefono">Eliminar</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 shadow-sm" id="add-telefono">+ Agregar Teléfono</button>
                        </div>

                        <!-- Sección: Celulares -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted fw-semibold mb-3">Celulares de Contacto</h6>
                            <div id="celulares-container">
                                @if($cliente->telefonos->where('tipo', 'celular')->count() > 0)
                                    @foreach($cliente->telefonos->where('tipo', 'celular') as $celular)
                                        <div class="input-group mb-2 celular-entry">
                                            <input type="text" name="celulares[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 987654321" value="{{ $celular->numero }}">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-celular">Eliminar</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 celular-entry">
                                        <input type="text" name="celulares[]" class="form-control form-control-sm shadow-sm" placeholder="Ej: 987654321">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-celular">Eliminar</button>
                                    </div>
                                @endif
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
                            Actualizar Cliente
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
        
        if (tipoCliente === 'natural') {
            dniFields.forEach(field => field.style.display = 'block');
            rucFields.forEach(field => field.style.display = 'none');
        } else if (tipoCliente === 'juridica') {
            dniFields.forEach(field => field.style.display = 'none');
            rucFields.forEach(field => field.style.display = 'block');
        } else {
            dniFields.forEach(field => field.style.display = 'none');
            rucFields.forEach(field => field.style.display = 'none');
        }
    }
    
    const tipoClienteSelect = document.getElementById('tipo_cliente');
    tipoClienteSelect.addEventListener('change', mostrarCamposSegunTipoCliente);
    
    // Si ya hay un tipo seleccionado al cargar la página, mostrar los campos correspondientes
    if (tipoClienteSelect.value) {
        mostrarCamposSegunTipoCliente();
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
                        
                        // Si estamos editando y hay una provincia seleccionada, la seleccionamos
                        const selectedProvincia = '{{ $cliente->provincia }}';
                        if (selectedProvincia) {
                            provinciaSelect.value = selectedProvincia;
                            const event = new Event('change');
                            provinciaSelect.dispatchEvent(event);
                        }
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
                        
                        // Si estamos editando y hay un distrito seleccionado, lo seleccionamos
                        const selectedDistrito = '{{ $cliente->distrito }}';
                        if (selectedDistrito) {
                            distritoSelect.value = selectedDistrito;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al cargar distritos:', error);
                    distritoSelect.disabled = false;
                    distritoSelect.innerHTML = '<option value="">Error al cargar - Intente de nuevo</option>';
                });
        }
    });

    // Validar documento con API Peru Dev
    document.getElementById('validar_documento').addEventListener('click', function () {
        const tipoCliente = document.getElementById('tipo_cliente').value;
        const numeroDocumento = document.getElementById('documento_identidad').value;

        if (!tipoCliente || !numeroDocumento) {
            alert('Por favor, seleccione el tipo de cliente y proporcione el número de documento.');
            return;
        }

        // Determinar tipo de documento según tipo de cliente
        const tipoDocumento = tipoCliente === 'natural' ? 'DNI' : 'RUC';
        
        // Mostrar indicador de carga
        const validarBtn = document.getElementById('validar_documento');
        validarBtn.disabled = true;
        validarBtn.textContent = 'Validando...';

        console.log('Enviando solicitud para validar documento:', { tipo_documento: tipoDocumento, numero_documento: numeroDocumento });

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

                    // Si vienen datos de ubicación, actualizar selectores
                    if (data.data.departamento) {
                        document.getElementById('departamento').value = data.data.departamento;
                        
                        // Disparar evento de cambio para cargar provincias
                        const departamentoEvent = new Event('change');
                        document.getElementById('departamento').dispatchEvent(departamentoEvent);

                        // Esperar a que carguen las provincias
                        setTimeout(() => {
                            if (data.data.provincia) {
                                document.getElementById('provincia').value = data.data.provincia;
                                
                                // Disparar evento de cambio para cargar distritos
                                const provinciaEvent = new Event('change');
                                document.getElementById('provincia').dispatchEvent(provinciaEvent);

                                // Esperar a que carguen los distritos
                                setTimeout(() => {
                                    if (data.data.distrito) {
                                        document.getElementById('distrito').value = data.data.distrito;
                                    }
                                }, 500);
                            }
                        }, 500);
                    }
                }
            } else {
                alert(data.message || 'No se encontraron datos para el documento ingresado.');
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Error al validar el documento. Intente nuevamente.');
        })
        .finally(() => {
            // Restaurar botón
            validarBtn.disabled = false;
            validarBtn.textContent = 'Validar Datos';
        });
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
});
   </script>
@endpush