<!-- Modales para gestionar cuentas -->
@foreach ($proveedores as $proveedor)
    <div class="modal fade" id="cuentaModal{{ $proveedor->id }}" tabindex="-1" aria-labelledby="cuentaModalLabel{{ $proveedor->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cuentaModalLabel{{ $proveedor->id }}">Gestionar Cuentas Bancarias</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Sección izquierda: Listado de cuentas -->
                        <div class="col-md-6 border-end">
                            <div class="mb-3">
                                <h6 class="text-muted fw-semibold">
                                    Proveedor:
                                    @if ($proveedor->tipo_documento == 'DNI')
                                        {{ $proveedor->apellido_paterno }} {{ $proveedor->apellido_materno }}, {{ $proveedor->nombres }}
                                    @else
                                        {{ $proveedor->razon_social }}
                                    @endif
                                </h6>
                            </div>
                            <div class="list-group" id="cuentas-list-{{ $proveedor->id }}">
                                @forelse ($proveedor->cuentas as $cuenta)
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-cuenta-id="{{ $cuenta->id }}">
                                        <div>
                                            <strong>{{ $cuenta->banco->nombre }}</strong> - {{ $cuenta->numero_cuenta }} ({{ $cuenta->moneda }})
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-warning me-1 edit-cuenta-btn" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.compras.proveedores.cuentas.destroy', [$proveedor, $cuenta]) }}" method="POST" class="d-inline delete-cuenta-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">No hay cuentas registradas.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Sección derecha: Formulario para agregar/editar cuenta -->
                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-3" id="form-title-{{ $proveedor->id }}">Agregar Nueva Cuenta</h6>
                            <form method="POST" action="{{ route('admin.compras.proveedores.cuentas.store', $proveedor) }}" class="needs-validation cuenta-form" novalidate id="cuenta-form-{{ $proveedor->id }}">
                                @csrf
                                <input type="hidden" name="_method" id="form-method-{{ $proveedor->id }}" value="POST">
                                <input type="hidden" name="cuenta_id" id="cuenta-id-{{ $proveedor->id }}">
                                <div class="mb-3">
                                    <label for="banco_id_{{ $proveedor->id }}" class="form-label small text-muted">Banco *</label>
                                    <select name="banco_id" id="banco_id_{{ $proveedor->id }}" class="form-select form-select-sm @error('banco_id') is-invalid @enderror" required>
                                        <option value="">Seleccione un banco</option>
                                        @foreach ($bancos as $banco)
                                            <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('banco_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">¿No encuentras el banco? <a href="{{ route('admin.configuracion.maestros.bancos.create') }}" target="_blank">Agrega uno nuevo</a></small>
                                </div>
                                <div class="mb-3">
                                    <label for="moneda_{{ $proveedor->id }}" class="form-label small text-muted">Moneda *</label>
                                    <select name="moneda" id="moneda_{{ $proveedor->id }}" class="form-select form-select-sm @error('moneda') is-invalid @enderror" required>
                                        <option value="">Seleccione una moneda</option>
                                        <option value="Soles">Soles</option>
                                        <option value="Dólares">Dólares</option>
                                    </select>
                                    @error('moneda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="tipo_cuenta_{{ $proveedor->id }}" class="form-label small text-muted">Tipo de Cuenta *</label>
                                    <select name="tipo_cuenta" id="tipo_cuenta_{{ $proveedor->id }}" class="form-select form-select-sm @error('tipo_cuenta') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="Ahorros">Ahorros</option>
                                        <option value="Corriente">Corriente</option>
                                    </select>
                                    @error('tipo_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="numero_cuenta_{{ $proveedor->id }}" class="form-label small text-muted">Número de Cuenta *</label>
                                    <input type="text" name="numero_cuenta" id="numero_cuenta_{{ $proveedor->id }}" class="form-control form-control-sm @error('numero_cuenta') is-invalid @enderror" required>
                                    @error('numero_cuenta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="cci_{{ $proveedor->id }}" class="form-label small text-muted">CCI *</label>
                                    <input type="text" name="cci" id="cci_{{ $proveedor->id }}" class="form-control form-control-sm @error('cci') is-invalid @enderror" required>
                                    @error('cci') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm reset-form-btn">Cancelar</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
