<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
    <div class="mb-3 mb-sm-0">
        <h2 class="h4 fw-bold mb-1 text-dark">
            Modelos Disponibles: <span class="text-primary">{{ $modelos->total() }}</span>
        </h2>
        <p class="text-muted small mb-0">Gestión de líneas y modelos de vehículos</p>
    </div>
    <div>
        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
            <i class="fas fa-plus me-2"></i> Agregar Modelo
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">#</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Marca</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Modelo</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Garantía</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Años Calidad</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Ficha Técnica</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($modelos as $index => $modelo)
                <tr>
                    <td class="px-4 py-3">{{ $modelos->firstItem() + $index }}</td>
                    <td class="px-4 py-3 fw-bold">{{ $modelo->marca->nombre }}</td>
                    <td class="px-4 py-3 text-primary">{{ $modelo->nombre }}</td>
                    <td class="px-4 py-3 text-center small text-muted">{{ $modelo->duracion_garantia ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ $modelo->cantidad_anos }} años</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if ($modelo->ficha_tecnica)
                            <a href="{{ asset('storage/' . $modelo->ficha_tecnica) }}" target="_blank" class="btn btn-link btn-sm text-decoration-none">
                                <i class="fas fa-file-pdf text-danger me-1"></i> Ver
                            </a>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.edit', $modelo) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.modelos.destroy', $modelo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este modelo?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-5 text-center">
                        <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                            <i class="fas fa-tags text-muted fa-3x"></i>
                        </div>
                        <h5 class="text-dark fw-bold">No hay modelos registrados</h5>
                        <p class="text-muted mb-0">Registra los modelos para cada marca</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $modelos->links() }}
</div>