<!-- resources/views/admin/ventas/clientes/partials/table.blade.php -->
<div class="table-responsive">
    <table class="table table-borderless table-hover align-middle mb-0" :class="darkMode ? 'table-dark' : ''">
        <thead class="bg-light bg-opacity-75" style="border-bottom: 1px solid #f1f5f9;">
            <tr>
                <th scope="col" class="text-muted small fw-bold text-uppercase px-4 py-3">#</th>
                <th scope="col" class="text-muted small fw-bold text-uppercase py-3">Cliente</th>
                <th scope="col" class="text-muted small fw-bold text-uppercase py-3">Tipo / Doc</th>
                <th scope="col" class="text-muted small fw-bold text-uppercase py-3">Clasificación</th>
                <th scope="col" class="text-muted small fw-bold text-uppercase py-3 text-end px-4">Opciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $index => $cliente)
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td class="px-4 py-3 text-muted fw-semibold">{{ $clientes->firstItem() + $index }}</td>
                    <td class="py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas {{ $cliente->tipo_cliente == 'natural' ? 'fa-user' : 'fa-building' }}"></i>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">
                                    @if ($cliente->tipo_cliente == 'natural')
                                        {{ $cliente->nombres }} {{ $cliente->apellido_paterno }} {{ $cliente->apellido_materno }}
                                    @else
                                        {{ $cliente->razon_social ?? $cliente->documento_identidad }}
                                    @endif
                                </span>
                                <small class="text-muted fw-semibold">
                                    @if($cliente->correo)
                                        <i class="fas fa-envelope me-1" style="font-size:0.7rem;"></i>{{ $cliente->correo }}
                                    @else
                                        Sin correo
                                    @endif
                                </small>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <div class="d-flex flex-column">
                            <span class="badge {{ $cliente->tipo_cliente == 'natural' ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary' }} rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center" style="width: fit-content;">
                                <span class="d-inline-block rounded-circle {{ $cliente->tipo_cliente == 'natural' ? 'bg-success' : 'bg-primary' }} me-2" style="width: 6px; height: 6px;"></span>
                                {{ ucfirst($cliente->tipo_cliente) }}
                            </span>
                            <small class="text-muted mt-1 fw-bold">{{ $cliente->documento_identidad }}</small>
                        </div>
                    </td>
                    <td class="py-3">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-dark">{{ $cliente->categoria->nombre }}</span>
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-bullhorn me-1" style="font-size:0.7rem;"></i> {{ $cliente->canalCaptacion->nombre }}
                            </small>
                        </div>
                    </td>
                    <td class="text-end px-4 py-3">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('admin.clientes.edit', $cliente) }}" 
                               class="btn btn-sm btn-light rounded-circle shadow-sm me-2 text-primary" 
                               data-bs-toggle="tooltip" 
                               title="Editar Cliente"
                               style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.clientes.destroy', $cliente) }}" method="POST" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de eliminar este cliente?')" 
                                        class="btn btn-sm btn-light rounded-circle shadow-sm text-danger" 
                                        data-bs-toggle="tooltip" 
                                        title="Eliminar Cliente"
                                        style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-users-slash fa-2x text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark">No hay clientes encontrados</h6>
                        <p class="text-muted small mb-3">Intenta ajustar los filtros de búsqueda o agrega un nuevo registro.</p>
                        <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="fas fa-plus me-2"></i> Agregar un nuevo cliente
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>