{{-- resources/views/admin/ventas/pos/partials/cart/totales-section.blade.php --}}
<div class="p-2 p-sm-3 border-bottom bg-light">
    <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
        <span class="fw-bold fs-7 fs-sm-6">
            <i class="fas fa-calculator me-1 text-muted"></i><span class="d-none d-sm-inline">Subtotal:</span>
        </span>
        <span id="subtotal" class="fw-bold fs-7 fs-sm-6">S/ 0.00</span>
    </div>
    <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
        <span class="fs-7 fs-sm-6">
            <i class="fas fa-percent me-1 text-muted"></i><span class="d-none d-sm-inline">IGV (18%):</span><span class="d-sm-none">IGV:</span>
        </span>
        <span id="igv" class="fs-7 fs-sm-6">S/ 0.00</span>
    </div>
    <hr class="my-2">
    <div class="d-flex justify-content-between fw-bold gap-1">
        <span class="text-primary" style="font-size: clamp(0.875rem, 4vw, 1.25rem);">
            <i class="fas fa-receipt me-1"></i><span class="d-none d-sm-inline">Total:</span>
        </span>
        <span id="total" class="text-primary" style="font-size: clamp(1rem, 4vw, 1.35rem);">S/ 0.00</span>
    </div>

    <!-- Información de abono (se muestra dinámicamente) -->
    <div id="abono-info" class="d-none mt-2 pt-2 border-top">
        <div class="d-flex justify-content-between mb-2 flex-wrap gap-1">
            <span class="text-success fs-7 fs-sm-6">
                <i class="fas fa-hand-holding-dollar me-1"></i>Abono:
            </span>
            <span id="abono-monto" class="text-success fw-bold fs-7 fs-sm-6">S/ 0.00</span>
        </div>
        <div class="d-flex justify-content-between flex-wrap gap-1">
            <span class="text-warning fs-7 fs-sm-6">
                <i class="fas fa-hourglass-half me-1"></i>Saldo:
            </span>
            <span id="saldo-pendiente" class="text-warning fw-bold fs-7 fs-sm-6">S/ 0.00</span>
        </div>
    </div>

    <!-- Información adicional del carrito -->
    <div class="mt-2 pt-2 border-top">
        <div class="d-flex justify-content-between fs-7 fs-sm-6 text-muted gap-1" id="currency-info">
            <span>
                <i class="fas fa-coins me-1"></i><span class="d-none d-sm-inline">Moneda:</span>
            </span>
            <span id="currency-display">Soles</span>
        </div>
        <div class="d-flex justify-content-between fs-7 fs-sm-6 text-muted gap-1" id="payment-info">
            <span>
                <i class="fas fa-credit-card me-1"></i><span class="d-none d-sm-inline">Pago:</span>
            </span>
            <span id="payment-display">Contado</span>
        </div>
    </div>
</div>

<style>
.bg-light {
    background-color: #f8f9fa !important;
}

.totales-section {
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.total-amount {
    font-size: 1.25rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.abono-section {
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.pulse-animation {
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.currency-symbol {
    font-weight: 600;
    color: var(--bs-primary);
}
</style>

<script>
$(document).ready(function() {
    console.log('💰 Inicializando sección de totales...');
    
    // Variables para totales
    let currentTotals = {
        subtotal: 0,
        igv: 0,
        total: 0,
        abono: 0,
        saldo: 0,
        itemCount: 0
    };
    
    let currentConfig = {
        moneda: 'Soles',
        forma_pago: 'Contado',
        porcentaje_abono: 100
    };
    
    // Función para formatear moneda
    function formatCurrency(amount, currency = currentConfig.moneda) {
        const symbol = currency === 'Dólares' ? 'US$' : 'S/';
        return `${symbol} ${parseFloat(amount || 0).toFixed(2)}`;
    }
    
    // Función para obtener símbolo de moneda
    function getCurrencySymbol(currency = currentConfig.moneda) {
        return currency === 'Dólares' ? 'US$' : 'S/';
    }
    
    // Función para obtener nombre completo de moneda
    function getCurrencyName(currency = currentConfig.moneda) {
        return currency === 'Dólares' ? 'Dólares (US$)' : 'Soles (S/)';
    }
    
    // Función para calcular totales
    function calculateTotals(items = []) {
        console.log('🧮 Calculando totales para', items.length, 'items...');
        
        let subtotal = 0;
        
        // Calcular subtotal de todos los items
        items.forEach(item => {
            const precio = parseFloat(item.precio || 0);
            const cantidad = parseInt(item.cantidad || 1);
            subtotal += precio * cantidad;
        });
        
        // Calcular IGV (18%)
        const igv = subtotal * 0.18;
        
        // Calcular total
        const total = subtotal + igv;
        
        // Calcular abono y saldo
        const porcentajeAbono = currentConfig.porcentaje_abono || 100;
        const abono = total * (porcentajeAbono / 100);
        const saldo = total - abono;
        
        currentTotals = {
            subtotal: subtotal,
            igv: igv,
            total: total,
            abono: abono,
            saldo: saldo,
            itemCount: items.length
        };
        
        console.log('💲 Totales calculados:', currentTotals);
        return currentTotals;
    }
    
    // Función para actualizar la interfaz de totales
    function updateTotalsDisplay() {
        console.log('🔄 Actualizando display de totales...');
        
        // Obtener símbolo de moneda actual
        const currencySymbol = getCurrencySymbol();
        
        // Actualizar totales principales con moneda
        $('#subtotal').text(formatCurrency(currentTotals.subtotal));
        $('#igv').text(formatCurrency(currentTotals.igv));
        $('#total').text(formatCurrency(currentTotals.total)).addClass('pulse-animation');
        
        // Actualizar labels con la moneda actual
        $('span:contains("Subtotal:")').html(`<i class="fas fa-calculator me-1 text-muted"></i>Subtotal (${currencySymbol}):`);
        $('span:contains("IGV (18%):")').html(`<i class="fas fa-percent me-1 text-muted"></i>IGV 18% (${currencySymbol}):`);
        $('span:contains("Total:")').html(`<i class="fas fa-receipt me-1"></i>Total (${currencySymbol}):`);
        
        // Actualizar labels de abono si están visibles
        if (!$('#abono-info').hasClass('d-none')) {
            $('#abono-info span:contains("Abono:")').html(`<i class="fas fa-hand-holding-dollar me-1"></i>Abono (${currencySymbol}):`);
            $('#abono-info span:contains("Saldo pendiente:")').html(`<i class="fas fa-hourglass-half me-1"></i>Saldo pendiente (${currencySymbol}):`);
        }
        
        // Actualizar información del carrito
        $('#items-count').text(currentTotals.itemCount);
        $('#currency-display').text(getCurrencyName());
        $('#payment-display').text(currentConfig.forma_pago);
        
        // Mostrar/ocultar información de abono
        const abonoInfo = $('#abono-info');
        if (currentConfig.forma_pago === 'Crédito' && currentConfig.porcentaje_abono < 100) {
            $('#abono-monto').text(formatCurrency(currentTotals.abono));
            $('#saldo-pendiente').text(formatCurrency(currentTotals.saldo));
            abonoInfo.removeClass('d-none').addClass('abono-section');
        } else {
            abonoInfo.addClass('d-none').removeClass('abono-section');
        }
        
        // Remover animación después de un tiempo
        setTimeout(() => {
            $('#total').removeClass('pulse-animation');
        }, 5000);
        
        console.log('✅ Display de totales actualizado');
    }
    
    // Función para actualizar configuración
    function updateConfig(newConfig) {
        console.log('⚙️ Actualizando configuración de totales:', newConfig);
        currentConfig = { ...currentConfig, ...newConfig };
        
        // Recalcular y actualizar display
        const items = window.pos ? window.pos.obtenerItems() : [];
        calculateTotals(items);
        updateTotalsDisplay();
    }
    
    // Función para actualizar items del carrito
    function updateCartItems(items) {
        console.log('🛒 Actualizando items del carrito en totales:', items.length);
        calculateTotals(items);
        updateTotalsDisplay();
    }
    
    // Función para obtener totales actuales
    function getCurrentTotals() {
        return { ...currentTotals };
    }
    
    // Función para obtener configuración actual
    function getCurrentConfig() {
        return { ...currentConfig };
    }
    
    // Event listeners para cambios de configuración
    $(document).on('configChanged', function(event, config) {
        console.log('📢 Configuración cambiada, actualizando totales...');
        updateConfig(config);
    });
    
    $(document).on('configUpdated', function(event, config, totals) {
        console.log('📢 Configuración y totales actualizados con conversión de moneda...');
        if (totals) {
            // Usar los totales convertidos desde el config section
            currentTotals = {
                subtotal: totals.subtotal,
                igv: totals.impuestos,
                total: totals.total,
                abono: totals.abono,
                saldo: totals.saldo,
                itemCount: currentTotals.itemCount
            };
            currentConfig = { ...currentConfig, ...config };
            updateTotalsDisplay();
        } else {
            updateConfig(config);
        }
    });
    
    // Event listener para cambios del carrito
    $(document).on('cartUpdated', function(event, items) {
        console.log('📢 Carrito actualizado, recalculando totales...');
        updateCartItems(items);
    });
    
    // Inicialización
    function initTotals() {
        console.log('🚀 Inicializando totales...');
        
        // Obtener configuración actual si existe
        if (window.posConfig && typeof window.posConfig.getConfig === 'function') {
            const config = window.posConfig.getConfig();
            updateConfig(config);
        }
        
        // Obtener items actuales si existen
        if (window.pos && typeof window.pos.obtenerItems === 'function') {
            const items = window.pos.obtenerItems();
            updateCartItems(items);
        } else {
            updateTotalsDisplay();
        }
    }
    
    // Exponer funciones globalmente
    window.totalesSection = {
        updateConfig: updateConfig,
        updateItems: updateCartItems,
        getCurrentTotals: getCurrentTotals,
        getCurrentConfig: getCurrentConfig,
        recalculate: function() {
            const items = window.pos ? window.pos.obtenerItems() : [];
            updateCartItems(items);
        }
    };
    
    // Función de test para debugging
    window.testTotales = function() {
        const testItems = [
            { precio: 100, cantidad: 2 },
            { precio: 50, cantidad: 1 }
        ];
        console.log('🧪 Probando totales con items de prueba...');
        calculateTotals(testItems);
        updateTotalsDisplay();
    };
    
    // Inicializar después de un pequeño delay para asegurar que otros componentes estén listos
    setTimeout(initTotals, 100);
    
    console.log('✅ Sección de totales inicializada correctamente');
});
</script>