{{-- resources/views/admin/ventas/pos/partials/modals/cliente-modal.blade.php --}}
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="clienteModalLabel">
                    <i class="fas fa-users me-2"></i>Gestionar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-primary">
                        <i class="fas fa-search me-2"></i>Buscar Cliente Existente
                    </h6>
                    <small class="text-muted">Selecciona un cliente de la lista o busca por nombre/documento</small>
                </div>
               
                <div id="clienteTabsContent">
                    <!-- Contenido de búsqueda -->
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 border-end-0" id="cliente-search"
                                       placeholder="Buscar por nombre, documento, correo...">
                                <button class="btn btn-primary" type="button" id="btn-search-cliente">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Escriba al menos 2 caracteres para buscar o deje vacío para ver clientes recientes
                            </small>
                        </div>
                       
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 20%;">Documento</th>
                                        <th style="width: 30%;">Cliente</th>
                                        <th style="width: 15%;">Tipo</th>
                                        <th style="width: 25%;">Contacto</th>
                                        <th style="width: 10%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="cliente-results">
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                                            <br>Cargando clientes recientes...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                       
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                ¿No encuentra el cliente?
                                <a href="#" class="text-primary" id="switch-to-create">Crear nuevo cliente</a>
                            </small>
                        </div>
                    </div>
                   
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Los datos se almacenan de forma segura
                    </small>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let clienteSearchTimeout = null;
    
    // Inicializar modal de cliente
    function initClienteModal() {
        // Cargar clientes recientes al abrir el modal
        $('#clienteModal').on('shown.bs.modal', function() {
            cargarClientesRecientes();
        });
        
        // Cambio de tipo de cliente
        $('input[name="tipo_cliente"]').on('change', function() {
            toggleCamposCliente();
        });
        
        // Búsqueda de clientes
        $('#cliente-search').on('input', function() {
            clearTimeout(clienteSearchTimeout);
            const query = $(this).val().trim();
            
            clienteSearchTimeout = setTimeout(() => {
                buscarClientes(query);
            }, 300);
        });
        
        // Botón de búsqueda
        $('#btn-search-cliente').on('click', function() {
            const query = $('#cliente-search').val().trim();
            buscarClientes(query);
        });
        
        // Enter en campo de búsqueda
        $('#cliente-search').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const query = $(this).val().trim();
                buscarClientes(query);
            }
        });
        
        // Cambiar a tab de crear cliente
        $('#switch-to-create').on('click', function(e) {
            e.preventDefault();
            $('#create-tab').click();
        });
        
        // Envío del formulario de cliente
        $('#cliente-form').on('submit', function(e) {
            e.preventDefault();
            crearCliente();
        });
        
        // Resetear formulario al cambiar de tab
        $('#search-tab').on('click', function() {
            resetClienteForm();
        });
        
        $('#create-tab').on('click', function() {
            resetClienteForm();
        });
    }
    
    // Cargar clientes recientes
    function cargarClientesRecientes() {
        mostrarLoadingClientes();
        
        $.ajax({
            url: '{{ route("admin.ventas.pos.buscar-clientes") }}',
            method: 'GET',
            data: { query: '' },
            success: function(response) {
                mostrarResultadosClientes(response.items || []);
            },
            error: function(xhr) {
                console.error('Error al cargar clientes recientes:', xhr);
                mostrarErrorClientes('Error al cargar clientes recientes');
            }
        });
    }
    
    // Buscar clientes
    function buscarClientes(query) {
        if (query.length > 0 && query.length < 2) {
            return;
        }
        
        mostrarLoadingClientes();
        
        $.ajax({
            url: '{{ route("admin.ventas.pos.buscar-clientes") }}',
            method: 'GET',
            data: { query: query },
            success: function(response) {
                if (response.error) {
                    mostrarErrorClientes(response.message || 'Error en la búsqueda');
                    return;
                }
                
                mostrarResultadosClientes(response.items || [], query);
            },
            error: function(xhr) {
                console.error('Error en búsqueda de clientes:', xhr);
                let mensaje = 'Error en la búsqueda de clientes';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                
                mostrarErrorClientes(mensaje);
            }
        });
    }
    
    // Mostrar loading en tabla de clientes
    function mostrarLoadingClientes() {
        $('#cliente-results').html(`
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Buscando clientes...
                </td>
            </tr>
        `);
    }
    
    // Mostrar resultados de clientes
    function mostrarResultadosClientes(clientes, query = '') {
        const tbody = $('#cliente-results');
        
        if (clientes.length === 0) {
            const mensaje = query ? 
                `No se encontraron clientes con "${query}"` : 
                'No hay clientes recientes';
                
            tbody.html(`
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                        <br>${mensaje}
                        <br><small>¿Desea <a href="#" id="crear-nuevo-desde-busqueda" class="text-primary">crear un nuevo cliente</a>?</small>
                    </td>
                </tr>
            `);
            
            // Event listener para el enlace de crear nuevo
            $('#crear-nuevo-desde-busqueda').on('click', function(e) {
                e.preventDefault();
                $('#create-tab').click();
            });
            
            return;
        }
        
        let html = '';
        clientes.forEach(cliente => {
            const estadoBadge = cliente.activo ? 
                '<span class="badge bg-success">Activo</span>' : 
                '<span class="badge bg-secondary">Inactivo</span>';
                
            const tipoIcon = cliente.tipo === 'natural' ? 
                '<i class="fas fa-user text-primary"></i>' : 
                '<i class="fas fa-building text-info"></i>';
                
            html += `
                <tr class="cliente-row" data-cliente='${JSON.stringify(cliente)}'>
                    <td>
                        <small class="text-muted">${cliente.tipo_documento || 'DNI'}</small><br>
                        <strong>${cliente.documento}</strong>
                    </td>
                    <td>
                        ${tipoIcon} <strong>${cliente.nombre}</strong><br>
                        ${estadoBadge}
                    </td>
                    <td>
                        <span class="badge ${cliente.tipo === 'natural' ? 'bg-primary' : 'bg-info'}">
                            ${cliente.tipo === 'natural' ? 'Natural' : 'Jurídico'}
                        </span>
                    </td>
                    <td>
                        <small>
                            ${cliente.telefono ? `<i class="fas fa-phone me-1"></i>${cliente.telefono}<br>` : ''}
                            ${cliente.correo ? `<i class="fas fa-envelope me-1"></i>${cliente.correo}` : '<span class="text-muted">Sin correo</span>'}
                        </small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary select-cliente-btn" 
                                data-cliente-id="${cliente.id}"
                                ${!cliente.activo ? 'disabled' : ''}>
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.html(html);
        
        // Event listeners para seleccionar cliente
        $('.select-cliente-btn').on('click', function() {
            const clienteData = $(this).closest('.cliente-row').data('cliente');
            seleccionarCliente(clienteData);
        });
        
        // Event listener para hacer click en toda la fila
        $('.cliente-row').on('click', function() {
            const clienteData = $(this).data('cliente');
            if (clienteData.activo) {
                seleccionarCliente(clienteData);
            }
        });
    }
    
    // Mostrar error en tabla de clientes
    function mostrarErrorClientes(mensaje) {
        $('#cliente-results').html(`
            <tr>
                <td colspan="5" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <br>${mensaje}
                    <br><small><a href="#" onclick="cargarClientesRecientes()" class="text-primary">Reintentar</a></small>
                </td>
            </tr>
        `);
    }
    
    // Seleccionar cliente
    function seleccionarCliente(cliente) {
        // Actualizar la información del cliente seleccionado en el POS
        if (typeof window.pos !== 'undefined' && window.pos.seleccionarCliente) {
            window.pos.seleccionarCliente(cliente);
        } else {
            // Fallback si no existe el objeto pos
            $('#cliente-seleccionado').html(`
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <strong>${cliente.nombre}</strong><br>
                        <small class="text-muted">${cliente.documento}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="limpiarClienteSeleccionado()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).data('cliente-id', cliente.id);
        }
        
        // Cerrar modal
        $('#clienteModal').modal('hide');
        
        // Mostrar notificación
        mostrarNotificacion('Cliente seleccionado correctamente', 'success');
    }
    
    // Toggle campos según tipo de cliente
    function toggleCamposCliente() {
        const tipoCliente = $('input[name="tipo_cliente"]:checked').val();
        
        if (tipoCliente === 'natural') {
            $('#campos-natural').show();
            $('#campos-juridico').hide();
            $('#nombres, #apellido_paterno').attr('required', true);
            $('#razon_social').attr('required', false);
        } else {
            $('#campos-natural').hide();
            $('#campos-juridico').show();
            $('#nombres, #apellido_paterno').attr('required', false);
            $('#razon_social').attr('required', true);
        }
        
        // Limpiar validaciones
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').empty();
    }
    
    // Crear cliente
    function crearCliente() {
        const formData = new FormData($('#cliente-form')[0]);
        const submitBtn = $('#btn-crear-cliente');
        const btnText = submitBtn.find('.btn-text');
        
        // Limpiar validaciones previas
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').empty();
        
        // Mostrar loading
        submitBtn.prop('disabled', true);
        btnText.html('<i class="fas fa-spinner fa-spin me-1"></i>Creando...');
        
        $.ajax({
            url: '{{ route("admin.ventas.pos.crear-cliente") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Seleccionar el cliente recién creado
                    seleccionarCliente(response.cliente);
                    
                    // Resetear formulario
                    resetClienteForm();
                    
                    // Cambiar a tab de búsqueda
                    $('#search-tab').click();
                    
                    mostrarNotificacion('Cliente creado exitosamente', 'success');
                } else {
                    mostrarNotificacion(response.message || 'Error al crear cliente', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error al crear cliente:', xhr);
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Mostrar errores de validación
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(field => {
                        const input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[field][0]);
                    });
                } else {
                    const mensaje = xhr.responseJSON?.message || 'Error al crear el cliente';
                    mostrarNotificacion(mensaje, 'error');
                }
            },
            complete: function() {
                // Restaurar botón
                submitBtn.prop('disabled', false);
                btnText.html('<i class="fas fa-user-plus me-1"></i>Crear Cliente');
            }
        });
    }
    
    // Resetear formulario de cliente
    function resetClienteForm() {
        $('#cliente-form')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').empty();
        $('input[name="tipo_cliente"][value="natural"]').prop('checked', true);
        toggleCamposCliente();
    }
    
    // Función para mostrar notificaciones (debe estar definida globalmente)
    function mostrarNotificacion(mensaje, tipo = 'info') {
        if (typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(mensaje, tipo);
        } else {
            // Fallback simple
            const alertClass = tipo === 'success' ? 'alert-success' : 
                              tipo === 'error' ? 'alert-danger' : 'alert-info';
            
            const alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('body').append(alert);
            
            setTimeout(() => {
                alert.alert('close');
            }, 5000);
        }
    }
    
    // Inicializar cuando el documento esté listo
    initClienteModal();
});
</script>