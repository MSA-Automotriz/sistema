/**
 * Sistema de Punto de Venta - JavaScript Completo y Corregido
 * Incluye: búsqueda, gestión de clientes, carrito, validaciones
 */

$(document).ready(function() {
    // =============================================
    // VARIABLES GLOBALES
    // =============================================
    
    let selectedCategoriaParteId = '';
    let selectedCategoriaServicioId = '';
    let itemsSeleccionados = [];
    let tipoActivo = 'todo';
    let generarRequerimiento = false;
    let porcentajeAbono = 100;
    let searchTimeout;
    let clienteSearchTimeout;
    let currentPage = 1;
    let isLoadingMore = false;
    let clientesEncontrados = [];
    
    const SEARCH_DELAY = 300;
    const ITEMS_PER_PAGE = 50;
    
    // =============================================
    // INICIALIZACIÓN
    // =============================================
    
    init();
    
    function init() {
        configurarEventos();
        mostrarTodasLasPartes();
        cargarItemsPopulares();
        configurarScrollInfinito();
        actualizarReloj();
        setInterval(actualizarReloj, 60000);
    }
    
    function configurarEventos() {
        // Eventos de filtro por tipo
        $('#btn-todo, #btn-partes, #btn-servicios').off('click').on('click', cambiarTipoFiltro);
        
        // Eventos de búsqueda de productos
        $('#search-items').off('input').on('input', manejarBusquedaEnTiempoReal);
        $('#search-items').off('keypress').on('keypress', manejarEnterBusqueda);
        $('#btn-search').off('click').on('click', ejecutarBusqueda);
        
        // Eventos de filtro por categoría
        $('.categoria-parte').off('click').on('click', filtrarPorCategoriaParte);
        $('.categoria-servicio').off('click').on('click', filtrarPorCategoriaServicio);
        
        // Eventos del modal de agregar item
        $('#form-agregar-item').off('submit').on('submit', agregarItemAVenta);
        
        // Eventos de configuración
        $('#generar-requerimiento').off('change').on('change', toggleGenerarRequerimiento);
        $('#porcentaje-abono').off('input').on('input', actualizarPorcentajeAbono);
        $('#almacen').off('change').on('change', function() {
            const searchText = $('#search-items').val().trim();
            if (searchText) {
                ejecutarBusqueda();
            } else {
                mostrarTodasLasPartes();
            }
            cargarItemsPopulares(); // Recargar items populares
        });
        
        // Botones de acción
        $('#btn-cancelar').off('click').on('click', cancelarVenta);
        $('#btn-procesar').off('click').on('click', procesarVenta);
        
        // Eventos de cliente
        configurarEventosCliente();
        
        // Eventos del modal de item
        configurarEventosModalItem();
        
        // Botón para limpiar búsqueda
        configurarBotonLimpiar();
        
        // Eventos para nueva venta desde modal de éxito
        $(document).off('click', '#btn-nueva-venta').on('click', '#btn-nueva-venta', function() {
            location.reload();
        });
        
        // Eventos dinámicos para items
        $(document).off('click', '.item-card').on('click', '.item-card', seleccionarItem);
        $(document).off('click', '.popular-item').on('click', '.popular-item', seleccionarItemPopular);
        $(document).off('click', '#btn-cargar-mas').on('click', '#btn-cargar-mas', cargarMasItems);
    }
    
    // =============================================
    // GESTIÓN DE CLIENTES
    // =============================================
    
    function configurarEventosCliente() {
        // Búsqueda de clientes
        $('#cliente-search').off('input').on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(clienteSearchTimeout);
            
            if (query.length >= 2) {
                clienteSearchTimeout = setTimeout(() => {
                    buscarClientes(query);
                }, 300);
            } else if (query.length === 0) {
                cargarClientesRecientes();
            }
        });
        
        // Botón de búsqueda de clientes
        $('#btn-search-cliente').off('click').on('click', function() {
            const query = $('#cliente-search').val().trim();
            if (query.length >= 2) {
                buscarClientes(query);
            } else {
                mostrarNotificacion('Ingrese al menos 2 caracteres para buscar', 'warning');
            }
        });
        
        // Enter en búsqueda de clientes
        $('#cliente-search').off('keypress').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const query = $(this).val().trim();
                if (query.length >= 2) {
                    buscarClientes(query);
                } else {
                    mostrarNotificacion('Ingrese al menos 2 caracteres para buscar', 'warning');
                }
            }
        });
        
        // Formulario de nuevo cliente
        $('#form-nuevo-cliente').off('submit').on('submit', crearNuevoCliente);
        
        // Cambio de tipo de cliente
        $('input[name="tipo_cliente"]').off('change').on('change', toggleTipoCliente);
        
        // Remover cliente
        $('#btn-remove-cliente').off('click').on('click', removerCliente);
        
        // Eventos dinámicos para seleccionar cliente
        $(document).off('click', '.btn-select-cliente').on('click', '.btn-select-cliente', seleccionarCliente);
        
        // Eventos del modal de cliente
        $('#clienteModal').off('show.bs.modal').on('show.bs.modal', function() {
            limpiarFormularioCliente();
            cargarClientesRecientes();
        });
        
        $('#create-tab').off('click').on('click', function() {
            limpiarFormularioCliente();
        });
        
        $('#search-tab').off('click').on('click', function() {
            $('#cliente-search').focus();
        });
        
        // Validaciones en tiempo real
        configurarValidacionesCliente();
    }
    
    function buscarClientes(query) {
        mostrarCargandoClientes();
        
        $.ajax({
            url: '/admin/ventas/pos/buscar-clientes',
            method: 'GET',
            data: { query: query },
            timeout: 10000,
            success: function(response) {
                if (response.error) {
                    mostrarErrorClientes(response.message);
                    return;
                }
                
                clientesEncontrados = response.items || [];
                mostrarResultadosClientes(clientesEncontrados, query);
            },
            error: function(xhr, status, error) {
                console.error('Error al buscar clientes:', error);
                let mensaje = 'Error en la búsqueda de clientes';
                
                if (xhr.status === 404) {
                    mensaje = 'Servicio de búsqueda no encontrado';
                } else if (xhr.status >= 500) {
                    mensaje = 'Error interno del servidor';
                } else if (status === 'timeout') {
                    mensaje = 'La búsqueda está tardando demasiado';
                }
                
                mostrarErrorClientes(mensaje);
            }
        });
    }
    
    function cargarClientesRecientes() {
        mostrarCargandoClientes();
        
        $.ajax({
            url: '/admin/ventas/pos/buscar-clientes',
            method: 'GET',
            data: { query: '' },
            success: function(response) {
                if (response.error) {
                    mostrarErrorClientes(response.message);
                    return;
                }
                
                clientesEncontrados = response.items || [];
                mostrarResultadosClientes(clientesEncontrados, '', true);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar clientes:', error);
                mostrarErrorClientes('Error al cargar clientes recientes');
            }
        });
    }
    
    function mostrarCargandoClientes() {
        $('#cliente-results').html(`
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    Buscando clientes...
                </td>
            </tr>
        `);
    }
    
    function mostrarErrorClientes(mensaje) {
        $('#cliente-results').html(`
            <tr>
                <td colspan="5" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${mensaje}
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="cargarClientesRecientes()">
                        <i class="fas fa-redo me-1"></i>Reintentar
                    </button>
                </td>
            </tr>
        `);
    }
    
    function mostrarResultadosClientes(clientes, query = '', esRecientes = false) {
        if (!clientes || clientes.length === 0) {
            const mensaje = query ? 
                `No se encontraron clientes que coincidan con "${query}"` : 
                'No hay clientes registrados';
                
            $('#cliente-results').html(`
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fas fa-user-slash fa-2x mb-2"></i>
                        <br>${mensaje}
                        ${query ? '<br><small>Intente con otros términos de búsqueda</small>' : ''}
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        
        clientes.forEach(cliente => {
            const tipoClase = cliente.tipo === 'natural' ? 'bg-info' : 'bg-warning';
            const tipoTexto = cliente.tipo === 'natural' ? 'Persona' : 'Empresa';
            
            html += `
                <tr class="cliente-row" data-cliente-id="${cliente.id}">
                    <td>
                        <strong class="text-primary">${cliente.documento}</strong>
                        ${cliente.tipo_documento ? `<br><small class="text-muted">${cliente.tipo_documento}</small>` : ''}
                    </td>
                    <td>
                        <strong>${cliente.nombre}</strong>
                        ${cliente.correo ? `<br><small class="text-muted"><i class="fas fa-envelope me-1"></i>${cliente.correo}</small>` : ''}
                    </td>
                    <td>
                        <span class="badge ${tipoClase}">${tipoTexto}</span>
                    </td>
                    <td>
                        ${cliente.telefono ? `<i class="fas fa-phone text-success me-1"></i>${cliente.telefono}` : '<span class="text-muted">Sin teléfono</span>'}
                        ${cliente.direccion ? `<br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${cliente.direccion}</small>` : ''}
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-success btn-select-cliente" 
                            data-id="${cliente.id}" 
                            data-nombre="${cliente.nombre}" 
                            data-documento="${cliente.documento}"
                            title="Seleccionar cliente">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        $('#cliente-results').html(html);
    }
    
    function seleccionarCliente() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        const documento = $(this).data('documento');
        
        if (!id || !nombre || !documento) {
            mostrarNotificacion('Error: Datos del cliente incompletos', 'error');
            return;
        }
        
        $('#cliente-id').val(id);
        $('.nombre-cliente').text(nombre);
        $('.documento-cliente').text(`DOC: ${documento}`);
        
        $('#cliente-placeholder').addClass('d-none');
        $('#cliente-seleccionado').removeClass('d-none');
        
        $('#clienteModal').modal('hide');
        mostrarNotificacion(`Cliente "${nombre}" seleccionado correctamente`, 'success');
        verificarEstadoProcesar();
    }
    
    function removerCliente() {
        if (confirm('¿Está seguro de remover el cliente seleccionado?')) {
            $('#cliente-id').val('');
            $('#cliente-seleccionado').addClass('d-none');
            $('#cliente-placeholder').removeClass('d-none');
            
            mostrarNotificacion('Cliente removido', 'info');
            verificarEstadoProcesar();
        }
    }
    
    function crearNuevoCliente(e) {
        e.preventDefault();
        
        if (!validarFormularioCompleto()) {
            return;
        }
        
        const $submitBtn = $('#form-nuevo-cliente button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creando...');
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '/admin/ventas/pos/crear-cliente',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const cliente = response.cliente;
                    
                    $('#cliente-id').val(cliente.id);
                    $('.nombre-cliente').text(cliente.nombre);
                    $('.documento-cliente').text(`DOC: ${cliente.documento}`);
                    
                    $('#cliente-placeholder').addClass('d-none');
                    $('#cliente-seleccionado').removeClass('d-none');
                    
                    $('#clienteModal').modal('hide');
                    limpiarFormularioCliente();
                    
                    mostrarNotificacion(`Cliente "${cliente.nombre}" creado y seleccionado`, 'success');
                    verificarEstadoProcesar();
                } else {
                    mostrarNotificacion(response.message || 'Error al crear cliente', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al crear cliente:', error);
                
                let mensaje = 'Error al crear cliente';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        const errores = Object.values(xhr.responseJSON.errors).flat();
                        mensaje = errores.join('<br>');
                    } else if (xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                }
                
                mostrarNotificacion(mensaje, 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function toggleTipoCliente() {
        const tipoNatural = $('#tipo-natural').is(':checked');
        
        if (tipoNatural) {
            $('#datos-natural').removeClass('d-none');
            $('#datos-juridico').addClass('d-none');
            $('#nombres, #apellido_paterno').prop('required', true);
            $('#razon_social').prop('required', false);
        } else {
            $('#datos-natural').addClass('d-none');
            $('#datos-juridico').removeClass('d-none');
            $('#nombres, #apellido_paterno').prop('required', false);
            $('#razon_social').prop('required', true);
        }
    }
    
    function limpiarFormularioCliente() {
        $('#form-nuevo-cliente')[0].reset();
        $('#tipo-natural').prop('checked', true);
        toggleTipoCliente();
        $('#form-nuevo-cliente .is-invalid').removeClass('is-invalid');
        $('#form-nuevo-cliente .is-valid').removeClass('is-valid');
    }
    
    function configurarValidacionesCliente() {
        // Validación de documento
        $('#documento_identidad').off('input').on('input', function() {
            const valor = $(this).val().replace(/[^0-9A-Za-z]/g, '');
            $(this).val(valor.toUpperCase());
            
            const tipoCliente = $('input[name="tipo_cliente"]:checked').val();
            let esValido = false;
            
            if (tipoCliente === 'natural') {
                esValido = /^[0-9]{8}$/.test(valor) || /^[A-Z0-9]{9,12}$/.test(valor);
            } else {
                esValido = /^[0-9]{11}$/.test(valor);
            }
            
            toggleValidationClass(this, esValido && valor.length > 0);
            
            // Auto-detectar tipo de documento
            if (/^[0-9]{8}$/.test(valor) && !$('#tipo-natural').is(':checked')) {
                $('#tipo-natural').prop('checked', true).trigger('change');
            } else if (/^[0-9]{11}$/.test(valor) && !$('#tipo-juridico').is(':checked')) {
                $('#tipo-juridico').prop('checked', true).trigger('change');
            }
        });
        
        // Validación de nombres
        $('#nombres, #apellido_paterno, #apellido_materno, #razon_social').off('input').on('input', function() {
            let valor = $(this).val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            valor = valor.replace(/\b\w+/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
            $(this).val(valor);
            
            const esRequerido = $(this).prop('required');
            const esValido = !esRequerido || (valor.trim().length >= 2);
            toggleValidationClass(this, esValido);
        });
        
        // Validación de teléfono
        $('#telefono').off('input').on('input', function() {
            let valor = $(this).val().replace(/[^0-9]/g, '');
            
            if (valor.length > 0) {
                if (valor.length <= 3) {
                    valor = valor;
                } else if (valor.length <= 6) {
                    valor = valor.substring(0, 3) + ' ' + valor.substring(3);
                } else {
                    valor = valor.substring(0, 3) + ' ' + valor.substring(3, 6) + ' ' + valor.substring(6, 9);
                }
            }
            
            $(this).val(valor);
            
            const numeroLimpio = valor.replace(/\s/g, '');
            const esValido = numeroLimpio.length === 0 || (numeroLimpio.length === 9 && /^9/.test(numeroLimpio));
            toggleValidationClass(this, esValido);
        });
        
        // Validación de correo
        $('#correo').off('input').on('input', function() {
            const valor = $(this).val().toLowerCase();
            $(this).val(valor);
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const esValido = valor.length === 0 || emailRegex.test(valor);
            toggleValidationClass(this, esValido);
        });
    }
    
    function toggleValidationClass(elemento, esValido) {
        const $elemento = $(elemento);
        
        if (esValido) {
            $elemento.removeClass('is-invalid').addClass('is-valid');
        } else {
            $elemento.removeClass('is-valid').addClass('is-invalid');
        }
    }
    
    function validarFormularioCompleto() {
        let esValido = true;
        const tipoCliente = $('input[name="tipo_cliente"]:checked').val();
        
        const documento = $('#documento_identidad').val().trim();
        if (!documento) {
            toggleValidationClass('#documento_identidad', false);
            esValido = false;
        }
        
        if (tipoCliente === 'natural') {
            const nombres = $('#nombres').val().trim();
            const apellidoPaterno = $('#apellido_paterno').val().trim();
            
            if (!nombres || nombres.length < 2) {
                toggleValidationClass('#nombres', false);
                esValido = false;
            }
            
            if (!apellidoPaterno || apellidoPaterno.length < 2) {
                toggleValidationClass('#apellido_paterno', false);
                esValido = false;
            }
        } else {
            const razonSocial = $('#razon_social').val().trim();
            
            if (!razonSocial || razonSocial.length < 3) {
                toggleValidationClass('#razon_social', false);
                esValido = false;
            }
        }
        
        return esValido;
    }
    
    // =============================================
    // BÚSQUEDA DE PRODUCTOS Y SERVICIOS
    // =============================================
    
    function cambiarTipoFiltro() {
        $('#btn-todo, #btn-partes, #btn-servicios').removeClass('active');
        $(this).addClass('active');
        
        tipoActivo = $(this).attr('id').replace('btn-', '');
        currentPage = 1;
        
        if (tipoActivo === 'todo') {
            $('#filter-categorias-partes, #filter-categorias-servicios').show();
        } else if (tipoActivo === 'partes') {
            $('#filter-categorias-partes').show();
            $('#filter-categorias-servicios').hide();
        } else if (tipoActivo === 'servicios') {
            $('#filter-categorias-partes').hide();
            $('#filter-categorias-servicios').show();
        }
        
        const searchText = $('#search-items').val().trim();
        if (searchText) {
            ejecutarBusqueda();
        } else {
            mostrarTodasLasPartes();
        }
    }
    
    function manejarBusquedaEnTiempoReal() {
        const query = $(this).val().trim();
        currentPage = 1;
        
        clearTimeout(searchTimeout);
        
        if (query.length > 0) {
            $('#btn-clear-search').show();
        } else {
            $('#btn-clear-search').hide();
        }
        
        if (query.length === 0) {
            mostrarTodasLasPartes();
            return;
        }
        
        if (query.length >= 2) {
            mostrarIndicadorCarga('Buscando...');
            
            searchTimeout = setTimeout(() => {
                ejecutarBusqueda();
            }, SEARCH_DELAY);
            
        } else if (query.length > 0) {
            mostrarMensaje('Escriba al menos 2 caracteres para buscar', 'info');
        }
    }
    
    function manejarEnterBusqueda(e) {
        if (e.which === 13) {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query.length >= 2) {
                clearTimeout(searchTimeout);
                ejecutarBusqueda();
            } else {
                mostrarNotificacion('Ingrese al menos 2 caracteres para buscar', 'warning');
            }
        }
    }
    
    function ejecutarBusqueda() {
        const query = $('#search-items').val().trim();
        currentPage = 1;
        
        if (query.length === 0) {
            mostrarTodasLasPartes();
            return;
        }
        
        if (query.length < 2) {
            mostrarNotificacion('Ingrese al menos 2 caracteres para buscar', 'warning');
            return;
        }
        
        realizarBusquedaCompleta(query);
    }
    
    function realizarBusquedaCompleta(query) {
        mostrarIndicadorCarga(`Buscando "${query}"...`);
        
        let promesas = [];
        
        if (tipoActivo === 'todo' || tipoActivo === 'partes') {
            promesas.push(buscarPartes(query));
        }
        
        if (tipoActivo === 'todo' || tipoActivo === 'servicios') {
            promesas.push(buscarServicios(query));
        }
        
        Promise.all(promesas)
            .then(resultados => {
                let todosLosItems = [];
                resultados.forEach(resultado => {
                    if (resultado && resultado.items) {
                        todosLosItems = todosLosItems.concat(resultado.items);
                    }
                });
                
                mostrarResultadosBusqueda(todosLosItems, query);
            })
            .catch(error => {
                console.error('Error en búsqueda:', error);
                mostrarError('Error en la búsqueda. Intente nuevamente.');
            });
    }
    
    function buscarPartes(query) {
        return $.ajax({
            url: '/admin/ventas/pos/buscar-partes',
            method: 'GET',
            data: {
                query: query,
                categoria_id: selectedCategoriaParteId,
                almacen_id: $('#almacen').val(),
                incluir_sin_stock: true
            },
            timeout: 15000
        });
    }
    
    function buscarServicios(query) {
        return $.ajax({
            url: '/admin/ventas/pos/buscar-servicios',
            method: 'GET',
            data: {
                query: query,
                categoria_id: selectedCategoriaServicioId
            },
            timeout: 15000
        });
    }
    
    function mostrarTodasLasPartes() {
        if (tipoActivo === 'servicios') {
            cargarTodosLosServicios();
            return;
        }
        
        mostrarIndicadorCarga('Cargando productos...');
        
        $.ajax({
            url: '/admin/ventas/pos/obtener-todas-partes',
            method: 'GET',
            data: {
                almacen_id: $('#almacen').val(),
                categoria_id: selectedCategoriaParteId,
                incluir_sin_stock: true,
                page: 1
            },
            success: function(response) {
                if (response.error) {
                    mostrarError(response.message);
                    return;
                }
                
                mostrarResultadosBusqueda(response.items, '', response.pagination);
                currentPage = 1;
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar partes:', error);
                mostrarError('Error al cargar los productos');
            }
        });
    }
    
    function cargarTodosLosServicios() {
        mostrarIndicadorCarga('Cargando servicios...');
        
        $.ajax({
            url: '/admin/ventas/pos/buscar-servicios',
            method: 'GET',
            data: {
                query: '',
                categoria_id: selectedCategoriaServicioId
            },
            success: function(response) {
                if (response.error) {
                    mostrarError(response.message);
                    return;
                }
                
                mostrarResultadosBusqueda(response.items, '');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar servicios:', error);
                mostrarError('Error al cargar los servicios');
            }
        });
    }
    
    function mostrarResultadosBusqueda(items, query = '', pagination = null) {
        let html = '';
        
        if (!items || items.length === 0) {
            html = generarMensajeVacio(query);
        } else {
            html = generarEncabezadoResultados(items, query);
            
            if (tipoActivo === 'todo') {
                html += generarResultadosSeparados(items);
            } else {
                html += generarGridItems(items);
            }
            
            if (pagination && pagination.has_more) {
                html += generarBotonCargarMas(pagination);
            }
        }
        
        $('#search-results').html(html);
    }
    
    function generarMensajeVacio(query) {
        if (query) {
            return `
                <div class="text-center text-muted p-4">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>No se encontraron resultados</h5>
                    <p>No hay productos o servicios que coincidan con "${query}"</p>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="$('#search-items').val('').focus(); mostrarTodasLasPartes();">
                            <i class="fas fa-list me-1"></i>Ver todos los productos
                        </button>
                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="limpiarBusqueda()">
                            <i class="fas fa-times me-1"></i>Limpiar búsqueda
                        </button>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="text-center text-muted p-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <h5>No hay productos disponibles</h5>
                    <p>No se encontraron productos en el almacén seleccionado</p>
                </div>
            `;
        }
    }
    
    function generarEncabezadoResultados(items, query) {
        const totalItems = items.length;
        const itemsConStock = items.filter(item => item.tipo === 'servicio' || item.stock_disponible > 0).length;
        const itemsSinStock = totalItems - itemsConStock;
        
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3 px-2 py-2 bg-light rounded">
                <div>
                    <small class="text-muted">
                        ${totalItems} resultado${totalItems !== 1 ? 's' : ''} encontrado${totalItems !== 1 ? 's' : ''}
                        ${query ? ` para "${query}"` : ''}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <small class="badge bg-success">${itemsConStock} disponible${itemsConStock !== 1 ? 's' : ''}</small>
                    ${itemsSinStock > 0 ? `<small class="badge bg-warning">${itemsSinStock} sin stock</small>` : ''}
                </div>
            </div>
        `;
        
        return html;
    }
    
    function generarResultadosSeparados(items) {
        let html = '';
        
        const partes = items.filter(item => item.tipo === 'parte');
        const servicios = items.filter(item => item.tipo === 'servicio');
        
        const partesConStock = partes.filter(item => item.stock_disponible > 0);
        const partesSinStock = partes.filter(item => item.stock_disponible <= 0);
        
        if (partesConStock.length > 0) {
            html += `
                <div class="mb-4">
                    <h6 class="mb-3 d-flex align-items-center">
                        <i class="fas fa-cogs me-2 text-info"></i> 
                        Partes Disponibles 
                        <span class="badge bg-info ms-2">${partesConStock.length}</span>
                    </h6>
                    ${generarGridItems(partesConStock)}
                </div>
            `;
        }
        
        if (servicios.length > 0) {
            html += `
                <div class="mb-4">
                    <h6 class="mb-3 d-flex align-items-center">
                        <i class="fas fa-tools me-2 text-success"></i> 
                        Servicios 
                        <span class="badge bg-success ms-2">${servicios.length}</span>
                    </h6>
                    ${generarGridItems(servicios)}
                </div>
            `;
        }
        
        if (partesSinStock.length > 0) {
            html += `
                <div class="alert alert-warning mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <h6 class="mb-1">Partes sin stock disponible</h6>
                            <small>Estos ítems requieren generar requerimiento de compra</small>
                        </div>
                    </div>
                </div>
                ${generarGridItems(partesSinStock)}
            `;
        }
        
        return html;
    }
    
    function generarGridItems(items) {
        let html = '<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">';
        
        items.forEach(item => {
            html += generarCardItem(item);
        });
        
        html += '</div>';
        return html;
    }
    
    function generarCardItem(item) {
        const simboloMoneda = (item.moneda === 'SOL' || item.moneda === 'Soles') ? 'S/ ' : 'US$ ';
        const precio = parseFloat(item.precio || 0).toFixed(2);
        
        let stockBadge = '';
        let cardClass = 'card h-100 shadow-sm item-card cursor-pointer';
        let headerAlert = '';
        
        if (item.tipo === 'parte') {
            if (item.stock_disponible > 0) {
                stockBadge = `<span class="badge bg-success">${item.stock_disponible} en stock</span>`;
            } else {
                stockBadge = `<span class="badge bg-danger">Sin stock</span>`;
                cardClass += ' border-warning opacity-75';
                headerAlert = `
                    <div class="alert alert-warning alert-sm mb-2 py-1 px-2">
                        <small><i class="fas fa-exclamation-triangle me-1"></i>Requiere requerimiento</small>
                    </div>
                `;
            }
        }
        
        const icono = item.tipo === 'parte' ? 'fas fa-cog text-info' : 'fas fa-tools text-success';
        
        let infoAdicional = '';
        if (item.marca) {
            infoAdicional += `<small class="text-muted d-block">Marca: ${item.marca}</small>`;
        }
        if (item.codigo_oem) {
            infoAdicional += `<small class="text-muted d-block">OEM: ${item.codigo_oem}</small>`;
        }
        
        return `
            <div class="col">
                <div class="${cardClass}" 
                    data-id="${item.id}" 
                    data-tipo="${item.tipo}" 
                    data-nombre="${item.nombre}" 
                    data-codigo="${item.codigo}" 
                    data-precio="${precio}"
                    data-moneda="${item.moneda}" 
                    data-unidad="${item.unidad || ''}"
                    data-stock="${item.stock_disponible || 0}">
                    
                    <div class="card-body p-3">
                        ${headerAlert}
                        
                        <div class="text-center mb-2">
                            <i class="${icono} fa-2x mb-2"></i>
                        </div>
                        
                        <h6 class="card-title mb-2 text-center" style="font-size: 0.9rem; height: 2.4em; overflow: hidden;">
                            ${item.nombre}
                        </h6>
                        
                        <div class="text-center mb-2">
                            <span class="text-primary fw-bold">${simboloMoneda}${precio}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Cód: ${item.codigo}</small>
                            <small class="text-muted">${item.unidad || ''}</small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                ${item.categoria || 'Sin categoría'}
                            </span>
                            ${stockBadge}
                        </div>
                        
                        ${infoAdicional}
                        
                        ${item.descripcion ? `
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem; height: 2em; overflow: hidden;">
                                ${item.descripcion.substring(0, 60)}${item.descripcion.length > 60 ? '...' : ''}
                            </small>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }
    
    function generarBotonCargarMas(pagination) {
        return `
            <div class="text-center mt-4">
                <button class="btn btn-outline-primary" id="btn-cargar-mas" data-page="${pagination.current_page + 1}">
                    <i class="fas fa-chevron-down me-2"></i>
                    Cargar más productos (${pagination.current_page * pagination.per_page} de ${pagination.total})
                </button>
            </div>
        `;
    }
    
    function cargarMasItems() {
        if (isLoadingMore) return;
        
        isLoadingMore = true;
        const nextPage = parseInt($('#btn-cargar-mas').data('page'));
        const $btnCargarMas = $('#btn-cargar-mas');
        
        $btnCargarMas.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Cargando...');
        
        $.ajax({
            url: '/admin/ventas/pos/obtener-todas-partes',
            method: 'GET',
            data: {
                almacen_id: $('#almacen').val(),
                categoria_id: selectedCategoriaParteId,
                incluir_sin_stock: true,
                page: nextPage
            },
            success: function(response) {
                if (response.error) {
                    mostrarError(response.message);
                    return;
                }
                
                const nuevosItems = generarGridItems(response.items);
                $btnCargarMas.closest('.text-center').before(nuevosItems);
                
                if (response.pagination && response.pagination.has_more) {
                    $btnCargarMas.data('page', nextPage + 1)
                        .prop('disabled', false)
                        .html(`<i class="fas fa-chevron-down me-2"></i>Cargar más productos (${nextPage * response.pagination.per_page} de ${response.pagination.total})`);
                } else {
                    $btnCargarMas.parent().remove();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar más items:', error);
                mostrarError('Error al cargar más productos');
                $btnCargarMas.prop('disabled', false).html('<i class="fas fa-chevron-down me-2"></i>Cargar más productos');
            },
            complete: function() {
                isLoadingMore = false;
            }
        });
    }
    
    function filtrarPorCategoriaParte() {
        selectedCategoriaParteId = $(this).data('id');
        const nombreCategoria = $(this).text();
        
        $('#filter-categorias-partes .dropdown-toggle').html(`
            <i class="fas fa-filter me-1"></i> ${nombreCategoria}
        `);
        
        const query = $('#search-items').val().trim();
        if (query) {
            ejecutarBusqueda();
        } else {
            mostrarTodasLasPartes();
        }
    }
    
    function filtrarPorCategoriaServicio() {
        selectedCategoriaServicioId = $(this).data('id');
        const nombreCategoria = $(this).text();
        
        $('#filter-categorias-servicios .dropdown-toggle').html(`
            <i class="fas fa-filter me-1"></i> ${nombreCategoria}
        `);
        
        const query = $('#search-items').val().trim();
        if (query) {
            ejecutarBusqueda();
        } else if (tipoActivo === 'servicios' || tipoActivo === 'todo') {
            cargarTodosLosServicios();
        }
    }
    
    function configurarBotonLimpiar() {
        if ($('#btn-clear-search').length === 0) {
            $('#search-items').parent().addClass('position-relative');
            $('#btn-search').before(`
                <button class="btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y" 
                        type="button" id="btn-clear-search" style="z-index: 10; display: none; margin-right: 90px;">
                    <i class="fas fa-times"></i>
                </button>
            `);
        }
        
        $('#btn-clear-search').off('click').on('click', limpiarBusqueda);
    }
    
    function limpiarBusqueda() {
        $('#search-items').val('').focus();
        $('#btn-clear-search').hide();
        clearTimeout(searchTimeout);
        currentPage = 1;
        mostrarTodasLasPartes();
    }
    
    function configurarScrollInfinito() {
        let scrollTimeout;
        
        $(window).off('scroll').on('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1000) {
                    if ($('#btn-cargar-mas').length && !isLoadingMore) {
                        $('#btn-cargar-mas').click();
                    }
                }
            }, 100);
        });
    }
    
    // =============================================
    // GESTIÓN DE ITEMS POPULARES
    // =============================================
    
    function cargarItemsPopulares() {
        $.ajax({
            url: '/admin/ventas/pos/items-populares',
            method: 'GET',
            data: {
                almacen_id: $('#almacen').val(),
                limite: 8
            },
            success: function(items) {
                if (items && items.length > 0) {
                    mostrarItemsPopulares(items);
                } else {
                    $('#popular-items').html(`
                        <div class="col-12 text-center text-muted p-4">
                            <i class="fas fa-star fa-2x mb-2"></i>
                            <p>No hay items populares disponibles</p>
                        </div>
                    `);
                }
            },
            error: function(error) {
                console.error('Error al cargar items populares:', error);
                $('#popular-items').html(`
                    <div class="col-12 text-center text-muted p-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error al cargar items populares</p>
                    </div>
                `);
            }
        });
    }
    
    function mostrarItemsPopulares(items) {
        let html = '';
        
        items.forEach(item => {
            const simboloMoneda = (item.moneda === 'SOL' || item.moneda === 'Soles') ? 'S/ ' : 'US$ ';
            const precio = parseFloat(item.precio || 0).toFixed(2);
            const icono = item.tipo === 'parte' ? 'fas fa-cog text-info' : 'fas fa-tools text-success';
            
            let stockBadge = '';
            if (item.tipo === 'parte') {
                if (item.stock_disponible > 0) {
                    stockBadge = `<small class="text-success"><i class="fas fa-check-circle me-1"></i>En stock</small>`;
                } else {
                    stockBadge = `<small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Sin stock</small>`;
                }
            }
            
            html += `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm popular-item cursor-pointer" 
                        data-id="${item.id}" 
                        data-tipo="${item.tipo}"
                        data-nombre="${item.nombre}"
                        data-codigo="${item.codigo}"
                        data-precio="${precio}"
                        data-moneda="${item.moneda}"
                        data-unidad="${item.unidad}">
                        <div class="card-body text-center">
                            <div class="icon-container mb-3">
                                <i class="${icono} fa-2x"></i>
                            </div>
                            <h6 class="card-title mb-2">${item.nombre}</h6>
                            <p class="card-text text-primary fw-bold mb-1">${simboloMoneda}${precio}</p>
                            <small class="text-muted d-block">Código: ${item.codigo}</small>
                            ${stockBadge}
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#popular-items').html(html);
    }
    
    // =============================================
    // SELECCIÓN Y MANEJO DE ITEMS
    // =============================================
    
    function seleccionarItem() {
        const id = $(this).data('id');
        const tipo = $(this).data('tipo');
        const nombre = $(this).data('nombre');
        const codigo = $(this).data('codigo');
        const precio = $(this).data('precio');
        const moneda = $(this).data('moneda');
        const unidad = $(this).data('unidad');
        const stock = $(this).data('stock');
        
        abrirModalAgregarItem(id, tipo, nombre, codigo, precio, moneda, unidad, stock);
    }
    
    function seleccionarItemPopular() {
        const id = $(this).data('id');
        const tipo = $(this).data('tipo');
        const nombre = $(this).data('nombre');
        const codigo = $(this).data('codigo');
        const precio = $(this).data('precio');
        const moneda = $(this).data('moneda');
        const unidad = $(this).data('unidad');
        
        abrirModalAgregarItem(id, tipo, nombre, codigo, precio, moneda, unidad);
    }
    
    function abrirModalAgregarItem(id, tipo, nombre, codigo, precio, moneda, unidad, stock = 0) {
        $('#item-id').val(id);
        $('#item-tipo').val(tipo);
        $('#item-nombre').val(nombre);
        $('#item-codigo').val(codigo);
        $('#item-precio').val(precio);
        
        $('#modal-item-title').text(tipo === 'parte' ? 'Agregar Parte' : 'Agregar Servicio');
        
        if (tipo === 'parte') {
            $('#stock-container').show();
            obtenerStockActualizado(id);
        } else {
            $('#stock-container').hide();
            $('#alerta-sin-stock').remove();
            $('button[type="submit"]', '#form-agregar-item').prop('disabled', false);
        }
        
        const simboloMoneda = (moneda === 'SOL' || moneda === 'Soles') ? 'S/' : 'US;
        $('#span-moneda').text(simboloMoneda);
        
        $('#item-cantidad').val(1);
        $('#item-descuento').val(0);
        
        actualizarResumenModal();
        $('#agregarItemModal').modal('show');
    }
    
    function obtenerStockActualizado(parteId) {
        $.ajax({
            url: '/admin/ventas/pos/get-stock-parte',
            method: 'GET',
            data: {
                parte_id: parteId,
                almacen_id: $('#almacen').val()
            },
            success: function(response) {
                if (response.error) {
                    $('#item-stock').val('Error');
                    return;
                }
                
                $('#item-stock').val(response.stock_disponible || 0);
                
                $('#alerta-sin-stock').remove();
                
                if (response.stock_disponible <= 0) {
                    $('#stock-container').after(`
                        <div id="alerta-sin-stock" class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atención:</strong> Este ítem no tiene stock disponible.
                            ${generarRequerimiento ? 'Se generará un requerimiento de compra automáticamente.' : 'Active la opción "Generar requerimiento de compra" para proceder.'}
                        </div>
                    `);
                    
                    $('button[type="submit"]', '#form-agregar-item').prop('disabled', !generarRequerimiento);
                } else {
                    $('button[type="submit"]', '#form-agregar-item').prop('disabled', false);
                }
            },
            error: function(error) {
                console.error("Error al obtener stock:", error);
                $('#item-stock').val('No disponible');
            }
        });
    }
    
    // =============================================
    // CONFIGURACIÓN DEL MODAL DE ITEM
    // =============================================
    
    function configurarEventosModalItem() {
        // Eventos para los controles del modal
        const itemCantidad = $('#item-cantidad');
        const itemPrecio = $('#item-precio');
        const itemDescuento = $('#item-descuento');
        
        itemCantidad.off('input').on('input', actualizarResumenModal);
        itemPrecio.off('input').on('input', actualizarResumenModal);
        itemDescuento.off('input').on('input', actualizarResumenModal);
        
        // Botones de cantidad
        window.cambiarCantidad = function(delta) {
            const cantidadInput = document.getElementById('item-cantidad');
            let cantidad = parseInt(cantidadInput.value) || 1;
            cantidad = Math.max(1, cantidad + delta);
            cantidadInput.value = cantidad;
            actualizarResumenModal();
        };
    }
    
    function actualizarResumenModal() {
        const cantidad = parseInt($('#item-cantidad').val()) || 1;
        const precio = parseFloat($('#item-precio').val()) || 0;
        const descuento = parseFloat($('#item-descuento').val()) || 0;
        
        const subtotal = cantidad * precio;
        const descuentoMonto = subtotal * (descuento / 100);
        const total = subtotal - descuentoMonto;
        
        const moneda = $('#span-moneda').text();
        
        $('#modal-subtotal').text(`${moneda} ${subtotal.toFixed(2)}`);
        $('#modal-descuento').text(`${moneda} ${descuentoMonto.toFixed(2)}`);
        $('#modal-total').text(`${moneda} ${total.toFixed(2)}`);
        $('#descuento-valor').text(`${descuento}%`);
    }
    
    // =============================================
    // GESTIÓN DEL CARRITO DE VENTA
    // =============================================
    
    function toggleGenerarRequerimiento() {
        generarRequerimiento = $(this).prop('checked');
        
        if ($('#agregarItemModal').hasClass('show') && parseFloat($('#item-stock').val()) <= 0) {
            if (generarRequerimiento) {
                $('button[type="submit"]', '#form-agregar-item').prop('disabled', false);
                $('#alerta-sin-stock').html(`
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Este ítem no tiene stock disponible.
                    Se generará un requerimiento de compra automáticamente.
                `);
            } else {
                $('button[type="submit"]', '#form-agregar-item').prop('disabled', true);
                $('#alerta-sin-stock').html(`
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atención:</strong> Este ítem no tiene stock disponible.
                    Active la opción "Generar requerimiento de compra" para proceder.
                `);
            }
        }
    }
    
    function actualizarPorcentajeAbono() {
        porcentajeAbono = $(this).val();
        $('#porcentaje-abono-valor').text(porcentajeAbono + '%');
        
        if (itemsSeleccionados.length > 0) {
            calcularTotales();
        }
    }
    
    function agregarItemAVenta(e) {
        e.preventDefault();
        
        const id = $('#item-id').val();
        const tipo = $('#item-tipo').val();
        const nombre = $('#item-nombre').val();
        const codigo = $('#item-codigo').val();
        const cantidad = parseFloat($('#item-cantidad').val());
        const precio = parseFloat($('#item-precio').val());
        const descuento = parseFloat($('#item-descuento').val()) || 0;
        
        if (isNaN(cantidad) || cantidad <= 0) {
            mostrarNotificacion('Ingrese una cantidad válida', 'warning');
            return;
        }
        
        if (isNaN(precio) || precio <= 0) {
            mostrarNotificacion('Ingrese un precio válido', 'warning');
            return;
        }
        
        if (tipo === 'parte' && !generarRequerimiento) {
            const stock = parseFloat($('#item-stock').val()) || 0;
            if (cantidad > stock) {
                mostrarNotificacion('La cantidad supera el stock disponible', 'warning');
                return;
            }
        }
        
        const existeIdx = itemsSeleccionados.findIndex(item => item.id === id && item.tipo === tipo);
        
        if (existeIdx >= 0) {
            itemsSeleccionados[existeIdx].cantidad += cantidad;
            itemsSeleccionados[existeIdx].total = calcularTotalItem(itemsSeleccionados[existeIdx]);
            mostrarNotificacion(`Cantidad actualizada para ${nombre}`, 'success');
        } else {
            const item = {
                id: id,
                tipo: tipo,
                nombre: nombre,
                codigo: codigo,
                cantidad: cantidad,
                precio: precio,
                descuento: descuento,
                total: 0,
                stock_cero: (tipo === 'parte' && parseFloat($('#item-stock').val()) <= 0)
            };
            
            item.total = calcularTotalItem(item);
            itemsSeleccionados.push(item);
            mostrarNotificacion(`${nombre} agregado a la venta`, 'success');
        }
        
        actualizarListaItems();
        calcularTotales();
        $('#agregarItemModal').modal('hide');
    }
    
    function calcularTotalItem(item) {
        const subtotal = item.cantidad * item.precio;
        return subtotal * (1 - (item.descuento || 0) / 100);
    }
    
    function actualizarListaItems() {
        if (itemsSeleccionados.length === 0) {
            $('#items-placeholder').removeClass('d-none');
            $('#items-list').addClass('d-none');
            $('#btn-procesar').prop('disabled', true);
            return;
        }
        
        $('#items-placeholder').addClass('d-none');
        $('#items-list').removeClass('d-none');
        verificarEstadoProcesar();
        
        let listaHTML = '';
        const simboloMoneda = $('#moneda').val() === 'Soles' ? 'S/ ' : 'US$ ';
        
        itemsSeleccionados.forEach((item, index) => {
            const iconoTipo = item.tipo === 'parte' ? 'fas fa-cog text-info' : 'fas fa-tools text-success';
            const stockCeroBadge = item.stock_cero ? 
                `<span class="badge bg-danger ms-1"><i class="fas fa-exclamation-triangle me-1"></i>Sin Stock</span>` : '';
            
            listaHTML += `
                <div class="item-row border rounded mb-2 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <i class="${iconoTipo} me-2"></i>
                                <h6 class="mb-0">${item.nombre}</h6>
                                ${stockCeroBadge}
                            </div>
                            <small class="text-muted">Código: ${item.codigo}</small>
                            ${item.descuento > 0 ? `<small class="text-success d-block">Descuento: ${item.descuento}%</small>` : ''}
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">${item.cantidad} × ${simboloMoneda}${item.precio.toFixed(2)}</div>
                            <div class="fw-bold text-primary">${simboloMoneda}${item.total.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-item" data-index="${index}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-index="${index}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        $('#items-list').html(listaHTML);
        $('#item-count').text(itemsSeleccionados.length);
        
        // Asignar eventos a botones de items
        $('.btn-edit-item').off('click').on('click', function() {
            const index = $(this).data('index');
            editarItem(index);
        });
        
        $('.btn-remove-item').off('click').on('click', function() {
            const index = $(this).data('index');
            eliminarItem(index);
        });
    }
    
    function editarItem(index) {
        const item = itemsSeleccionados[index];
        
        $('#item-id').val(item.id);
        $('#item-tipo').val(item.tipo);
        $('#item-nombre').val(item.nombre);
        $('#item-codigo').val(item.codigo);
        $('#item-cantidad').val(item.cantidad);
        $('#item-precio').val(item.precio);
        $('#item-descuento').val(item.descuento || 0);
        
        $('#modal-item-title').text(item.tipo === 'parte' ? 'Editar Parte' : 'Editar Servicio');
        
        if (item.tipo === 'parte') {
            $('#stock-container').show();
            obtenerStockActualizado(item.id);
        } else {
            $('#stock-container').hide();
        }
        
        itemsSeleccionados.splice(index, 1);
        actualizarListaItems();
        calcularTotales();
        
        actualizarResumenModal();
        $('#agregarItemModal').modal('show');
    }
    
    function eliminarItem(index) {
        const item = itemsSeleccionados[index];
        
        if (confirm(`¿Está seguro de eliminar "${item.nombre}" de la venta?`)) {
            itemsSeleccionados.splice(index, 1);
            actualizarListaItems();
            calcularTotales();
            mostrarNotificacion(`${item.nombre} eliminado de la venta`, 'info');
        }
    }
    
    function calcularTotales() {
        if (itemsSeleccionados.length === 0) {
            $('#subtotal').text('S/ 0.00');
            $('#igv').text('S/ 0.00');
            $('#total').text('S/ 0.00');
            $('#abono-info').hide();
            return;
        }
        
        const simboloMoneda = $('#moneda').val() === 'Soles' ? 'S/ ' : 'US$ ';
        
        const subtotal = itemsSeleccionados.reduce((sum, item) => sum + item.total, 0);
        const igv = subtotal * 0.18;
        const total = subtotal + igv;
        
        $('#subtotal').text(`${simboloMoneda}${subtotal.toFixed(2)}`);
        $('#igv').text(`${simboloMoneda}${igv.toFixed(2)}`);
        $('#total').text(`${simboloMoneda}${total.toFixed(2)}`);
        
        if (porcentajeAbono < 100) {
            const abono = total * (porcentajeAbono / 100);
            const saldoPendiente = total - abono;
            
            if ($('#abono-info').length === 0) {
                $('.p-3.border-bottom.bg-light').append(`
                    <div id="abono-info" class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Abono (<span id="abono-porcentaje">${porcentajeAbono}</span>%):</span>
                            <span id="abono-monto" class="text-success">${simboloMoneda}${abono.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span class="text-danger">Saldo pendiente:</span>
                            <span id="saldo-pendiente" class="text-danger">${simboloMoneda}${saldoPendiente.toFixed(2)}</span>
                        </div>
                    </div>
                `);
            } else {
                $('#abono-porcentaje').text(porcentajeAbono);
                $('#abono-monto').text(`${simboloMoneda}${abono.toFixed(2)}`);
                $('#saldo-pendiente').text(`${simboloMoneda}${saldoPendiente.toFixed(2)}`);
                $('#abono-info').show();
            }
        } else {
            $('#abono-info').hide();
        }
    }
    
    function verificarEstadoProcesar() {
        const tieneCliente = $('#cliente-id').val() !== '';
        const tieneItems = itemsSeleccionados && itemsSeleccionados.length > 0;
        
        $('#btn-procesar').prop('disabled', !(tieneCliente && tieneItems));
    }
    
    function cancelarVenta() {
        if (itemsSeleccionados.length === 0) {
            mostrarNotificacion('No hay items para cancelar', 'info');
            return;
        }
        
        if (confirm('¿Está seguro de cancelar la venta? Se perderán todos los items seleccionados.')) {
            itemsSeleccionados = [];
            actualizarListaItems();
            calcularTotales();
            
            $('#cliente-seleccionado').addClass('d-none');
            $('#cliente-placeholder').removeClass('d-none');
            $('#cliente-id').val('');
            
            mostrarNotificacion('Venta cancelada', 'info');
        }
    }
    
    function procesarVenta() {
        if (itemsSeleccionados.length === 0) {
            mostrarNotificacion('Agregue al menos un item a la venta', 'warning');
            return;
        }
        
        if (!$('#cliente-id').val()) {
            mostrarNotificacion('Seleccione un cliente para la venta', 'warning');
            return;
        }
        
        if (!$('#almacen').val()) {
            mostrarNotificacion('Seleccione un almacén', 'warning');
            return;
        }
        
        const total = $('#total').text();
        if (!confirm(`¿Procesar venta por ${total}?`)) {
            return;
        }
        
        const $btnProcesar = $('#btn-procesar');
        $btnProcesar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');
        
        const datosVenta = {
            cliente_id: $('#cliente-id').val(),
            almacen_id: $('#almacen').val(),
            moneda: $('#moneda').val(),
            condicion: $('#condicion').val(),
            forma_pago: $('#forma-pago').val(),
            porcentaje_abono: porcentajeAbono,
            generar_requerimiento: generarRequerimiento,
            items: itemsSeleccionados.map(item => ({
                id: item.id,
                tipo: item.tipo,
                cantidad: item.cantidad,
                precio: item.precio,
                descuento: item.descuento || 0,
                nombre: item.nombre
            }))
        };
        
        $.ajax({
            url: '/admin/ventas/pos/procesar-venta',
            method: 'POST',
            data: datosVenta,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    mostrarModalExito(response);
                } else {
                    mostrarNotificacion(response.message || 'Error al procesar la venta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al procesar venta:', error);
                let mensaje = 'Error al procesar la venta';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                
                mostrarNotificacion(mensaje, 'error');
            },
            complete: function() {
                $btnProcesar.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Procesar Venta');
            }
        });
    }
    
    function mostrarModalExito(response) {
        $('#cotizacion-codigo').text(response.codigo);
        $('#btn-ver-cotizacion').attr('href', response.redirect);
        
        if (response.requerimientos_generados && response.requerimientos_generados.length > 0) {
            let infoRequerimientos = '<div class="alert alert-info mt-3"><h6>Requerimientos generados:</h6><ul class="mb-0">';
            response.requerimientos_generados.forEach(req => {
                infoRequerimientos += `<li>${req.codigo} - ${req.parte_nombre} (${req.cantidad} unidades)</li>`;
            });
            infoRequerimientos += '</ul></div>';
            
            $('#requerimientos-info').html(infoRequerimientos);
        }
        
        $('#successModal').modal('show');
    }
    
    // =============================================
    // FUNCIONES DE UI Y UTILIDADES
    // =============================================
    
    function mostrarIndicadorCarga(mensaje = 'Cargando...') {
        $('#search-results').html(`
            <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">${mensaje}</span>
                    </div>
                    <p class="text-muted">${mensaje}</p>
                </div>
            </div>
        `);
    }
    
    function mostrarMensaje(mensaje, tipo = 'info') {
        const iconos = {
            'info': 'fas fa-info-circle',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-exclamation-circle',
            'success': 'fas fa-check-circle'
        };
        
        $('#search-results').html(`
            <div class="text-center text-${tipo} p-4">
                <i class="${iconos[tipo]} fa-3x mb-3"></i>
                <p>${mensaje}</p>
            </div>
        `);
    }
    
    function mostrarError(mensaje) {
        $('#search-results').html(`
            <div class="text-center text-danger p-4">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h5>Error</h5>
                <p>${mensaje}</p>
                <button class="btn btn-primary btn-sm" onclick="location.reload()">
                    <i class="fas fa-redo me-1"></i>Recargar página
                </button>
            </div>
        `);
    }
    
    function mostrarNotificacion(mensaje, tipo = 'info') {
        const toastId = 'toast-' + Date.now();
        const iconos = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        
        const colores = {
            'success': 'success',
            'error': 'danger',
            'warning': 'warning',
            'info': 'info'
        };
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${colores[tipo]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${iconos[tipo]} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        if ($('#toast-container').length === 0) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;"></div>');
        }
        
        $('#toast-container').append(toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: tipo === 'error' ? 8000 : 5000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    function actualizarReloj() {
        const ahora = new Date();
        const tiempo = ahora.toLocaleTimeString('es-PE', {
            hour: '2-digit',
            minute: '2-digit'
        });
        if ($('#current-time').length) {
            $('#current-time').text(tiempo);
        }
    }
    
    // =============================================
    // EXPONER FUNCIONES GLOBALES
    // =============================================
    
    window.mostrarTodasLasPartes = mostrarTodasLasPartes;
    window.limpiarBusqueda = limpiarBusqueda;
    window.ejecutarBusqueda = ejecutarBusqueda;
    window.cargarClientesRecientes = cargarClientesRecientes;
    window.buscarClientes = buscarClientes;
    window.seleccionarCliente = seleccionarCliente;
    window.removerCliente = removerCliente;
    window.verificarEstadoProcesar = verificarEstadoProcesar;
    window.mostrarNotificacion = mostrarNotificacion;
    window.cargarItemsPopulares = cargarItemsPopulares;
    window.cambiarCantidad = function(delta) {
        const cantidadInput = document.getElementById('item-cantidad');
        let cantidad = parseInt(cantidadInput.value) || 1;
        cantidad = Math.max(1, cantidad + delta);
        cantidadInput.value = cantidad;
        actualizarResumenModal();
    };
    window.actualizarResumenModal = actualizarResumenModal;
    window.validarFormularioCompleto = validarFormularioCompleto;
    
});