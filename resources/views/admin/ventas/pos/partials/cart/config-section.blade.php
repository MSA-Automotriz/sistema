{{-- resources/views/admin/ventas/pos/partials/cart/config-section.blade.php --}}
<div class="p-2 p-sm-3 border-bottom">
    <h6 class="mb-2 mb-sm-3 fs-7 fs-sm-6">
        <i class="fas fa-cog me-2 text-primary"></i>Configuración
    </h6>

    <!-- Configuración principal en dos líneas en móvil, una en desktop -->
    <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 mb-2 mb-sm-3">
        <!-- Moneda -->
        <div class="flex-fill">
            <label class="form-label fs-7 fs-sm-6 fw-bold mb-1">
                <i class="fas fa-coins me-1"></i><span class="d-none d-sm-inline">Moneda</span>
            </label>
            <div class="btn-group w-100" role="group" id="moneda-group">
                <input type="radio" class="btn-check" name="moneda" id="moneda-soles" value="Soles" checked>
                <label class="btn btn-outline-primary btn-sm fs-7 fs-sm-6" for="moneda-soles">
                    <i class="fas fa-money-bill me-0 me-sm-1"></i><span class="d-none d-sm-inline">S/</span>
                </label>

                <input type="radio" class="btn-check" name="moneda" id="moneda-dolares" value="Dólares">
                <label class="btn btn-outline-primary btn-sm fs-7 fs-sm-6" for="moneda-dolares">
                    <i class="fas fa-dollar-sign me-0 me-sm-1"></i><span class="d-none d-sm-inline">US$</span>
                </label>
            </div>
        </div>

        <!-- Forma de Pago -->
        <div class="flex-fill">
            <label class="form-label fs-7 fs-sm-6 fw-bold mb-1">
                <i class="fas fa-credit-card me-1"></i><span class="d-none d-sm-inline">Pago</span>
            </label>
            <div class="btn-group w-100" role="group" id="forma-pago-group">
                <input type="radio" class="btn-check" name="forma_pago" id="pago-contado" value="Contado" checked>
                <label class="btn btn-outline-success btn-sm fs-7 fs-sm-6" for="pago-contado">
                    <i class="fas fa-money-bills me-0 me-sm-1"></i><span class="d-none d-sm-inline">Contado</span>
                </label>

                <input type="radio" class="btn-check" name="forma_pago" id="pago-credito" value="Crédito">
                <label class="btn btn-outline-warning btn-sm fs-7 fs-sm-6" for="pago-credito">
                    <i class="fas fa-calendar-days me-0 me-sm-1"></i><span class="d-none d-sm-inline">Crédito</span>
                </label>
            </div>
        </div>
    </div>
    
    <!-- Tipo de Cambio (visible cuando hay USD/PEN) -->
    <div class="mb-2 mb-sm-3" id="tipo-cambio-section">
        <div class="d-flex flex-column flex-sm-row align-items-start align-sm-items-center justify-content-between p-2 bg-light rounded gap-1">
            <div class="d-flex align-items-center gap-1">
                <i class="fas fa-exchange-alt text-success me-1"></i>
                <span class="fs-7 fs-sm-6 fw-bold">TC:</span>
                <button type="button" class="btn btn-link btn-sm p-0"
                        id="refresh-tc-btn" title="Actualizar tipo de cambio">
                    <i class="fas fa-refresh text-muted"></i>
                </button>
            </div>
            <div class="text-start text-sm-end w-100 w-sm-auto">
                <div class="fs-7 fs-sm-6">
                    <span class="badge bg-success" id="tc-compra">C: 0.00</span>
                    <span class="badge bg-primary" id="tc-venta">V: 0.00</span>
                </div>
                <div class="tiny text-muted mt-1" id="tc-fecha">--</div>
            </div>
        </div>
    </div>
    
    <!-- Porcentaje de Abono (solo visible en crédito) -->
    <div class="mb-2 mb-sm-3 d-none" id="abono-section">
        <div class="d-flex flex-column flex-sm-row align-items-start align-sm-items-center gap-2 mb-1">
            <label class="form-label fs-7 fs-sm-6 fw-bold mb-0">
                <i class="fas fa-percentage me-1"></i>Abono:
            </label>
            <div class="input-group input-group-sm" style="max-width: fit-content;">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="btn-abono-menos">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" class="form-control text-center" id="porcentaje-abono"
                       value="50" min="0" max="100" step="5" style="width: 50px;">
                <span class="input-group-text fs-7 fs-sm-6">%</span>
                <button class="btn btn-outline-secondary btn-sm" type="button" id="btn-abono-mas">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="d-flex gap-1 flex-wrap">
                <button class="btn btn-outline-primary btn-xs fs-7" onclick="setAbono(0)">0%</button>
                <button class="btn btn-outline-primary btn-xs fs-7" onclick="setAbono(50)">50%</button>
                <button class="btn btn-outline-primary btn-xs fs-7" onclick="setAbono(100)">100%</button>
            </div>
        </div>
        <small class="text-muted d-block fs-7 fs-sm-6" id="abono-info">
            Abono: <span id="abono-monto">S/ 0.00</span> | Saldo: <span id="saldo-monto">S/ 0.00</span>
        </small>
    </div>
    
    <!-- Opciones y acciones -->
    <div class="d-flex justify-content-start align-items-center">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="habilitar-descuentos">
            <label class="form-check-label fs-7 fs-sm-6" for="habilitar-descuentos">
                <i class="fas fa-percent me-1"></i>Descuentos
            </label>
        </div>
    </div>
