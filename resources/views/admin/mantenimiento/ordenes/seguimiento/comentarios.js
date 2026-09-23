// resources/views/admin/mantenimiento/ordenes/seguimiento/comentarios.js

// Definir globalmente antes del DOMContentLoaded
window.seguimientoActual = null;

// Función global para abrir el sidebar
window.abrirSidebarComentarios = function(seguimientoId) {
    try {
        console.log('Intentando abrir sidebar para seguimiento:', seguimientoId);
        
        // Verificamos si Bootstrap está disponible
        if (typeof bootstrap === 'undefined') {
            console.error('Error: Bootstrap no está disponible');
            alert('Error al cargar la interfaz. Por favor, recarga la página.');
            return;
        }
        
        // Almacenar ID para uso posterior
        window.seguimientoActual = seguimientoId;
        
        // Verificar si ya existe el sidebar en el DOM
        let sidebarComentarios = document.getElementById('sidebarComentarios');
        
        // Si no existe, hacemos fetch del HTML del sidebar
        if (!sidebarComentarios) {
            console.log('Sidebar no encontrado en el DOM, cargando desde el servidor...');
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                alert('Error de seguridad. Por favor, recarga la página.');
                return;
            }
            
            // Mostrar indicador de carga en el botón
            const btnComentarios = document.querySelector(`[data-seguimiento-id="${seguimientoId}"]`);
            let originalContent = '';
            if (btnComentarios) {
                originalContent = btnComentarios.innerHTML;
                btnComentarios.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                btnComentarios.disabled = true;
            }
            
            // Cargar el HTML del sidebar desde el servidor
            fetch(`/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/sidebar`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la respuesta: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                // Eliminar sidebar existente si hay uno
                const existingSidebar = document.getElementById('sidebarComentarios');
                if (existingSidebar) {
                    existingSidebar.remove();
                }
                
                // Añadir el nuevo sidebar al DOM
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                document.body.appendChild(tempDiv.querySelector('.offcanvas'));
                
                // Obtener la referencia al sidebar recién agregado
                sidebarComentarios = document.getElementById('sidebarComentarios');
                
                if (!sidebarComentarios) {
                    throw new Error('No se pudo encontrar el sidebar después de agregarlo al DOM');
                }
                
                // Agregar el ID del seguimiento como atributo al sidebar
                sidebarComentarios.setAttribute('data-seguimiento-id', seguimientoId);
                
                // Inicializar eventos del sidebar
                initSidebarEvents(sidebarComentarios, seguimientoId);
                
                // Inicializar el offcanvas y mostrarlo
                const bsOffcanvas = new bootstrap.Offcanvas(sidebarComentarios);
                bsOffcanvas.show();
                
                // Cargar comentarios
                window.cargarComentarios(seguimientoId);
                
                console.log('Sidebar cargado y mostrado correctamente');
            })
            .catch(error => {
                console.error('Error al cargar sidebar:', error);
                alert(`Error al cargar comentarios: ${error.message}`);
            })
            .finally(() => {
                // Restaurar botón
                if (btnComentarios) {
                    btnComentarios.innerHTML = originalContent;
                    btnComentarios.disabled = false;
                }
            });
        } else {
            console.log('Sidebar ya existe en el DOM, mostrando...');
            
            // Actualizar el ID del seguimiento
            sidebarComentarios.setAttribute('data-seguimiento-id', seguimientoId);
            
            // Inicializar el offcanvas y mostrarlo
            const bsOffcanvas = new bootstrap.Offcanvas(sidebarComentarios);
            bsOffcanvas.show();
            
            // Inicializar eventos si no se han inicializado
            initSidebarEvents(sidebarComentarios, seguimientoId);
            
            // Cargar comentarios
            window.cargarComentarios(seguimientoId);
        }
    } catch (error) {
        console.error('Error al abrir el sidebar:', error);
        alert('Ocurrió un error al abrir la ventana de comentarios.');
    }
};

// Definir funciones globales que necesitamos con un valor por defecto
window.resetearFormulario = function() { console.log('resetearFormulario no inicializado'); };
window.cargarComentarios = function() { console.log('cargarComentarios no inicializado'); };
window.mostrarAlerta = function(mensaje, tipo) { 
    console.log(`Alerta: ${mensaje} (${tipo})`);
    alert(mensaje);
};
window.ocultarAlerta = function() { console.log('ocultarAlerta no inicializado'); };

// Función para inicializar eventos del sidebar
function initSidebarEvents(sidebar, seguimientoId) {
    if (!sidebar) return;
    
    // Referencias a elementos del formulario
    const formComentario = document.getElementById('formComentario');
    const inputSeguimientoId = document.getElementById('seguimientoIdComentario');
    const inputComentarioId = document.getElementById('comentarioIdEdicion');
    const inputContenido = document.getElementById('contenidoComentario');
    const inputArchivo = document.getElementById('archivoComentario');
    const previewContenedor = document.getElementById('previewContenedor');
    const previewImagen = document.getElementById('previewImagen');
    const previewDocumento = document.getElementById('previewDocumento');
    const nombreArchivo = document.getElementById('nombreArchivo');
    const btnCancelarEdicion = document.getElementById('cancelarEdicion');
    const btnEnviarTexto = document.getElementById('btnEnviarTexto');
    
    // Eventos para el modal de imagen
    document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#imagenModal"]').forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const imgSrc = this.href || this.querySelector('img').src;
            const imagenModalSrc = document.getElementById('imagenModalSrc');
            const imagenModalDownload = document.getElementById('imagenModalDownload');
            
            if (imagenModalSrc) imagenModalSrc.src = imgSrc;
            if (imagenModalDownload) {
                imagenModalDownload.href = imgSrc;
                imagenModalDownload.download = imgSrc.split('/').pop();
            }
            
            const modal = document.getElementById('imagenModal');
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        });
    });
    
    // Establecer el ID del seguimiento en el formulario
    if (inputSeguimientoId) {
        inputSeguimientoId.value = seguimientoId;
    }
    
    // Manejar vista previa de archivos
    if (inputArchivo) {
        inputArchivo.addEventListener('change', function() {
            if (this.files && this.files[0] && previewContenedor && previewImagen && previewDocumento) {
                const file = this.files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(extension);
                
                previewContenedor.classList.remove('d-none');
                
                if (esImagen) {
                    // Vista previa de imagen
                    previewImagen.classList.remove('d-none');
                    previewDocumento.classList.add('d-none');
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgPreview = previewImagen.querySelector('img');
                        if (imgPreview) imgPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Vista previa de documento
                    previewImagen.classList.add('d-none');
                    previewDocumento.classList.remove('d-none');
                    if (nombreArchivo) nombreArchivo.textContent = file.name;
                }
            } else if (previewContenedor) {
                previewContenedor.classList.add('d-none');
            }
        });
    }
    
    // Eliminar archivo seleccionado
    const btnEliminarArchivo = document.getElementById('eliminarArchivo');
    if (btnEliminarArchivo && inputArchivo && previewContenedor) {
        btnEliminarArchivo.addEventListener('click', function() {
            inputArchivo.value = '';
            previewContenedor.classList.add('d-none');
        });
    }
    
    // Cancelar edición
    if (btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', function() {
            window.resetearFormulario();
        });
    }
    
    // Formulario de comentario
    if (formComentario) {
        formComentario.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const modoEdicion = inputComentarioId && inputComentarioId.value;
            let url;
            let method;
            
            if (modoEdicion) {
                url = `/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/comentarios/${inputComentarioId.value}`;
                method = 'POST';
                formData.append('_method', 'PUT'); // Para simular PUT
            } else {
                url = `/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/comentarios`;
                method = 'POST';
            }
            
            // Estado de carga del botón
            const btnSubmit = formComentario.querySelector('button[type="submit"]');
            const btnTextoOriginal = btnSubmit ? btnSubmit.innerHTML : '';
            
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
            }
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                return;
            }
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la respuesta: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.mostrarAlerta(data.message, 'success');
                    window.resetearFormulario();
                    window.cargarComentarios(seguimientoId);
                    
                    // Actualizar contador en el botón de comentarios
                    const badge = document.querySelector(`.comentarios-count-${seguimientoId}`);
                    if (badge && !modoEdicion) {
                        badge.textContent = parseInt(badge.textContent) + 1;
                    }
                } else {
                    window.mostrarAlerta(data.message || 'Error al procesar la solicitud', 'error');
                }
            })
            .catch(error => {
                console.error('Error al enviar comentario:', error);
                window.mostrarAlerta('Error al procesar la solicitud: ' + error.message, 'error');
            })
            .finally(() => {
                // Restaurar botón
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnTextoOriginal;
                }
            });
        });
    }
    
    // Búsqueda de comentarios
    const inputBuscar = document.getElementById('buscarComentario');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase();
            document.querySelectorAll('.comentario-item').forEach(comentario => {
                const contenido = comentario.querySelector('.comentario-contenido');
                const usuario = comentario.querySelector('.comentario-usuario');
                
                if (contenido && usuario) {
                    const contenidoTexto = contenido.textContent.toLowerCase();
                    const usuarioTexto = usuario.textContent.toLowerCase();
                    
                    comentario.style.display = (contenidoTexto.includes(textoBusqueda) || 
                                               usuarioTexto.includes(textoBusqueda)) ? 'block' : 'none';
                }
            });
        });
    }
    
    // Iniciar actualización automática
    let intervalActualizacion = null;
    
    function iniciarActualizacionAutomatica() {
        // Limpiar intervalo existente si hay uno
        if (intervalActualizacion) {
            clearInterval(intervalActualizacion);
        }
        
        // Actualizar cada 30 segundos
        intervalActualizacion = setInterval(() => {
            if (window.seguimientoActual) {
                window.cargarComentarios(window.seguimientoActual, true);
            }
        }, 30000);
    }
    
    function detenerActualizacionAutomatica() {
        if (intervalActualizacion) {
            clearInterval(intervalActualizacion);
            intervalActualizacion = null;
        }
    }
    
    // Eventos de mostrar/ocultar sidebar
    sidebar.addEventListener('shown.bs.offcanvas', function() {
        iniciarActualizacionAutomatica();
    });
    
    sidebar.addEventListener('hidden.bs.offcanvas', function() {
        detenerActualizacionAutomatica();
        window.resetearFormulario();
    });
    
    // Drag & drop de archivos
    if (formComentario && inputArchivo) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            formComentario.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            formComentario.addEventListener(eventName, () => {
                formComentario.classList.add('dragover');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            formComentario.addEventListener(eventName, () => {
                formComentario.classList.remove('dragover');
            }, false);
        });
        
        formComentario.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                inputArchivo.files = dt.files;
                // Disparar evento change manualmente
                const event = new Event('change');
                inputArchivo.dispatchEvent(event);
            }
        }, false);
    }
}

// Script principal cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('Inicializando componente de comentarios y seguimiento...');
        
        // Verificar dependencias necesarias
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado correctamente');
            // Cargar Bootstrap dinámicamente si es necesario
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js';
            document.head.appendChild(script);
            script.onload = function() {
                console.log('Bootstrap cargado dinámicamente');
            };
        }
        
        // Referencias a elementos del DOM
        const formSeguimiento = document.getElementById('formNuevoSeguimiento');
        const recordatorioCheckbox = document.getElementById('recordatorio');
        const recordatorioContainer = document.getElementById('recordatorioContainer');
        
        // Mostrar/ocultar campo de fecha de recordatorio
        if (recordatorioCheckbox && recordatorioContainer) {
            recordatorioCheckbox.addEventListener('change', function() {
                recordatorioContainer.style.display = this.checked ? 'block' : 'none';
            });
        }
        
        // Implementar funciones globales
        window.resetearFormulario = function() {
            const formComentario = document.getElementById('formComentario');
            const inputComentarioId = document.getElementById('comentarioIdEdicion');
            const btnCancelarEdicion = document.getElementById('cancelarEdicion');
            const btnEnviarTexto = document.getElementById('btnEnviarTexto');
            const previewContenedor = document.getElementById('previewContenedor');
            
            if (formComentario) formComentario.reset();
            if (inputComentarioId) inputComentarioId.value = '';
            if (btnCancelarEdicion) btnCancelarEdicion.classList.add('d-none');
            if (btnEnviarTexto) btnEnviarTexto.textContent = 'Enviar';
            if (previewContenedor) previewContenedor.classList.add('d-none');
            
            // Quitar marcado de edición
            document.querySelectorAll('.comentario-item').forEach(item => {
                item.classList.remove('editando');
            });
        };
        
        window.mostrarAlerta = function(mensaje, tipo) {
            console.log(`Alerta: ${mensaje} (${tipo})`);
            
            const alerta = document.getElementById('sidebarAlerta');
            const alertaMensaje = document.getElementById('alertaMensaje');
            const alertaIcono = document.getElementById('alertaIcono');
            
            if (!alerta || !alertaMensaje || !alertaIcono) {
                // Fallback si no se encuentra el elemento de alerta
                alert(mensaje);
                return;
            }
            
            // Configurar clase y icono según el tipo
            alerta.className = 'alert m-2';
            
            if (tipo === 'success') {
                alerta.classList.add('alert-success');
                alertaIcono.className = 'fas fa-check-circle me-2';
            } else if (tipo === 'error' || tipo === 'danger') {
                alerta.classList.add('alert-danger');
                alertaIcono.className = 'fas fa-exclamation-circle me-2';
            } else if (tipo === 'info') {
                alerta.classList.add('alert-info');
                alertaIcono.className = 'fas fa-info-circle me-2';
            } else {
                alerta.classList.add('alert-warning');
                alertaIcono.className = 'fas fa-exclamation-triangle me-2';
            }
            
            // Mostrar mensaje
            alertaMensaje.textContent = mensaje;
            alerta.classList.remove('d-none');
            
            // Auto ocultar después de 3 segundos
            setTimeout(window.ocultarAlerta, 3000);
        };
        
        window.ocultarAlerta = function() {
            const alerta = document.getElementById('sidebarAlerta');
            if (alerta) alerta.classList.add('d-none');
        };
        
        window.cargarComentarios = function(seguimientoId, silencioso = false) {
            if (!seguimientoId) {
                console.error('ID de seguimiento no proporcionado para cargar comentarios');
                return;
            }
            
            console.log(`Cargando comentarios para seguimiento #${seguimientoId}${silencioso ? ' (silencioso)' : ''}`);
            
            // Obtener referencias
            const comentariosList = document.querySelector('.comentarios-lista');
            const comentariosContador = document.querySelector('.comentarios-contador');
            const comentariosPlaceholder = document.querySelector('.comentarios-placeholder');
            
            if (!comentariosList) {
                console.error('Elemento .comentarios-lista no encontrado');
                return;
            }
            
            // Mostrar indicador de carga si no es silencioso
            if (!silencioso) {
                comentariosList.innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted small">Cargando comentarios...</p>
                    </div>
                `;
            }
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                if (!silencioso) {
                    window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                }
                return;
            }
            
            // Cargar comentarios del servidor
            fetch(`/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/comentarios`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la respuesta: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Comentarios cargados:', data);
                
                // Si no es silencioso, limpiar lista
                if (!silencioso) {
                    comentariosList.innerHTML = '';
                }
                
                // Actualizar detalles del seguimiento
                actualizarDetallesSeguimiento(data.seguimiento);
                
                // Mostrar comentarios
                if (data.comentarios && data.comentarios.length > 0) {
                    // Actualizar contador
                    if (comentariosContador) {
                        comentariosContador.textContent = data.comentarios.length;
                    }
                    
                    // Ocultar placeholder
                    if (comentariosPlaceholder) {
                        comentariosPlaceholder.style.display = 'none';
                    }
                    
                    // Si es silencioso, solo actualizamos los comentarios nuevos
                    if (silencioso) {
                        const comentariosExistentes = new Set();
                        document.querySelectorAll('.comentario-item').forEach(item => {
                            comentariosExistentes.add(Number(item.dataset.id));
                        });
                        
                        // Ordenar comentarios por fecha (más recientes primero)
                        const comentariosOrdenados = data.comentarios.sort((a, b) => {
                            return new Date(b.created_at) - new Date(a.created_at);
                        });
                        
                        // Agregar solo los comentarios nuevos
                        let hayNuevos = false;
                        comentariosOrdenados.forEach(comentario => {
                            if (!comentariosExistentes.has(Number(comentario.id))) {
                                const nuevoComentario = crearElementoComentario(comentario);
                                comentariosList.prepend(nuevoComentario);
                                hayNuevos = true;
                                
                                // Agregar animación para destacar
                                setTimeout(() => {
                                    nuevoComentario.classList.add('highlight-new');
                                }, 100);
                                setTimeout(() => {
                                    nuevoComentario.classList.remove('highlight-new');
                                }, 3000);
                            }
                        });
                        
                        if (hayNuevos) {
                            window.mostrarAlerta('Se han agregado nuevos comentarios', 'info');
                        }
                    } else {
                        // Si no es silencioso, recreamos toda la lista
                        comentariosList.innerHTML = '';
                        
                        // Ordenar comentarios por fecha (más recientes primero)
                        const comentariosOrdenados = data.comentarios.sort((a, b) => {
                            return new Date(b.created_at) - new Date(a.created_at);
                        });
                        
                        // Crear elementos de comentario
                        comentariosOrdenados.forEach(comentario => {
                            const comentarioElement = crearElementoComentario(comentario);
                            comentariosList.appendChild(comentarioElement);
                        });
                    }
                    
                    // Inicializar botones de editar/eliminar
                    inicializarBotonesComentarios();
                    
                    // Actualizar contador en el botón de comentarios
                    const contador = document.querySelector(`.comentarios-count-${seguimientoId}`);
                    if (contador) {
                        contador.textContent = data.comentarios.length;
                    }
                } else {
                    // No hay comentarios
                    if (!silencioso) {
                        comentariosList.innerHTML = `
                            <div class="text-center py-5 text-muted comentarios-placeholder">
                                <div class="d-inline-block p-3 bg-light rounded-circle mb-3">
                                    <i class="fas fa-comments fa-2x opacity-50"></i>
                                </div>
                                <p class="mb-0">No hay comentarios para este seguimiento</p>
                                <small class="d-block mt-2">Sé el primero en comentar</small>
                            </div>
                        `;
                    }
                    
                    // Actualizar contador
                    if (comentariosContador) {
                        comentariosContador.textContent = '0';
                    }
                    
                    // Actualizar contador en el botón
                    const contador = document.querySelector(`.comentarios-count-${seguimientoId}`);
                    if (contador) {
                        contador.textContent = '0';
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar comentarios:', error);
                if (!silencioso) {
                    comentariosList.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error al cargar comentarios: ${error.message}
                        </div>
                    `;
                    window.mostrarAlerta('Error al cargar comentarios: ' + error.message, 'error');
                }
            });
        };
        
        // Funciones auxiliares para comentarios
        function actualizarDetallesSeguimiento(seguimiento) {
            if (!seguimiento) return;
            
            // Referencias a elementos
            const tipoElement = document.querySelector('.seguimiento-tipo-badge');
            const contenidoElement = document.querySelector('.seguimiento-contenido');
            const usuarioElement = document.querySelector('.seguimiento-usuario');
            const fechaElement = document.querySelector('.seguimiento-fecha span');
            const iconoElement = document.querySelector('.seguimiento-icono i');
            const iconoContenedor = document.querySelector('.seguimiento-icono');
            const recordatorioContainer = document.querySelector('.seguimiento-recordatorio');
            const recordatorioFecha = document.querySelector('.recordatorio-fecha');
            const recordatorioEstado = document.querySelector('.recordatorio-estado');
            const realizadoEstado = document.querySelector('.seguimiento-realizado');
            const pendienteEstado = document.querySelector('.seguimiento-pendiente');
            
            // Verificar que los elementos existan
            if (!tipoElement || !contenidoElement || !usuarioElement) return;
            
            // Obtener clases según el tipo
            let badgeClass = '';
            let iconoClass = '';
            let bgColor = '';
            
            switch (seguimiento.tipo) {
                case 'nota':
                    badgeClass = 'bg-warning-subtle text-warning';
                    iconoClass = 'fa-sticky-note';
                    bgColor = '#ffc107';
                    break;
                case 'llamada':
                    badgeClass = 'bg-success-subtle text-success';
                    iconoClass = 'fa-phone-alt';
                    bgColor = '#198754';
                    break;
                case 'reunion':
                    badgeClass = 'bg-primary-subtle text-primary';
                    iconoClass = 'fa-handshake';
                    bgColor = '#0d6efd';
                    break;
                case 'email':
                    badgeClass = 'bg-info-subtle text-info';
                    iconoClass = 'fa-envelope';
                    bgColor = '#0dcaf0';
                    break;
                default:
                    badgeClass = 'bg-secondary-subtle text-secondary';
                    iconoClass = 'fa-comment';
                    bgColor = '#6c757d';
            }
            
            // Actualizar elementos
            tipoElement.className = `badge seguimiento-tipo-badge rounded-pill px-3 py-1 ${badgeClass}`;
            tipoElement.textContent = seguimiento.tipo.charAt(0).toUpperCase() + seguimiento.tipo.slice(1);
            contenidoElement.textContent = seguimiento.contenido;
            usuarioElement.textContent = seguimiento.usuario ? seguimiento.usuario.name : 'Usuario no especificado';
            
            // Actualizar icono
            if (iconoElement && iconoContenedor) {
                iconoElement.className = `fas fa-lg ${iconoClass}`;
                iconoContenedor.style.backgroundColor = bgColor;
            }
            
            // Formatear fecha
            if (fechaElement && seguimiento.fecha_seguimiento) {
                try {
                    const fecha = new Date(seguimiento.fecha_seguimiento);
                    fechaElement.textContent = fecha.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    fechaElement.textContent = seguimiento.fecha_seguimiento;
                }
            }
            
            // Actualizar estado realizado/pendiente
            if (realizadoEstado && pendienteEstado) {
                if (seguimiento.realizado) {
                    realizadoEstado.classList.remove('d-none');
                    pendienteEstado.classList.add('d-none');
                } else {
                    realizadoEstado.classList.add('d-none');
                    pendienteEstado.classList.remove('d-none');
                }
            }
            
            // Actualizar recordatorio
            if (recordatorioContainer && recordatorioFecha && recordatorioEstado) {
                if (seguimiento.recordatorio && seguimiento.fecha_recordatorio) {
                    recordatorioContainer.classList.remove('d-none');
                    
                    try {
                        const fechaRecordatorio = new Date(seguimiento.fecha_recordatorio);
                        recordatorioFecha.textContent = fechaRecordatorio.toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        // Verificar si está vencido
                        if (fechaRecordatorio < new Date()) {
                            recordatorioEstado.className = 'badge bg-danger ms-1';
                            recordatorioEstado.textContent = 'Vencido';
                        } else {
                            recordatorioEstado.className = 'badge bg-success ms-1';
                            recordatorioEstado.textContent = 'Pendiente';
                        }
                    } catch (e) {
                        recordatorioFecha.textContent = seguimiento.fecha_recordatorio;
                    }
                } else {
                    recordatorioContainer.classList.add('d-none');
                }
            }
        }
        
        function crearElementoComentario(comentario) {
            // Obtener template
            const template = document.getElementById('comentario-template');
            if (!template) {
                console.error('Template de comentario no encontrado');
                return document.createElement('div');
            }
            
            // Clonar template
            const element = template.content.cloneNode(true);
            const comentarioItem = element.querySelector('.comentario-item');
            
            if (!comentarioItem) {
                console.error('Elemento .comentario-item no encontrado en el template');
                return document.createElement('div');
            }
            
            // Establecer ID del comentario
            comentarioItem.dataset.id = comentario.id;
            
            // Usuario y avatar
            const usuario = comentarioItem.querySelector('.comentario-usuario');
            const inicial = comentarioItem.querySelector('.initial');
            const avatarInicial = comentarioItem.querySelector('.avatar-inicial');
            
            if (usuario) usuario.textContent = comentario.usuario ? comentario.usuario.name : 'Usuario';
            if (inicial && comentario.usuario) inicial.textContent = comentario.usuario.name.charAt(0).toUpperCase();
            
            // Color aleatorio para el avatar basado en el nombre
            if (avatarInicial && comentario.usuario) {
                // Generar color basado en el nombre
                const colorIndex = comentario.usuario.name.charCodeAt(0) % 5;
                const colors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1']; // blue, green, red, yellow, purple
                avatarInicial.style.backgroundColor = colors[colorIndex];
            }
            
            // Fecha
            const fecha = comentarioItem.querySelector('.comentario-fecha');
            if (fecha && comentario.created_at) {
                try {
                    const fechaComentario = new Date(comentario.created_at);
                    fecha.textContent = fechaComentario.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    fecha.textContent = comentario.created_at;
                }
            }
            
            // Contenido
            const contenido = comentarioItem.querySelector('.comentario-contenido');
            if (contenido) contenido.textContent = comentario.contenido;
            
            // Archivo adjunto (imagen o documento)
            if (comentario.archivo) {
                const rutaArchivo = comentario.ruta_archivo || `/storage/${comentario.archivo}`;
                const nombreOriginal = comentario.nombre_archivo || comentario.archivo.split('/').pop();
                const extension = comentario.extension_archivo || nombreOriginal.split('.').pop().toLowerCase();
                
                // Determinar si es imagen
                const esImagen = comentario.es_imagen || ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(extension);
                
                if (esImagen) {
                    // Mostrar imagen
                    const imagenContainer = comentarioItem.querySelector('.comentario-imagen');
                    if (imagenContainer) {
                        const img = imagenContainer.querySelector('img');
                        const enlace = imagenContainer.querySelector('a');
                        
                        if (img) img.src = rutaArchivo;
                        if (enlace) enlace.href = rutaArchivo;
                        
                        imagenContainer.classList.remove('d-none');
                    }
                } else {
                    // Mostrar documento
                    const docContainer = comentarioItem.querySelector('.comentario-documento');
                    if (docContainer) {
                        const enlace = docContainer.querySelector('a');
                        const nombreElement = docContainer.querySelector('.comentario-archivo-nombre');
                        const icono = docContainer.querySelector('.archivo-icono');
                        
                        if (enlace) {
                            enlace.href = rutaArchivo;
                            enlace.download = nombreOriginal;
                        }
                        
                        if (nombreElement) nombreElement.textContent = nombreOriginal;
                        
                        // Icono según extensión
                        if (icono) {
                            let iconoClass = 'fa-file';
                            
                            switch (extension) {
                                case 'pdf':
                                    iconoClass = 'fa-file-pdf text-danger';
                                    break;
                                case 'doc':
                                case 'docx':
                                    iconoClass = 'fa-file-word text-primary';
                                    break;
                                case 'xls':
                                case 'xlsx':
                                    iconoClass = 'fa-file-excel text-success';
                                    break;
                                case 'ppt':
                                case 'pptx':
                                    iconoClass = 'fa-file-powerpoint text-warning';
                                    break;
                                case 'zip':
                                case 'rar':
                                case '7z':
                                    iconoClass = 'fa-file-archive text-secondary';
                                    break;
                                default:
                                    iconoClass = 'fa-file-alt text-info';
                            }
                            
                            icono.className = `fas ${iconoClass} me-2`;
                        }
                        
                        docContainer.classList.remove('d-none');
                    }
                }
            }
            
            // Mostrar acciones solo si es el autor
            const accionesContainer = comentarioItem.querySelector('.comentario-acciones');
            if (accionesContainer) {
                // Obtener ID del usuario actual
                // En lugar de usar Auth::id() directamente en JS, usamos un meta tag o data attribute
                const usuarioActualId = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || 
                                        document.querySelector('[data-user-id]')?.dataset.userId;
                
                if (comentario.user_id != usuarioActualId) {
                    accionesContainer.style.display = 'none';
                }
            }
            
            return comentarioItem;
        }
        
        function inicializarBotonesComentarios() {
            // Botones para editar comentario
            document.querySelectorAll('.btn-editar-comentario').forEach(btn => {
                btn.addEventListener('click', function() {
                    const comentarioItem = this.closest('.comentario-item');
                    const comentarioId = comentarioItem.dataset.id;
                    const contenido = comentarioItem.querySelector('.comentario-contenido').textContent;
                    
                    // Obtener referencias
                    const inputComentarioId = document.getElementById('comentarioIdEdicion');
                    const inputContenido = document.getElementById('contenidoComentario');
                    const btnCancelarEdicion = document.getElementById('cancelarEdicion');
                    const btnEnviarTexto = document.getElementById('btnEnviarTexto');
                    
                    // Establecer modo edición
                    if (inputComentarioId) inputComentarioId.value = comentarioId;
                    if (inputContenido) inputContenido.value = contenido;
                    if (btnCancelarEdicion) btnCancelarEdicion.classList.remove('d-none');
                    if (btnEnviarTexto) btnEnviarTexto.textContent = 'Actualizar';
                    
                    // Marcar visualmente el comentario en edición
                    document.querySelectorAll('.comentario-item').forEach(item => {
                        item.classList.remove('editando');
                    });
                    comentarioItem.classList.add('editando');
                    
                    // Enfocar el campo de texto
                    if (inputContenido) inputContenido.focus();
                });
            });
            
            // Botones para eliminar comentario
            document.querySelectorAll('.btn-eliminar-comentario').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!confirm('¿Estás seguro de que deseas eliminar este comentario?')) return;
                    
                    const comentarioItem = this.closest('.comentario-item');
                    const comentarioId = comentarioItem.dataset.id;
                    const seguimientoId = window.seguimientoActual;
                    
                    if (!seguimientoId || !comentarioId) {
                        console.error('ID de seguimiento o comentario no disponible');
                        return;
                    }
                    
                    // Obtener token CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('Token CSRF no encontrado');
                        window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                        return;
                    }
                    
                    // Mostrar estado de carga
                    comentarioItem.classList.add('eliminando');
                    const originalHTML = comentarioItem.innerHTML;
                    comentarioItem.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-danger" role="status"></div> <small class="text-muted">Eliminando...</small></div>';
                    
                    fetch(`/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/comentarios/${comentarioId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error en la respuesta: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.mostrarAlerta('Comentario eliminado correctamente', 'success');
                            
                            // Animar eliminación
                            comentarioItem.style.height = comentarioItem.offsetHeight + 'px';
                            comentarioItem.classList.add('fade-out');
                            
                            setTimeout(() => {
                                comentarioItem.style.height = '0';
                                comentarioItem.style.marginBottom = '0';
                                comentarioItem.style.paddingTop = '0';
                                comentarioItem.style.paddingBottom = '0';
                                comentarioItem.style.overflow = 'hidden';
                                
                                setTimeout(() => {
                                    comentarioItem.remove();
                                    
                                    // Actualizar contador
                                    const contador = document.querySelector('.comentarios-contador');
                                    const badge = document.querySelector(`.comentarios-count-${seguimientoId}`);
                                    
                                    if (contador) {
                                        const actual = parseInt(contador.textContent) - 1;
                                        contador.textContent = actual;
                                    }
                                    
                                    if (badge) {
                                        const actual = parseInt(badge.textContent) - 1;
                                        badge.textContent = actual;
                                    }
                                    
                                    // Mostrar placeholder si no hay más comentarios
                                    const comentariosLista = document.querySelector('.comentarios-lista');
                                    const comentariosPlaceholder = document.querySelector('.comentarios-placeholder');
                                    
                                    if (comentariosLista && comentariosPlaceholder && !comentariosLista.querySelector('.comentario-item')) {
                                        comentariosPlaceholder.style.display = 'block';
                                    }
                                }, 300);
                            }, 50);
                        } else {
                            // Restaurar elemento
                            comentarioItem.classList.remove('eliminando');
                            comentarioItem.innerHTML = originalHTML;
                            
                            window.mostrarAlerta(data.message || 'Error al eliminar el comentario', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error al eliminar comentario:', error);
                        
                        // Restaurar elemento
                        comentarioItem.classList.remove('eliminando');
                        comentarioItem.innerHTML = originalHTML;
                        
                        window.mostrarAlerta('Error al eliminar el comentario: ' + error.message, 'error');
                    });
                });
            });
        }
        
        // Manejar envío del formulario de seguimiento (sin recarga)
        if (formSeguimiento) {
            formSeguimiento.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const formErrors = document.getElementById('formErrors');
                
                if (formErrors) {
                    formErrors.style.display = 'none';
                    formErrors.innerHTML = '';
                }
                
                // Validación básica del formulario
                const contenido = formData.get('contenido');
                const recordatorio = formData.get('recordatorio');
                const fechaRecordatorio = formData.get('fecha_recordatorio');
                
                if (!contenido || !contenido.trim()) {
                    if (formErrors) {
                        formErrors.style.display = 'block';
                        formErrors.innerHTML = 'El contenido del seguimiento es obligatorio';
                    }
                    return;
                }
                
                if (recordatorio === '1' && !fechaRecordatorio) {
                    if (formErrors) {
                        formErrors.style.display = 'block';
                        formErrors.innerHTML = 'Debe seleccionar una fecha para el recordatorio';
                    }
                    return;
                }
                
                // Mostrar indicador de carga
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
                }
                
                // Obtener token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('Token CSRF no encontrado');
                    if (formErrors) {
                        formErrors.style.display = 'block';
                        formErrors.innerHTML = 'Error de seguridad. Por favor, recarga la página.';
                    }
                    return;
                }
                
                // Enviar formulario
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error en la respuesta: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        const mensaje = document.createElement('div');
                        mensaje.className = 'alert alert-success alert-dismissible fade show';
                        mensaje.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        
                        // Insertar mensaje antes del formulario
                        formSeguimiento.parentNode.insertBefore(mensaje, formSeguimiento);
                        
                        // Auto ocultar después de 3 segundos
                        setTimeout(() => {
                            mensaje.classList.remove('show');
                            setTimeout(() => mensaje.remove(), 150);
                        }, 3000);
                        
                        // Resetear formulario
                        formSeguimiento.reset();
                        if (recordatorioContainer) recordatorioContainer.style.display = 'none';
                        
                        // Agregar el nuevo seguimiento a la lista sin recargar
                        agregarNuevoSeguimiento(data.seguimiento);
                    } else {
                        // Mostrar errores
                        if (formErrors) {
                            formErrors.style.display = 'block';
                            formErrors.innerHTML = data.message || 'No se pudo registrar el seguimiento';
                            
                            if (data.errors) {
                                for (const [key, messages] of Object.entries(data.errors)) {
                                    formErrors.innerHTML += `<p>${messages.join(', ')}</p>`;
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al enviar formulario:', error);
                    
                    if (formErrors) {
                        formErrors.style.display = 'block';
                        formErrors.innerHTML = 'Error en la comunicación con el servidor: ' + error.message;
                    }
                })
                .finally(() => {
                    // Restaurar botón
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
            });
        }
        
        // Función para agregar un nuevo seguimiento a la lista sin recargar
        function agregarNuevoSeguimiento(seguimiento) {
            if (!seguimiento) return;
            
            const seguimientosContainer = document.querySelector('.timeline');
            const noSeguimientosContainer = document.querySelector('.text-center.py-5');
            
            if (!seguimientosContainer) return;
            
            // Si no hay seguimientos, quitar el mensaje de "no hay seguimientos"
            if (noSeguimientosContainer) {
                noSeguimientosContainer.remove();
            }
            
            // Crear elemento HTML para el nuevo seguimiento
            const nuevoSeguimiento = document.createElement('div');
            nuevoSeguimiento.className = 'timeline-item mb-3 new-item';
            
            // Generar color y icono según tipo
            let badgeClass = '';
            let iconoClass = '';
            
            switch (seguimiento.tipo) {
                case 'nota':
                    badgeClass = 'bg-warning';
                    iconoClass = 'fa-sticky-note';
                    break;
                case 'llamada':
                    badgeClass = 'bg-success';
                    iconoClass = 'fa-phone-alt';
                    break;
                case 'reunion':
                    badgeClass = 'bg-primary';
                    iconoClass = 'fa-handshake';
                    break;
                case 'email':
                    badgeClass = 'bg-info';
                    iconoClass = 'fa-envelope';
                    break;
                default:
                    badgeClass = 'bg-secondary';
                    iconoClass = 'fa-comment';
            }
            
            // Generar HTML del nuevo seguimiento
            nuevoSeguimiento.innerHTML = `
                <div class="timeline-badge ${badgeClass}">
                    <i class="fas ${iconoClass}"></i>
                </div>
                <div class="timeline-panel card border-0 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-${badgeClass.replace('bg-', '')}-subtle text-${badgeClass.replace('bg-', '')} rounded-pill text-capitalize small">
                            ${seguimiento.tipo}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">
                                <i class="far fa-user me-1"></i>${seguimiento.usuario ? seguimiento.usuario.name : 'N/A'}
                            </span>
                            <span class="text-muted small">
                                <i class="far fa-clock me-1"></i>${formatDate(seguimiento.fecha_seguimiento)}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <p class="mb-0 small">${seguimiento.contenido}</p>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" 
                                    data-seguimiento-id="${seguimiento.id}"
                                    onclick="window.abrirSidebarComentarios(${seguimiento.id})">
                                <i class="fas fa-comments"></i>
                                <span class="badge bg-secondary comentarios-count-${seguimiento.id}">
                                    0
                                </span>
                            </button>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-realizado" type="checkbox" 
                                       id="realizado-${seguimiento.id}" 
                                       data-id="${seguimiento.id}"
                                       ${seguimiento.realizado ? 'checked' : ''}>
                                <label class="form-check-label small" for="realizado-${seguimiento.id}">
                                    ${seguimiento.realizado ? 'Realizado' : 'Pendiente'}
                                </label>
                            </div>
                        </div>
                    </div>
                    ${seguimiento.recordatorio ? `
                        <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-bell"></i>
                            </span>
                            <small class="text-muted">
                                Recordatorio: ${formatDate(seguimiento.fecha_recordatorio)}
                                <span class="badge 
                                    ${new Date(seguimiento.fecha_recordatorio) < new Date() ? 'bg-danger' : 'bg-success'} ms-1">
                                    ${new Date(seguimiento.fecha_recordatorio) < new Date() ? 'Vencido' : 'Pendiente'}
                                </span>
                            </small>
                        </div>
                    ` : ''}
                </div>
            `;
            
            // Insertar al inicio de la lista
            if (seguimientosContainer.firstChild) {
                seguimientosContainer.insertBefore(nuevoSeguimiento, seguimientosContainer.firstChild);
            } else {
                seguimientosContainer.appendChild(nuevoSeguimiento);
            }
            
            // Animar entrada
            setTimeout(() => {
                nuevoSeguimiento.classList.add('highlight');
                setTimeout(() => nuevoSeguimiento.classList.remove('highlight'), 2000);
            }, 100);
            
            // Inicializar eventos del nuevo seguimiento
            const toggleBtn = nuevoSeguimiento.querySelector('.toggle-realizado');
            if (toggleBtn) {
                toggleBtn.addEventListener('change', handleToggleRealizado);
            }
        }
        
        // Helper para formatear fechas
        function formatDate(dateString) {
            if (!dateString) return '';
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateString;
            }
        }
        
        // Manejar cambio de estado realizado/pendiente
        function handleToggleRealizado() {
            const seguimientoId = this.dataset.id;
            const isChecked = this.checked;
            const label = document.querySelector(`label[for="realizado-${seguimientoId}"]`);
            
            if (!seguimientoId) return;
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                alert('Error de seguridad. Por favor, recarga la página.');
                return;
            }
            
            // Cambiar visualmente mientras se procesa
            if (label) label.textContent = isChecked ? 'Actualizando...' : 'Actualizando...';
            
            fetch(`/admin/mantenimiento/ordenes/seguimientos/${seguimientoId}/toggle-realizado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ realizado: isChecked })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la respuesta: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Actualizar etiqueta
                    if (label) label.textContent = isChecked ? 'Realizado' : 'Pendiente';
                    
                    // Mostrar mensaje flotante
                    const mensaje = document.createElement('div');
                    mensaje.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3';
                    mensaje.setAttribute('role', 'alert');
                    mensaje.setAttribute('aria-live', 'assertive');
                    mensaje.setAttribute('aria-atomic', 'true');
                    mensaje.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i> ${data.message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    `;
                    document.body.appendChild(mensaje);
                    
                    // Mostrar toast
                    const bsToast = new bootstrap.Toast(mensaje, { delay: 3000 });
                    bsToast.show();
                    
                    // Eliminar después de ocultarse
                    mensaje.addEventListener('hidden.bs.toast', () => mensaje.remove());
                    
                    // Si el sidebar está abierto y corresponde al mismo seguimiento, actualizar
                    const sidebar = document.getElementById('sidebarComentarios');
                    if (sidebar && sidebar.getAttribute('data-seguimiento-id') === seguimientoId) {
                        const realizadoBadge = sidebar.querySelector('.seguimiento-realizado');
                        const pendienteBadge = sidebar.querySelector('.seguimiento-pendiente');
                        
                        if (realizadoBadge && pendienteBadge) {
                            if (isChecked) {
                                realizadoBadge.classList.remove('d-none');
                                pendienteBadge.classList.add('d-none');
                            } else {
                                realizadoBadge.classList.add('d-none');
                                pendienteBadge.classList.remove('d-none');
                            }
                        }
                    }
                } else {
                    // Revertir cambio
                    this.checked = !isChecked;
                    if (label) label.textContent = !isChecked ? 'Realizado' : 'Pendiente';
                    
                    alert(data.message || 'Error al actualizar el estado');
                }
            })
            .catch(error => {
                console.error('Error al cambiar estado:', error);
                
                // Revertir cambio
                this.checked = !isChecked;
                if (label) label.textContent = !isChecked ? 'Realizado' : 'Pendiente';
                
                alert('Error al actualizar el estado: ' + error.message);
            });
        }
        
        // Inicializar eventos para botones toggle
        document.querySelectorAll('.toggle-realizado').forEach(checkbox => {
            checkbox.removeEventListener('change', handleToggleRealizado);
            checkbox.addEventListener('change', handleToggleRealizado);
        });

        // Inicializar eventos para botones de comentarios
        document.querySelectorAll('[data-seguimiento-id]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const seguimientoId = this.getAttribute('data-seguimiento-id');
                if (seguimientoId) {
                    window.abrirSidebarComentarios(seguimientoId);
                }
            });
        });

        console.log('Componente de comentarios y seguimiento inicializado correctamente');
    } catch (error) {
        console.error('Error general al inicializar el componente:', error);
    }

    // Añadir estilos CSS dinámicamente para las animaciones y efectos
    const dynamicStyles = document.createElement('style');
    dynamicStyles.textContent = `
        .timeline-item.highlight {
            animation: highlight-new-item 2s ease-in-out;
        }
        
        .comentario-item.highlight-new {
            animation: highlight-new-comentario 2s ease-in-out;
        }
        
        .comentario-item.editando {
            background-color: rgba(13, 110, 253, 0.05);
            border-left: 3px solid #0d6efd;
        }
        
        .comentario-item.fade-out {
            opacity: 0;
            transition: opacity 0.3s, height 0.3s, margin 0.3s, padding 0.3s;
        }
        
        .dragover {
            border: 2px dashed #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        @keyframes highlight-new-item {
            0% { background-color: rgba(13, 110, 253, 0.2); }
            100% { background-color: transparent; }
        }
        
        @keyframes highlight-new-comentario {
            0% { background-color: rgba(13, 110, 253, 0.2); }
            100% { background-color: transparent; }
        }
    `;
    document.head.appendChild(dynamicStyles);
});