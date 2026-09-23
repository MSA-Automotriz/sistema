@extends('admin.layouts.app')

@section('title', 'Detalles del Plan de Mantenimiento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="fas fa-wrench mr-2"></i>
                    {{ $planMantenimiento->nombre }}
                    @if(!$planMantenimiento->activo)
                        <span class="badge badge-danger ml-2">Inactivo</span>
                    @endif
                </h2>
                <div>
                    <a href="{{ route('admin.planes-mantenimiento.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver
                    </a>
                    <a href="{{ route('admin.planes-mantenimiento.edit', $planMantenimiento) }}" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Editar
                    </a>
                    <form action="{{ route('admin.planes-mantenimiento.duplicate', $planMantenimiento) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-copy mr-1"></i>
                            Duplicar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información General -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Información General</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">Modelo:</th>
                                    <td>{{ $planMantenimiento->modelo_vehiculo }}</td>
                                </tr>
                                <tr>
                                    <th>Año:</th>
                                    <td>{{ $planMantenimiento->ano_modelo }}</td>
                                </tr>
                                <tr>
                                    <th>Transmisión:</th>
                                    <td>{{ $planMantenimiento->tipo_transmision }}</td>
                                </tr>
                                @if($planMantenimiento->tono_vehiculo)
                                <tr>
                                    <th>Tono:</th>
                                    <td>{{ $planMantenimiento->tono_vehiculo }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Creado por:</th>
                                    <td>{{ $planMantenimiento->usuario->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $planMantenimiento->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">Intervalo Base:</th>
                                    <td>{{ number_format($planMantenimiento->intervalo_base) }} km</td>
                                </tr>
                                <tr>
                                    <th>Km Máximo:</th>
                                    <td>{{ number_format($planMantenimiento->kilometraje_maximo) }} km</td>
                                </tr>
                                <tr>
                                    <th>Relación Hrs/Km:</th>
                                    <td>{{ $planMantenimiento->relacion_horas_km }} hrs por 5000 km</td>
                                </tr>
                                <tr>
                                    <th>Moneda Principal:</th>
                                    <td>{{ $planMantenimiento->moneda_principal == 'USD' ? 'Dólares (USD)' : 'Soles (PEN)' }}</td>
                                </tr>
                                <tr>
                                    <th>Tarifa M.O.:</th>
                                    <td>{{ $planMantenimiento->tarifa_mano_obra > 0 ? number_format($planMantenimiento->tarifa_mano_obra, 2) . ' por hora' : 'No definida' }}</td>
                                </tr>
                                <tr>
                                    <th>Impuestos:</th>
                                    <td>{{ number_format($planMantenimiento->impuestos, 2) }}%</td>
                                </tr>
                            </table>
                        </div>
                        @if($planMantenimiento->descripcion)
                        <div class="col-12 mt-3">
                            <strong>Descripción:</strong>
                            <p class="mt-2">{{ $planMantenimiento->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Resumen</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-number">{{ $planMantenimiento->componentesPlan->count() }}</span>
                                    <span class="info-box-text">Componentes</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-number">{{ count($intervalos) }}</span>
                                    <span class="info-box-text">Intervalos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($planMantenimiento->proveedorPredeterminado)
                    <div class="mt-3">
                        <strong>Proveedor Predeterminado:</strong>
                        <div class="mt-1">
                            <i class="fas fa-building mr-1"></i>
                            {{ $planMantenimiento->proveedorPredeterminado->nombre_completo }}
                        </div>
                    </div>
                    @endif

                    <div class="mt-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" {{ $planMantenimiento->mostrar_precios ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Mostrar precios</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" {{ $planMantenimiento->activo ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Plan activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Componentes del Plan -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Componentes del Plan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Componente</th>
                                    <th>Cantidad</th>
                                    <th>Acción</th>
                                    <th>Proveedor</th>
                                    @if($planMantenimiento->mostrar_precios)
                                        <th>Precio Base</th>
                                    @endif
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planMantenimiento->componentesPlan as $componente)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $componente->parte->nombre }}</div>
                                        @if($componente->parte->codigo)
                                            <small class="text-muted">{{ $componente->parte->codigo }}</small>
                                        @endif
                                        @if($componente->parte->marca)
                                            <br><small class="badge badge-light">{{ $componente->parte->marca }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($componente->cantidad, 2) }} {{ $componente->unidad_medida }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $componente->accion == 'Reemplazar' ? 'danger' : 
                                            ($componente->accion == 'Inspeccionar' ? 'warning' : 'info') 
                                        }}">
                                            {{ $componente->accion_texto }} - {{ $componente->accion }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($componente->proveedor)
                                            {{ $componente->proveedor->nombre_completo }}
                                        @elseif($planMantenimiento->proveedorPredeterminado)
                                            <em class="text-muted">{{ $planMantenimiento->proveedorPredeterminado->nombre_completo }}</em>
                                        @else
                                            <em class="text-muted">Sin definir</em>
                                        @endif
                                    </td>
                                    @if($planMantenimiento->mostrar_precios)
                                        <td>{{ $componente->precio_formateado }}</td>
                                    @endif
                                    <td>
                                        @if($componente->observaciones)
                                            <small>{{ Str::limit($componente->observaciones, 50) }}</small>
                                        @else
                                            <em class="text-muted">Sin observaciones</em>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matriz de Intervalos -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Matriz de Mantenimiento por Intervalos</h4>
                    <small class="text-muted">Programación de componentes según kilometraje</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="min-width: 250px;">Componente</th>
                                    @foreach($intervalos as $km)
                                        <th class="text-center" style="min-width: 100px;">
                                            {{ number_format($km) }}<br>
                                            <small>km</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planMantenimiento->componentesPlan as $componente)
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ $componente->parte->nombre }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $componente->accion_texto }} - {{ number_format($componente->cantidad, 2) }} {{ $componente->unidad_medida }}
                                        </small>
                                    </td>
                                    @foreach($intervalos as $km)
                                        @php
                                            $intervalo = $componente->intervalos->where('kilometraje', $km)->first();
                                        @endphp
                                        <td class="text-center">
                                            @if($intervalo && $intervalo->aplica)
                                                <i class="fas fa-check text-success fa-lg"></i>
                                                @if($intervalo->cantidad_especifica)
                                                    <br><small>{{ number_format($intervalo->cantidad_especifica, 2) }}</small>
                                                @endif
                                                @if($planMantenimiento->mostrar_precios && $intervalo->precio_especifico)
                                                    <br><small class="text-primary">{{ $intervalo->precio_formateado }}</small>
                                                @endif
                                            @else
                                                <i class="fas fa-minus text-muted"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Costos por Intervalo -->
    @if($planMantenimiento->mostrar_precios)
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Resumen de Costos por Intervalo</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kilometraje</th>
                                    <th>Horas Equivalentes</th>
                                    <th>Componentes que Aplican</th>
                                    <th>Costo Componentes</th>
                                    @if($planMantenimiento->tarifa_mano_obra > 0)
                                        <th>Costo Mano de Obra</th>
                                        <th>Subtotal</th>
                                    @endif
                                    <th>Impuestos ({{ $planMantenimiento->impuestos }}%)</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $costoTotal = 0; @endphp
                                @foreach($intervalos as $km)
                                    @php
                                        $horas = ($km * $planMantenimiento->relacion_horas_km) / 5000;
                                        $componentesAplican = 0;
                                        $costoComponentes = 0;
                                        
                                        foreach($planMantenimiento->componentesPlan as $componente) {
                                            $intervalo = $componente->intervalos->where('kilometraje', $km)->first();
                                            if ($intervalo && $intervalo->aplica) {
                                                $componentesAplican++;
                                                $costoComponentes += $intervalo->obtenerPrecioFinal();
                                            }
                                        }
                                        
                                        $costoManoObra = $planMantenimiento->tarifa_mano_obra * $horas;
                                        $subtotal = $costoComponentes + $costoManoObra;
                                        $impuestos = $subtotal * ($planMantenimiento->impuestos / 100);
                                        $totalIntervalo = $subtotal + $impuestos;
                                        $costoTotal += $totalIntervalo;
                                    @endphp
                                    @if($componentesAplican > 0)
                                    <tr>
                                        <td class="font-weight-bold">{{ number_format($km) }} km</td>
                                        <td>{{ number_format($horas, 1) }} hrs</td>
                                        <td>{{ $componentesAplican }} componentes</td>
                                        <td>{{ number_format($costoComponentes, 2) }}</td>
                                        @if($planMantenimiento->tarifa_mano_obra > 0)
                                            <td>{{ number_format($costoManoObra, 2) }}</td>
                                            <td>{{ number_format($subtotal, 2) }}</td>
                                        @endif
                                        <td>{{ number_format($impuestos, 2) }}</td>
                                        <td class="font-weight-bold">{{ number_format($totalIntervalo, 2) }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="{{ $planMantenimiento->tarifa_mano_obra > 0 ? '7' : '5' }}">COSTO TOTAL DEL PLAN</th>
                                    <th>{{ number_format($costoTotal, 2) }} {{ $planMantenimiento->moneda_principal }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .info-box {
        display: block;
        min-height: 70px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        border-radius: .25rem;
        margin-bottom: 15px;
    }

    .info-box-icon {
        border-radius: .25rem 0 0 .25rem;
        color: #fff;
        display: block;
        float: left;
        height: 70px;
        line-height: 70px;
        text-align: center;
        width: 70px;
    }

    .info-box-content {
        padding: 5px 10px;
        margin-left: 70px;
    }

    .info-box-number {
        display: block;
        font-weight: 700;
        font-size: 18px;
    }

    .info-box-text {
        display: block;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bg-info .info-box-icon {
        background-color: #17a2b8;
    }

    .bg-success .info-box-icon {
        background-color: #28a745;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
@endsection