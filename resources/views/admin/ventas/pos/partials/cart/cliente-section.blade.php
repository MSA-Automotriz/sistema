{{-- resources/views/admin/ventas/pos/partials/cart/cliente-section.blade.php --}}
<div class="p-2 p-sm-3 border-bottom bg-light">
    <div class="d-flex justify-content-between align-items-center mb-2 mb-sm-3 gap-1">
        <h6 class="mb-0 fs-7 fs-sm-6">
            <i class="fas fa-user-circle me-2 text-primary"></i><span class="d-none d-sm-inline">Cliente</span>
        </h6>
        <button type="button" class="btn btn-sm btn-primary fs-7 fs-sm-6" data-bs-toggle="modal" data-bs-target="#clienteModal">
            <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">Seleccionar</span>
        </button>
    </div>

    <!-- Cliente seleccionado -->
    <div id="cliente-seleccionado" class="d-none">
        <div class="d-flex align-items-center p-2 bg-white rounded border gap-2">
            <div class="avatar-circle flex-shrink-0">
                <i class="fas fa-user"></i>
            </div>
            <div class="flex-grow-1 min-width-0">
                <h6 class="mb-0 nombre-cliente fs-7 fs-sm-6 text-break">Nombre del Cliente</h6>
                <small class="text-muted documento-cliente d-block text-break">DOC: 12345678</small>
                <input type="hidden" id="cliente-id" value="">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" id="btn-remove-cliente">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Placeholder cliente -->
    <div id="cliente-placeholder" class="text-center text-muted p-2 p-sm-3 bg-white rounded border border-dashed">
        <i class="fas fa-user-plus mb-2 opacity-50" style="font-size: clamp(1.5rem, 8vw, 2rem);"></i>
        <p class="mb-0 small">Seleccione un cliente</p>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.avatar-circle.juridico {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
}

#cliente-seleccionado {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.border-dashed {
    border-style: dashed !important;
    border-width: 2px !important;
}

.cliente-info-extra {
    font-size: 0.75rem;
    color: #6c757d;
}
</style>

