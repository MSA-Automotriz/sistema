@php
    use App\Models\PlacaInfo;
    if (!isset($placas)) {
        $placas = $cotizacion->placas()->with(['documentos', 'comentarios.usuario'])->get();
    }
    if (!isset($estados)) {
        $estados = PlacaInfo::ESTADOS;
    }
@endphp

<style>
    .placa-card { transition: all 0.3s ease; }
    .placa-card.collapsed .placa-details { max-height: 0; overflow: hidden; }
    .placa-card .placa-details { max-height: 1000px; transition: max-height 0.3s ease; }
    .status-badge { padding: 0.5rem 1rem; border-radius: 1rem; font-size: 0.875rem; }
    .action-btn { padding: 0.25rem 0.5rem; }
    .table-sm td, .table-sm th { padding: 0.5rem; }
    .comment-box { max-height: 200px; overflow-y: auto; }
    .comment-message { background: #f8f9fa; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.5rem; }
    .bg-amber-100 { background-color: #fef3c7; } /* Color ámbar claro */
    .bg-green-100 { background-color: #d1fae5; } /* Color verde claro */
</style>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-id-card me-2 text-primary"></i> Gestión de Placas
            </h5>
            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNuevaPlaca">
                <i class="fas fa-plus me-1"></i> Nueva Placa
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        @if($placas->count() > 0)
            @foreach($placas as $placa)
                <div class="placa-card card mb-3 border-0 shadow-sm rounded-3" data-bs-toggle="collapse" data-bs-target="#placaDetails{{ $placa->id }}">
                    <div class="card-header {{ $placa->tipo_placa === 'rotativa' ? 'bg-amber-100' : 'bg-green-100' }} rounded-top-3 py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-id-card me-2 text-primary"></i>
                                {{ $placa->tipo_texto }} - {{ $placa->numero_placa ?? 'Sin número' }}
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="status-badge bg-info text-white">{{ $placa->estado_placa }}</span>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm action-btn" data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarPlaca{{ $placa->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm action-btn" data-bs-toggle="modal" 
                                        data-bs-target="#modalEliminarPlaca{{ $placa->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="placaDetails{{ $placa->id }}" class="placa-details collapse">
                        <div class="card-body py-3 px-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted">Fecha emisión:</small>
                                        <p class="mb-0">{{ $placa->fecha_emision ? $placa->fecha_emision->format('d/m/Y') : 'No definida' }}</p>
                                    </div>
                                    @if($placa->observaciones)
                                        <div class="mb-3">
                                            <small class="text-muted">Observaciones:</small>
                                            <p class="mb-0 small">{{ $placa->observaciones }}</p>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="fw-semibold">Comentarios</small>
                                            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" 
                                                data-bs-target="#modalNuevoComentarioPlaca{{ $placa->id }}">
                                                <i class="fas fa-comment me-1"></i> Comentar
                                            </button>
                                        </div>
                                        <div class="comment-box">
                                            @forelse($placa->comentarios as $comentario)
                                                <div class="comment-message">
                                                    <small class="d-block text-muted">
                                                        {{ $comentario->usuario->name }} - {{ $comentario->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                    <p class="mb-0 small">{{ $comentario->comentario }}</p>
                                                    <button class="btn btn-outline-danger btn-sm action-btn mt-1" data-bs-toggle="modal"
                                                        data-bs-target="#modalEliminarComentario{{ $comentario->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @empty
                                                <p class="text-muted small">No hay comentarios</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="fw-semibold">Documentos</small>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" 
                                            data-bs-target="#modalNuevoDocumentoPlaca{{ $placa->id }}">
                                            <i class="fas fa-plus me-1"></i> Documento
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Tipo</th>
                                                    <th>Fecha</th>
                                                    <th class="text-end">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($placa->documentos as $documento)
                                                    <tr>
                                                        <td>{{ $documento->nombre }}</td>
                                                        <td>{{ $documento->tipo_texto }}</td>
                                                        <td>{{ $documento->fecha->format('d/m/Y') }}</td>
                                                        <td class="text-end">
                                                            <div class="btn-group btn-group-sm">
                                                                @if($documento->archivo)
                                                                    <a href="{{ asset('storage/'.$documento->archivo) }}" 
                                                                       target="_blank" class="btn btn-outline-info action-btn">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endif
                                                                <button class="btn btn-outline-primary action-btn" data-bs-toggle="modal" 
                                                                    data-bs-target="#modalEditarDocumentoPlaca{{ $documento->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger action-btn" data-bs-toggle="modal" 
                                                                    data-bs-target="#modalEliminarDocumentoPlaca{{ $documento->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted">
                                                            Sin documentos registrados
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('admin.ventas.cotizaciones.proceso.placa.modals.editar-placa', ['placa' => $placa])
                @include('admin.ventas.cotizaciones.proceso.placa.modals.eliminar-placa', ['placa' => $placa])
                @include('admin.ventas.cotizaciones.proceso.placa.modals.nuevo-documento', ['placa' => $placa])
                @include('admin.ventas.cotizaciones.proceso.placa.modals.nuevo-comentario', ['placa' => $placa])

                @foreach($placa->documentos as $documento)
                    @include('admin.ventas.cotizaciones.proceso.placa.modals.editar-documento', ['documento' => $documento])
                    @include('admin.ventas.cotizaciones.proceso.placa.modals.eliminar-documento', ['documento' => $documento])
                @endforeach

                @foreach($placa->comentarios as $comentario)
                    @include('admin.ventas.cotizaciones.proceso.placa.modals.eliminar-comentario', ['placa' => $placa, 'comentario' => $comentario])
                @endforeach
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">No hay placas registradas</p>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNuevaPlaca">
                    <i class="fas fa-plus me-1"></i> Registrar Placa
                </button>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modalNuevaPlaca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <form action="/admin/ventas/cotizaciones/{{ $cotizacion->id }}/placas" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-semibold">Nueva Placa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_placa" class="form-label small fw-semibold">Tipo de Placa</label>
                            <select class="form-select" id="tipo_placa" name="tipo_placa" required>
                                <option value="">Seleccione...</option>
                                <option value="rotativa">Rotativa</option>
                                <option value="definitiva">Definitiva</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="paso_actual" class="form-label small fw-semibold">Estado Actual</label>
                            <select class="form-select" id="paso_actual" name="paso_actual" required>
                                @foreach(PlacaInfo::ESTADOS as $paso => $estado)
                                    <option value="{{ $paso }}">{{ $paso }}. {{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_placa" class="form-label small fw-semibold">Número de Placa</label>
                            <input type="text" class="form-control" id="numero_placa" name="numero_placa" 
                                placeholder="Ej: ABC-123">
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_emision" class="form-label small fw-semibold">Fecha de Emisión</label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision">
                        </div>
                        <div class="col-12">
                            <label for="observaciones" class="form-label small fw-semibold">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>