</div>

<style>
.btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.7rem;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-check:checked + .btn {
    transform: scale(0.98);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

tiny {
    font-size: 0.65rem;
    line-height: 1;
}

.form-check-switch .form-check-input {
    width: 2em;
}

#abono-section {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.config-section-animation {
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
$(document).ready(function() {
    console.log('⚙️ Inicializando panel de configuración...');
    
    // Configuración por defecto
    let config = {
        moneda: 'Soles',
        forma_pago: 'Contado',
        porcentaje_abono: 100,
        habilitar_descuentos: false
    };
    
    // Variables para tipo de cambio
    let tipoCambio = {
        compra: 0,
        venta: 0,
        fecha: null,
        disponible: false
    };
    
    // Cargar tipo de cambio desde la API
    async function cargarTipoCambio() {
        try {
            console.log('💱 Cargando tipo de cambio...');
            const response = await fetch('/admin/mantenimiento/api/tipo-cambio', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    tipoCambio = {
                        compra: parseFloat(data.data.compra),
                        venta: parseFloat(data.data.venta),
                        fecha: data.data.fecha,
                        disponible: true
                    };
                    
                    actualizarUITipoCambio();
                    console.log('✅ Tipo de cambio cargado:', tipoCambio);
                } else {
                    console.warn('⚠️ No hay tipo de cambio disponible');
                    tipoCambio.disponible = false;
                }
            } else {
                throw new Error('Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al cargar tipo de cambio:', error);
            tipoCambio.disponible = false;
            mostrarErrorTipoCambio();
        }
    }
    
    // Actualizar UI del tipo de cambio
    function actualizarUITipoCambio() {
        if (tipoCambio.disponible) {
            $('#tc-compra').text(`Compra: S/ ${tipoCambio.compra.toFixed(4)}`);
            $('#tc-venta').text(`Venta: S/ ${tipoCambio.venta.toFixed(4)}`);
            
            // Formatear fecha
            const fechaFormateada = tipoCambio.fecha ? 
                new Date(tipoCambio.fecha).toLocaleDateString('es-PE') : 
                'Sin fecha';
            $('#tc-fecha').text(`Actualizado: ${fechaFormateada}`);
            
            $('#tipo-cambio-section').removeClass('d-none');
        } else {
            $('#tipo-cambio-section').addClass('d-none');
        }
    }
    
    // Mostrar error en tipo de cambio
    function mostrarErrorTipoCambio() {
        $('#tc-compra').text('No disponible');
        $('#tc-venta').text('No disponible');
        $('#tc-fecha').text('Error al cargar');
        $('#tipo-cambio-section').removeClass('d-none');
        
        // Agregar clases de error
        $('#tipo-cambio-section .bg-light').removeClass('bg-light').addClass('bg-warning-subtle');
    }

    // Inicializar configuración
    function initConfig() {
        // Cargar configuración guardada si existe
        const savedConfig = localStorage.getItem('pos_config');
        if (savedConfig) {
            try {
                config = { ...config, ...JSON.parse(savedConfig) };
                console.log('📁 Configuración cargada:', config);
            } catch (e) {
                console.warn('⚠️ Error al cargar configuración guardada');
            }
        }
        
        // Aplicar configuración a la interfaz
        applyConfigToUI();
        updateCartTotals();
        
        // Cargar tipo de cambio
        cargarTipoCambio();
    }
    
    // Aplicar configuración a la interfaz
    function applyConfigToUI() {
        // Moneda
        $(`input[name="moneda"][value="${config.moneda}"]`).prop('checked', true);
        
        // Forma de pago
        $(`input[name="forma_pago"][value="${config.forma_pago}"]`).prop('checked', true);
        
        // Porcentaje de abono
        $('#porcentaje-abono').val(config.porcentaje_abono);
        
        // Opciones
        $('#habilitar-descuentos').prop('checked', config.habilitar_descuentos);
        
        // Mostrar/ocultar sección de abono
        toggleAbonoSection();
    }
    
    // Event listeners para cambios de configuración
    function setupEventListeners() {
        // Cambio de moneda
        $('input[name="moneda"]').on('change', function() {
            config.moneda = $(this).val();
            console.log('💱 Moneda cambiada a:', config.moneda);
            updateCartTotals();
            triggerConfigChange();
        });
        
        // Cambio de forma de pago
        $('input[name="forma_pago"]').on('change', function() {
            config.forma_pago = $(this).val();
            console.log('💳 Forma de pago cambiada a:', config.forma_pago);
            toggleAbonoSection();
            updateCartTotals();
            triggerConfigChange();
        });
        
        // Cambio de porcentaje de abono
        $('#porcentaje-abono').on('input change', function() {
            let value = parseInt($(this).val()) || 0;
            value = Math.max(0, Math.min(100, value));
            $(this).val(value);
            config.porcentaje_abono = value;
            updateCartTotals();
            triggerConfigChange();
        });
        
        // Botones de abono
        $('#btn-abono-menos').on('click', function() {
            const current = parseInt($('#porcentaje-abono').val()) || 0;
            const newValue = Math.max(0, current - 5);
            setAbono(newValue);
        });
        
        $('#btn-abono-mas').on('click', function() {
            const current = parseInt($('#porcentaje-abono').val()) || 0;
            const newValue = Math.min(100, current + 5);
            setAbono(newValue);
        });
        
        // Opciones adicionales
        $('#habilitar-descuentos').on('change', function() {
            config.habilitar_descuentos = $(this).is(':checked');
            console.log('💰 Habilitar descuentos:', config.habilitar_descuentos);
            
            // Mostrar/ocultar controles de descuento en items del carrito
            if (config.habilitar_descuentos) {
                $('.discount-controls').removeClass('d-none');
                showNotification('Descuentos habilitados - Disponibles en el carrito', 'info');
            } else {
                $('.discount-controls').addClass('d-none');
                showNotification('Descuentos deshabilitados', 'info');
            }
            
            triggerConfigChange();
        });
        
        // Acciones rápidas
        $('#btn-limpiar-carrito').on('click', function() {
            if (confirm('¿Está seguro de que desea limpiar el carrito?')) {
                limpiarCarrito();
            }
        });
        
        $('#btn-guardar-configuracion').on('click', function() {
            guardarConfiguracion();
        });
        
        // Botón de actualizar tipo de cambio
        $('#refresh-tc-btn').on('click', function() {
            const btn = $(this);
            const icon = btn.find('i');
            
            // Animar botón
            icon.addClass('fa-spin');
            btn.prop('disabled', true);
            
            cargarTipoCambio().finally(() => {
                setTimeout(() => {
                    icon.removeClass('fa-spin');
                    btn.prop('disabled', false);
                }, 1000);
            });
        });
    }
    
    // Mostrar/ocultar sección de abono
    function toggleAbonoSection() {
        const abonoSection = $('#abono-section');
        
        if (config.forma_pago === 'Crédito') {
            abonoSection.removeClass('d-none').addClass('config-section-animation');
            // En crédito, permitir abono parcial (por defecto 50%)
            if (config.porcentaje_abono === 100) {
                config.porcentaje_abono = 50;
            }
            console.log('💳 Modo CRÉDITO activado - Se permite pago parcial');
            showNotification('Modo Crédito: Se permite pago parcial', 'info');
        } else {
            abonoSection.addClass('d-none');
            config.porcentaje_abono = 100; // En contado siempre es 100%
            console.log('💰 Modo CONTADO activado - Pago completo requerido');
            showNotification('Modo Contado: Pago completo requerido', 'info');
        }
        
        $('#porcentaje-abono').val(config.porcentaje_abono);
        updateCartTotals();
    }
    
    // Establecer porcentaje de abono
    function setAbono(porcentaje) {
        config.porcentaje_abono = porcentaje;
        $('#porcentaje-abono').val(porcentaje);
        updateCartTotals();
        triggerConfigChange();
    }
    
    // Actualizar totales del carrito
// Actualizar totales del carrito
function updateCartTotals() {
    let items = [];
    
    // Verificar si window.pos existe y tiene el método obtenerItems
    if (window.pos && typeof window.pos.obtenerItems === 'function') {
        try {
            items = window.pos.obtenerItems();
        } catch (error) {
            console.error('Error al obtener items del carrito:', error);
            items = [];
        }
    } else {
        console.warn('⚠️ window.pos.obtenerItems no está disponible, usando array vacío');
        // Intentar obtener items de fuentes alternativas si están disponibles
        items = window.cartItems || [];
    }
    
    let subtotal = 0;
    
    if (Array.isArray(items)) {
        items.forEach(item => {
            const precio = parseFloat(item.precio || 0);
            const cantidad = parseInt(item.cantidad || 1);
            const descuento = parseFloat(item.descuento || 0);
            
            // Calcular el precio después del descuento si está habilitado
            let precioFinal = precio;
            if (config.habilitar_descuentos && descuento > 0) {
                precioFinal = precio * (1 - (descuento / 100));
            }
            
            subtotal += precioFinal * cantidad;
        });
    }
    
    const impuestos = subtotal * 0.18;
    const total = subtotal + impuestos;
    const abono = total * (config.porcentaje_abono / 100);
    const saldo = total - abono;
    
    // Conversión de moneda si es necesario
    let subtotalFinal = subtotal;
    let impuestosFinal = impuestos;
    let totalFinal = total;
    let abonoFinal = abono;
    let saldoFinal = saldo;
    
    if (config.moneda === 'Dólares' && tipoCambio.disponible) {
        // Convertir de soles a dólares usando tipo de cambio de venta
        subtotalFinal = subtotal / tipoCambio.venta;
        impuestosFinal = impuestos / tipoCambio.venta;
        totalFinal = total / tipoCambio.venta;
        abonoFinal = abono / tipoCambio.venta;
        saldoFinal = saldo / tipoCambio.venta;
    }
    
    const simboloMoneda = config.moneda === 'Dólares' ? 'US$' : 'S/';
    
    // Actualizar información de abono
    $('#abono-monto').text(`${simboloMoneda} ${abonoFinal.toFixed(2)}`);
    $('#saldo-monto').text(`${simboloMoneda} ${saldoFinal.toFixed(2)}`);
    
    // Trigger evento para otros componentes con información de conversión
    const totales = {
        subtotal: subtotalFinal,
        impuestos: impuestosFinal,
        total: totalFinal,
        abono: abonoFinal,
        saldo: saldoFinal,
        subtotalOriginal: subtotal,
        impuestosOriginal: impuestos,
        totalOriginal: total,
        abonoOriginal: abono,
        saldoOriginal: saldo,
        moneda: config.moneda,
        tipoCambio: tipoCambio.disponible ? tipoCambio : null
    };
    
    $(document).trigger('configUpdated', [config, totales]);
}
    
    // Limpiar carrito
    function limpiarCarrito() {
        if (window.pos && window.pos.limpiarCarrito) {
            window.pos.limpiarCarrito();
            console.log('🧹 Carrito limpiado desde configuración');
            showNotification('Carrito limpiado', 'info');
        }
    }
    
    // Guardar configuración
    function guardarConfiguracion() {
        try {
            localStorage.setItem('pos_config', JSON.stringify(config));
            console.log('💾 Configuración guardada:', config);
            showNotification('Configuración guardada', 'success');
            
            // Animar botón
            const btn = $('#btn-guardar-configuracion');
            btn.html('<i class="fas fa-check me-1"></i>Guardado');
            setTimeout(() => {
                btn.html('<i class="fas fa-save me-1"></i>Guardar configuración');
            }, 2000);
        } catch (e) {
            console.error('❌ Error al guardar configuración:', e);
            showNotification('Error al guardar configuración', 'error');
        }
    }
    
    // Trigger cambio de configuración
    function triggerConfigChange() {
        console.log('📡 Emitiendo evento configChanged con configuración:', config);
        $(document).trigger('configChanged', [config]);
        updateCartTotals();
        
        // También actualizar directamente los totales como fallback
        if (window.totalesSection && window.totalesSection.updateConfig) {
            console.log('🔄 Actualizando totales directamente como fallback');
            window.totalesSection.updateConfig(config);
        }
    }
    
    // Obtener configuración actual
    function getConfig() {
        return { ...config };
    }
    
    // Mostrar notificaciones
    function showNotification(message, type = 'info') {
        if (typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(message, type);
        } else {
            console.log(`📢 ${type.toUpperCase()}: ${message}`);
        }
    }
    
    // Exponer funciones globalmente
    window.posConfig = {
        getConfig: getConfig,
        setAbono: setAbono,
        updateTotals: updateCartTotals,
        save: guardarConfiguracion,
        clear: limpiarCarrito,
        tipoCambio: {
            obtener: () => tipoCambio,
            recargar: cargarTipoCambio,
            disponible: () => tipoCambio.disponible
        }
    };
    
    // Hacer setAbono global para los botones
    window.setAbono = setAbono;
    
    // Escuchar cambios del carrito
    $(document).on('cartUpdated', function(event, items) {
        console.log('🔄 Carrito actualizado, recalculando totales...');
        updateCartTotals();
    });
    
    // Inicializar
    initConfig();
    setupEventListeners();
    
    console.log('✅ Panel de configuración inicializado');
    console.log('⚙️ Configuración actual:', config);
});
</script>