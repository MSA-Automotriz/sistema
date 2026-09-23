@extends('admin.layouts.app')

@section('title', 'Editar Vale de Devolución')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Editar Vale de Devolución</h5>
                    <div>
                        <a href="{{ route('admin.devoluciones.show', $devolucion->id) }}" class="btn btn-sm btn-info me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                            Ver
                        </a>
                        <a href="{{ route('admin.devoluciones.index') }}" class="btn btn-sm btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.devoluciones.update', $devolucion->id) }}" method="POST" id="form-devolucion">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numero" class="form-label">Número de Vale</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="{{ $devolucion->numero }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" name="fecha" value="{{ old('fecha', $devolucion->fecha) }}" required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-control @error('proveedor_id') is-invalid @enderror" 
                                            id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" 
                                                {{ old('proveedor_id', $devolucion->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="motivo" class="form-label">Motivo de Devolución <span class="text-danger">*</span></label>
                                    <select class="form-control @error('motivo') is-invalid @enderror" 
                                            id="motivo" name="motivo" required>
                                        <option value="">Seleccione el motivo</option>
                                        <option value="producto_defectuoso" {{ old('motivo', $devolucion->motivo) == 'producto_defectuoso' ? 'selected' : '' }}>Producto defectuoso</option>
                                        <option value="producto_incorrecto" {{ old('motivo', $devolucion->motivo) == 'producto_incorrecto' ? 'selected' : '' }}>Producto incorrecto</option>
                                        <option value="exceso_inventario" {{ old('motivo', $devolucion->motivo) == 'exceso_inventario' ? 'selected' : '' }}>Exceso de inventario</option>
                                        <option value="otros" {{ old('motivo', $devolucion->motivo) == 'otros' ? 'selected' : '' }}>Otros</option>
                                    </select>
                                    @error('motivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              id="observaciones" name="observaciones" rows="3" 
                                              placeholder="Ingrese observaciones adicionales...">{{ old('observaciones', $devolucion->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Productos existentes -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">Productos en la Devolución</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Código</th>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                                <th>Motivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($devolucion->detalles as $index => $detalle)
                                                <tr>
                                                    <td>{{ $detalle->nombre_producto }}</td>
                                                    <td>{{ $detalle->codigo_producto }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ ucfirst($detalle->tipo_producto) }}</span>
                                                    </td>
                                                    <td>{{ number_format($detalle->cantidad, 2) }}</td>
                                                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                                                    <td>{{ $detalle->motivo_detalle ?: '-' }}</td>
                                                </tr>
                                                <!-- Hidden inputs para mantener los productos existentes -->
                                                <input type="hidden" name="productos[{{ $index }}][id]" value="{{ $detalle->producto_id }}">
                                                <input type="hidden" name="productos[{{ $index }}][tipo]" value="{{ $detalle->tipo_producto }}">
                                                <input type="hidden" name="productos[{{ $index }}][cantidad]" value="{{ $detalle->cantidad }}">
                                                <input type="hidden" name="productos[{{ $index }}][precio]" value="{{ $detalle->precio_unitario }}">
                                                <input type="hidden" name="productos[{{ $index }}][motivo]" value="{{ $detalle->motivo_detalle }}">
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-muted small mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Para modificar los productos, deberá crear un nuevo vale de devolución.
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.devoluciones.show', $devolucion->id) }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Vale</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection