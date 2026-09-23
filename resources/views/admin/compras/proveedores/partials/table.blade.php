<style>
/* Asegurar que los modales estén encima del overlay */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1059 !important;
}

.modal.show {
    display: block;
    z-index: 1060 !important;
}
</style>

<div class="table-responsive" id="proveedores-table">
    <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
        <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
            <tr>
                <th scope="col" class="text-uppercase small">#</th>
                <th scope="col" class="text-uppercase small">Tipo Doc.</th>
                <th scope="col" class="text-uppercase small">Nro. Doc.</th>
                <th scope="col" class="text-uppercase small">Proveedor</th>
                <th scope="col" class="text-uppercase small">Categoría</th>
                <th scope="col" class="text-uppercase small">Correos</th>
                <th scope="col" class="text-uppercase small">Contactos</th>
                <th scope="col" class="text-uppercase small">Cuentas</th>
                <th scope="col" class="text-uppercase small text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proveedores as $index => $proveedor)
                <tr>
                    <td>{{ $proveedores->firstItem() + $index }}</td>
                    <td>{{ $proveedor->tipo_documento }}</td>
                    <td>{{ $proveedor->numero_documento }}</td>
                    <td>
                        @if ($proveedor->tipo_documento == 'DNI')
                            {{ $proveedor->apellido_paterno }} {{ $proveedor->apellido_materno }}, {{ $proveedor->nombres }}
                        @else
                            {{ $proveedor->razon_social }}
                        @endif
                    </td>
                    <td>{{ $proveedor->categoriaProveedor->nombre_categoria_proveedor }}</td>
                    <td>
                        @foreach ($proveedor->correos as $correo)
                            <span class="badge bg-primary">{{ $correo->correo }}</span>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($proveedor->contactos as $contacto)
                            <span class="badge bg-secondary">{{ $contacto->nombre }} ({{ $contacto->telefono }})</span>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($proveedor->cuentas as $cuenta)
                            <span class="badge bg-info">{{ $cuenta->banco->nombre }} - {{ $cuenta->numero_cuenta }}</span>
                        @endforeach
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cuentaModal{{ $proveedor->id }}" title="Gestionar Cuentas">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                            <a href="{{ route('admin.compras.proveedores.edit', $proveedor) }}" class="btn btn-outline-warning" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.compras.proveedores.destroy', $proveedor) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este proveedor?')" class="btn btn-outline-danger" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-5 text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 3rem; width: 3rem;" class="text-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-muted mb-2">No hay proveedores registrados</p>
                            <a href="{{ route('admin.compras.proveedores.create') }}" class="btn btn-sm btn-link text-decoration-none">
                                Agregar un nuevo proveedor
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>