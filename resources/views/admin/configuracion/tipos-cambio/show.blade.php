@extends('admin.layouts.app')

@section('title', 'Ver Tipo de Cambio')

@push('styles')
<style>
    .info-card {
        border: none;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        border-radius: 0.75rem;
    }
    
    .info-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.75rem 0.75rem 0 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    
    .badge-origen-sunat {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }
    
    .badge-origen-manual {
        background: linear-gradient(45deg, #6c757d, #495057);
        color: white;
    }
    
    .tipo-cambio-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalles del Tipo de Cambio</h1>
            <p class="text-muted mb-0">Información completa del tipo de cambio USD-PEN</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.configuracion.tipos-cambio.edit', $tipoCambio) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
            <a href="{{ route('admin.configuracion.tipos-cambio.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card info-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Fecha del Tipo de Cambio</div>
                            <div class="info-value">{{ $tipoCambio->fecha->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Origen</div>
                            <div class="info-value">
                                @if($tipoCambio->origen === 'sunat')
                                    <span class="badge badge-origen-sunat">
                                        <i class="fas fa-building me-1"></i>SUNAT
                                    </span>
                                @else
                                    <span class="badge badge-origen-manual">
                                        <i class="fas fa-user-edit me-1"></i>Manual
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Fecha Inicio Vigencia</div>
                            <div class="info-value">{{ $tipoCambio->fecha_inicio->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Fecha Fin Vigencia</div>
                            <div class="info-value">
                                {{ $tipoCambio->fecha_fin ? $tipoCambio->fecha_fin->format('d/m/Y') : 'Sin fecha límite' }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                @if($tipoCambio->activo)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-pause me-1"></i>Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Creado por</div>
                            <div class="info-value">
                                <i class="fas fa-user me-1"></i>{{ $tipoCambio->usuario->name ?? 'Usuario no disponible' }}
                            </div>
                        </div>
                    </div>

                    @if($tipoCambio->observaciones)
                    <div class="row">
                        <div class="col-12">
                            <div class="info-label">Observaciones</div>
                            <div class="info-value">
                                <div class="bg-light p-3 rounded">
                                    {{ $tipoCambio->observaciones }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="info-label">Fecha de Registro</div>
                            <div class="info-value text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $tipoCambio->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card tipo-cambio-display mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3 text-center">
                        <i class="fas fa-dollar-sign me-2"></i>Tipo de Cambio USD/PEN
                    </h6>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                <small class="opacity-75">COMPRA</small>
                            </div>
                            <div class="h4 mb-0">
                                S/ {{ number_format($tipoCambio->compra, 4) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <small class="opacity-75">VENTA</small>
                            </div>
                            <div class="h4 mb-0">
                                S/ {{ number_format($tipoCambio->venta, 4) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Historial
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Creado</small>
                        <div>{{ $tipoCambio->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($tipoCambio->updated_at != $tipoCambio->created_at)
                    <div>
                        <small class="text-muted">Última modificación</small>
                        <div>{{ $tipoCambio->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection