```blade
@extends('admin.layouts.app')

@section('title', 'Nueva Cita de Mantenimiento')

@push('styles')
    <!-- Select2 para mejorar los selectores -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        .cliente-info, .vehiculo-info, .conductor-info {
            display: none;
        }
        
        .card-header-tabs .nav-link {
            cursor: pointer;
        }
        
        .telefono-container, .email-container, .conductor-container, .vehiculo-container, .servicio-container {
            position: relative;
            margin-bottom: 10px;
        }
        
        .remove-item {
            position: absolute;
            right: 0;
            top: 0;
            cursor: pointer;
            color: red;
            padding: 5px;
        }
        
        #clienteForm, #vehiculoForm {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nueva Cita de Mantenimiento</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.mantenimiento.citas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <form id="citaForm" action="{{ route('admin.mantenimiento.citas.store') }}" method="POST">
        @csrf
        
        <!-- Datos del Cliente -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Datos del Cliente</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cliente_search" class="form-label">Buscar Cliente (DNI/RUC/Nombre)</label>
                        <div class="input-group">
                            <input type="text" id="cliente_search" class="form-control" placeholder="Ingrese DNI, RUC o nombre del cliente">
                            <button type="button" id="buscarCliente" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <button type="button" id="nuevoCliente" class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Información del cliente seleccionado -->
                <div id="clienteInfo" class="cliente-info">
                    <div class="alert alert-info">
                        <h6>Cliente Seleccionado:</h6>
                        <p id="cliente_nombre"></p>
                        <p id="cliente_documento"></p>
                        <p id="cliente_direccion"></p>
                        <input type="hidden" name="cliente_id" id="cliente_id">
                    </div>
                </div>
                
                <!-- Formulario para nuevo cliente -->
                <div id="clienteForm">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="tipoClienteTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="persona-tab" data-bs-toggle="tab" href="#persona" role="tab" aria-controls="persona" aria-selected="true">Persona Natural</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="empresa-tab" data-bs-toggle="tab" href="#empresa" role="tab" aria-controls="empresa" aria-selected="false">Empresa</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="tipoClienteTabContent">
                                <!-- Tab Persona Natural -->
                                <div class="tab-pane fade show active" id="persona" role="tabpanel" aria-labelledby="persona-tab">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="dni" class="form-label">DNI</label>
                                            <input type="text" class="form-control" id="dni" maxlength="8">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="nombres" class="form-label">Nombres</label>
                                            <input type="text" class="form-control" id="nombres">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                            <input type="text" class="form-control" id="apellido_paterno">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                            <input type="text" class="form-control" id="apellido_materno">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ocupacion_persona" class="form-label">Ocupación</label>
                                            <input type="text" class="form-control" id="ocupacion_persona">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tab Empresa -->
                                <div class="tab-pane fade" id="empresa" role="tabpanel" aria-labelledby="empresa-tab">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="ruc" class="form-label">RUC</label>
                                            <input type="text" class="form-control" id="ruc" maxlength="11">
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label for="razon_social" class="form-label">Razón Social</label>
                                            <input type="text" class="form-control" id="razon_social">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Datos comunes para ambos tipos de cliente -->
                            <div class="row mt-3">
                                <div class="col-md-8 mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <input type="text" class="form-control" id="departamento">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="provincia" class="form-label">Provincia</label>
                                    <input type="text" class="form-control" id="provincia">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="distrito" class="form-label">Distrito</label>
                                    <input type="text" class="form-control" id="distrito">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="correo" class="form-label">Correo Principal</label>
                                    <input type="email" class="form-control" id="correo">
                                </div>
                            </div>
                            
                            <!-- Teléfonos (múltiples) -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Teléfonos</label>
                                    <div id="telefonos-container">
                                        <div class="telefono-container row">
                                            <div class="col-md-3">
                                                <select class="form-select telefono-tipo">
                                                    <option value="celular">Celular</option>
                                                    <option value="fijo">Fijo</option>
                                                </select>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control telefono-numero" placeholder="Número de teléfono">
                                                    <button type="button" class="btn btn-outline-success add-telefono">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Correos adicionales (múltiples) -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Correos Adicionales</label>
                                    <div id="emails-container">
                                        <div class="email-container input-group mb-2">
                                            <input type="email" class="form-control email-valor" placeholder="Correo electrónico adicional">
                                            <button type="button" class="btn btn-outline-success add-email">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sección de conductores (múltiples) -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="cliente_es_conductor" checked>
                                        <label class="form-check-label" for="cliente_es_conductor">
                                            El cliente es el conductor
                                        </label>
                                    </div>
                                    
                                    <div id="conductores-container" style="display: none;">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h6>Conductores</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="conductor-container row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">DNI</label>
                                                        <input type="text" class="form-control conductor-dni" maxlength="8">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Nombres</label>
                                                        <input type="text" class="form-control conductor-nombres">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Apellidos</label>
                                                        <input type="text" class="form-control conductor-apellidos">
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-success add-conductor">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-3">
                                <button type="button" id="guardarCliente" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cliente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Datos del Vehículo -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Datos del Vehículo</h5>
            </div>
            <div class="card-body">
                <div id="vehiculos-container">
                    <div class="vehiculo-section mb-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vehiculo_search" class="form-label">Buscar Vehículo (Placa)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control vehiculo-search" placeholder="Ingrese número de placa">
                                    <button type="button" class="btn btn-outline-primary buscar-vehiculo">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button type="button" class="btn btn-outline-success nuevo-vehiculo">
                                        <i class="fas fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información del vehículo seleccionado -->
                        <div class="vehiculo-info" style="display: none;">
                            <div class="alert alert-info">
                                <h6>Vehículo Seleccionado:</h6>
                                <p class="vehiculo-marca-modelo"></p>
                                <p class="vehiculo-placa"></p>
                                <p class="vehiculo-detalles"></p>
                                <input type="hidden" name="vehiculos[0][id]" class="vehiculo-id">
                                <button type="button" class="btn btn-success agregar-vehiculo-encontrado">Agregar Vehículo Encontrado</button>
                            </div>
                        </div>
                        
                        <!-- Formulario para nuevo vehículo -->
                        <div class="vehiculo-form" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Nuevo Vehículo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Marca</label>
                                            <select class="form-select vehiculo-marca" name="vehiculos[0][marca_id]">
                                                <option value="">Seleccione una marca</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Modelo</label>
                                            <select class="form-select vehiculo-modelo" name="vehiculos[0][modelo_id]">
                                                <option value="">Seleccione un modelo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Año Fabricación</label>
                                            <input type="number" class="form-control vehiculo-anio" name="vehiculos[0][anio]" min="1900" max="{{ date('Y') + 1 }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Placa</label>
                                            <input type="text" class="form-control vehiculo-placa-input" name="vehiculos[0][nro_placa]" maxlength="10">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">N° Chasis</label>
                                            <input type="text" class="form-control vehiculo-chasis" name="vehiculos[0][serie_vim]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">N° Motor</label>
                                            <input type="text" class="form-control vehiculo-motor" name="vehiculos[0][motor]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Color</label>
                                            <input type="text" class="form-control vehiculo-color" name="vehiculos[0][color]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Combustible</label>
                                            <select class="form-select vehiculo-combustible" name="vehiculos[0][combustible_id]">
                                                <option value="">Seleccione un combustible</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kilometraje</label>
                                            <input type="number" class="form-control vehiculo-kilometraje" name="vehiculos[0][kilometraje]" min="0">
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <button type="button" class="btn btn-primary guardar-vehiculo">
                                            <i class="fas fa-save"></i> Guardar Vehículo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Servicios requeridos para este vehículo -->
                        <div class="servicios-section mt-3" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Servicios Requeridos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="servicios-container">
                                        <div class="servicio-container input-group mb-2">
                                            <input type="text" name="vehiculos[0][servicios][]" class="form-control servicio-descripcion" placeholder="Descripción del servicio requerido">
                                            <button type="button" class="btn btn-outline-success add-servicio">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-3">
                    <button type="button" id="agregarVehiculo" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Otro Vehículo
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Datos de la Cita -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Datos de la Cita</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_hora_cita" class="form-label">Fecha y Hora de la Cita</label>
                        <input type="datetime-local" name="fecha_hora_cita" id="fecha_hora_cita" class="form-control" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="motivo_visita" class="form-label">Motivo de la Visita</label>
                        <input type="text" name="motivo_visita" id="motivo_visita" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="descripcion_problema" class="form-label">Descripción del Problema</label>
                        <textarea name="descripcion_problema" id="descripcion_problema" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="asesor_servicio" class="form-label">Asesor de Servicio</label>
                        <select name="asesor_servicio" id="asesor_servicio" class="form-select">
                            <option value="">Seleccione un asesor</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="notas_adicionales" class="form-label">Observaciones</label>
                        <textarea name="notas_adicionales" id="notas_adicionales" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <!-- Adelanto de dinero -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="tiene_adelanto">
                            <label class="form-check-label" for="tiene_adelanto">
                                Registrar adelanto de dinero
                            </label>
                        </div>
                        
                        <div id="adelanto-container" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="adelanto" class="form-label">Monto de Adelanto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input type="number" name="adelanto" id="adelanto" class="form-control" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-select">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                        <option value="transferencia">Transferencia Bancaria</option>
                                        <option value="yape">Yape</option>
                                        <option value="plin">Plin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-calendar-check"></i> Registrar Cita
            </button>
        </div>
    </form>
@endsection

```blade
@push('scripts')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Definir activeSubMenu globalmente antes de que Alpine.js se inicialice
        window.activeSubMenu = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Asegurarse de que los formularios estén cerrados por defecto
            $('#clienteForm').hide();
            $('.vehiculo-form').hide();
            $('.vehiculo-info').hide();
            $('.servicios-section').hide();
            $('#clienteInfo').hide();
            $('#conductores-container').hide();
            $('#adelanto-container').hide();
            
            // Inicializar Select2
            $('.form-select').select2({
                theme: 'bootstrap-5'
            });
            
            // Variables para controlar el estado
            let clienteSeleccionado = false;
            
            // ==================== MANEJO DE CLIENTE ====================
            
            $('#buscarCliente').click(function() {
                const query = $('#cliente_search').val().trim();
                if (!query) {
                    alert('Ingrese un DNI, RUC o nombre para buscar');
                    return;
                }
                
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Buscando...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('admin.mantenimiento.clientes.buscar') }}",
                    type: 'GET',
                    data: { query: query },
                    dataType: 'json',
                    success: function(response) {
                        if (Array.isArray(response) && response.length === 0) {
                            alert('No se encontraron clientes con ese criterio');
                            return;
                        }
                        if (response.length === 1) {
                            seleccionarCliente(response[0]);
                            return;
                        }
                        if (response.length > 1) {
                            mostrarSelectorClientes(response);
                            return;
                        }
                    },
                    error: function(xhr) {
                        console.error('Error en la búsqueda:', xhr.responseText);
                        alert('Error al buscar cliente');
                    },
                    complete: function() {
                        $('#buscarCliente').html('<i class="fas fa-search"></i> Buscar').prop('disabled', false);
                    }
                });
            });
            
            function mostrarSelectorClientes(clientes) {
                let clientesHTML = '';
                clientes.forEach(cliente => {
                    const nombre = cliente.tipo_cliente === 'persona' || cliente.tipo_cliente === 'natural'
                        ? `${cliente.nombres || ''} ${cliente.apellido_paterno || ''} ${cliente.apellido_materno || ''}`
                        : cliente.razon_social;
                    const documento = cliente.tipo_cliente === 'persona' || cliente.tipo_cliente === 'natural' ? 'DNI' : 'RUC';
                    clientesHTML += `
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">${nombre}</h5>
                                <small>${documento}: ${cliente.documento_identidad}</small>
                            </div>
                            <p class="mb-1">${cliente.direccion || ''}, ${cliente.distrito || ''}</p>
                            <button type="button" class="btn btn-sm btn-primary seleccionar-cliente-modal" 
                                    data-cliente-id="${cliente.id}">
                                <i class="fas fa-check"></i> Seleccionar
                            </button>
                        </div>
                    `;
                });
                
                const modalHTML = `
                    <div class="modal fade" id="selectorClientesModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Seleccionar Cliente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-group">
                                        ${clientesHTML}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#selectorClientesModal').remove();
                $('body').append(modalHTML);
                const modal = new bootstrap.Modal(document.getElementById('selectorClientesModal'));
                modal.show();
                
                $('.seleccionar-cliente-modal').click(function() {
                    const clienteId = $(this).data('cliente-id');
                    const clienteSeleccionado = clientes.find(c => c.id == clienteId);
                    if (clienteSeleccionado) {
                        seleccionarCliente(clienteSeleccionado);
                        modal.hide();
                    }
                });
            }
            
            $('#nuevoCliente').click(function() {
                $('#clienteInfo').hide();
                $('#clienteForm').show();
            });
            
            function seleccionarCliente(cliente) {
                if (cliente.tipo_cliente === 'persona' || cliente.tipo_cliente === 'natural') {
                    $('#cliente_nombre').text(`${cliente.nombres || ''} ${cliente.apellido_paterno || ''} ${cliente.apellido_materno || ''}`);
                } else {
                    $('#cliente_nombre').text(cliente.razon_social || '');
                }
                const tipoDoc = (cliente.tipo_cliente === 'persona' || cliente.tipo_cliente === 'natural') ? 'DNI' : 'RUC';
                $('#cliente_documento').text(`${tipoDoc}: ${cliente.documento_identidad || ''}`);
                $('#cliente_direccion').text(`${cliente.direccion || ''}, ${cliente.departamento || ''}, ${cliente.provincia || ''}, ${cliente.distrito || ''}`);
                $('#cliente_id').val(cliente.id);
                
                $('#clienteInfo').show();
                $('#clienteForm').hide();
                clienteSeleccionado = true;
            }
            
            $('#guardarCliente').click(function() {
                const tipoCliente = $('#persona-tab').hasClass('active') ? 'persona' : 'empresa';
                
                // Validación manual
                if (tipoCliente === 'persona') {
                    if (!$('#dni').val() || !$('#nombres').val() || !$('#apellido_paterno').val()) {
                        alert('Complete DNI, Nombres y Apellido Paterno para la persona');
                        return;
                    }
                } else {
                    if (!$('#ruc').val() || !$('#razon_social').val()) {
                        alert('Complete RUC y Razón Social para la empresa');
                        return;
                    }
                }
                if (!$('#direccion').val() || !$('#departamento').val() || !$('#provincia').val() || !$('#distrito').val()) {
                    alert('Complete los campos de dirección');
                    return;
                }
                
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);
                
                const telefonos = [];
                $('.telefono-container').each(function() {
                    const tipo = $(this).find('.telefono-tipo').val();
                    const numero = $(this).find('.telefono-numero').val();
                    if (numero) telefonos.push({ tipo, numero });
                });
                
                const emails = [];
                $('.email-container').each(function() {
                    const email = $(this).find('.email-valor').val();
                    if (email) emails.push(email);
                });
                
                const conductores = [];
                if (!$('#cliente_es_conductor').is(':checked')) {
                    $('.conductor-container').each(function() {
                        const dni = $(this).find('.conductor-dni').val();
                        const nombres = $(this).find('.conductor-nombres').val();
                        const apellidos = $(this).find('.conductor-apellidos').val();
                        if (dni && nombres && apellidos) {
                            conductores.push({ documento_identidad: dni, nombres, apellidos });
                        }
                    });
                }
                
                const clienteData = {
                    documento_identidad: tipoCliente === 'persona' ? $('#dni').val() : $('#ruc').val(),
                    tipo_cliente: tipoCliente,
                    nombres: tipoCliente === 'persona' ? $('#nombres').val() : null,
                    apellido_paterno: tipoCliente === 'persona' ? $('#apellido_paterno').val() : null,
                    apellido_materno: tipoCliente === 'persona' ? $('#apellido_materno').val() : null,
                    razon_social: tipoCliente === 'empresa' ? $('#razon_social').val() : null,
                    direccion: $('#direccion').val(),
                    departamento: $('#departamento').val(),
                    provincia: $('#provincia').val(),
                    distrito: $('#distrito').val(),
                    correo: $('#correo').val(),
                    ocupacion: tipoCliente === 'persona' ? $('#ocupacion_persona').val() : null,
                    telefonos,
                    conductores
                };
                
                $.ajax({
                    url: "{{ route('admin.mantenimiento.clientes.guardar') }}",
                    type: 'POST',
                    data: clienteData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            seleccionarCliente(response.cliente);
                            alert('Cliente guardado correctamente');
                        } else {
                            alert('Error al guardar cliente: ' + (response.message || 'Error desconocido'));
                        }
                    },
                    error: function(xhr) {
                        alert('Error al guardar cliente: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                    },
                    complete: function() {
                        $('#guardarCliente').html('<i class="fas fa-save"></i> Guardar Cliente').prop('disabled', false);
                    }
                });
            });
            
            // ==================== MANEJO DE VEHÍCULOS ====================
            
            $(document).on('click', '.buscar-vehiculo', function() {
                const container = $(this).closest('.vehiculo-section');
                const placa = container.find('.vehiculo-search').val().trim();
                
                if (!placa) {
                    alert('Ingrese un número de placa para buscar');
                    return;
                }
                
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Buscando...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('admin.mantenimiento.vehiculos.buscar') }}",
                    type: 'GET',
                    data: { placa },
                    success: function(response) {
                        if (!response) {
                            alert('No se encontró un vehículo con esa placa');
                            return;
                        }
                        seleccionarVehiculo(container, response);
                    },
                    error: function(xhr) {
                        alert('Error al buscar vehículo');
                    },
                    complete: function() {
                        container.find('.buscar-vehiculo').html('<i class="fas fa-search"></i> Buscar').prop('disabled', false);
                    }
                });
            });
            
            $(document).on('click', '.nuevo-vehiculo', function() {
                const container = $(this).closest('.vehiculo-section');
                container.find('.vehiculo-info').hide();
                container.find('.vehiculo-form').show();
                container.find('.servicios-section').hide();
                cargarMarcas(container);
                cargarCombustibles(container);
            });
            
            function seleccionarVehiculo(container, vehiculo) {
                container.find('.vehiculo-marca-modelo').text(`${vehiculo.marca ? vehiculo.marca.nombre : ''} ${vehiculo.modelo ? vehiculo.modelo.nombre : ''}`);
                container.find('.vehiculo-placa').text(`Placa: ${vehiculo.nro_placa || ''}`);
                container.find('.vehiculo-detalles').text(`${vehiculo.color || ''} - Año: ${vehiculo.anio || ''} - Km: ${vehiculo.kilometraje || ''}`);
                container.find('.vehiculo-id').val(vehiculo.id);
                
                container.find('.vehiculo-info').show();
                container.find('.vehiculo-form').hide();
                container.find('.servicios-section').hide();
            }
            
            // Reemplaza la función existente con esta versión mejorada
$(document).on('click', '.agregar-vehiculo-encontrado', function() {
    const container = $(this).closest('.vehiculo-section');
    container.addClass('vehiculo-seleccionado');
    
    // Mostrar la sección de servicios
    container.find('.servicios-section').show();
    
    // Deshabilitar este botón para mostrar que ya se agregó
    $(this).prop('disabled', true).text('Vehículo Agregado');
    
    // Si no hay servicios, agregar uno vacío
    const serviciosContainer = container.find('.servicios-container');
    if (serviciosContainer.find('.servicio-container').length === 0) {
        const index = container.data('vehiculo-index') || 0;
        const servicioTemplate = `
            <div class="servicio-container input-group mb-2">
                <input type="text" name="vehiculos[${index}][servicios][]" class="form-control servicio-descripcion" placeholder="Descripción del servicio requerido">
                <button type="button" class="btn btn-outline-success add-servicio">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        `;
        serviciosContainer.append(servicioTemplate);
    }
    
    // Asegurar que el vehiculo-id esté correctamente asignado al name del input de servicios
    const vehiculoId = container.find('.vehiculo-id').val();
    console.log("Vehículo agregado con ID:", vehiculoId);
});

// Agrega esta función para garantizar que los vehículos seleccionados tengan servicios
function asegurarServiciosParaVehiculos() {
    // Para cada vehículo seleccionado, asegurarse de que tenga servicios
    $('.vehiculo-section.vehiculo-seleccionado').each(function() {
        const serviciosContainer = $(this).find('.servicios-container');
        
        // Si no hay servicios, añadir uno
        if (serviciosContainer.find('.servicio-container').length === 0) {
            const index = $(this).data('vehiculo-index') || 0;
            const servicioTemplate = `
                <div class="servicio-container input-group mb-2">
                    <input type="text" name="vehiculos[${index}][servicios][]" class="form-control servicio-descripcion" placeholder="Descripción del servicio requerido">
                    <button type="button" class="btn btn-outline-success add-servicio">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            `;
            serviciosContainer.append(servicioTemplate);
        }
    });
}

// Modifica la función buscar-vehiculo para asegurar que los vehículos seleccionados sean válidos
$(document).on('click', '.buscar-vehiculo', function() {
    const container = $(this).closest('.vehiculo-section');
    const placa = container.find('.vehiculo-search').val().trim();
    
    if (!placa) {
        alert('Ingrese un número de placa para buscar');
        return;
    }
    
    $(this).html('<i class="fas fa-spinner fa-spin"></i> Buscando...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('admin.mantenimiento.vehiculos.buscar') }}",
        type: 'GET',
        data: { placa },
        success: function(response) {
            if (!response) {
                alert('No se encontró un vehículo con esa placa');
                return;
            }
            
            // Asegurarse de que el vehículo tenga un ID válido
            if (!response.id) {
                alert('El vehículo encontrado no tiene un ID válido');
                return;
            }
            
            seleccionarVehiculo(container, response);
            
            // Mostrar el botón para agregar
            container.find('.agregar-vehiculo-encontrado').prop('disabled', false).text('Agregar Vehículo Encontrado');
        },
        error: function(xhr) {
            alert('Error al buscar vehículo');
        },
        complete: function() {
            container.find('.buscar-vehiculo').html('<i class="fas fa-search"></i> Buscar').prop('disabled', false);
        }
    });
});
            
            $(document).on('click', '.guardar-vehiculo', function() {
                const container = $(this).closest('.vehiculo-section');
                const btn = $(this);
                
                if (!clienteSeleccionado) {
                    alert('Debe seleccionar o registrar un cliente primero');
                    return;
                }
                
                const marca_id = container.find('.vehiculo-marca').val();
                const modelo_id = container.find('.vehiculo-modelo').val();
                const anio = container.find('.vehiculo-anio').val();
                const placa = container.find('.vehiculo-placa-input').val().trim();
                const chasis = container.find('.vehiculo-chasis').val().trim();
                const motor = container.find('.vehiculo-motor').val().trim();
                const color = container.find('.vehiculo-color').val().trim();
                const combustible_id = container.find('.vehiculo-combustible').val();
                const kilometraje = container.find('.vehiculo-kilometraje').val();
                
                if (!marca_id || !modelo_id || !anio || !placa || !chasis || !motor || !color || !combustible_id || !kilometraje) {
                    alert('Todos los campos del vehículo son obligatorios');
                    return;
                }
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                
                const vehiculoData = {
                    marca_id, modelo_id, anio, color, nro_placa: placa,
                    serie_vim: chasis, motor, combustible_id, kilometraje,
                    cliente_id: $('#cliente_id').val()
                };
                
                $.ajax({
                    url: "{{ route('admin.mantenimiento.vehiculos.guardar') }}",
                    type: 'POST',
                    data: vehiculoData,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            // Guarda el ID del vehículo y muestra la sección de servicios
                            seleccionarVehiculo(container, response.vehiculo);
                            container.addClass('vehiculo-seleccionado');
                            container.find('.servicios-section').show();
                            container.find('.vehiculo-id').val(response.vehiculo.id);
                            alert('Vehículo guardado correctamente');
                        } else {
                            alert('Error al guardar vehículo: ' + (response.message || 'Error desconocido'));
                        }
                    },
                    error: function(xhr) {
                        alert('Error al guardar vehículo: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Vehículo');
                    }
                });
            });
            
            function cargarMarcas(container) {
                $.ajax({
                    url: "{{ route('admin.mantenimiento.vehiculos.marcas') }}",
                    type: 'GET',
                    success: function(marcas) {
                        const select = container.find('.vehiculo-marca');
                        select.empty().append('<option value="">Seleccione una marca</option>');
                        marcas.forEach(marca => select.append(`<option value="${marca.id}">${marca.nombre}</option>`));
                    },
                    error: function(xhr) {
                        console.error('Error al cargar marcas:', xhr.responseText);
                    }
                });
            }
            
            $(document).on('change', '.vehiculo-marca', function() {
                const container = $(this).closest('.vehiculo-section');
                const marca_id = $(this).val();
                if (!marca_id) {
                    container.find('.vehiculo-modelo').empty().append('<option value="">Seleccione un modelo</option>');
                    return;
                }
                $.ajax({
                    url: "{{ route('admin.mantenimiento.vehiculos.modelos') }}",
                    type: 'GET',
                    data: { marca_id },
                    success: function(modelos) {
                        const select = container.find('.vehiculo-modelo');
                        select.empty().append('<option value="">Seleccione un modelo</option>');
                        modelos.forEach(modelo => select.append(`<option value="${modelo.id}">${modelo.nombre}</option>`));
                    },
                    error: function(xhr) {
                        console.error('Error al cargar modelos:', xhr.responseText);
                    }
                });
            });
            
            function cargarCombustibles(container) {
                $.ajax({
                    url: "{{ route('admin.mantenimiento.vehiculos.combustibles') }}",
                    type: 'GET',
                    success: function(combustibles) {
                        const select = container.find('.vehiculo-combustible');
                        select.empty().append('<option value="">Seleccione un combustible</option>');
                        combustibles.forEach(combustible => select.append(`<option value="${combustible.id}">${combustible.nombre}</option>`));
                    },
                    error: function(xhr) {
                        console.error('Error al cargar combustibles:', xhr.responseText);
                    }
                });
            }
            
            // ==================== MANEJO DE ELEMENTOS DINÁMICOS ====================
            
            $('#cliente_es_conductor').change(function() {
                $('#conductores-container').toggle(!$(this).is(':checked'));
            });
            
            $('#tiene_adelanto').change(function() {
                if ($(this).is(':checked')) {
                    $('#adelanto-container').show();
                } else {
                    $('#adelanto-container').hide();
                    $('#adelanto').val('');
                }
            });
            
            $(document).on('click', '.add-telefono', function() {
                const telefonoTemplate = `
                    <div class="telefono-container row mt-2">
                        <div class="col-md-3">
                            <select class="form-select telefono-tipo">
                                <option value="celular">Celular</option>
                                <option value="fijo">Fijo</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input type="text" class="form-control telefono-numero" placeholder="Número de teléfono">
                                <button type="button" class="btn btn-outline-danger remove-telefono">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#telefonos-container').append(telefonoTemplate);
            });
            
            $(document).on('click', '.remove-telefono', function() {
                $(this).closest('.telefono-container').remove();
            });
            
            $(document).on('click', '.add-email', function() {
                const emailTemplate = `
                    <div class="email-container input-group mb-2">
                        <input type="email" class="form-control email-valor" placeholder="Correo electrónico adicional">
                        <button type="button" class="btn btn-outline-danger remove-email">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                $('#emails-container').append(emailTemplate);
            });
            
            $(document).on('click', '.remove-email', function() {
                $(this).closest('.email-container').remove();
            });
            
            $(document).on('click', '.add-conductor', function() {
                const conductorTemplate = `
                    <div class="conductor-container row g-3 mt-2">
                        <div class="col-md-3">
                            <input type="text" class="form-control conductor-dni" maxlength="8" placeholder="DNI">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control conductor-nombres" placeholder="Nombres">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control conductor-apellidos" placeholder="Apellidos">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger remove-conductor">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                `;
                $(this).closest('.card-body').append(conductorTemplate);
            });
            
            $(document).on('click', '.remove-conductor', function() {
                $(this).closest('.conductor-container').remove();
            });
            
            $(document).on('click', '.add-servicio', function() {
                const container = $(this).closest('.vehiculo-section');
                const index = container.data('vehiculo-index') || 0;
                const servicioTemplate = `
                    <div class="servicio-container input-group mb-2">
                        <input type="text" name="vehiculos[${index}][servicios][]" class="form-control servicio-descripcion" placeholder="Descripción del servicio requerido">
                        <button type="button" class="btn btn-outline-danger remove-servicio">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.find('.servicios-container').append(servicioTemplate);
            });
            
            $(document).on('click', '.remove-servicio', function() {
                $(this).closest('.servicio-container').remove();
            });
            
            let vehiculoIndex = 0;
            $('#agregarVehiculo').click(function() {
                vehiculoIndex++;
                const vehiculoTemplate = `
                    <div class="vehiculo-section mb-4" data-vehiculo-index="${vehiculoIndex}">
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Buscar Vehículo (Placa)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control vehiculo-search" placeholder="Ingrese número de placa">
                                    <button type="button" class="btn btn-outline-primary buscar-vehiculo">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button type="button" class="btn btn-outline-success nuevo-vehiculo">
                                        <i class="fas fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-outline-danger remove-vehiculo">
                                    <i class="fas fa-trash"></i> Eliminar este vehículo
                                </button>
                            </div>
                        </div>
                        <div class="vehiculo-info" style="display: none;">
                            <div class="alert alert-info">
                                <h6>Vehículo Seleccionado:</h6>
                                <p class="vehiculo-marca-modelo"></p>
                                <p class="vehiculo-placa"></p>
                                <p class="vehiculo-detalles"></p>
                                <input type="hidden" name="vehiculos[${vehiculoIndex}][id]" class="vehiculo-id">
                                <button type="button" class="btn btn-success agregar-vehiculo-encontrado">Agregar Vehículo Encontrado</button>
                            </div>
                        </div>
                        <div class="vehiculo-form" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Nuevo Vehículo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Marca</label>
                                            <select class="form-select vehiculo-marca" name="vehiculos[${vehiculoIndex}][marca_id]">
                                                <option value="">Seleccione una marca</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Modelo</label>
                                            <select class="form-select vehiculo-modelo" name="vehiculos[${vehiculoIndex}][modelo_id]">
                                                <option value="">Seleccione un modelo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Año Fabricación</label>
                                            <input type="number" class="form-control vehiculo-anio" name="vehiculos[${vehiculoIndex}][anio]" min="1900" max="${new Date().getFullYear() + 1}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Placa</label>
                                            <input type="text" class="form-control vehiculo-placa-input" name="vehiculos[${vehiculoIndex}][nro_placa]" maxlength="10">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">N° Chasis</label>
                                            <input type="text" class="form-control vehiculo-chasis" name="vehiculos[${vehiculoIndex}][serie_vim]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">N° Motor</label>
                                            <input type="text" class="form-control vehiculo-motor" name="vehiculos[${vehiculoIndex}][motor]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Color</label>
                                            <input type="text" class="form-control vehiculo-color" name="vehiculos[${vehiculoIndex}][color]" maxlength="50">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Combustible</label>
                                            <select class="form-select vehiculo-combustible" name="vehiculos[${vehiculoIndex}][combustible_id]">
                                                <option value="">Seleccione un combustible</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kilometraje</label>
                                            <input type="number" class="form-control vehiculo-kilometraje" name="vehiculos[${vehiculoIndex}][kilometraje]" min="0">
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <button type="button" class="btn btn-primary guardar-vehiculo">
                                            <i class="fas fa-save"></i> Guardar Vehículo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="servicios-section mt-3" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Servicios Requeridos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="servicios-container">
                                        <div class="servicio-container input-group mb-2">
                                            <input type="text" name="vehiculos[${vehiculoIndex}][servicios][]" class="form-control servicio-descripcion" placeholder="Descripción del servicio requerido">
                                            <button type="button" class="btn btn-outline-success add-servicio">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#vehiculos-container').append(vehiculoTemplate);
                $('.vehiculo-section').last().find('.form-select').select2({ theme: 'bootstrap-5' });
            });
            
            $(document).on('click', '.remove-vehiculo', function() {
                $(this).closest('.vehiculo-section').remove();
            });
            
            // Validación del formulario antes de enviar
            // Reemplaza la función de validación actual con esta versión mejorada
// Reemplaza la función de validación actual con esta versión actualizada
$('#citaForm').submit(function(e) {
    e.preventDefault();

    // Eliminar servicios vacíos antes de enviar
    $('.servicio-descripcion').each(function() {
        if (!$(this).val().trim()) {
            $(this).closest('.servicio-container').remove();
        }
    });

    // Verificar cliente
    if (!$('#cliente_id').val()) {
        if ($('#clienteForm').is(':visible')) {
            // Lógica para guardar cliente nuevo
            $('#guardarCliente').click();
            return false;
        } else {
            alert('Debe seleccionar o registrar un cliente');
            return false;
        }
    }

    // Verificar vehículos y servicios
    let vehiculosValidos = false;
    
    // Primera pasada: verificar si hay vehículos ya agregados
    $('.vehiculo-section').each(function() {
        // Comprobar si este vehículo ya ha sido seleccionado y agregado
        if ($(this).find('.agregar-vehiculo-encontrado').prop('disabled') === true || 
            $(this).hasClass('vehiculo-seleccionado')) {
            // Este vehículo ha sido agregado correctamente
            vehiculosValidos = true;
            
            // Asegurar que tenga al menos un servicio
            const serviciosContainer = $(this).find('.servicios-container');
            if (serviciosContainer.find('.servicio-descripcion').length === 0) {
                alert('Cada vehículo seleccionado debe tener al menos un servicio');
                vehiculosValidos = false;
                return false;
            }
            
            // Verificar que al menos un servicio tenga texto
            let tieneServicio = false;
            serviciosContainer.find('.servicio-descripcion').each(function() {
                if ($(this).val().trim()) {
                    tieneServicio = true;
                    return false; // Salir del bucle each
                }
            });
            
            if (!tieneServicio) {
                alert('Debe ingresar al menos un servicio para el vehículo seleccionado');
                vehiculosValidos = false;
                return false;
            }
        }
    });
    
    if (!vehiculosValidos) {
        // Si no hay vehículos ya agregados, verificar si hay algún vehículo encontrado pero no agregado
        $('.vehiculo-section').each(function() {
            if ($(this).find('.vehiculo-info').is(':visible') && 
                !$(this).find('.agregar-vehiculo-encontrado').prop('disabled')) {
                alert('Ha seleccionado un vehículo pero no ha pulsado en "Agregar Vehículo Encontrado"');
                return false;
            }
        });
        
        alert('Debe registrar o seleccionar al menos un vehículo con servicios');
        return false;
    }

    // Verificar campos obligatorios de la cita
    if (!$('#fecha_hora_cita').val() || !$('#motivo_visita').val()) {
        alert('Complete la fecha y el motivo de la cita');
        return false;
    }

    // Verificar adelanto si está seleccionado
    if ($('#tiene_adelanto').is(':checked')) {
        if (!$('#adelanto').val() || parseFloat($('#adelanto').val()) <= 0) {
            alert('Ingrese un monto de adelanto válido');
            return false;
        }
    }

    console.log('Formulario válido, enviando...');
    
    // Enviar el formulario si todo está correcto
    this.submit();
});
        });
    </script>
@endpush