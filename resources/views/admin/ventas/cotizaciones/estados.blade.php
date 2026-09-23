

<!-- Estado card removed in favor of global Dashboard Hero header -->

<!-- Overlay común -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar para editar estado de cotización -->
<div class="sidebar-estados" id="sidebarCotizacion">
    <div class="sidebar-header">
        <h6 class="mb-0">Editar Estado de Cotización</h6>
        <button type="button" class="btn-close btn-sm" id="btnCerrarCotizacion"></button>
    </div>
    <div class="sidebar-body">
        <form id="formCambiarEstado" data-cotizacion-id="{{ $cotizacion->id }}">
            @csrf
            <div class="mb-3">
                <label for="estado_id" class="form-label small">Estado de cotización</label>
                <select class="form-select form-select-sm" id="estado_id" name="estado_id">
                    @if(isset($estados) && $estados->isNotEmpty())
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" 
                                    {{ $cotizacion->estado_id == $estado->id ? 'selected' : '' }}
                                    data-estado="{{ $estado->nombre }}"
                                    data-color="{{ $estado->color }}">
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    @else
                        <option value="">No hay estados disponibles</option>
                    @endif
                </select>
            </div>
            <div class="mb-3">
                <label for="comentario" class="form-label small">Comentario</label>
                <textarea class="form-control form-control-sm" id="comentario" name="comentario" rows="2" 
                          placeholder="Comentario sobre el cambio de estado..." required></textarea>
            </div>
            <div id="camposAdicionales" class="mb-3">
                <div class="mb-2 small fw-medium text-muted">Campos adicionales</div>
                <!-- Campos para estado Interesado -->
                <div class="mb-2 campo-estado" data-estado="Interesado">
                    <label for="cotizacion_enviada" class="form-label small">¿Cotización enviada?</label>
                    <div class="d-flex">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cotizacion_enviada" id="cotizacionEnviadaSi" value="si"
                                   {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['cotizacion_enviada']) && $cotizacion->ultimoSeguimiento->datos_adicionales['cotizacion_enviada'] === 'si' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="cotizacionEnviadaSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="cotizacion_enviada" id="cotizacionEnviadaNo" value="no"
                                   {{ !$cotizacion->ultimoSeguimiento || !isset($cotizacion->ultimoSeguimiento->datos_adicionales['cotizacion_enviada']) || $cotizacion->ultimoSeguimiento->datos_adicionales['cotizacion_enviada'] !== 'si' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="cotizacionEnviadaNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 campo-estado" data-estado="Interesado">
                    <label for="metodo_pago" class="form-label small">Método de pago</label>
                    <select class="form-select form-select-sm" id="metodo_pago" name="metodo_pago">
                        <option value="">Seleccione una opción</option>
                        <option value="Crédito" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago']) && $cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago'] === 'Crédito' ? 'selected' : '' }}>Crédito</option>
                        <option value="Contado" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago']) && $cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago'] === 'Contado' ? 'selected' : '' }}>Contado</option>
                        <option value="Leasing" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago']) && $cotizacion->ultimoSeguimiento->datos_adicionales['metodo_pago'] === 'Leasing' ? 'selected' : '' }}>Leasing</option>
                    </select>
                </div>
                <!-- Campos para Crédito -->
                <div class="mb-2 campo-pago" data-metodo="Crédito">
                    <label for="solicitud_credito" class="form-label small">¿Solicitud de crédito?</label>
                    <div class="d-flex">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="solicitud_credito" id="solicitudCreditoSi" value="si"
                                   {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['solicitud_credito']) && $cotizacion->ultimoSeguimiento->datos_adicionales['solicitud_credito'] === 'si' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="solicitudCreditoSi">Sí</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="solicitud_credito" id="solicitudCreditoNo" value="no"
                                   {{ !$cotizacion->ultimoSeguimiento || !isset($cotizacion->ultimoSeguimiento->datos_adicionales['solicitud_credito']) || $cotizacion->ultimoSeguimiento->datos_adicionales['solicitud_credito'] !== 'si' ? 'checked' : '' }}>
                            <label class="form-check-label small" for="solicitudCreditoNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="mb-2 campo-pago" data-metodo="Crédito">
                    <label for="estado_credito" class="form-label small">Estado de solicitud</label>
                    <select class="form-select form-select-sm" id="estado_credito" name="estado_credito">
                        <option value="">Seleccione una opción</option>
                        <option value="Pendiente documentos" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito']) && $cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito'] === 'Pendiente documentos' ? 'selected' : '' }}>Pendiente documentos</option>
                        <option value="En Proceso" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito']) && $cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito'] === 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="Rechazado" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito']) && $cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito'] === 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                        <option value="Aprobado" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito']) && $cotizacion->ultimoSeguimiento->datos_adicionales['estado_credito'] === 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                    </select>
                </div>
                <!-- Campos para estado NO CUMPLE PERFIL -->
                <div class="mb-2 campo-estado" data-estado="NO CUMPLE PERFIL">
                    <label for="motivo_rechazo" class="form-label small">Motivo de rechazo</label>
                    <select class="form-select form-select-sm" id="motivo_rechazo" name="motivo_rechazo">
                        <option value="">Seleccione una opción</option>
                        <option value="Ingresos" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Ingresos' ? 'selected' : '' }}>Ingresos</option>
                        <option value="Chatarrización modelo anterior" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Chatarrización modelo anterior' ? 'selected' : '' }}>Chatarrización modelo anterior</option>
                        <option value="Ubicación" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Ubicación' ? 'selected' : '' }}>Ubicación</option>
                        <option value="Perdido" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Perdido' ? 'selected' : '' }}>Perdido</option>
                        <option value="Disponibilidad" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Disponibilidad' ? 'selected' : '' }}>Disponibilidad</option>
                        <option value="Interesado en usado" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Interesado en usado' ? 'selected' : '' }}>Interesado en usado</option>
                        <option value="Precio" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo'] === 'Precio' ? 'selected' : '' }}>Precio</option>
                    </select>
                </div>
                <!-- Campos para estado CERRADO GANADO -->
                <div class="mb-2 campo-estado" data-estado="CERRADO GANADO">
                    <label for="fecha_cierre" class="form-label small">Fecha de cierre</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_cierre" name="fecha_cierre"
                           value="{{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['fecha_cierre']) ? $cotizacion->ultimoSeguimiento->datos_adicionales['fecha_cierre'] : now()->format('Y-m-d') }}">
                </div>
                <div class="mb-2 campo-estado" data-estado="CERRADO GANADO">
                    <label for="monto_venta" class="form-label small">Monto de venta</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">{{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}</span>
                        <input type="number" class="form-control form-control-sm" id="monto_venta" name="monto_venta"
                               value="{{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['monto_venta']) ? $cotizacion->ultimoSeguimiento->datos_adicionales['monto_venta'] : $cotizacion->total }}">
                    </div>
                </div>
                <!-- Campo para estado Aceptada -->
                <div class="mb-2 campo-estado" data-estado="Aceptada">
                    <label for="fecha_aceptacion" class="form-label small">Fecha de aceptación</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_aceptacion" name="fecha_aceptacion"
                           value="{{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['fecha_aceptacion']) ? $cotizacion->ultimoSeguimiento->datos_adicionales['fecha_aceptacion'] : now()->format('Y-m-d') }}">
                </div>
                <!-- Campo para estado Rechazada -->
                <div class="mb-2 campo-estado" data-estado="Rechazada">
                    <label for="motivo_rechazo_estado" class="form-label small">Motivo de rechazo</label>
                    <select class="form-select form-select-sm" id="motivo_rechazo_estado" name="motivo_rechazo_estado">
                        <option value="">Seleccione una opción</option>
                        <option value="Precio elevado" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado'] === 'Precio elevado' ? 'selected' : '' }}>Precio elevado</option>
                        <option value="Mejor oferta de competencia" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado'] === 'Mejor oferta de competencia' ? 'selected' : '' }}>Mejor oferta de competencia</option>
                        <option value="Problemas de financiamiento" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado'] === 'Problemas de financiamiento' ? 'selected' : '' }}>Problemas de financiamiento</option>
                        <option value="Cambio de opinión" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado'] === 'Cambio de opinión' ? 'selected' : '' }}>Cambio de opinión</option>
                        <option value="Otro" {{ $cotizacion->ultimoSeguimiento && isset($cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado']) && $cotizacion->ultimoSeguimiento->datos_adicionales['motivo_rechazo_estado'] === 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary px-3">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}

.sidebar-estados {
    position: fixed;
    top: 0;
    right: -320px;
    width: 320px;
    height: 100%;
    background-color: #fff;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    transition: right 0.3s ease;
    overflow-y: auto;
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.sidebar-body {
    padding: 1rem;
}

@media (max-width: 576px) {
    .sidebar-estados {
        width: 280px;
        right: -280px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asegurarse de que jQuery esté disponible
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado. Usando JavaScript vanilla.');
        
        // Variables para las funciones de JS vanilla
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarCotizacion = document.getElementById('sidebarCotizacion');
        const btnEditarCotizacion = document.getElementById('btnEditarCotizacion');
        const btnCerrarCotizacion = document.getElementById('btnCerrarCotizacion');
        const estadoSelect = document.getElementById('estado_id');
        const metodoPagoSelect = document.getElementById('metodo_pago');
        const formCambiarEstado = document.getElementById('formCambiarEstado');
        
        // Función para mostrar el sidebar
        function mostrarSidebar() {
            console.log('Abriendo sidebar con JS vanilla');
            sidebarOverlay.style.display = 'block';
            sidebarCotizacion.style.right = '0';
            document.body.style.overflow = 'hidden';
            mostrarCamposEstado();
        }
        
        // Función para cerrar el sidebar
        function cerrarSidebar() {
            sidebarOverlay.style.display = 'none';
            sidebarCotizacion.style.right = '-320px';
            document.body.style.overflow = 'auto';
        }
        
        // Función para mostrar/ocultar campos según estado
        function mostrarCamposEstado() {
            const estadoSeleccionado = estadoSelect.options[estadoSelect.selectedIndex].getAttribute('data-estado');
            const camposEstado = document.querySelectorAll('.campo-estado');
            
            // Ocultar todos los campos
            camposEstado.forEach(campo => {
                campo.style.display = 'none';
                const inputs = campo.querySelectorAll('input, select');
                inputs.forEach(input => input.disabled = true);
            });
            
            // Mostrar los campos del estado seleccionado
            if (estadoSeleccionado) {
                const camposVisibles = document.querySelectorAll(`.campo-estado[data-estado="${estadoSeleccionado}"]`);
                camposVisibles.forEach(campo => {
                    campo.style.display = 'block';
                    const inputs = campo.querySelectorAll('input, select');
                    inputs.forEach(input => input.disabled = false);
                });
            }
            
            // Mostrar campos de pago si es necesario
            mostrarCamposPago();
        }
        
        // Función para mostrar campos de pago
        function mostrarCamposPago() {
            const metodoPago = metodoPagoSelect.value;
            const camposPago = document.querySelectorAll('.campo-pago');
            
            // Ocultar todos los campos de pago
            camposPago.forEach(campo => {
                campo.style.display = 'none';
                const inputs = campo.querySelectorAll('input, select');
                inputs.forEach(input => input.disabled = true);
            });
            
            // Mostrar los campos del método de pago seleccionado
            if (metodoPago) {
                const camposVisibles = document.querySelectorAll(`.campo-pago[data-metodo="${metodoPago}"]`);
                camposVisibles.forEach(campo => {
                    campo.style.display = 'block';
                    const inputs = campo.querySelectorAll('input, select');
                    inputs.forEach(input => input.disabled = false);
                });
            }
        }
        
        // Agregar eventos
        if (btnEditarCotizacion) {
            btnEditarCotizacion.addEventListener('click', function(e) {
                e.preventDefault();
                mostrarSidebar();
            });
        } else {
            console.error('Botón de edición no encontrado en el DOM');
        }
        
        if (btnCerrarCotizacion) {
            btnCerrarCotizacion.addEventListener('click', cerrarSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', cerrarSidebar);
        }
        
        if (estadoSelect) {
            estadoSelect.addEventListener('change', mostrarCamposEstado);
        }
        
        if (metodoPagoSelect) {
            metodoPagoSelect.addEventListener('change', mostrarCamposPago);
        }
        
        // Inicializar visibilidad de campos
        if (estadoSelect) {
            mostrarCamposEstado();
        }
        
        // Gestionar el envío del formulario
        if (formCambiarEstado) {
            formCambiarEstado.addEventListener('submit', function(e) {
                e.preventDefault();
                // Aquí iría la lógica de envío del formulario con fetch API
                // Por simplicidad, no se implementa completamente aquí
                alert('Implementar envío de formulario con fetch API');
            });
        }
    } else {
        // Usar jQuery si está disponible
        (function($) {
            console.log('jQuery está cargado correctamente:', $.fn.jquery);
            
            // Función para cerrar sidebars
            function closeSidebars() {
                $('#sidebarOverlay').fadeOut(300);
                $('.sidebar-estados').css('right', '-320px');
                $('body').css('overflow', 'auto');
            }
            
            // Función para mostrar/ocultar campos según estado
            function mostrarCamposEstado() {
                const estadoSeleccionado = $('#estado_id option:selected').data('estado');
                $('.campo-estado').hide().find('input, select').prop('disabled', true);
                
                if (estadoSeleccionado) {
                    $(`.campo-estado[data-estado="${estadoSeleccionado}"]`)
                        .show()
                        .find('input, select')
                        .prop('disabled', false);
                }
                
                // Mostrar campos de pago si es Interesado
                mostrarCamposPago();
            }
            
            // Función para mostrar campos de pago
            function mostrarCamposPago() {
                const metodoPago = $('#metodo_pago').val();
                $('.campo-pago').hide().find('input, select').prop('disabled', true);
                
                if (metodoPago) {
                    $(`.campo-pago[data-metodo="${metodoPago}"]`)
                        .show()
                        .find('input, select')
                        .prop('disabled', false);
                }
            }

            // Asegurarnos de que el selector está correcto y existe en la página
            const btnEditarCotizacion = $('#btnEditarCotizacion');
            if (btnEditarCotizacion.length === 0) {
                console.error('El botón #btnEditarCotizacion no se encontró en la página');
            } else {
                console.log('Botón de edición encontrado');
            }
            
            // Mostrar sidebar de estados - esto está delegado al documento para manejar casos donde el botón
            // podría estar en contenido cargado dinámicamente
            $(document).on('click', '#btnEditarCotizacion', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Botón de edición clickeado');
                $('#sidebarOverlay').fadeIn(300);
                $('#sidebarCotizacion').css('right', '0');
                $('body').css('overflow', 'hidden');
                mostrarCamposEstado();
            });
            
            // Para probar, también podemos agregar otro manejador directo al botón
            btnEditarCotizacion.on('click', function(e) {
                console.log('Click directo en btnEditarCotizacion');
            });
            
            // Cerrar sidebars
            $('#btnCerrarCotizacion, #sidebarOverlay').on('click', closeSidebars);
            
            // Eventos de cambio
            $('#estado_id').change(mostrarCamposEstado);
            $('#metodo_pago').change(mostrarCamposPago);
            
            // Envío del formulario
            $('#formCambiarEstado').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const cotizacionId = form.data('cotizacion-id');
                const estadoSeleccionado = $('#estado_id').val();
                const estadoActual = '{{ $cotizacion->estado_id }}';
                
                // Validar si el estado es el mismo
                if (estadoSeleccionado === estadoActual) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('El estado seleccionado es el mismo que el actual');
                    } else {
                        alert('El estado seleccionado es el mismo que el actual');
                    }
                    return;
                }
                
                // Validaciones
                const estadoNombre = $('#estado_id option:selected').data('estado');
                let isValid = true;
                
                // Validar comentario
                if (!$('#comentario').val().trim()) {
                    isValid = false;
                    $('#comentario').addClass('is-invalid');
                    if (typeof toastr !== 'undefined') {
                        toastr.error('El campo comentario es obligatorio');
                    } else {
                        alert('El campo comentario es obligatorio');
                    }
                } else {
                    $('#comentario').removeClass('is-invalid');
                }
                
                // Validar selects visibles
                $(`.campo-estado[data-estado="${estadoNombre}"] select:visible`).each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        if (typeof toastr !== 'undefined') {
                            toastr.error(`El campo "${$(this).attr('name')}" es requerido`);
                        } else {
                            alert(`El campo "${$(this).attr('name')}" es requerido`);
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                // Validar inputs visibles
                $(`.campo-estado[data-estado="${estadoNombre}"] input[type="date"]:visible, .campo-estado[data-estado="${estadoNombre}"] input[type="number"]:visible`).each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        if (typeof toastr !== 'undefined') {
                            toastr.error(`El campo "${$(this).attr('name')}" es requerido`);
                        } else {
                            alert(`El campo "${$(this).attr('name')}" es requerido`);
                       }
                   } else {
                       $(this).removeClass('is-invalid');
                   }
               });
               
               if (!isValid) return;
               
               // AJAX request
               const submitBtn = form.find('button[type="submit"]');
               submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
               
               $.ajax({
                   url: `/admin/ventas/cotizaciones/${cotizacionId}/cambiar-estado`,
                   method: 'POST',
                   data: form.serialize(),
                   headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                   success: function(response) {
                       if (response.success) {
                           const estadoLabel = $('span[data-color]');
                           estadoLabel.text(response.estado_nombre)
                                      .css('border-left-color', `#${response.estado_color}`)
                                      .attr('data-color', response.estado_color);
                           
                           if (typeof toastr !== 'undefined') {
                               toastr.success(response.message);
                           } else {
                               alert(response.message || 'Estado actualizado con éxito');
                           }
                           
                           closeSidebars();
                           
                           if (typeof recargarSeguimientos === 'function') {
                               recargarSeguimientos();
                           }
                       }
                   },
                   error: function(xhr) {
                       const errorMsg = xhr.responseJSON?.message || 'Error al cambiar el estado';
                       if (typeof toastr !== 'undefined') {
                           toastr.error(errorMsg);
                       } else {
                           alert(errorMsg);
                       }
                   },
                   complete: function() {
                       submitBtn.prop('disabled', false).html('Guardar cambios');
                   }
               });
           });
           
           // Inicializar campos al cargar
           mostrarCamposEstado();
           
           // Agregar manejador global para abrir el sidebar en caso de problemas con eventos delegados
           setTimeout(function() {
               const btnManual = document.getElementById('btnEditarCotizacion');
               if (btnManual) {
                   console.log('Agregando manejador de evento adicional al botón');
                   btnManual.addEventListener('click', function(e) {
                       console.log('Click desde manejador manual');
                       $('#sidebarOverlay').fadeIn(300);
                       $('#sidebarCotizacion').css('right', '0');
                       $('body').css('overflow', 'hidden');
                       mostrarCamposEstado();
                   });
               }
           }, 1000);
       })(jQuery);
   }
});
</script>