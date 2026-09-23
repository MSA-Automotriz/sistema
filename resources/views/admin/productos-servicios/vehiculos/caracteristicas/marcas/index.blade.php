<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
    <div class="mb-3 mb-sm-0">
        <h2 class="h4 fw-bold mb-1 text-dark">
            Marcas Registradas: <span class="text-primary">{{ $marcas->total() }}</span>
        </h2>
        <p class="text-muted small mb-0">Gestión de fabricantes y marcas comerciales</p>
    </div>
    <div>
        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.marcas.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
            <i class="fas fa-plus me-2"></i> Agregar Marca
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
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Nombre de la Marca</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($marcas as $index => $marca)
                <tr>
                    <td class="px-4 py-3">{{ $marcas->firstItem() + $index }}</td>
                    <td class="px-4 py-3 fw-bold text-primary">{{ $marca->nombre }}</td>
                    <td class="px-4 py-3 text-end">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.marcas.edit', $marca) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.marcas.destroy', $marca) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta marca?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-5 text-center">
                        <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                            <i class="fas fa-copyright text-muted fa-3x"></i>
                        </div>
                        <h5 class="text-dark fw-bold">No hay marcas registradas</h5>
                        <p class="text-muted mb-0">Registra las marcas de los vehículos que manejas</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $marcas->links() }}
</div>
