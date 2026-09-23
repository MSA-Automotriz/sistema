{{-- resources/views/admin/ventas/pos/partials/cart/actions-section.blade.php --}}
<div class="p-2 p-sm-3">
    <div class="d-grid gap-2">
        <button type="button" class="btn btn-success" id="btn-procesar" disabled style="font-size: clamp(0.875rem, 3vw, 1rem); padding: clamp(0.375rem, 2vw, 0.75rem) clamp(0.5rem, 3vw, 1rem);">
            <i class="fas fa-check-circle me-1 me-sm-2"></i><span class="d-none d-sm-inline">Procesar Venta</span><span class="d-sm-none">Procesar</span>
        </button>
        <button type="button" class="btn btn-outline-danger" id="btn-cancelar" style="font-size: clamp(0.875rem, 3vw, 1rem); padding: clamp(0.375rem, 2vw, 0.75rem) clamp(0.5rem, 3vw, 1rem);">
            <i class="fas fa-trash me-1 me-sm-2"></i><span class="d-none d-sm-inline">Cancelar Venta</span><span class="d-sm-none">Cancelar</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('🔄 Inicializando botones de acciones de venta...');
    
    // Referencia a los botones
    const btnProcesar = $('#btn-procesar');
    const btnCancelar = $('#btn-cancelar');
    
    // Función para verificar si el carrito tiene items y habilitar/deshabilitar el botón procesar
    function verificarEstadoCarrito() {
        // Obtener los items del carrito de forma segura
        let items = [];
        if (window.pos && typeof window.pos.obtenerItems === 'function') {
            try {
                items = window.pos.obtenerItems();
            } catch (error) {
                console.error('Error al obtener items del carrito:', error);
                items = [];
            }
        } else if (window.cartItems) {
            items = window.cartItems;
        }
        
        // Habilitar el botón procesar solo si hay items en el carrito
        if (Array.isArray(items) && items.length > 0) {
            btnProcesar.prop('disabled', false);
            console.log('✅ Carrito con items, botón procesar habilitado');
        } else {
            btnProcesar.prop('disabled', true);
            console.log('⚠️ Carrito vacío, botón procesar deshabilitado');
        }
    }
    
    // Evento para el botón Procesar Venta
    btnProcesar.on('click', function() {
        console.log('🛒 Procesando venta...');
        
        // Obtener la configuración actual
        let config = {};
        if (window.posConfig && typeof window.posConfig.getConfig === 'function') {
            config = window.posConfig.getConfig();
        }
        
        // Obtener los items del carrito
        let items = [];
        if (window.pos && typeof window.pos.obtenerItems === 'function') {
            items = window.pos.obtenerItems();
        } else if (window.cartItems) {
            items = window.cartItems;
        }
        
        // Verificar que haya items
        if (!Array.isArray(items) || items.length === 0) {
            mostrarNotificacion('No hay productos en el carrito', 'error');
            return;
        }
        
        // Calcular totales
        let subtotal = 0;
        items.forEach(item => {
            const precio = parseFloat(item.precio || 0);
            const cantidad = parseInt(item.cantidad || 1);
            const descuento = parseFloat(item.descuento || 0);
            
            let precioFinal = precio;
            if (config.habilitar_descuentos && descuento > 0) {
                precioFinal = precio * (1 - (descuento / 100));
            }
            
            subtotal += precioFinal * cantidad;
        });
        
        const impuestos = subtotal * 0.18;
        const total = subtotal + impuestos;
        const abono = total * (config.porcentaje_abono / 100);
        const saldo = total - abono;
        
        // Preparar datos para enviar al servidor
        const datosVenta = {
            items: items,
            config: config,
            totales: {
                subtotal: subtotal,
                impuestos: impuestos,
                total: total,
                abono: abono,
                saldo: saldo
            }
        };
        
        // Mostrar modal de confirmación
        mostrarModalConfirmacion(datosVenta);
    });
    
    // Evento para el botón Cancelar Venta
    btnCancelar.on('click', function() {
        console.log('❌ Cancelando venta...');
        
        // Confirmar cancelación
        if (confirm('¿Está seguro de que desea cancelar la venta actual?')) {
            // Limpiar carrito
            if (window.pos && typeof window.pos.limpiarCarrito === 'function') {
                window.pos.limpiarCarrito();
            } else if (window.posConfig && typeof window.posConfig.clear === 'function') {
                window.posConfig.clear();
            } else {
                // Fallback: intentar limpiar mediante evento
                $(document).trigger('clearCart');
            }
            
            // Mostrar notificación
            mostrarNotificacion('Venta cancelada', 'info');
            
            // Deshabilitar botón procesar
            btnProcesar.prop('disabled', true);
        }
    });
    
    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo = 'info') {
        if (typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(mensaje, tipo);
        } else {
            // Fallback de notificación usando alert o toast de Bootstrap
            if (tipo === 'error') {
                alert('Error: ' + mensaje);
            } else if (window.bootstrap && window.bootstrap.Toast) {
                // Crear toast de Bootstrap
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-white bg-${tipo === 'info' ? 'info' : tipo === 'success' ? 'success' : 'primary'} border-0`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            ${mensaje}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;
                document.body.appendChild(toastEl);
                const toast = new window.bootstrap.Toast(toastEl);
                toast.show();
                
                // Eliminar el toast después de mostrarse
                toastEl.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toastEl);
                });
            } else {
                // Último recurso: console.log
                console.log(`📢 ${tipo.toUpperCase()}: ${mensaje}`);
            }
        }
    }
    
    // Función para mostrar modal de confirmación - CORREGIDA
    function mostrarModalConfirmacion(datosVenta) {
        // Verificar si existe un modal previo y eliminarlo
        $('#modal-confirmar-venta').remove();
        
        // Obtener símbolo de moneda
        const simboloMoneda = datosVenta.config.moneda === 'Dólares' ? 'US$' : 'S/';
        
        // OBTENER DATOS DEL CLIENTE SELECCIONADO
        let clienteSeleccionado = null;
        let clienteId = '1'; // Default: Cliente General
        
        // Verificar si hay un cliente seleccionado
        if (window.pos && typeof window.pos.tieneClienteSeleccionado === 'function' && window.pos.tieneClienteSeleccionado()) {
            clienteSeleccionado = window.pos.getClienteActual();
            console.log('Cliente detectado para modal:', clienteSeleccionado);
            
            if (clienteSeleccionado && clienteSeleccionado.id) {
                clienteId = clienteSeleccionado.id;
            }
        }
        
        // Crear modal de confirmación
        const modalHTML = `
        <div class="modal fade" id="modal-confirmar-venta" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalLabel"><i class="fas fa-check-circle me-2"></i>Confirmar Venta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            ${clienteSeleccionado ? `
                            <div class="alert alert-success py-2">
                                <small>
                                    <i class="fas fa-user-check me-1"></i>
                                    <strong>Cliente:</strong> ${clienteSeleccionado.nombre}
                                    ${clienteSeleccionado.documento ? ` - ${clienteSeleccionado.documento}` : ''}
                                </small>
                            </div>
                            ` : `
                            <div class="alert alert-info py-2">
                                <small>
                                    <i class="fas fa-user me-1"></i>
                                    <strong>Cliente:</strong> Cliente General
                                </small>
                            </div>
                            `}
                        </div>

                        <div class="mb-3">
                            <h6 class="mb-2">Resumen de la venta:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-end">${simboloMoneda} ${datosVenta.totales.subtotal.toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td>IGV (18%):</td>
                                            <td class="text-end">${simboloMoneda} ${datosVenta.totales.impuestos.toFixed(2)}</td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total:</td>
                                            <td class="text-end">${simboloMoneda} ${datosVenta.totales.total.toFixed(2)}</td>
                                        </tr>
                                        ${datosVenta.config.forma_pago === 'Crédito' ? `
                                        <tr class="text-success">
                                            <td>Abono (${datosVenta.config.porcentaje_abono}%):</td>
                                            <td class="text-end">${simboloMoneda} ${datosVenta.totales.abono.toFixed(2)}</td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td>Saldo pendiente:</td>
                                            <td class="text-end">${simboloMoneda} ${datosVenta.totales.saldo.toFixed(2)}</td>
                                        </tr>
                                        ` : ''}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2">Tipo de documento:</h6>
                            <div class="btn-group w-100" role="group" id="tipo-documento-group">
                                <input type="radio" class="btn-check" name="tipo-documento" id="tipo-boleta" value="Boleta" checked>
                                <label class="btn btn-outline-primary" for="tipo-boleta">
                                    <i class="fas fa-receipt me-1"></i>Boleta
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo-documento" id="tipo-factura" value="Factura">
                                <label class="btn btn-outline-primary" for="tipo-factura">
                                    <i class="fas fa-file-invoice me-1"></i>Factura
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo-documento" id="tipo-ticket" value="Ticket">
                                <label class="btn btn-outline-primary" for="tipo-ticket">
                                    <i class="fas fa-ticket-alt me-1"></i>Ticket
                                </label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Moneda: <strong>${datosVenta.config.moneda}</strong> | 
                                Forma de pago: <strong>${datosVenta.config.forma_pago}</strong>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btn-confirmar-venta">
                            <i class="fas fa-check me-1"></i>Confirmar Venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Añadir modal al DOM
        $('body').append(modalHTML);
        
        // Inicializar modal
        const modalEl = document.getElementById('modal-confirmar-venta');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        
        // Evento para confirmar venta
        $('#btn-confirmar-venta').on('click', function() {
            // Obtener el tipo de documento seleccionado
            const tipoDocumento = $('input[name="tipo-documento"]:checked').val();
            
            // Verificar que se haya seleccionado un tipo de documento
            if (!tipoDocumento) {
                mostrarNotificacion('Debe seleccionar un tipo de documento', 'error');
                return;
            }
            
            // Preparar datos del documento
            datosVenta.documento = {
                tipo: tipoDocumento,
                cliente: clienteId
            };
            
            // Si hay cliente seleccionado, agregar datos adicionales
            if (clienteSeleccionado) {
                datosVenta.documento.cliente_nombre = clienteSeleccionado.nombre;
                datosVenta.documento.cliente_documento = clienteSeleccionado.documento;
            }
            
            console.log('Datos de venta preparados:', datosVenta);
            
            // Enviar datos al servidor
            enviarVenta(datosVenta, modal);
        });
    }
    
    // Función para enviar la venta al servidor - CORREGIDA SEGÚN BACKEND
    function enviarVenta(datosVenta, modal) {
        console.log('🚀 Enviando venta al servidor:', datosVenta);
        
        // Reestructurar los datos para que coincidan EXACTAMENTE con lo que espera el backend
        const datosParaEnviar = {
            // Cliente
            cliente_id: datosVenta.documento.cliente || null,
            
            // Almacén
            almacen_id: datosVenta.config.almacen_id || null,
            
            // Items - CORREGIDO según validaciones del backend
            items: datosVenta.items.map(item => ({
                id: item.id,
                tipo: 'parte', // FORZAR a 'parte' como espera el backend
                cantidad: parseInt(item.cantidad) || 1,
                precio: parseFloat(item.precio) || 0,
                descuento: parseFloat(item.descuento) || 0,
                almacen_id: item.almacen_id || datosVenta.config.almacen_id || null
            })),
            
            // Configuraciones - CORREGIDAS según validaciones
            moneda: datosVenta.config.moneda || 'Soles', // Default a Soles si no está definido
            condicion: datosVenta.config.condicion || 'Nuevo', // REQUERIDO por backend
            forma_pago: datosVenta.config.forma_pago || 'Contado',
            
            // Porcentaje de abono - CORREGIDO según validación conditional
            porcentaje_abono: (datosVenta.config.forma_pago === 'Crédito') 
                ? (datosVenta.config.porcentaje_abono || 30) 
                : 100,
            
            // Opcionales
            //generar_requerimiento: datosVenta.config.generar_requerimiento || false,
            generar_requerimiento: true, 
            datos_adicionales: datosVenta.config.notas || 'Venta generada desde POS',
            
            // Tipo de documento - REQUERIDO
            tipo_documento: datosVenta.documento.tipo
        };

        console.log('📦 Datos reestructurados para enviar:', datosParaEnviar);
        
        // DEBUG: Verificar campos requeridos según el backend
        console.log('🔍 Verificación de campos requeridos:');
        console.log('✓ items (array):', Array.isArray(datosParaEnviar.items) && datosParaEnviar.items.length > 0);
        console.log('✓ moneda:', ['Soles', 'Dólares'].includes(datosParaEnviar.moneda));
        console.log('✓ condicion:', ['Nuevo', 'Usado'].includes(datosParaEnviar.condicion));
        console.log('✓ forma_pago:', ['Contado', 'Crédito'].includes(datosParaEnviar.forma_pago));
        console.log('✓ tipo_documento:', ['Boleta', 'Factura', 'Ticket'].includes(datosParaEnviar.tipo_documento));
        console.log('✓ porcentaje_abono:', typeof datosParaEnviar.porcentaje_abono === 'number');
        
        // Verificar items individualmente
        datosParaEnviar.items.forEach((item, index) => {
            console.log(`📦 Item ${index + 1} validación:`, {
                '✓ id existe': !!item.id,
                '✓ tipo es parte': item.tipo === 'parte',
                '✓ cantidad > 0': item.cantidad > 0,
                '✓ precio >= 0': item.precio >= 0,
                '✓ descuento válido': item.descuento >= 0 && item.descuento <= 100
            });
        });
        
        // Mostrar indicador de carga
        $('#btn-confirmar-venta').html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.ventas.pos.procesar-venta") }}',
            type: 'POST',
            data: JSON.stringify(datosParaEnviar),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('✅ Venta procesada correctamente:', response);
                
                // Cerrar modal
                modal.hide();
                
                // Mostrar mensaje apropiado
                let mensaje = response.message || 'Venta procesada correctamente';
                if (response.items_sin_stock && response.items_sin_stock.length > 0) {
                    mensaje += '\n\nItems sin stock: ' + response.items_sin_stock.join(', ');
                }
                
                mostrarNotificacion(mensaje, 'success');
                
                // Limpiar carrito
                if (window.pos && typeof window.pos.limpiarCarrito === 'function') {
                    window.pos.limpiarCarrito();
                }
                
                // CORREGIR REDIRECCIÓN - Ir a ventas POS en lugar de cotizaciones
                setTimeout(function() {
                    if (response.venta_id) {
                        // Redirigir a la vista de ventas POS
                        window.location.href = '{{ route("admin.ventas.pos.ventas") }}';
                    } else if (response.cotizacion_id) {
                        // Si solo hay cotización (items sin stock), ir a cotizaciones
                        window.location.href = '{{ route("admin.ventas.cotizaciones.show", "") }}/' + response.cotizacion_id;
                    } else {
                        // Fallback: recargar página actual
                        window.location.reload();
                    }
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error completo:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
                
                let mensajeError = 'Error al procesar la venta';
                let erroresDetallados = [];
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    console.error('🔍 Errores de validación detallados:', xhr.responseJSON.errors);
                    
                    // Mostrar cada error específico
                    Object.keys(xhr.responseJSON.errors).forEach(campo => {
                        const erroresCampo = xhr.responseJSON.errors[campo];
                        console.error(`❌ Campo "${campo}":`, erroresCampo);
                        erroresDetallados.push(`${campo}: ${erroresCampo.join(', ')}`);
                    });
                    
                    mensajeError = `Errores de validación:\n\n${erroresDetallados.join('\n')}`;
                    
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensajeError = xhr.responseJSON.message;
                    console.error('📝 Mensaje de error:', mensajeError);
                }
                
                // Mostrar el error detallado
                console.error('📋 Resumen del error:', mensajeError);
                mostrarNotificacion(mensajeError, 'error');
                
                // Restaurar botón
                $('#btn-confirmar-venta').html('<i class="fas fa-check me-1"></i>Confirmar Venta').prop('disabled', false);
            }
        });
    }
    
    // Escuchar eventos de actualización del carrito
    $(document).on('cartUpdated', function(event, items) {
        console.log('🔄 Carrito actualizado, verificando estado de botones...');
        verificarEstadoCarrito();
    });
    
    // Inicializar verificación de estado del carrito
    verificarEstadoCarrito();
    
    console.log('✅ Botones de acciones inicializados');
});
</script>