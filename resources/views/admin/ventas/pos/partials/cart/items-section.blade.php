{{-- resources/views/admin/ventas/pos/partials/cart/items-section.blade.php --}}
<div class="p-2 p-sm-3 border-bottom" style="max-height: clamp(250px, 50vh, 400px); overflow-y: auto;">
    <div class="d-flex justify-content-between align-items-center mb-2 mb-sm-3">
        <h6 class="mb-0 fs-7 fs-sm-6">
            <i class="fas fa-list me-2 text-primary"></i>Items
        </h6>
        <span class="badge bg-primary rounded-pill fs-7 fs-sm-6" id="item-count">0</span>
    </div>
    
    <div id="items-container">
        <!-- Placeholder cuando no hay items -->
        <div id="items-placeholder" class="text-center text-muted p-4">
            <i class="fas fa-cart-plus fa-3x mb-3 opacity-50"></i>
            <h6>Carrito vacío</h6>
            <p class="mb-0 small">Agregue productos para comenzar</p>
        </div>
        
        <!-- Lista de items -->
        <div id="items-list" class="d-none">
            <div class="list-group">
                <!-- Items will be added here dynamically -->
            </div>
        </div>
    </div>
</div>

<style>
.item-cart {
    border-left: 3px solid var(--bs-primary);
    transition: all 0.2s ease;
}

.item-cart:hover {
    background-color: #f8f9fa;
}

.item-cart .item-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.item-cart:hover .item-actions {
    opacity: 1;
}

.quantity-input {
    width: 60px;
    text-align: center;
}

.price-display {
    font-weight: 600;
    color: var(--bs-primary);
}

.subtotal-display {
    font-weight: 600;
    color: var(--bs-success);
}

.item-badge-stock {
    font-size: 0.7em;
}

.discount-controls {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.item-cart:hover .discount-controls {
    opacity: 1;
}
</style>

<script>
$(document).ready(function() {
    console.log('🛒 Inicializando carrito...');
    
    // Array para almacenar los items del carrito
    let cartItems = [];
    
    // Function to update cart display
    function updateCartDisplay(items = cartItems) {
        const itemsList = $('#items-list');
        const itemsPlaceholder = $('#items-placeholder');
        const itemCount = $('#item-count');
        
        // Update item count
        itemCount.text(items.length);
        
        if (items.length === 0) {
            itemsList.addClass('d-none');
            itemsPlaceholder.removeClass('d-none');
            return;
        }
        
        itemsPlaceholder.addClass('d-none');
        itemsList.removeClass('d-none');
        
        // Clear current items
        const listGroup = itemsList.find('.list-group');
        listGroup.empty();
        
        // Obtener configuración actual y tipo de cambio
        const currentConfig = window.posConfig ? window.posConfig.getConfig() : { moneda: 'Soles' };
        const tipoCambio = window.posConfig ? window.posConfig.tipoCambio.obtener() : null;
        
        // Add each item to the list
        items.forEach((item) => {
            const stockBadge = item.tiene_stock 
                ? '<span class="badge bg-success item-badge-stock">En stock</span>' 
                : '<span class="badge bg-warning item-badge-stock">Sin stock</span>';
            
            // Determinar moneda de display y conversión de precios
            const displayCurrency = currentConfig.moneda || 'Soles';
            const monedaSymbol = displayCurrency === 'Dólares' ? 'US$' : 'S/';
            
            let precio = parseFloat(item.precio || 0);
            const cantidad = parseInt(item.cantidad || 1);
            
            // Aplicar conversión de moneda si es necesario
            if (displayCurrency === 'Dólares' && tipoCambio && tipoCambio.disponible) {
                // El precio original está en soles, convertir a dólares
                precio = precio / tipoCambio.venta;
            } else if (displayCurrency === 'Soles' && item.moneda === 'Dólares' && tipoCambio && tipoCambio.disponible) {
                // El precio original está en dólares, convertir a soles  
                precio = precio * tipoCambio.venta;
            }
            
            // Calcular descuento si existe
            let subtotal = precio * cantidad;
            let descuentoInfo = '';
            
            if (item.descuento && item.descuento > 0) {
                let descuentoMonto = 0;
                if (item.descuento_tipo === 'porcentaje') {
                    descuentoMonto = (subtotal * item.descuento) / 100;
                } else {
                    descuentoMonto = parseFloat(item.descuento);
                }
                subtotal = subtotal - descuentoMonto;
                
                descuentoInfo = `
                    <small class="text-success d-block">
                        <i class="fas fa-tag me-1"></i>
                        -${item.descuento_tipo === 'porcentaje' ? item.descuento + '%' : monedaSymbol + ' ' + item.descuento.toFixed(2)}
                    </small>
                `;
            }
            
            const itemHtml = `
                <div class="list-group-item item-cart py-2 px-3" data-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">${item.nombre}</h6>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <small class="text-muted">${item.codigo}</small>
                                        <span class="text-muted">•</span>
                                        <small class="text-muted">${item.unidad}</small>
                                        ${stockBadge}
                                    </div>
                                    ${item.almacen_nombre ? `<small class="text-info"><i class="fas fa-warehouse me-1"></i>${item.almacen_nombre}</small>` : ''}
                                </div>
                                <div class="text-end ms-2">
                                    <div class="price-display">${monedaSymbol} ${precio.toFixed(2)}</div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="item-actions d-flex align-items-center gap-1">
                                    <button class="btn btn-sm btn-outline-danger btn-remove-item" data-id="${item.id}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary btn-decrease-qty" data-id="${item.id}" title="Disminuir">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm quantity-input" 
                                           value="${cantidad}" min="1" max="9999" data-id="${item.id}">
                                    <button class="btn btn-sm btn-outline-secondary btn-increase-qty" data-id="${item.id}" title="Aumentar">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    
                                    <!-- Botón de descuento (oculto por defecto) -->
                                    <div class="discount-controls d-none ms-2">
                                        <button class="btn btn-sm btn-outline-warning btn-discount" data-id="${item.id}" title="Aplicar descuento">
                                            <i class="fas fa-percent"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="subtotal-display fw-bold">
                                        ${monedaSymbol} ${subtotal.toFixed(2)}
                                    </div>
                                    ${descuentoInfo}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            listGroup.append(itemHtml);
        });
        
        // Add event listeners
        addCartItemEventListeners();
        
        // Trigger cart update event
        $(document).trigger('cartUpdated', [items]);
    }
    
    // Function to add event listeners to cart items
    function addCartItemEventListeners() {
        // Remove item button
        $('.btn-remove-item').off('click').on('click', function(e) {
            e.preventDefault();
            const itemId = $(this).data('id');
            removeFromCart(itemId);
        });
        
        // Decrease quantity button
        $('.btn-decrease-qty').off('click').on('click', function(e) {
            e.preventDefault();
            const itemId = $(this).data('id');
            const input = $(`.quantity-input[data-id="${itemId}"]`);
            let value = parseInt(input.val()) || 1;
            
            if (value > 1) {
                value--;
                input.val(value);
                updateItemQuantity(itemId, value);
            }
        });
        
        // Increase quantity button
        $('.btn-increase-qty').off('click').on('click', function(e) {
            e.preventDefault();
            const itemId = $(this).data('id');
            const input = $(`.quantity-input[data-id="${itemId}"]`);
            let value = parseInt(input.val()) || 1;
            
            value++;
            input.val(value);
            updateItemQuantity(itemId, value);
        });
        
        // Quantity input change
        $('.quantity-input').off('change input').on('change input', function(e) {
            const itemId = $(this).data('id');
            let newQuantity = parseInt($(this).val());
            
            if (isNaN(newQuantity) || newQuantity < 1) {
                newQuantity = 1;
                $(this).val(1);
            }
            
            updateItemQuantity(itemId, newQuantity);
        });
        
        // Discount button
        $('.btn-discount').off('click').on('click', function(e) {
            e.preventDefault();
            const itemId = $(this).data('id');
            showDiscountModal(itemId);
        });
    }
    
    // Function to add item to cart
    function addToCart(item) {
        if (!item.id || !item.nombre) {
            console.error('❌ Item inválido:', item);
            return false;
        }
        
        // Check if item already exists
        const existingIndex = cartItems.findIndex(cartItem => cartItem.id === item.id);
        
        if (existingIndex !== -1) {
            // Update quantity if exists
            cartItems[existingIndex].cantidad = (cartItems[existingIndex].cantidad || 1) + 1;
        } else {
            // Add new item
            const newItem = {
                id: item.id,
                nombre: item.nombre || 'Sin nombre',
                codigo: item.codigo || 'N/A',
                precio: parseFloat(item.precio || 0),
                moneda: item.moneda === 'SOL' ? 'Soles' : (item.moneda || 'Soles'),
                unidad: item.unidad || 'Unidad',
                categoria: item.categoria || 'Sin categoría',
                stock_disponible: parseFloat(item.stock_disponible || 0),
                tiene_stock: parseFloat(item.stock_disponible || 0) > 0,
                marca: item.marca || '',
                almacen_nombre: item.almacen_nombre || null,
                cantidad: 1,
                descuento: 0,
                descuento_tipo: 'porcentaje'
            };
            
            cartItems.push(newItem);
        }
        
        updateCartDisplay();
        
        const mensaje = item.tiene_stock 
            ? `${item.nombre} agregado al carrito` 
            : `${item.nombre} agregado al carrito (Sin stock)`;
        showNotification(mensaje, item.tiene_stock ? 'success' : 'warning');
        
        return true;
    }
    
    // Function to remove item from cart
    function removeFromCart(itemId) {
        cartItems = cartItems.filter(item => item.id !== itemId);
        updateCartDisplay();
        showNotification('Item removido del carrito', 'info');
    }
    
    // Function to update item quantity
    function updateItemQuantity(itemId, newQuantity) {
        const itemIndex = cartItems.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            cartItems[itemIndex].cantidad = newQuantity;
            updateCartDisplay();
        }
    }
    
    // Function to clear cart
    function clearCart() {
        cartItems = [];
        updateCartDisplay();
        showNotification('Carrito limpiado', 'info');
    }
    
    // Function to get cart items
    function getCartItems() {
        return [...cartItems];
    }
    
    // Function to show discount modal (versión simple)
    function showDiscountModal(itemId) {
        const item = cartItems.find(item => item.id === itemId);
        if (!item) return;
        
        const discountValue = prompt('Ingrese el descuento (%):', item.descuento || 0);
        if (discountValue !== null && !isNaN(discountValue)) {
            const discount = Math.max(0, Math.min(100, parseFloat(discountValue)));
            applyDiscountToItem(itemId, discount, 'porcentaje');
        }
    }
    
    // Function to apply discount to item
    function applyDiscountToItem(itemId, discountValue, discountType) {
        const itemIndex = cartItems.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            cartItems[itemIndex].descuento = discountValue;
            cartItems[itemIndex].descuento_tipo = discountType;
            updateCartDisplay();
            
            const mensaje = discountValue > 0 
                ? `Descuento de ${discountValue}% aplicado`
                : 'Descuento removido';
            showNotification(mensaje, 'success');
        }
    }
    
    // Function to show notifications
    function showNotification(message, type = 'info') {
        if (typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
    
    // Expose cart functions globally
    window.pos = window.pos || {};
    window.pos.agregarAlCarrito = addToCart;
    window.pos.removerDelCarrito = removeFromCart;
    window.pos.actualizarCantidad = updateItemQuantity;
    window.pos.limpiarCarrito = clearCart;
    window.pos.obtenerItems = getCartItems;
    
    // Escuchar cambios de configuración para mostrar/ocultar descuentos y actualizar moneda
    $(document).on('configChanged', function(event, config) {
        if (config.habilitar_descuentos) {
            $('.discount-controls').removeClass('d-none');
        } else {
            $('.discount-controls').addClass('d-none');
        }
        
        // Actualizar display del carrito cuando cambie la configuración de moneda
        console.log('🔄 Configuración cambiada, actualizando display del carrito...');
        updateCartDisplay();
    });
    
    console.log('✅ Carrito inicializado correctamente');
});
</script>