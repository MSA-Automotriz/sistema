<!-- resources/views/admin/mantenimiento/ordenes/seguimiento/sidebar.blade.php -->

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