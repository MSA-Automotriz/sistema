<!-- Timeline de seguimientos -->
<div class="timeline-container">
    @if($cotizacion->seguimientos && $cotizacion->seguimientos->count() > 0)
    <div class="modern-timeline p-3">
        @foreach($cotizacion->seguimientos->sortByDesc('fecha_seguimiento') as $seguimiento)
        <div class="modern-timeline-item mb-3" data-seguimiento-id="{{ $seguimiento->id }}">
            <div class="modern-timeline-badge 
                @if($seguimiento->tipo === 'nota') bg-warning
                @elseif($seguimiento->tipo === 'llamada') bg-success
                @elseif($seguimiento->tipo === 'reunion') bg-primary
                @elseif($seguimiento->tipo === 'email') bg-info
                @else bg-secondary
                @endif">
                @if($seguimiento->tipo === 'nota')
                <i class="fas fa-sticky-note"></i>
                @elseif($seguimiento->tipo === 'llamada')
                <i class="fas fa-phone-alt"></i>
                @elseif($seguimiento->tipo === 'reunion')
                <i class="fas fa-handshake"></i>
                @elseif($seguimiento->tipo === 'email')
                <i class="fas fa-envelope"></i>
                @else
                <i class="fas fa-comment"></i>
                @endif
            </div>
            <div class="modern-timeline-panel">
                <div class="modern-timeline-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge 
                            @if($seguimiento->tipo === 'nota') bg-warning-subtle text-warning
                            @elseif($seguimiento->tipo === 'llamada') bg-success-subtle text-success
                            @elseif($seguimiento->tipo === 'reunion') bg-primary-subtle text-primary
                            @elseif($seguimiento->tipo === 'email') bg-info-subtle text-info
                            @else bg-secondary-subtle text-secondary
                            @endif rounded-pill text-capitalize">
                            {{ $seguimiento->tipo }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted small me-2">
                            <i class="far fa-user me-1 opacity-50"></i>{{ $seguimiento->usuario?->name ?? 'Usuario no especificado' }}
                        </span>
                        <span class="text-muted small">
                            <i class="far fa-clock me-1 opacity-50"></i>{{ $seguimiento->fecha_seguimiento ? $seguimiento->fecha_seguimiento->format('d/m/y H:i') : '' }}
                        </span>
                    </div>
                </div>
                <div class="modern-timeline-body mt-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <p class="mb-0">{{ $seguimiento->contenido }}</p>
                        
                        <div class="d-flex align-items-center">
                            <!-- Botón para abrir el sidebar de comentarios -->
                            <button class="btn btn-sm btn-outline-primary me-2" 
                                    data-seguimiento-id="{{ $seguimiento->id }}"
                                    onclick="abrirSidebarComentarios({{ $seguimiento->id }})">
                                <i class="fas fa-comments"></i> 
                                <span class="badge bg-secondary comentarios-count-{{ $seguimiento->id }}">
                                    {{ $seguimiento->comentarios->count() }}
                                </span>
                            </button>
                            
                            <!-- Switch de realizado -->
                            <div class="form-check form-switch ms-2">
                                <input class="form-check-input toggle-realizado" type="checkbox" 
                                       id="realizado-{{ $seguimiento->id }}" 
                                       data-id="{{ $seguimiento->id }}"
                                       {{ $seguimiento->realizado ? 'checked' : '' }}>
                                <label class="form-check-label small" for="realizado-{{ $seguimiento->id }}">
                                    {{ $seguimiento->realizado ? 'Realizado' : 'Pendiente' }}
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    @if($seguimiento->recordatorio)
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning text-dark me-2">
                                <i class="fas fa-bell"></i>
                            </span>
                            <small class="text-muted">
                                Recordatorio: {{ $seguimiento->fecha_recordatorio->format('d/m/Y H:i') }}
                                @if($seguimiento->fecha_recordatorio->isPast())
                                <span class="badge bg-danger ms-1">Vencido</span>
                                @else
                                <span class="badge bg-success ms-1">Pendiente</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="d-flex flex-column align-items-center justify-content-center py-5">
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTYgMkg4QzcuNDQ3NzIgMiA3IDIuNDQ3NzIgNyAzVjE2QzcgMTYuNTUyMyA3LjQ0NzcyIDE3IDggMTdIMjBDMjAuNTUyMyAxNyAyMSAxNi41NTIzIDIxIDE2VjdDMjEgNi40NDc3MiAyMC41NTIzIDYgMjAgNkgxN1YzQzE3IDIuNDQ3NzIgMTYuNTUyMyAyIDE2IDJaIiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0xNiA2LjAxMDM3TDIxIDYuMDEwMzciIHN0cm9rZT0iIzk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHBhdGggZD0iTTMgOEgxN1YyMUgzVjhaIiBmaWxsPSIjRTdFN0U3IiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==" 
            width="80" height="80" alt="No hay seguimientos" class="opacity-50 mb-3">
        <p class="text-muted mb-0">No hay seguimientos registrados</p>
    </div>
    @endif
</div>

<!-- Sidebar moderno para comentarios -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarComentarios" aria-labelledby="sidebarComentariosLabel">
    <!-- Header del sidebar -->
    <div class="offcanvas-header border-bottom py-3">
        <div>
            <h5 class="offcanvas-title mb-0 d-flex align-items-center" id="sidebarComentariosLabel">
                <i class="fas fa-comment-dots me-2"></i>
                Detalles del seguimiento
            </h5>
            <small class="text-muted seguimiento-fecha d-inline-block mt-1">
                <i class="far fa-calendar-alt me-1"></i>
                <span>Fecha</span>
            </small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <!-- Sistema de alertas integrado -->
    <div id="sidebarAlerta" class="alert m-2 d-none">
        <div class="d-flex align-items-center">
            <i id="alertaIcono" class="fas me-2"></i>
            <span id="alertaMensaje"></span>
            <button type="button" class="btn-close btn-sm ms-auto" onclick="ocultarAlerta()"></button>
        </div>
    </div>
    
    <div class="offcanvas-body d-flex flex-column p-0">
        <!-- Detalles del seguimiento principal -->
        <div class="p-3 border-bottom bg-light">
            <div class="d-flex align-items-start mb-2">
                <div class="seguimiento-icono rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0 text-white" style="width: 40px; height: 40px;">
                    <i class="fas fa-lg"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge seguimiento-tipo-badge rounded-pill px-3 py-1">Tipo</span>
                        <small class="text-muted">
                            <i class="far fa-user me-1"></i>
                            <span class="seguimiento-usuario">Usuario</span>
                        </small>
                    </div>
                    <p class="seguimiento-contenido mb-0 text-dark">Contenido del seguimiento...</p>
                    
                    <!-- Estado de realización -->
                    <div class="mt-2">
                        <span class="badge seguimiento-realizado bg-success-subtle text-success d-none">
                            <i class="fas fa-check me-1"></i> Realizado
                        </span>
                        <span class="badge seguimiento-pendiente bg-warning-subtle text-warning d-none">
                            <i class="fas fa-clock me-1"></i> Pendiente
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Recordatorio si existe -->
            <div class="seguimiento-recordatorio d-none mt-2 pt-2 border-top">
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning text-dark me-2">
                        <i class="fas fa-bell"></i>
                    </span>
                    <small class="text-muted">
                        Recordatorio: <span class="recordatorio-fecha">00/00/0000</span>
                        <span class="badge recordatorio-estado ms-1">Estado</span>
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Buscador y contador de comentarios -->
        <div class="px-3 py-2 border-bottom d-flex align-items-center">
            <div class="input-group input-group-sm me-2 flex-grow-1">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 shadow-none ps-0" id="buscarComentario" placeholder="Buscar en comentarios...">
            </div>
            <div class="badge bg-primary rounded-pill comentarios-contador">0</div>
        </div>
        
        <!-- Lista de comentarios estilo chat -->
        <div class="comentarios-lista flex-grow-1 overflow-auto p-3">
            <!-- Los comentarios se cargarán dinámicamente aquí -->
            <div class="text-center py-5 text-muted comentarios-placeholder">
                <div class="d-inline-block p-3 bg-light rounded-circle mb-3">
                    <i class="fas fa-comments fa-2x opacity-50"></i>
                </div>
                <p class="mb-0">No hay comentarios para este seguimiento</p>
                <small class="d-block mt-2">Sé el primero en comentar</small>
            </div>
        </div>
        
        <!-- Formulario para añadir comentario -->
        <div class="border-top p-3 bg-light">
            <form id="formComentario" enctype="multipart/form-data" class="comment-form">
                <input type="hidden" id="seguimientoIdComentario" name="seguimiento_id">
                <input type="hidden" id="comentarioIdEdicion" name="comentario_id">
                
                <div class="mb-2">
                    <textarea class="form-control" id="contenidoComentario" name="contenido" rows="2" 
                              placeholder="Escribe un comentario..." required></textarea>
                </div>
                
                <!-- Vista previa de imágenes y archivos -->
                <div id="previewContenedor" class="mb-2 d-none">
                    <div class="rounded border p-2 bg-white">
                        <!-- Previsualización de imagen -->
                        <div id="previewImagen" class="text-center mb-2 d-none">
                            <img src="" class="img-preview img-fluid rounded" style="max-height: 150px;">
                        </div>
                        
                        <!-- Previsualización de documento -->
                        <div id="previewDocumento" class="d-none">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <i class="fas fa-file me-2 text-primary"></i>
                                <span id="nombreArchivo" class="text-truncate"></span>
                                <button type="button" class="btn btn-sm text-danger ms-auto" id="eliminarArchivo">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <!-- Botón para adjuntar archivo -->
                        <div class="file-upload position-relative">
                            <input class="d-none" type="file" id="archivoComentario" name="archivo">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('archivoComentario').click()">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                        
                        <!-- Modo edición -->
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2 d-none" id="cancelarEdicion">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-paper-plane me-1"></i> 
                        <span id="btnEnviarTexto">Enviar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plantilla para un comentario (oculta) -->
<template id="comentario-template">
    <div class="chat-message mb-3 comentario-item" data-id="">
        <div class="d-flex">
            <!-- Avatar/Inicial del usuario -->
            <div class="avatar-inicial rounded-circle d-flex align-items-center justify-content-center text-white me-2 flex-shrink-0" 
                 style="width: 32px; height: 32px; background-color: var(--bs-primary);">
                <span class="initial"></span>
            </div>
            
            <!-- Contenido del comentario -->
            <div class="flex-grow-1">
                <div class="chat-bubble rounded p-2 bg-white border">
                    <!-- Cabecera del comentario -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-medium comentario-usuario">Usuario</span>
                            <small class="text-muted ms-2 comentario-fecha">Fecha</small>
                        </div>
                        <div class="dropdown comentario-acciones">
                            <button class="btn btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item btn-editar-comentario"><i class="fas fa-edit me-2"></i> Editar</button></li>
                                <li><button class="dropdown-item btn-eliminar-comentario text-danger"><i class="fas fa-trash me-2"></i> Eliminar</button></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Texto del comentario -->
                    <p class="comentario-contenido mb-2">Contenido del comentario...</p>
                    
                    <!-- Archivo adjunto: imagen -->
                    <div class="comentario-imagen mt-2 d-none">
                        <a href="#" class="d-block" data-bs-toggle="modal" data-bs-target="#imagenModal">
                            <img src="" class="img-fluid rounded" style="max-height: 150px;">
                        </a>
                    </div>
                    
                    <!-- Archivo adjunto: documento -->
                    <div class="comentario-documento mt-2 d-none">
                        <a href="#" class="d-flex align-items-center p-2 border rounded text-decoration-none" download>
                            <i class="archivo-icono fas me-2"></i>
                            <span class="comentario-archivo-nombre text-truncate">archivo.pdf</span>
                            <i class="fas fa-download ms-auto"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Modal para ver imágenes en tamaño completo -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">Vista previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="imagenModalSrc" class="img-fluid">
            </div>
            <div class="modal-footer py-1">
                <a href="#" class="btn btn-primary" id="imagenModalDownload" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para la timeline de seguimientos */
.timeline-container {
    max-height: 600px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.timeline-container::-webkit-scrollbar {
    width: 6px;
}

.timeline-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.timeline-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.modern-timeline {
    position: relative;
    padding-left: 40px;
}

.modern-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 19px;
    width: 2px;
    background: #e9ecef;
}

.modern-timeline-item {
    position: relative;
}

.modern-timeline-badge {
    position: absolute;
    top: 0;
    left: -40px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 2px solid white;
    z-index: 1;
}

.modern-timeline-panel {
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    border: 1px solid #f0f0f0;
    margin-left: 15px;
    position: relative;
}

.modern-timeline-panel::before {
    content: '';
    position: absolute;
    top: 16px;
    left: -8px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid white;
    z-index: 1;
}

.modern-timeline-header {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.modern-timeline-body {
    font-size: 0.95rem;
}

/* Estilos para el sidebar y comentarios */
#sidebarComentarios {
    max-width: 450px;
    width: 100%;
}

.comentarios-lista {
    max-height: calc(100vh - 320px);
    overflow-y: auto;
    scrollbar-width: thin;
}

.comentarios-lista::-webkit-scrollbar {
    width: 5px;
}

.comentarios-lista::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.comentarios-lista::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

/* Estilo para burbujas de chat */
.chat-bubble {
    position: relative;
    background-color: #f8f9fa;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 0.5rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Estilo para comentarios propios */
.chat-message.me .chat-bubble {
    background-color: #e7f3ff;
    border-color: #c8e1fb;
}

/* Animación para nuevos comentarios */
@keyframes highlightNew {
    from { background-color: rgba(13, 110, 253, 0.1); }
    to { background-color: transparent; }
}

.comentario-item.nuevo {
    animation: highlightNew 2s ease-out;
}

/* Estilos para el drop zone */
.comment-form.dragover {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}

/* Colores para el avatar/inicial del usuario según el primer carácter */
.avatar-inicial[data-initial="A"], .avatar-inicial[data-initial="J"], .avatar-inicial[data-initial="S"] {
    background-color: #0d6efd !important;
}
.avatar-inicial[data-initial="B"], .avatar-inicial[data-initial="K"], .avatar-inicial[data-initial="T"] {
    background-color: #6610f2 !important;
}
.avatar-inicial[data-initial="C"], .avatar-inicial[data-initial="L"], .avatar-inicial[data-initial="U"] {
    background-color: #6f42c1 !important;
}
.avatar-inicial[data-initial="D"], .avatar-inicial[data-initial="M"], .avatar-inicial[data-initial="V"] {
    background-color: #d63384 !important;
}
.avatar-inicial[data-initial="E"], .avatar-inicial[data-initial="N"], .avatar-inicial[data-initial="W"] {
    background-color: #dc3545 !important;
}
.avatar-inicial[data-initial="F"], .avatar-inicial[data-initial="O"], .avatar-inicial[data-initial="X"] {
    background-color: #fd7e14 !important;
}
.avatar-inicial[data-initial="G"], .avatar-inicial[data-initial="P"], .avatar-inicial[data-initial="Y"] {
    background-color: #198754 !important;
}
.avatar-inicial[data-initial="H"], .avatar-inicial[data-initial="Q"], .avatar-inicial[data-initial="Z"] {
    background-color: #20c997 !important;
}
.avatar-inicial[data-initial="I"], .avatar-inicial[data-initial="R"], .avatar-inicial[data-initial="0"] {
    background-color: #0dcaf0 !important;
}

/* Estilos para previsualización de archivo */
.img-preview {
    object-fit: contain;
    max-width: 100%;
    border-radius: 0.375rem;
}

/* Asignar iconos según extensión de archivo */
.archivo-icono.doc, .archivo-icono.docx {
    color: #4285f4;
}
.archivo-icono.pdf {
    color: #ea4335;
}
.archivo-icono.xls, .archivo-icono.xlsx {
    color: #34a853;
}
.archivo-icono.ppt, .archivo-icono.pptx {
    color: #fbbc05;
}
.archivo-icono.zip, .archivo-icono.rar {
    color: #7c4dff;
}
.archivo-icono.txt {
    color: #607d8b;
}

@media (max-width: 767.98px) {
    .modern-timeline {
        padding-left: 30px;
    }
    
    .modern-timeline::before {
        left: 14px;
    }
    
    .modern-timeline-badge {
        width: 28px;
        height: 28px;
        left: -30px;
        font-size: 0.8rem;
    }
    
    .modern-timeline-panel {
        margin-left: 5px;
        padding: 0.75rem;
    }
    
    #sidebarComentarios {
        max-width: 100%;
    }
}
</style>

<script>
// Definir globalmente antes del DOMContentLoaded
window.seguimientoActual = null;

// Función global para abrir el sidebar
window.abrirSidebarComentarios = function(seguimientoId) {
    try {
        console.log('Abriendo sidebar para seguimiento:', seguimientoId);
        
        // Verificamos si Bootstrap está disponible
        if (typeof bootstrap === 'undefined') {
            console.error('Error: Bootstrap no está disponible');
            alert('Error al cargar la interfaz. Por favor, recarga la página.');
            return;
        }
        
        const sidebarComentarios = document.getElementById('sidebarComentarios');
        if (!sidebarComentarios) {
            console.error('Error: No se encontró el elemento sidebarComentarios');
            return;
        }
        
        // Almacenar ID para uso posterior
        window.seguimientoActual = seguimientoId;
        console.log('seguimientoActual establecido:', window.seguimientoActual);
        
        // Inicializar el sidebar con Bootstrap
        const sidebarInstance = new bootstrap.Offcanvas(sidebarComentarios);
        
        // Establecer el ID del seguimiento en el formulario
        const inputSeguimientoId = document.getElementById('seguimientoIdComentario');
        console.log('Estableciendo seguimientoId en input:', {
            inputElement: inputSeguimientoId,
            seguimientoIdToSet: seguimientoId
        });
        if (inputSeguimientoId) {
            inputSeguimientoId.value = seguimientoId;
            console.log('Input value después de establecer:', inputSeguimientoId.value);
        } else {
            console.error('No se encontró el input seguimientoIdComentario');
        }
        
        // Cargar comentarios y mostrar el sidebar
        window.cargarComentarios(seguimientoId);
        sidebarInstance.show();
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

// Función para cargar comentarios de todos los seguimientos al inicializar
function cargarTodosLosComentarios() {
    // Obtener todos los seguimientos disponibles
    const seguimientoElements = document.querySelectorAll('[data-seguimiento-id]');
    
    seguimientoElements.forEach(element => {
        const seguimientoId = element.getAttribute('data-seguimiento-id');
        if (seguimientoId) {
            console.log('Cargando comentarios para seguimiento:', seguimientoId);
            window.cargarComentarios(seguimientoId, true); // silencioso = true
        }
    });
}

// Script principal cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Verificar dependencias necesarias
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado correctamente');
            return;
        }
        
        console.log('Inicializando componente de comentarios de seguimiento...');
        
        // Cargar comentarios existentes para todos los seguimientos
        cargarTodosLosComentarios();
        
        // Referencias al sidebar
        const sidebarComentarios = document.getElementById('sidebarComentarios');
        if (!sidebarComentarios) {
            console.error('No se encontró el elemento sidebarComentarios');
            return;
        }
        
        const sidebarInstance = new bootstrap.Offcanvas(sidebarComentarios);
        
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
        
        // Referencias para el modal de imagen
        const imagenModal = document.getElementById('imagenModal');
        const imagenModalSrc = document.getElementById('imagenModalSrc');
        const imagenModalDownload = document.getElementById('imagenModalDownload');
        
        // Variables locales
        let modoEdicion = false;
        let intervalActualizacion = null;
        
        // Sobrescribir funciones globales con implementaciones reales
        window.resetearFormulario = function() {
            modoEdicion = false;
            if (formComentario) formComentario.reset();
            if (inputComentarioId) inputComentarioId.value = '';
            if (previewContenedor) previewContenedor.classList.add('d-none');
            if (previewImagen) previewImagen.classList.add('d-none');
            if (previewDocumento) previewDocumento.classList.add('d-none');
            if (btnCancelarEdicion) btnCancelarEdicion.classList.add('d-none');
            if (btnEnviarTexto) btnEnviarTexto.textContent = 'Enviar';
            
            // Quitar marcado de edición
            document.querySelectorAll('.comentario-item').forEach(item => {
                item.classList.remove('editando');
            });
        };
        
        window.mostrarAlerta = function(mensaje, tipo) {
            const alerta = document.getElementById('sidebarAlerta');
            const alertaMensaje = document.getElementById('alertaMensaje');
            const alertaIcono = document.getElementById('alertaIcono');
            
            if (!alerta || !alertaMensaje || !alertaIcono) {
                console.error('Elementos de alerta no encontrados');
                alert(mensaje);
                return;
            }
            
            // Configurar tipo
            alerta.className = 'alert m-2';
            
            if (tipo === 'success') {
                alerta.classList.add('alert-success');
                alertaIcono.className = 'fas fa-check-circle me-2';
            } else if (tipo === 'error') {
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
                console.error('ID de seguimiento no proporcionado');
                return;
            }
            
            // Recopilar IDs de comentarios existentes para detectar nuevos
            const comentariosAnteriores = new Set();
            
            if (!silencioso) {
                document.querySelectorAll('.comentario-item').forEach(item => {
                    comentariosAnteriores.add(Number(item.dataset.id));
                });
            }
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                return;
            }
            
            console.log(`Cargando comentarios para seguimiento #${seguimientoId}`);
            
            fetch(`{{ url('/admin/ventas/cotizaciones') }}/seguimientos/${seguimientoId}/comentarios`, {
                headers: {
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
                // Actualizar detalles del seguimiento
                actualizarDetallesSeguimiento(data.seguimiento);
                
                // Detectar comentarios nuevos si es actualización silenciosa
                if (silencioso && comentariosAnteriores.size > 0) {
                    let hayComentariosNuevos = false;
                    data.comentarios.forEach(comentario => {
                        if (!comentariosAnteriores.has(comentario.id)) {
                            hayComentariosNuevos = true;
                        }
                    });
                    
                    if (hayComentariosNuevos) {
                        window.mostrarAlerta('Se han agregado nuevos comentarios', 'info');
                    }
                }
                
                // Actualizar lista de comentarios
                mostrarComentarios(data.comentarios);
            })
            .catch(error => {
                console.error('Error al cargar comentarios:', error);
                if (!silencioso) {
                    window.mostrarAlerta('Error al cargar los comentarios: ' + error.message, 'error');
                }
            });
        };
        
        // Inicializar toggle de realizado usando delegación de eventos
        document.addEventListener('change', function(e) {
            // Solo procesar si el elemento es un toggle de realizado
            if (e.target && e.target.classList.contains('toggle-realizado')) {
                const toggle = e.target;
                try {
                    const seguimientoId = toggle.getAttribute('data-id');
                    if (!seguimientoId) {
                        console.error('No se pudo obtener el ID del seguimiento para el elemento:', toggle);
                        console.log('Atributos del elemento:', {
                            id: toggle.id,
                            className: toggle.className,
                            dataId: toggle.getAttribute('data-id'),
                            allAttributes: Array.from(toggle.attributes).map(attr => `${attr.name}="${attr.value}"`).join(', ')
                        });
                        return;
                    }
                    
                    const label = toggle.nextElementSibling;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    
                    if (!csrfToken) {
                        console.error('Token CSRF no encontrado');
                        window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                        return;
                    }
                    
                    console.log(`Cambiando estado 'realizado' para seguimiento #${seguimientoId}`);
                    
                    // Realizar petición AJAX
                    fetch(`{{ url('/admin/ventas/cotizaciones') }}/seguimientos/${seguimientoId}/toggle-realizado`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({}) // Cuerpo vacío pero necesario para algunos servidores
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error en la respuesta: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Actualizar la etiqueta
                            if (label) {
                                label.textContent = data.realizado ? 'Realizado' : 'Pendiente';
                            }
                            
                            // Si el sidebar está abierto con este seguimiento, actualizar ahí también
                            if (window.seguimientoActual == seguimientoId) {
                                const realizadoEstado = document.querySelector('.seguimiento-realizado');
                                const pendienteEstado = document.querySelector('.seguimiento-pendiente');
                                
                                if (realizadoEstado && pendienteEstado) {
                                    if (data.realizado) {
                                        realizadoEstado.classList.remove('d-none');
                                        pendienteEstado.classList.add('d-none');
                                    } else {
                                        realizadoEstado.classList.add('d-none');
                                        pendienteEstado.classList.remove('d-none');
                                    }
                                }
                            }
                            
                            // Mostrar mensaje
                            window.mostrarAlerta(data.message, 'success');
                        } else {
                            // Revertir el cambio en caso de error
                            toggle.checked = !toggle.checked;
                            window.mostrarAlerta(data.message || 'Error al actualizar el estado', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error en toggle realizado:', error);
                        toggle.checked = !toggle.checked;
                        window.mostrarAlerta('Error en la comunicación con el servidor: ' + error.message, 'error');
                    });
                } catch (e) {
                    console.error('Error inesperado en toggle realizado:', e);
                    toggle.checked = !toggle.checked;
                }
            }
        });
        
        // Iniciar actualización automática cuando el sidebar está abierto
        sidebarComentarios.addEventListener('shown.bs.offcanvas', function() {
            iniciarActualizacionAutomatica();
        });
        
        // Detener actualización cuando se cierra el sidebar
        sidebarComentarios.addEventListener('hidden.bs.offcanvas', function() {
            detenerActualizacionAutomatica();
            window.resetearFormulario();
        });
        
        // Actualización automática de comentarios
        function iniciarActualizacionAutomatica() {
            // Actualizar cada 30 segundos
            intervalActualizacion = setInterval(() => {
                if (window.seguimientoActual) {
                    window.cargarComentarios(window.seguimientoActual, true);
                }
            }, 30000);
        }
        
        function detenerActualizacionAutomatica() {
            clearInterval(intervalActualizacion);
        }
        
        // Actualizar la información del seguimiento en el sidebar
        function actualizarDetallesSeguimiento(seguimiento) {
            if (!seguimiento) {
                console.error('Datos de seguimiento no proporcionados');
                return;
            }
            
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
            
            // Verificar que todos los elementos existan
            if (!tipoElement || !contenidoElement || !usuarioElement || !fechaElement || 
                !iconoElement || !iconoContenedor || !recordatorioContainer || 
                !recordatorioFecha || !recordatorioEstado || !realizadoEstado || !pendienteEstado) {
                console.error('Faltan elementos en el DOM para actualizar detalles del seguimiento');
                return;
            }
            
            // Obtener la clase y el texto según el tipo
            let badgeClass = '';
            let iconoClass = '';
            let bgColor = '';
            
            if (seguimiento.tipo === 'nota') {
                badgeClass = 'bg-warning-subtle text-warning';
                iconoClass = 'fa-sticky-note';
                bgColor = '#ffc107';
            } else if (seguimiento.tipo === 'llamada') {
                badgeClass = 'bg-success-subtle text-success';
                iconoClass = 'fa-phone-alt';
                bgColor = '#198754';
            } else if (seguimiento.tipo === 'reunion') {
                badgeClass = 'bg-primary-subtle text-primary';
                iconoClass = 'fa-handshake';
                bgColor = '#0d6efd';
            } else if (seguimiento.tipo === 'email') {
                badgeClass = 'bg-info-subtle text-info';
                iconoClass = 'fa-envelope';
                bgColor = '#0dcaf0';
            } else {
                badgeClass = 'bg-secondary-subtle text-secondary';
                iconoClass = 'fa-comment';
                bgColor = '#6c757d';
            }
            
            // Actualizar los elementos
            tipoElement.className = `badge seguimiento-tipo-badge rounded-pill px-3 py-1 ${badgeClass}`;
            tipoElement.textContent = seguimiento.tipo.charAt(0).toUpperCase() + seguimiento.tipo.slice(1);
            contenidoElement.textContent = seguimiento.contenido;
            usuarioElement.textContent = seguimiento.usuario ? seguimiento.usuario.name : 'Usuario no especificado';
            
            // Actualizar icono y su color
            iconoElement.className = `fas fa-lg ${iconoClass}`;
            iconoContenedor.style.backgroundColor = bgColor;
            
            // Formatear la fecha
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
                console.error('Error al formatear fecha:', e);
                fechaElement.textContent = seguimiento.fecha_seguimiento || 'Fecha desconocida';
            }
            
            // Actualizar estado de realización
            if (seguimiento.realizado) {
                realizadoEstado.classList.remove('d-none');
                pendienteEstado.classList.add('d-none');
            } else {
                realizadoEstado.classList.add('d-none');
                pendienteEstado.classList.remove('d-none');
            }
            
            // Actualizar información de recordatorio si existe
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
                    
                    // Verificar si el recordatorio ha vencido
                    const ahora = new Date();
                    if (fechaRecordatorio < ahora) {
                        recordatorioEstado.className = 'badge bg-danger ms-1';
                        recordatorioEstado.textContent = 'Vencido';
                    } else {
                        recordatorioEstado.className = 'badge bg-success ms-1';
                        recordatorioEstado.textContent = 'Pendiente';
                    }
                } catch (e) {
                    console.error('Error al formatear fecha de recordatorio:', e);
                    recordatorioFecha.textContent = seguimiento.fecha_recordatorio || 'Fecha desconocida';
                }
            } else {
                recordatorioContainer.classList.add('d-none');
            }
            
            // Actualizar contador de comentarios
            const contadorComentarios = document.querySelector('.comentarios-contador');
            if (contadorComentarios) {
                contadorComentarios.textContent = seguimiento.comentarios ? seguimiento.comentarios.length : '0';
            }
        }
        
        // Mostrar la lista de comentarios
        function mostrarComentarios(comentarios) {
            if (!comentarios) {
                console.error('No se proporcionaron comentarios');
                return;
            }
            
            const listaComentarios = document.querySelector('.comentarios-lista');
            const placeholder = document.querySelector('.comentarios-placeholder');
            const template = document.getElementById('comentario-template');
            
            if (!listaComentarios || !placeholder || !template) {
                console.error('Faltan elementos necesarios para mostrar comentarios');
                return;
            }
            
            // Limpiar la lista actual
            while (listaComentarios.firstChild) {
                if (listaComentarios.firstChild.classList && listaComentarios.firstChild.classList.contains('comentarios-placeholder')) {
                    break;
                }
                listaComentarios.removeChild(listaComentarios.firstChild);
            }
            
            // Mostrar placeholder si no hay comentarios
            if (!comentarios || comentarios.length === 0) {
                placeholder.style.display = 'block';
                return;
            } else {
                placeholder.style.display = 'none';
            }
            
            // Usuario actual para identificar mis comentarios
            const usuarioActualId = {{ Auth::id() }};
            
            // Agregar cada comentario
            comentarios.forEach(comentario => {
                try {
                    if (!comentario.id) {
                        console.error('Comentario sin ID detectado', comentario);
                        return;
                    }
                    
                    const comentarioElement = template.content.cloneNode(true).querySelector('.comentario-item');
                    
                    // Establecer atributo de ID
                    comentarioElement.dataset.id = comentario.id;
                    
                    // Verificar si el comentario es del usuario actual
                    if (comentario.user_id === usuarioActualId) {
                        comentarioElement.classList.add('me');
                    }
                    
                    // Obtener inicial del usuario para el avatar
                    const avatar = comentarioElement.querySelector('.avatar-inicial');
                    const inicial = comentarioElement.querySelector('.initial');
                    const nombreUsuario = comentario.usuario ? comentario.usuario.name : 'Usuario';
                    const primeraLetra = nombreUsuario.charAt(0).toUpperCase();
                    
                    if (avatar && inicial) {
                        inicial.textContent = primeraLetra;
                        avatar.dataset.initial = primeraLetra;
                    }
                    
                    // Usuario y fecha
                    const usuario = comentarioElement.querySelector('.comentario-usuario');
                    const fecha = comentarioElement.querySelector('.comentario-fecha');
                    
                    if (usuario) usuario.textContent = nombreUsuario;
                    
                    // Formatear la fecha
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
                            console.error('Error al formatear fecha del comentario:', e);
                            fecha.textContent = comentario.created_at;
                        }
                    }
                    
                    // Contenido
                    const contenido = comentarioElement.querySelector('.comentario-contenido');
                    if (contenido) contenido.textContent = comentario.contenido;
                    
                    // Archivo adjunto si existe
                    if (comentario.archivo) {
                        const rutaArchivo = `/storage/${comentario.archivo}`;
                        const nombreOriginal = comentario.archivo.split('/').pop().split('_').slice(1).join('_');
                        const extension = nombreOriginal.split('.').pop().toLowerCase();
                        
                        // Determinar el tipo de archivo
                        const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(extension);
                        
                        if (esImagen) {
                            // Mostrar vista previa de imagen
                            const imagenContainer = comentarioElement.querySelector('.comentario-imagen');
                            if (imagenContainer) {
                                const imagen = document.createElement('img');
                                imagen.src = rutaArchivo;
                                imagen.alt = nombreOriginal;
                                imagen.classList.add('img-fluid', 'rounded');
                                
                                // Limpiar y agregar la imagen
                                imagenContainer.innerHTML = '';
                                imagenContainer.appendChild(imagen);
                                imagenContainer.classList.remove('d-none');
                                
                                // Configurar enlace para ver en modal
                                const enlace = imagenContainer.querySelector('a');
                                if (enlace && imagenModal && imagenModalSrc && imagenModalDownload) {
                                    enlace.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        
                                        // Configurar modal
                                        imagenModalSrc.src = rutaArchivo;
                                        imagenModalDownload.href = rutaArchivo;
                                        imagenModalDownload.download = nombreOriginal;
                                        
                                        // Mostrar modal
                                        if (typeof bootstrap !== 'undefined') {
                                            new bootstrap.Modal(imagenModal).show();
                                        } else {
                                            imagenModal.classList.add('show');
                                            imagenModal.style.display = 'block';
                                        }
                                    });
                                }
                            }
                        } else {
                            // Mostrar enlace de documento
                            const documentoContainer = comentarioElement.querySelector('.comentario-documento');
                            if (documentoContainer) {
                                const documentoNombre = comentarioElement.querySelector('.comentario-archivo-nombre');
                                const documentoEnlace = documentoContainer.querySelector('a');
                                const documentoIcono = comentarioElement.querySelector('.archivo-icono');
                                
                                // Configurar icono según extensión
                                let iconoClase = 'fa-file';
                                
                                // Asignar iconos por tipo de archivo
                                if (['doc', 'docx'].includes(extension)) {
                                    iconoClase = 'fa-file-word';
                                    if (documentoIcono) documentoIcono.classList.add('text-primary');
                                } else if (extension === 'pdf') {
                                    iconoClase = 'fa-file-pdf';
                                    if (documentoIcono) documentoIcono.classList.add('text-danger');
                                } else if (['xls', 'xlsx'].includes(extension)) {
                                    iconoClase = 'fa-file-excel';
                                    if (documentoIcono) documentoIcono.classList.add('text-success');
                                } else if (['ppt', 'pptx'].includes(extension)) {
                                    iconoClase = 'fa-file-powerpoint';
                                    if (documentoIcono) documentoIcono.classList.add('text-warning');
                                } else if (['zip', 'rar', '7z'].includes(extension)) {
                                    iconoClase = 'fa-file-archive';
                                    if (documentoIcono) documentoIcono.classList.add('text-secondary');
                                } else if (['txt', 'csv'].includes(extension)) {
                                    iconoClase = 'fa-file-alt';
                                    if (documentoIcono) documentoIcono.classList.add('text-info');
                                }
                                
                                if (documentoIcono) documentoIcono.className = `archivo-icono fas ${iconoClase} me-2`;
                                if (documentoIcono) documentoIcono.classList.add(extension);
                                if (documentoNombre) documentoNombre.textContent = nombreOriginal;
                                if (documentoEnlace) {
                                    documentoEnlace.href = rutaArchivo;
                                    documentoEnlace.download = nombreOriginal;
                                }
                                
                                documentoContainer.classList.remove('d-none');
                            }
                        }
                    }
                    
                    // Botones de acción (solo visibles para el autor)
                    const accionesContainer = comentarioElement.querySelector('.comentario-acciones');
                    if (accionesContainer) {
                        const btnEditar = comentarioElement.querySelector('.btn-editar-comentario');
                        const btnEliminar = comentarioElement.querySelector('.btn-eliminar-comentario');
                        
                        // Verificar si el usuario actual es el autor
                        if (comentario.user_id !== usuarioActualId) {
                            accionesContainer.style.display = 'none';
                        } else {
                            // Configurar eventos para editar y eliminar
                            if (btnEditar) btnEditar.addEventListener('click', () => iniciarEdicionComentario(comentario));
                            if (btnEliminar) btnEliminar.addEventListener('click', () => eliminarComentario(comentario.id));
                        }
                    }
                    
                    // Añadir comentario al inicio de la lista
                    listaComentarios.prepend(comentarioElement);
                } catch (e) {
                    console.error('Error al procesar comentario:', e, comentario);
                }
            });
            
            // Actualizar el contador en el botón
            const contador = document.querySelector(`.comentarios-count-${window.seguimientoActual}`);
            if (contador) {
                contador.textContent = comentarios.length;
            }
        }
        
        // Iniciar la edición de un comentario
        function iniciarEdicionComentario(comentario) {
            if (!comentario || !comentario.id) {
                console.error('Comentario inválido para edición', comentario);
                return;
            }
            
            modoEdicion = true;
            if (inputComentarioId) inputComentarioId.value = comentario.id;
            if (inputContenido) inputContenido.value = comentario.contenido;
            if (btnCancelarEdicion) btnCancelarEdicion.classList.remove('d-none');
            if (btnEnviarTexto) btnEnviarTexto.textContent = 'Actualizar';
            
            // Si hay un archivo, mostrar el preview
            if (comentario.archivo && previewContenedor && previewImagen && previewDocumento) {
                const rutaArchivo = `/storage/${comentario.archivo}`;
                const nombreOriginal = comentario.archivo.split('/').pop().split('_').slice(1).join('_');
                const extension = nombreOriginal.split('.').pop().toLowerCase();
                const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(extension);
                
                previewContenedor.classList.remove('d-none');
                
                if (esImagen) {
                    // Mostrar preview de imagen
                    previewImagen.classList.remove('d-none');
                    previewDocumento.classList.add('d-none');
                    const imgPreview = previewImagen.querySelector('img');
                    if (imgPreview) imgPreview.src = rutaArchivo;
                } else {
                    // Mostrar preview de documento
                    previewImagen.classList.add('d-none');
                    previewDocumento.classList.remove('d-none');
                    if (nombreArchivo) nombreArchivo.textContent = nombreOriginal;
                }
            }
            
            // Marcar visualmente el comentario en edición
            document.querySelectorAll('.comentario-item').forEach(item => {
                if (item.dataset.id == comentario.id) {
                    item.classList.add('editando');
                    item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    item.classList.remove('editando');
                }
            });
            
            // Enfocar el campo de texto
            if (inputContenido) inputContenido.focus();
        }
        
        // Cancelar la edición
        if (btnCancelarEdicion) {
            btnCancelarEdicion.addEventListener('click', function() {
                window.resetearFormulario();
                
                // Quitar marcado de edición
                document.querySelectorAll('.comentario-item').forEach(item => {
                    item.classList.remove('editando');
                });
            });
        }
        
        // Manejar la vista previa del archivo
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
        
        // Enviar el formulario (crear o actualizar comentario)
        if (formComentario) {
            formComentario.addEventListener('submit', function(e) {
                e.preventDefault();
                
                try {
                    const formData = new FormData(formComentario);
                    const seguimientoId = (inputSeguimientoId && inputSeguimientoId.value && inputSeguimientoId.value.trim()) ? inputSeguimientoId.value : window.seguimientoActual;
                    const comentarioId = inputComentarioId ? inputComentarioId.value : '';
                    
                    console.log('Debug info:', {
                        inputSeguimientoId: inputSeguimientoId,
                        inputSeguimientoIdValue: inputSeguimientoId ? inputSeguimientoId.value : null,
                        windowSeguimientoActual: window.seguimientoActual,
                        finalSeguimientoId: seguimientoId
                    });
                    
                    if (!seguimientoId) {
                        console.error('No se pudo obtener el ID del seguimiento');
                        window.mostrarAlerta('Error: Falta ID del seguimiento', 'error');
                        return;
                    }
                    
                    let url = '';
                    let method = '';
                    
                    if (modoEdicion && comentarioId) {
                        url = `{{ url('/admin/ventas/cotizaciones') }}/seguimientos/${seguimientoId}/comentarios/${comentarioId}`;
                        method = 'POST';
                        formData.append('_method', 'PUT'); // Simulando PUT con POST + _method
                    } else {
                        url = `{{ url('/admin/ventas/cotizaciones') }}/seguimientos/${seguimientoId}/comentarios`;
                        method = 'POST';
                    }
                    
                    // Obtener token CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('Token CSRF no encontrado');
                        window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                        return;
                    }
                    
                    // Agregar token CSRF
                    formData.append('_token', csrfToken.getAttribute('content'));
                    
                    // Mostrar estado de carga
                    const btnSubmit = formComentario.querySelector('button[type="submit"]');
                    const btnTextoOriginal = btnSubmit ? btnSubmit.innerHTML : '';
                    
                    if (btnSubmit) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
                    }
                    
                    console.log(`Enviando ${modoEdicion ? 'actualización' : 'nuevo'} comentario para seguimiento #${seguimientoId}`);
                    
                    fetch(url, {
                        method: method,
                        body: formData
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
                            
                            // Si es comentario nuevo, hacer scroll al principio (comentarios más recientes)
                            if (!modoEdicion) {
                                setTimeout(() => {
                                    const listaComentarios = document.querySelector('.comentarios-lista');
                                    if (listaComentarios) listaComentarios.scrollTop = 0;
                                }, 300);
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
                } catch (e) {
                    console.error('Error inesperado al enviar comentario:', e);
                    window.mostrarAlerta('Error inesperado. Por favor, inténtalo de nuevo.', 'error');
                }
            });
        }
        
        // Eliminar un comentario
        function eliminarComentario(comentarioId) {
            if (!comentarioId) {
                console.error('ID de comentario no proporcionado');
                return;
            }
            
            if (!confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
                return;
            }
            
            try {
                const seguimientoId = window.seguimientoActual;
                console.log('Debug delete - window.seguimientoActual:', window.seguimientoActual);
                if (!seguimientoId) {
                    console.error('No se pudo obtener el ID del seguimiento');
                    window.mostrarAlerta('Error: Falta ID del seguimiento', 'error');
                    return;
                }
                
                // Obtener token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('Token CSRF no encontrado');
                    window.mostrarAlerta('Error de seguridad. Por favor, recarga la página.', 'error');
                    return;
                }
                
                console.log(`Eliminando comentario #${comentarioId} de seguimiento #${seguimientoId}`);
                
                fetch(`{{ url('/admin/ventas/cotizaciones') }}/seguimientos/${seguimientoId}/comentarios/${comentarioId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
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
                        window.cargarComentarios(seguimientoId);
                    } else {
                        window.mostrarAlerta(data.message || 'Error al eliminar el comentario', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar comentario:', error);
                    window.mostrarAlerta('Error al eliminar el comentario: ' + error.message, 'error');
                });
            } catch (e) {
                console.error('Error inesperado al eliminar comentario:', e);
                window.mostrarAlerta('Error inesperado. Por favor, inténtalo de nuevo.', 'error');
            }
        }
        
        // Filtrar comentarios con búsqueda
        const inputBuscar = document.getElementById('buscarComentario');
        if (inputBuscar) {
            inputBuscar.addEventListener('input', function() {
                const textoBusqueda = this.value.toLowerCase();
                const comentarios = document.querySelectorAll('.comentario-item');
                
                comentarios.forEach(comentario => {
                    try {
                        const contenido = comentario.querySelector('.comentario-contenido');
                        const usuario = comentario.querySelector('.comentario-usuario');
                        
                        if (contenido && usuario) {
                            const contenidoTexto = contenido.textContent.toLowerCase();
                            const usuarioTexto = usuario.textContent.toLowerCase();
                            
                            if (contenidoTexto.includes(textoBusqueda) || usuarioTexto.includes(textoBusqueda)) {
                                comentario.style.display = 'block';
                            } else {
                                comentario.style.display = 'none';
                            }
                        }
                    } catch (e) {
                        console.error('Error al filtrar comentario:', e);
                    }
                });
            });
        }
        
        // Soporte para drag & drop de archivos
        const dropZone = document.getElementById('formComentario');
        if (dropZone && inputArchivo) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropZone.classList.add('dragover');
            }
            
            function unhighlight() {
                dropZone.classList.remove('dragover');
            }
            
            dropZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                try {
                    const dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length > 0) {
                        inputArchivo.files = dt.files;
                        // Disparar evento change manualmente
                        const event = new Event('change');
                        inputArchivo.dispatchEvent(event);
                    }
                } catch (error) {
                    console.error('Error al procesar archivos arrastrados:', error);
                }
            }
        }
        
        // Confirmación antes de cerrar si hay cambios sin guardar
        let hayFormularioSinGuardar = false;
        
        // Detectar cambios en el formulario
        if (inputContenido) {
            inputContenido.addEventListener('input', () => {
                hayFormularioSinGuardar = true;
            });
        }
        
        if (inputArchivo) {
            inputArchivo.addEventListener('change', () => {
                hayFormularioSinGuardar = true;
            });
        }
        
        // Resetear después de enviar o cancelar
        if (formComentario) {
            formComentario.addEventListener('submit', () => {
                hayFormularioSinGuardar = false;
            });
        }
        
        if (btnCancelarEdicion) {
            btnCancelarEdicion.addEventListener('click', () => {
                hayFormularioSinGuardar = false;
            });
        }
        
        // Confirmar antes de cerrar el sidebar si hay cambios
        if (sidebarComentarios) {
            sidebarComentarios.addEventListener('hide.bs.offcanvas', function(e) {
                if (hayFormularioSinGuardar) {
                    const confirmar = confirm('Tienes cambios sin guardar. ¿Estás seguro de que quieres cerrar?');
                    if (!confirmar) {
                        e.preventDefault();
                    } else {
                        hayFormularioSinGuardar = false;
                    }
                }
            });
        }
        
        console.log('Componente de comentarios inicializado correctamente');
    } catch (error) {
        console.error('Error general al inicializar el componente de comentarios:', error);
    }
});
</script>