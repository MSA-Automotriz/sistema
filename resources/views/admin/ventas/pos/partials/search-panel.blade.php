{{-- resources/views/admin/ventas/pos/partials/search-panel.blade.php --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="fas fa-search text-primary me-2"></i>
            Buscar Partes y Repuestos
        </h5>
    </div>
    
    <div class="card-body">
        <!-- Barra de búsqueda -->
        <div class="mb-3">
            <div class="input-group input-group-sm input-group-md">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="buscar-partes"
                       placeholder="Buscar por nombre, código o marca...">
                <button class="btn btn-primary" type="button" id="btn-buscar">
                    <i class="fas fa-search d-md-none"></i><span class="d-none d-md-inline">Buscar</span>
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row g-2 g-md-3 mb-3">
            <div class="col-12 col-sm-6 col-md-4">
                <select class="form-select form-select-sm form-select-md" id="categoria-filtro">
                    <option value="">Todas las categorías</option>
                    @if(isset($categoriasParte))
                        @foreach($categoriasParte as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <select class="form-select form-select-sm form-select-md" id="almacen-filtro">
                    <option value="">Todos los almacenes</option>
                    @if(isset($almacenes))
                        @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-check mt-1 mt-md-2">
                    <input class="form-check-input" type="checkbox" id="incluir-sin-stock" checked>
                    <label class="form-check-label fs-7 fs-md-6" for="incluir-sin-stock">
                        Incluir sin stock
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Resultados -->
        <div id="resultados-busqueda">
            <div class="text-center text-muted p-4">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p>Busque partes para agregar al carrito</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA CONFIRMAR ITEMS SIN STOCK -->
<div class="modal fade" id="modalSinStock" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Producto sin stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-box-open fa-3x text-warning mb-3"></i>
                    <h6 id="nombre-producto-modal">Nombre del producto</h6>
                    <p class="text-muted" id="info-producto-modal">Información del producto</p>
                </div>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>¿Qué sucede al agregar sin stock?</h6>
                    <ul class="mb-0">
                        <li>Se agregará al carrito normalmente</li>
                        <li>Se marcará como "Sin stock"</li>
                        <li>Se puede generar un requerimiento de compra</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmar-agregar-sin-stock">
                    <i class="fas fa-plus me-1"></i>Sí, agregar al carrito
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.item-resultado {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s;
    cursor: pointer;
}

.item-resultado:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    transform: translateY(-2px);
}

.item-sin-stock {
    opacity: 0.7;
    background-color: #f8f9fa;
}

.badge-stock {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>

<script>
$(document).ready(function() {
    console.log('🔍 Inicializando búsqueda de partes...');
    
    let itemPendienteAgregar = null; // Para guardar el item que se quiere agregar sin stock
    
    // Función para buscar partes (MODIFICADA)
    function buscarPartes() {
        const query = $('#buscar-partes').val().trim();
        const categoria = $('#categoria-filtro').val();
        const almacen = $('#almacen-filtro').val();
        const incluirSinStock = $('#incluir-sin-stock').is(':checked');
        
        // NUEVA LÓGICA: Si no hay almacén seleccionado (todos los almacenes), mostrar productos
        const todosLosAlmacenes = !almacen || almacen === '';
        
        // Condición modificada: mostrar productos si:
        // 1. Hay query de al menos 2 caracteres, O
        // 2. Hay categoría seleccionada, O
        // 3. Hay almacén específico seleccionado, O
        // 4. Está seleccionado "todos los almacenes" (nuevo)
        if (!todosLosAlmacenes && query.length < 2 && !categoria && !almacen) {
            $('#resultados-busqueda').html(`
                <div class="text-center text-muted p-4">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p>Ingrese al menos 2 caracteres para buscar</p>
                </div>
            `);
            return;
        }
        
        // Mostrar loading
        $('#resultados-busqueda').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <p class="mt-2 text-muted">Buscando partes...</p>
            </div>
        `);
        
        // Realizar búsqueda
        const params = {
            query: query,
            categoria_id: categoria,
            almacen_id: almacen,
            incluir_sin_stock: incluirSinStock
        };
        
        // MODIFICACIÓN: Usar obtenerTodasPartes cuando no hay query específico
        const url = query && query.length >= 2 ? 
            '{{ route("admin.ventas.pos.buscar-partes") }}' : 
            '{{ route("admin.ventas.pos.obtener-todas-partes") }}';
        
        $.ajax({
            url: url,
            method: 'GET',
            data: params,
            success: function(response) {
                console.log('📋 Resultados obtenidos:', response);
                mostrarResultados(response.items || []);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error en búsqueda:', error);
                $('#resultados-busqueda').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al buscar partes: ${error}
                    </div>
                `);
            }
        });
    }
    
    // Función para mostrar resultados
    function mostrarResultados(items) {
        console.log('📊 Mostrando', items.length, 'resultados');
        
        if (!items || items.length === 0) {
            $('#resultados-busqueda').html(`
                <div class="text-center text-muted p-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p>No se encontraron partes</p>
                </div>
            `);
            return;
        }
        
        let html = '<div class="row">';
        
        items.forEach(function(item) {
            const tieneStock = parseFloat(item.stock_disponible || 0) > 0;
            const stockClass = tieneStock ? '' : 'item-sin-stock';
            const stockBadge = tieneStock ? 
                `<span class="badge bg-success badge-stock">Stock: ${item.stock_disponible}</span>` :
                `<span class="badge bg-danger badge-stock">Sin stock</span>`;
            
            const precio = parseFloat(item.precio || 0);
            const simboloMoneda = item.moneda === 'Dólares' ? 'US$' : 'S/';
            
            // Mostrar información de almacén si es "todos los almacenes"
            const almacenInfo = item.almacen_nombre && item.almacen_nombre !== 'Multiple' ? 
                `<small class="text-info d-block"><i class="fas fa-warehouse me-1"></i>${item.almacen_nombre}</small>` : 
                (item.almacen_nombre === 'Multiple' ? 
                    `<small class="text-info d-block"><i class="fas fa-warehouse me-1"></i>Múltiples almacenes</small>` : '');
            
            html += `
                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                    <div class="item-resultado ${stockClass} position-relative h-100"
                         onclick="intentarAgregarAlCarrito(${item.id}, '${item.nombre.replace(/'/g, "\\'")}', ${precio}, '${item.moneda}', '${item.codigo}', '${item.unidad}', ${tieneStock}, ${item.stock_disponible})">
                        ${stockBadge}

                        <div class="d-flex flex-column h-100">
                            <div class="d-flex align-items-start mb-2">
                                <div class="me-2">
                                    <i class="fas fa-cogs text-primary" style="font-size: clamp(1rem, 4vw, 1.5rem);"></i>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <h6 class="mb-1 fs-7 fs-md-6 text-break">${item.nombre}</h6>
                                    <small class="text-muted d-block text-break">${item.codigo}</small>
                                    <small class="text-muted d-block">${item.categoria || 'Sin categoría'}</small>
                                    ${almacenInfo}
                                </div>
                            </div>

                            <div class="mt-auto pt-2">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <span class="h6 text-primary mb-0">${simboloMoneda} ${precio.toFixed(2)}</span>
                                        <small class="text-muted d-block">${item.unidad}</small>
                                    </div>
                                    <button class="btn btn-sm btn-primary flex-shrink-0" onclick="event.stopPropagation(); intentarAgregarAlCarrito(${item.id}, '${item.nombre.replace(/'/g, "\\'")}', ${precio}, '${item.moneda}', '${item.codigo}', '${item.unidad}', ${tieneStock}, ${item.stock_disponible})">
                                        <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">Agregar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        $('#resultados-busqueda').html(html);
    }
    
    // Función PRINCIPAL para intentar agregar al carrito
    window.intentarAgregarAlCarrito = function(id, nombre, precio, moneda, codigo, unidad, tieneStock, stockDisponible) {
        console.log('🛒 Intentando agregar:', {id, nombre, tieneStock});
        
        const item = {
            id: id,
            nombre: nombre,
            precio: precio,
            moneda: moneda === 'SOL' ? 'Soles' : moneda,
            codigo: codigo,
            unidad: unidad,
            tiene_stock: tieneStock,
            stock_disponible: stockDisponible,
            tipo: 'parte'
        };
        
        // Si tiene stock, agregar directamente
        if (tieneStock) {
            console.log('✅ Tiene stock, agregando directamente');
            ejecutarAgregarAlCarrito(item);
            return;
        }
        
        // Si no tiene stock, mostrar modal de confirmación
        console.log('⚠️ Sin stock, mostrando modal de confirmación');
        itemPendienteAgregar = item; // Guardar para usar después
        
        // Actualizar contenido del modal
        $('#nombre-producto-modal').text(nombre);
        $('#info-producto-modal').text(`Código: ${codigo} | Stock disponible: ${stockDisponible}`);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalSinStock'));
        modal.show();
    };
    
    // Función para ejecutar la adición al carrito
    function ejecutarAgregarAlCarrito(item) {
        console.log('📦 Ejecutando agregar al carrito:', item);
        
        // Intentar usar la función del carrito si está disponible
        if (window.pos && typeof window.pos.agregarAlCarrito === 'function') {
            console.log('✅ Usando window.pos.agregarAlCarrito');
            const resultado = window.pos.agregarAlCarrito(item);
            console.log('Resultado:', resultado);
            
            // Mostrar notificación
            const mensaje = item.tiene_stock ? 
                `${item.nombre} agregado al carrito` : 
                `${item.nombre} agregado al carrito (SIN STOCK)`;
            mostrarNotificacion(mensaje, item.tiene_stock ? 'success' : 'warning');
            
        } else {
            console.error('❌ window.pos.agregarAlCarrito no disponible');
            
            // Fallback: mostrar mensaje
            const mensaje = item.tiene_stock ? 
                `${item.nombre} agregado al carrito` : 
                `${item.nombre} agregado al carrito (SIN STOCK)`;
            alert(mensaje);
        }
    }
    
    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo) {
        if (window.mostrarNotificacion && typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(mensaje, tipo);
        } else {
            console.log(`NOTIFICACIÓN [${tipo}]: ${mensaje}`);
        }
    }
    
    // Event listeners
    $('#btn-buscar').on('click', buscarPartes);
    
    $('#buscar-partes').on('keyup', function(e) {
        if (e.key === 'Enter') {
            buscarPartes();
        }
        
        // Búsqueda automática después de 500ms
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(buscarPartes, 500);
    });
    
    // MODIFICACIÓN: Disparar búsqueda cuando cambian los filtros
    $('#categoria-filtro, #almacen-filtro, #incluir-sin-stock').on('change', function() {
        // Limpiar timeout previo
        clearTimeout(window.searchTimeout);
        // Ejecutar búsqueda inmediatamente cuando cambian los filtros
        buscarPartes();
    });
    
    // Confirmar agregar sin stock
    $('#confirmar-agregar-sin-stock').on('click', function() {
        if (itemPendienteAgregar) {
            console.log('✅ Usuario confirmó agregar sin stock');
            ejecutarAgregarAlCarrito(itemPendienteAgregar);
            itemPendienteAgregar = null;
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalSinStock'));
            modal.hide();
        }
    });
    
    // Limpiar item pendiente cuando se cierra el modal
    $('#modalSinStock').on('hidden.bs.modal', function() {
        itemPendienteAgregar = null;
    });
    
    console.log('✅ Búsqueda de partes inicializada');
    
    // MODIFICACIÓN: Cargar partes iniciales automáticamente
    setTimeout(function() {
        console.log('🚀 Cargando productos iniciales...');
        buscarPartes();
    }, 500);
});
</script>