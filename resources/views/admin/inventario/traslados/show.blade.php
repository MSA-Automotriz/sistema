@extends('admin.layouts.app')

@section('title', 'Detalle del Traslado')

@section('header', 'Detalle del Traslado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 fw-bold mb-0" :class="darkMode ? 'text-light' : 'text-dark'">
                        Traslado #{{ $traslado->id }}
                    </h2>
                    <a href="{{ route('admin.inventario.traslados.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i> Volver
                    </a>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Información del Traslado</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">ID:</dt>
                                    <dd class="col-sm-8">{{ $traslado->id }}</dd>
                                    
                                    <dt class="col-sm-4">Fecha:</dt>
                                    <dd class="col-sm-8">{{ $traslado->fecha_traslado->format('d/m/Y H:i') }}</dd>
                                    
                                    <dt class="col-sm-4">Estado:</dt>
                                    <dd class="col-sm-8">
                                        @if($traslado->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($traslado->estado == 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @elseif($traslado->estado == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </dd>
                                    
                                    <dt class="col-sm-4">Usuario:</dt>
                                    <dd class="col-sm-8">{{ $traslado->usuario->name }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Almacenes</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Origen:</dt>
                                    <dd class="col-sm-8">{{ $traslado->almacenOrigen->nombre }}</dd>
                                    
                                    <dt class="col-sm-4">Destino:</dt>
                                    <dd class="col-sm-8">{{ $traslado->almacenDestino->nombre }}</dd>
                                    
                                    <dt class="col-sm-4">Motivo:</dt>
                                    <dd class="col-sm-8">{{ $traslado->motivo }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Productos Trasladados</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Código</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($traslado->items as $item)
                                        <tr>
                                            <td>{{ $item->tipo_item == 'parte' ? 'Parte/Repuesto' : 'Vehículo' }}</td>
                                            <td>{{ $item->getCodigoItemAttribute() }}</td>
                                            <td>{{ $item->getNombreItemAttribute() }}</td>
                                            <td>{{ number_format($item->cantidad, 2) }}</td>
                                            <td>
                                                @if($item->tipo_item == 'parte')
                                                    {{ $item->parte->unidad->nombre ?? 'N/A' }}
                                                @else
                                                    Unidad
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($traslado->estado == 'pendiente')
                <div class="d-flex justify-content-end mt-4">
                    <form method="POST" action="{{ route('admin.inventario.traslados.cambiar-estado', $traslado) }}" class="me-2">
                        @csrf
                        <input type="hidden" name="estado" value="completado">
                        <button type="submit" class="btn btn-success" onclick="return confirm('¿Confirmar traslado como completado?')">
                            <i class="fa fa-check me-2"></i> Completar Traslado
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.inventario.traslados.cambiar-estado', $traslado) }}">
                        @csrf
                        <input type="hidden" name="estado" value="cancelado">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar este traslado? Esta acción revertirá los movimientos de inventario.')">
                            <i class="fa fa-times me-2"></i> Cancelar Traslado
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection