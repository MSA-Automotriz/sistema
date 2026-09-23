<div class="container-fluid p-3">
    <div class="row g-3">
        <!-- Formulario de Nuevo Seguimiento -->
        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div id="formErrors" class="alert alert-danger small p-2" style="display: none;"></div>
                    <form id="formNuevoSeguimiento" action="{{ route('admin.mantenimiento.ordenes.seguimientos.store', $orden) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tipo" class="form-label small fw-bold">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select form-select-sm" required>
                                <option value="nota">Nota</option>
                                <option value="llamada">Llamada</option>
                                <option value="reunion">Reunión</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contenido" class="form-label small fw-bold">Contenido</label>
                            <textarea name="contenido" id="contenido" class="form-control form-control-sm" rows="3" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="recordatorio" id="recordatorio" class="form-check-input" value="1">
                            <label for="recordatorio" class="form-check-label small">Activar Recordatorio</label>
                        </div>
                        <div id="recordatorioContainer" class="mb-3" style="display: none;">
                            <label for="fecha_recordatorio" class="form-label small fw-bold">Fecha de Recordatorio</label>
                            <input type="datetime-local" name="fecha_recordatorio" id="fecha_recordatorio" class="form-control form-control-sm">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Registrar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Seguimientos -->
        <div class="col-12 col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Seguimientos</h5>
                    @if($orden->seguimientos && $orden->seguimientos->count() > 0)
                        <div class="timeline">
                            @foreach($orden->seguimientos->sortByDesc('fecha_seguimiento') as $seguimiento)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-badge
                                        @if($seguimiento->tipo === 'nota') bg-warning
                                        @elseif($seguimiento->tipo === 'llamada') bg-success
                                        @elseif($seguimiento->tipo === 'reunion') bg-primary
                                        @elseif($seguimiento->tipo === 'email') bg-info
                                        @else bg-secondary
                                        @endif">
                                        <i class="fas fa-{{ $seguimiento->tipo === 'nota' ? 'sticky-note' : ($seguimiento->tipo === 'llamada' ? 'phone-alt' : ($seguimiento->tipo === 'reunion' ? 'handshake' : ($seguimiento->tipo === 'email' ? 'envelope' : 'comment'))) }}"></i>
                                    </div>
                                    <div class="timeline-panel card border-0 shadow-sm p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge
                                                @if($seguimiento->tipo === 'nota') bg-warning-subtle text-warning
                                                @elseif($seguimiento->tipo === 'llamada') bg-success-subtle text-success
                                                @elseif($seguimiento->tipo === 'reunion') bg-primary-subtle text-primary
                                                @elseif($seguimiento->tipo === 'email') bg-info-subtle text-info
                                                @else bg-secondary-subtle text-secondary
                                                @endif rounded-pill text-capitalize small">
                                                {{ $seguimiento->tipo }}
                                            </span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted small">
                                                    <i class="far fa-user me-1"></i>{{ $seguimiento->usuario->name ?? 'N/A' }}
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="far fa-clock me-1"></i>{{ $seguimiento->fecha_seguimiento ? $seguimiento->fecha_seguimiento->format('d/m/y H:i') : '' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <p class="mb-0 small">{{ $seguimiento->contenido }}</p>
                                            <div class="d-flex align-items-center gap-2">
                                                <button class="btn btn-outline-primary btn-sm" 
                                                        data-seguimiento-id="{{ $seguimiento->id }}"
                                                        onclick="window.abrirSidebarComentarios({{ $seguimiento->id }})">
                                                    <i class="fas fa-comments"></i>
                                                    <span class="badge bg-secondary comentarios-count-{{ $seguimiento->id }}">
                                                        {{ $seguimiento->comentarios->count() }}
                                                    </span>
                                                </button>
                                                <div class="form-check form-switch">
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
                                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-bell"></i>
                                                </span>
                                                <small class="text-muted">
                                                    Recordatorio: {{ $seguimiento->fecha_recordatorio->format('d/m/Y H:i') }}
                                                    <span class="badge 
                                                        {{ $seguimiento->fecha_recordatorio->isPast() ? 'bg-danger' : 'bg-success' }} ms-1">
                                                        {{ $seguimiento->fecha_recordatorio->isPast() ? 'Vencido' : 'Pendiente' }}
                                                    </span>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTYgMkg4QzcuNDQ3NzIgMiA3IDIuNDQ3NzIgNyAzVjE2QzcgMTYuNTUyMyA3LjQ0NzcyIDE3IDggMTdIMjBDMjAuNTUyMyAxNyAyMSAxNi41NTIzIDIxIDE2VjdDMjEgNi40NDc3MiAyMC41NTIzIDYgMjAgNkgxN1YzQzE3IDIuNDQ3NzIgMTYuNTUyMyAyIDE2IDJaIiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0xNiA2LjAxMDM3TDIxIDYuMDEwMzciIHN0cm9rZT0iIzk5OSIgc3Ryb2tlLXdpZHRoPSIyIi8+PHBhdGggZD0iTTMgOEgxN1YyMUgzVjhaIiBmaWxsPSIjRTdFN0U3IiBzdHJva2U9IiM5OTkiIHN0cm9rZS13aWR0aD0iMiIvPjwvc3ZnPg==" 
                                 width="60" height="60" alt="No hay seguimientos" class="opacity-50 mb-3">
                            <p class="text-muted small mb-0">No hay seguimientos registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template para comentarios -->
<template id="comentario-template">
    <div class="comentario-item" data-id="">
        <div class="d-flex">
            <div class="avatar-inicial rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 32px; height: 32px; background-color: var(--bs-primary);">
                <span class="initial"></span>
            </div>
            <div class="flex-grow-1">
                <div class="chat-bubble rounded p-2 bg-white border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-medium comentario-usuario">Usuario</span>
                            <small class="text-muted ms-2 comentario-fecha">Fecha</small>
                        </div>
                    </div>
                    <p class="comentario-contenido mb-2">Contenido</p>
                    <div class="comentario-imagen mt-2 d-none">
                        <a href="#" class="d-block" data-bs-toggle="modal" data-bs-target="#imagenModal">
                            <img src="" class="img-fluid rounded" style="max-height: 150px;">
                        </a>
                    </div>
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

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-badge {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.timeline-panel {
    background: white;
    border-radius: 0.5rem;
}

.card {
    border-radius: 0.75rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
}

.btn-primary {
    border-radius: 0.5rem;
    background: linear-gradient(45deg, #007bff, #00aaff);
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado, función definida:', window.abrirSidebarComentarios);

    const formSeguimiento = document.getElementById('formNuevoSeguimiento');
    const recordatorioCheckbox = document.getElementById('recordatorio');
    const recordatorioContainer = document.getElementById('recordatorioContainer');

    // Mostrar/ocultar campo de fecha de recordatorio
    if (recordatorioCheckbox && recordatorioContainer) {
        recordatorioCheckbox.addEventListener('change', function() {
            recordatorioContainer.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Manejar cambio de estado de realizado
    document.querySelectorAll('.toggle-realizado').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const seguimientoId = this.dataset.id;
            const isChecked = this.checked;

            fetch(`{{ url('admin/mantenimiento/ordenes/seguimientos') }}/${seguimientoId}/toggle-realizado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ realizado: isChecked })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const label = document.querySelector(`label[for="realizado-${seguimientoId}"]`);
                    label.textContent = isChecked ? 'Realizado' : 'Pendiente';
                } else {
                    alert(data.message);
                    this.checked = !isChecked;
                }
            })
            .catch(error => {
                alert('Error al actualizar el estado');
                this.checked = !isChecked;
                console.error('Error:', error);
            });
        });
    });

    // Función para abrir el sidebar de comentarios
    window.abrirSidebarComentarios = function(seguimientoId) {
        fetch(`{{ url('admin/mantenimiento/ordenes/seguimientos') }}/${seguimientoId}/sidebar`, {
            headers: {
                'Accept': 'text/html',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(html => {
            // Eliminar sidebar existente si hay uno
            const sidebarExistente = document.getElementById('sidebarComentarios');
            if (sidebarExistente) {
                sidebarExistente.remove();
            }
            
            const sidebar = document.createElement('div');
            sidebar.innerHTML = html;
            document.body.appendChild(sidebar.querySelector('.offcanvas'));
            const bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('sidebarComentarios'));
            bsOffcanvas.show();

            cargarComentarios(seguimientoId);
        })
        .catch(error => {
            console.error('Error al cargar sidebar:', error);
            alert('Error al cargar comentarios');
        });
    };

    // Función para cerrar el sidebar
    window.cerrarSidebarComentarios = function() {
        const sidebar = document.getElementById('sidebarComentarios');
        if (sidebar) {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }
    };

    // Función para cargar comentarios dinámicamente
    function cargarComentarios(seguimientoId) {
        const comentariosList = document.querySelector('.comentarios-lista');
        if (!comentariosList) return;

        fetch(`{{ url('admin/mantenimiento/ordenes/seguimientos') }}/${seguimientoId}/comentarios`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            comentariosList.innerHTML = '';
            if (data.comentarios && data.comentarios.length > 0) {
                data.comentarios.forEach(comentario => {
                    const template = document.getElementById('comentario-template').content.cloneNode(true);
                    template.querySelector('.comentario-item').dataset.id = comentario.id;
                    template.querySelector('.comentario-usuario').textContent = comentario.usuario?.name || 'N/A';
                    template.querySelector('.comentario-fecha').textContent = comentario.created_at;
                    template.querySelector('.comentario-contenido').textContent = comentario.contenido;
                    template.querySelector('.initial').textContent = comentario.usuario?.name?.charAt(0) || 'U';

                    if (comentario.archivo) {
                        if (comentario.es_imagen) {
                            const img = template.querySelector('.comentario-imagen');
                            img.classList.remove('d-none');
                            img.querySelector('img').src = comentario.ruta_archivo;
                            img.querySelector('a').href = comentario.ruta_archivo;
                        } else {
                            const doc = template.querySelector('.comentario-documento');
                            doc.classList.remove('d-none');
                            doc.querySelector('a').href = comentario.ruta_archivo;
                            doc.querySelector('.comentario-archivo-nombre').textContent = comentario.nombre_archivo;
                            doc.querySelector('.archivo-icono').className = `fas fa-file-${comentario.extension_archivo} me-2`;
                        }
                    }

                    comentariosList.appendChild(template);
                });
                
                // Inicializar formulario de comentario - Colocado aquí para evitar duplicación
                const formComentario = document.getElementById('formComentario');
                if (formComentario) {
                    // Remover eventos previos
                    const nuevoFormComentario = formComentario.cloneNode(true);
                    formComentario.parentNode.replaceChild(nuevoFormComentario, formComentario);
                    
                    nuevoFormComentario.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        formData.append('seguimiento_id', seguimientoId);

                        fetch(`{{ url('admin/mantenimiento/ordenes/seguimientos') }}/${seguimientoId}/comentarios`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                cargarComentarios(seguimientoId);
                                nuevoFormComentario.reset();
                                const badge = document.querySelector(`.comentarios-count-${seguimientoId}`);
                                if (badge) {
                                    badge.textContent = parseInt(badge.textContent) + 1;
                                }
                            } else {
                                alert(data.message || 'Error al agregar comentario');
                            }
                        })
                        .catch(error => {
                            alert('Error al agregar comentario');
                            console.error('Error:', error);
                        });
                    });
                }
            } else {
                comentariosList.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="d-inline-block p-3 bg-light rounded-circle mb-3">
                            <i class="fas fa-comments fa-2x opacity-50"></i>
                        </div>
                        <p class="mb-0">No hay comentarios para este seguimiento</p>
                    </div>
                `;
                
                // Inicializar formulario de comentario incluso si no hay comentarios
                const formComentario = document.getElementById('formComentario');
                if (formComentario) {
                    // Remover eventos previos
                    const nuevoFormComentario = formComentario.cloneNode(true);
                    formComentario.parentNode.replaceChild(nuevoFormComentario, formComentario);
                    
                    nuevoFormComentario.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        formData.append('seguimiento_id', seguimientoId);

                        fetch(`{{ url('admin/mantenimiento/ordenes/seguimientos') }}/${seguimientoId}/comentarios`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                cargarComentarios(seguimientoId);
                                nuevoFormComentario.reset();
                                const badge = document.querySelector(`.comentarios-count-${seguimientoId}`);
                                if (badge) {
                                    badge.textContent = parseInt(badge.textContent) + 1;
                                }
                            } else {
                                alert(data.message || 'Error al agregar comentario');
                            }
                        })
                        .catch(error => {
                            alert('Error al agregar comentario');
                            console.error('Error:', error);
                        });
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar comentarios:', error);
            comentariosList.innerHTML = '<p class="text-danger small">Error al cargar comentarios.</p>';
        });
    }
});
</script>
@endpush