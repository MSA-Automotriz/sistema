<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
    <div class="mb-3 mb-sm-0">
        <h2 class="h4 fw-bold mb-1 text-dark">
            Años de Modelo: <span class="text-primary">{{ $aniosModelo->total() }}</span>
        </h2>
        <p class="text-muted small mb-0">Gestión de periodos de fabricación y precios referenciales</p>
    </div>
    <div>
        <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition hover:scale-105 border-0">
            <i class="fas fa-plus me-2"></i> Agregar Año
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
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small">Vehículo</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Año</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-center">Precio Ref.</th>
                <th scope="col" class="py-3 px-4 border-0 text-uppercase small text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($aniosModelo as $index => $anioModelo)
                <tr>
                    <td class="px-4 py-3">{{ $aniosModelo->firstItem() + $index }}</td>
                    <td class="px-4 py-3">
                        <div class="fw-bold">{{ $anioModelo->marca->nombre }} {{ $anioModelo->modelo->nombre }}</div>
                        <div class="text-muted small">{{ $anioModelo->version->nombre }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $anioModelo->anio }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="fw-bold text-success">
                            {{ number_format($anioModelo->precio, 2) }} <small>{{ $anioModelo->moneda }}</small>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <a href="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.edit', $anioModelo) }}" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-warning hover:text-white" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.productos-servicios.vehiculos.caracteristicas.anios-modelo.destroy', $anioModelo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este año de modelo?')" class="btn btn-white btn-sm border-0 px-3 transition hover:bg-danger hover:text-white" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-5 text-center">
                        <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                            <i class="fas fa-calendar-alt text-muted fa-3x"></i>
                        </div>
                        <h5 class="text-dark fw-bold">No hay años registrados</h5>
                        <p class="text-muted mb-0">Define los años de modelo y sus precios base</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $aniosModelo->links() }}
</div>