@extends('admin.layouts.app')
@section('title', 'Kardex de Inventario')
@section('header', 'Kardex de Inventario')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px;">
        <div class="card-body text-white p-4">
            <h2 class="mb-2 fw-bold">
                <i class="fas fa-clipboard-list me-3"></i>
                Kardex de Inventario
            </h2>
            <p class="mb-0 opacity-75">Historial detallado de movimientos de inventario</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.inventario.kardex.form') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Inventario</label>
                        <select name="inventario_id" class="form-select">
                            <option value="">Seleccionar inventario</option>
                            @forelse($inventarios as $inventario)
                                <option value="{{ $inventario->id }}" {{ request('inventario_id') == $inventario->id ? 'selected' : '' }}>
                                    {{ $inventario->parte ? $inventario->parte->nombre : ($inventario->vehiculo ? $inventario->vehiculo->marca->nombre . ' ' . $inventario->vehiculo->modelo->nombre : 'N/A') }}
                                </option>
                            @empty
                                <option value="" disabled>No hay inventarios disponibles</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Almacén</label>
                        <select name="almacen_id" class="form-select">
                            <option value="">Todos los almacenes</option>
                            @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Tipo Item</label>
                        <select name="tipo_item" class="form-select">
                            <option value="">Todos</option>
                            <option value="parte" {{ request('tipo_item') == 'parte' ? 'selected' : '' }}>Partes</option>
                            <option value="vehiculo" {{ request('tipo_item') == 'vehiculo' ? 'selected' : '' }}>Vehículos</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Fecha Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.inventario.kardex.form') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Kardex Table -->
    <div class="card border-0 shadow" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="text-black py-3 px-4" style="border: none;">Fecha</th>
                            <th class="text-black py-3 px-4" style="border: none;">Item</th>
                            <th class="text-black py-3 px-4" style="border: none;">Almacén</th>
                            <th class="text-black py-3 px-4" style="border: none;">Movimiento</th>
                            <th class="text-black py-3 px-4 text-center" style="border: none;">Entrada</th>
                            <th class="text-black py-3 px-4 text-center" style="border: none;">Salida</th>
                            <th class="text-black py-3 px-4 text-center" style="border: none;">Stock</th>
                            <th class="text-black py-3 px-4 text-end" style="border: none;">Costo Unit.</th>
                            <th class="text-black py-3 px-4" style="border: none;">Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            {{-- 🔧 SECCIÓN CORREGIDA DE FECHA --}}
                            <td class="py-3 px-4">
                                @php
                                    // Manejar fecha de manera robusta
                                    try {
                                        $fecha = $movimiento->fecha_movimiento;
                                        // Si es string, convertir a Carbon
                                        if (is_string($fecha)) {
                                            $fecha = \Carbon\Carbon::parse($fecha);
                                        }
                                        // Si es null, usar fecha actual
                                        if (!$fecha) {
                                            $fecha = \Carbon\Carbon::now();
                                        }
                                    } catch (\Exception $e) {
                                        // En caso de error, usar fecha actual
                                        $fecha = \Carbon\Carbon::now();
                                    }
                                @endphp
                                <strong>{{ $fecha->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ $fecha->format('H:i') }}</small>
                            </td>
                            {{-- FIN SECCIÓN CORREGIDA --}}
                            
                            <td class="py-3 px-4">
                                @if($movimiento->parte)
                                    <strong>{{ $movimiento->parte->nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $movimiento->parte->codigo }}</small>
                                    <br>
                                    <span class="badge bg-primary">Parte</span>
                                @elseif($movimiento->vehiculo)
                                    <strong>{{ $movimiento->vehiculo->marca->nombre ?? '' }} {{ $movimiento->vehiculo->modelo->nombre ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">VIN: {{ $movimiento->vehiculo->serie_vin }}</small>
                                    <br>
                                    <span class="badge bg-info">Vehículo</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $movimiento->almacen->nombre }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $badgeClass = $movimiento->tipo_movimiento == 'ENTRADA' ? 'success' : 
                                                ($movimiento->tipo_movimiento == 'SALIDA' ? 'danger' : 'warning');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $movimiento->tipo_movimiento }}</span>
                                <br>
                                <small class="text-muted">{{ $movimiento->concepto }}</small>
                            </td>
                            <td class="text-center py-3 px-4">
                                @if($movimiento->cantidad_entrada > 0)
                                    <span class="text-success fw-bold">+{{ number_format($movimiento->cantidad_entrada, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center py-3 px-4">
                                @if($movimiento->cantidad_salida > 0)
                                    <span class="text-danger fw-bold">-{{ number_format($movimiento->cantidad_salida, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center py-3 px-4">
                                <span class="fw-bold">{{ number_format($movimiento->stock_actual, 2) }}</span>
                            </td>
                            <td class="text-end py-3 px-4">
                                @if($movimiento->costo_unitario > 0)
                                    <span class="fw-bold">S/ {{ number_format($movimiento->costo_unitario, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <strong>{{ $movimiento->numero_documento }}</strong>
                                @if($movimiento->observaciones)
                                    <br>
                                    <small class="text-muted">{{ $movimiento->observaciones }}</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                                <h5 class="text-muted">No se encontraron movimientos</h5>
                                <p class="text-muted">Ajusta los filtros para ver los movimientos de kardex</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($movimientos->hasPages())
        <div class="card-footer">
            {{ $movimientos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection