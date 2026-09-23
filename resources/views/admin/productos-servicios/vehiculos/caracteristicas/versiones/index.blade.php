<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
    <div class="mb-3 mb-sm-0">
        <h2 class="h4 fw-bold mb-1 text-dark">
            Versiones Disponibles: <span class="text-primary">{{ $versiones->total() }}</span>
        </h2>
        <p class="text-muted small mb-0">Especificaciones técnicas y variantes por modelo</p>
    </div>
    <div>
        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
            <i class="fas fa-plus me-2"></i> Agregar Versión
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
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Marca/Modelo</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Versión</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Especificaciones</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Transmisión</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Combustible</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($versiones as $index => $version)
                <tr>
                    <td class="px-4 py-3">{{ $versiones->firstItem() + $index }}</td>
                    <td class="px-4 py-3">
                        <div class="fw-bold">{{ $version->marca->nombre }}</div>
                        <div class="text-muted small">{{ $version->modelo->nombre }}</div>
                    </td>
                    <td class="px-4 py-3 fw-bold text-primary">{{ $version->nombre }}</td>
                    <td class="px-4 py-3">
                        <div class="small">
                            <span class="text-muted">Carr:</span> {{ $version->carroceria }}<br>
                            <span class="text-muted">Cil:</span> {{ $version->cilindrada }} | 
                            <span class="text-muted">Trac:</span> {{ $version->traccion }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-light text-dark border rounded-pill px-3">{{ $version->transmision }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $version->combustible->nombre }}</span>
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.edit', $version) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.versiones.destroy', $version) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta versión?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
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
                            <i class="fas fa-code-branch text-muted fa-3x"></i>
                        </div>
                        <h5 class="text-dark fw-bold">No hay versiones registradas</h5>
                        <p class="text-muted mb-0">Crea variaciones técnicas para tus modelos</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $versiones->links() }}
</div>