<script>
$(document).ready(function() {
    
    // Objeto global para manejar el cliente en el POS
    if (typeof window.pos === 'undefined') {
        window.pos = {};
    }
    
    // Función para seleccionar cliente (llamada desde el modal)
    window.pos.seleccionarCliente = function(cliente) {
        console.log('Seleccionando cliente:', cliente);
        
        // Validar datos del cliente
        if (!cliente || !cliente.id) {
            console.error('Datos de cliente inválidos:', cliente);
            mostrarNotificacion('Error: Datos de cliente inválidos', 'error');
            return;
        }
        
        // Actualizar campos ocultos
        $('#cliente-id').val(cliente.id);
        
        // Actualizar información visual
        $('.nombre-cliente').text(cliente.nombre || 'Cliente sin nombre');
        
        // Formatear documento
        const tipoDoc = cliente.tipo_documento || 'DOC';
        const documento = cliente.documento || 'Sin documento';
        $('.documento-cliente').text(`${tipoDoc}: ${documento}`);
        
        // Cambiar icono del avatar según tipo de cliente
        const avatarIcon = $('.avatar-circle i');
        const avatarCircle = $('.avatar-circle');
        
        if (cliente.tipo === 'juridico') {
            avatarIcon.removeClass('fa-user').addClass('fa-building');
            avatarCircle.addClass('juridico');
        } else {
            avatarIcon.removeClass('fa-building').addClass('fa-user');
            avatarCircle.removeClass('juridico');
        }
        
        // Agregar información extra si existe
        let infoExtra = '';
        if (cliente.telefono) {
            infoExtra += `<i class="fas fa-phone me-1"></i>${cliente.telefono} `;
        }
        if (cliente.correo) {
            infoExtra += `<i class="fas fa-envelope me-1"></i>${cliente.correo}`;
        }
        
        // Remover info extra anterior si existe
        $('.cliente-info-extra').remove();
        
        if (infoExtra.trim()) {
            $('.documento-cliente').after(`<div class="cliente-info-extra mt-1">${infoExtra}</div>`);
        }
        
        // Mostrar sección de cliente seleccionado y ocultar placeholder
        $('#cliente-seleccionado').removeClass('d-none');
        $('#cliente-placeholder').addClass('d-none');
        
        // Actualizar texto del botón de seleccionar
        $('[data-bs-target="#clienteModal"]').html('<i class="fas fa-edit me-1"></i>Cambiar');
        
        // Trigger evento personalizado para otros componentes
        $(document).trigger('clienteSeleccionado', [cliente]);
        
        // Log para debug
        console.log('Cliente seleccionado correctamente:', {
            id: cliente.id,
            nombre: cliente.nombre,
            documento: cliente.documento
        });
    };
    
    // Función para limpiar cliente seleccionado
    window.pos.limpiarClienteSeleccionado = function() {
        console.log('Limpiando cliente seleccionado');
        
        // Limpiar campos
        $('#cliente-id').val('');
        $('.nombre-cliente').text('');
        $('.documento-cliente').text('');
        $('.cliente-info-extra').remove();
        
        // Resetear avatar
        const avatarIcon = $('.avatar-circle i');
        const avatarCircle = $('.avatar-circle');
        avatarIcon.removeClass('fa-building').addClass('fa-user');
        avatarCircle.removeClass('juridico');
        
        // Mostrar placeholder y ocultar cliente seleccionado
        $('#cliente-seleccionado').addClass('d-none');
        $('#cliente-placeholder').removeClass('d-none');
        
        // Restaurar texto del botón
        $('[data-bs-target="#clienteModal"]').html('<i class="fas fa-plus me-1"></i>Seleccionar');
        
        // Trigger evento personalizado
        $(document).trigger('clienteLimpiado');
        
        console.log('Cliente limpiado correctamente');
    };
    
    // Función para obtener cliente actual
    window.pos.getClienteActual = function() {
        const clienteId = $('#cliente-id').val();
        if (!clienteId) {
            return null;
        }
        
        return {
            id: clienteId,
            nombre: $('.nombre-cliente').text(),
            documento: $('.documento-cliente').text().replace(/^[A-Z]+:\s*/, ''),
            tiene_telefono: $('.cliente-info-extra').find('.fa-phone').length > 0,
            tiene_correo: $('.cliente-info-extra').find('.fa-envelope').length > 0
        };
    };
    
    // Función para validar si hay cliente seleccionado
    window.pos.tieneClienteSeleccionado = function() {
        return $('#cliente-id').val() !== '';
    };
    
    // Event listener para el botón de remover cliente
    $('#btn-remove-cliente').on('click', function() {
        console.log('Botón remover cliente clickeado');
        
        // Confirmar acción
        if (confirm('¿Está seguro de que desea quitar el cliente seleccionado?')) {
            window.pos.limpiarClienteSeleccionado();
            mostrarNotificacion('Cliente removido', 'info');
        }
    });
    
    // Event listener para detectar cambios en el cliente (útil para validaciones)
    $(document).on('clienteSeleccionado', function(event, cliente) {
        console.log('Evento clienteSeleccionado disparado:', cliente);
        
        // Aquí puedes agregar lógica adicional cuando se selecciona un cliente
        // Por ejemplo, habilitar botones, recalcular precios, etc.
        
        // Ejemplo: habilitar botón de procesar venta si existe
        const btnProcesar = $('#btn-procesar-venta, .btn-procesar-venta');
        if (btnProcesar.length) {
            btnProcesar.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
        }
        
        // Ejemplo: mostrar/ocultar secciones que requieren cliente
        $('.require-cliente').removeClass('d-none');
    });
    
    $(document).on('clienteLimpiado', function() {
        console.log('Evento clienteLimpiado disparado');
        
        // Aquí puedes agregar lógica adicional cuando se limpia el cliente
        
        // Ejemplo: deshabilitar botón de procesar venta si existe
        const btnProcesar = $('#btn-procesar-venta, .btn-procesar-venta');
        if (btnProcesar.length) {
            btnProcesar.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
        }
        
        // Ejemplo: ocultar secciones que requieren cliente
        $('.require-cliente').addClass('d-none');
    });
    
    // Función global para mostrar notificaciones (fallback si no existe)
    if (typeof window.mostrarNotificacion !== 'function') {
        window.mostrarNotificacion = function(mensaje, tipo = 'info') {
            console.log(`[${tipo.toUpperCase()}] ${mensaje}`);
            
            // Crear notificación simple con Bootstrap
            const alertClass = tipo === 'success' ? 'alert-success' : 
                              tipo === 'error' ? 'alert-danger' : 
                              tipo === 'warning' ? 'alert-warning' : 'alert-info';
            
            const alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="bottom: 100px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
                    <strong>${tipo === 'error' ? 'Error:' : tipo === 'success' ? 'Éxito:' : 'Info:'}</strong> ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('body').append(alert);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                alert.alert('close');
            }, 3000);
        };
    }
    
    // Inicialización
    console.log('Cliente section inicializada correctamente');
    
    // Verificar si ya hay un cliente seleccionado al cargar la página
    if (window.pos.tieneClienteSeleccionado()) {
        console.log('Cliente ya seleccionado detectado al cargar');
        // Trigger evento para componentes que lo necesiten
        const clienteActual = window.pos.getClienteActual();
        if (clienteActual) {
            $(document).trigger('clienteSeleccionado', [clienteActual]);
        }
    }
});
</script>