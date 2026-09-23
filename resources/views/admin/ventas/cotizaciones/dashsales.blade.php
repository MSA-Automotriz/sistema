@extends('admin.layouts.app')

@section('title', 'Dashboard de Ventas')

@section('header')
@endsection

@section('content')
<div class="dashboard-hero" style="padding: 2rem 2rem; border-radius: 0 0 1.5rem 1.5rem; margin-bottom: 2.5rem;">
    <div class="hero-glow-alt" style="top: -50px; right: 0; filter: blur(60px); opacity: 0.2;"></div>
    <div class="container-fluid position-relative z-1">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <div class="d-inline-flex align-items-center px-3 py-1 bg-white bg-opacity-10 rounded-pill fs-6 mb-3 border border-white border-opacity-25 backdrop-blur">
                    <i class="fas fa-chart-pie text-info me-2"></i> Gestión Comercial
                </div>
                <h2 class="fw-bold mb-1 tracking-tight text-white display-6 d-flex align-items-center">Panel de Oportunidades y Ventas</h2>
                <p class="text-white-50 mb-0">Visualiza, crea y gestiona oportunidades, cotizaciones y ventas.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group" style="width: auto;">
                    <input type="text" class="form-control border-0 bg-white bg-opacity-75" id="search-global" placeholder="Buscar..." style="border-radius: 50rem 0 0 50rem;">
                    <button class="btn btn-light dropdown-toggle bg-white bg-opacity-75 border-0" type="button" data-bs-toggle="dropdown" style="border-radius: 0 50rem 50rem 0;">
                        Filtrar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">
                        <li><a class="dropdown-item filter-option py-2" data-filter="all" href="#">Todos</a></li>
                        <li><a class="dropdown-item filter-option py-2" data-filter="recent" href="#">Recientes (7 días)</a></li>
                        <li><a class="dropdown-item filter-option py-2" data-filter="mine" href="#">Mis elementos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item filter-option py-2" data-filter="opportunities" href="#">Solo oportunidades</a></li>
                        <li><a class="dropdown-item filter-option py-2" data-filter="quotes" href="#">Solo cotizaciones</a></li>
                    </ul>
                </div>
                <!--button class="btn bg-white bg-opacity-10 text-white rounded-pill px-4 py-2 fw-bold border border-white border-opacity-25 backdrop-blur transition hover:scale-105 ms-2" data-bs-toggle="modal" data-bs-target="#modalColumnaPersonalizada">
                    <i class="fas fa-columns me-2 text-info"></i> Columna
                </button-->
                <div class="dropdown">
                    <button class="btn bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 ms-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border: 1px solid rgba(255,255,255,0.8);">
                        <i class="fas fa-plus me-2 text-primary"></i> Crear
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">
                        <li><a class="dropdown-item py-2 fw-semibold" href="#" data-bs-toggle="modal" data-bs-target="#modalCrearOportunidad"><i class="fas fa-lightbulb text-warning me-2 bg-warning bg-opacity-10 p-2 rounded-lg"></i>Nueva Oportunidad</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('admin.ventas.cotizaciones.create') }}"><i class="fas fa-file-invoice text-primary me-2 bg-primary bg-opacity-10 p-2 rounded-lg"></i>Nueva Cotización</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-4 position-relative sales-dashboard-container" style="top: -3.5rem; z-index: 10;">

    <!-- Dashboard Tabs -->
    <ul class="nav nav-tabs mb-3" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pipeline-tab" data-bs-toggle="tab" data-bs-target="#pipeline" type="button" role="tab" aria-controls="pipeline" aria-selected="true">
                Pipeline de Ventas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">
                Estadísticas
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabsContent">
        <!-- Pipeline Tab -->
        <div class="tab-pane fade show active" id="pipeline" role="tabpanel" aria-labelledby="pipeline-tab">
            <!-- Columnas de Kanban -->
            <div class="kanban-board">
                <div class="row flex-nowrap overflow-auto pb-3" id="kanban-columns">
                    <!-- Columna de Oportunidades (siempre presente) -->
                    <div class="col-12 col-md-3 kanban-column" data-column-type="opportunities">
                        <div class="card h-100">
                            <div class="card-header bg-info bg-opacity-25 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    Oportunidades
                                    <span class="badge bg-info rounded-pill ms-2">
                                        {{ $oportunidades->count() }}
                                    </span>
                                </h6>
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalCrearOportunidad">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="card-body p-2 kanban-items-container" data-droppable="opportunities">
                                @if($oportunidades->isEmpty())
                                    <div class="text-center p-4 text-muted">
                                        <i class="fas fa-lightbulb fa-2x mb-3"></i>
                                        <p class="mb-0">No hay oportunidades registradas</p>
                                    </div>
                                @else
                                    @foreach($oportunidades as $oportunidad)
                                        <div class="card mb-2 kanban-item opportunity-item" 
                                             data-id="{{ $oportunidad->id }}" 
                                             data-user-id="{{ $oportunidad->user_id }}"
                                             data-date="{{ $oportunidad->created_at->format('Y-m-d') }}"
                                             draggable="true">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="badge bg-info">OP-{{ $oportunidad->id }} <i class="fas fa-calendar me-1"></i> {{ $oportunidad->created_at->format('d/m/Y') }}</span>
                                                    <span class="text-primary">
                                                        <i class="fas fa-thermometer-half"></i> {{ $oportunidad->probabilidad }}%
                                                    </span>
                                                </div>
                                                <!--h6 class="font-weight-bold mb-1">{{ $oportunidad->titulo }}</h6-->
                                                <p class="small text-muted mb-2">
                                                    <i class="fas fa-user me-1"></i> 
                                                    @if($oportunidad->cliente->tipo_cliente === 'natural')
                                                        {{ $oportunidad->cliente->nombres }} {{ $oportunidad->cliente->apellido_paterno }}
                                                    @else
                                                        {{ $oportunidad->cliente->razon_social }}
                                                    @endif
                                                    <br>
                                                </p>
                                                @if($oportunidad->seguimientos->count() > 0)
                                                    <div class="border-top pt-2 mt-2">
                                                        <p class="small mb-1 fw-semibold">Últimos seguimientos:</p>
                                                        @foreach($oportunidad->seguimientos->take(2) as $seguimiento)
                                                            <div class="d-flex align-items-start small mb-1 seguimiento-item">
                                                                @if($seguimiento->tipo === 'nota')
                                                                    <i class="fas fa-sticky-note text-warning me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'llamada')
                                                                    <i class="fas fa-phone text-success me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'reunion')
                                                                    <i class="fas fa-handshake text-primary me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'email')
                                                                    <i class="fas fa-envelope text-info me-2 mt-1"></i>
                                                                @else
                                                                    <i class="fas fa-comment text-secondary me-2 mt-1"></i>
                                                                @endif
                                                                <div class="text-truncate" style="max-width: 180px;" title="{{ $seguimiento->contenido }}">
                                                                    {{ $seguimiento->contenido }}
                                                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $seguimiento->fecha_seguimiento->format('d/m/Y H:i') }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                                    <button class="btn btn-sm btn-outline-primary view-opportunity-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalVerOportunidad"
                                                            data-id="{{ $oportunidad->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success convert-to-quote-btn"
                                                            data-id="{{ $oportunidad->id }}"
                                                            data-cliente-id="{{ $oportunidad->cliente_id }}">
                                                        <i class="fas fa-file-invoice"></i> Cotizar
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary add-note-btn"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalSeguimiento"
                                                            data-id="{{ $oportunidad->id }}"
                                                            data-type="oportunidad"
                                                            data-titulo="{{ $oportunidad->titulo }}">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Columna de Cotizaciones (siempre presente) -->
                    <div class="col-12 col-md-3 kanban-column" data-column-type="quotes" data-estado-id="1">
                        <div class="card h-100">
                            <div class="card-header bg-primary bg-opacity-25 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    Cotizaciones Nuevas
                                    <span class="badge bg-primary rounded-pill ms-2">
                                        {{ isset($cotizacionesPorEstado[1]) ? $cotizacionesPorEstado[1]['cotizaciones']->count() : 0 }}
                                    </span>
                                </h6>
                                <a href="{{ route('admin.ventas.cotizaciones.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                            <div class="card-body p-2 kanban-items-container" data-droppable="quotes">
                                @if(!isset($cotizacionesPorEstado[1]) || $cotizacionesPorEstado[1]['cotizaciones']->isEmpty())
                                    <div class="text-center p-4 text-muted">
                                        <i class="fas fa-file-invoice fa-2x mb-3"></i>
                                        <p class="mb-0">No hay cotizaciones nuevas</p>
                                    </div>
                                @else
                                    @foreach($cotizacionesPorEstado[1]['cotizaciones'] as $cotizacion)
                                        <div class="card mb-2 kanban-item quote-item" 
                                             data-cotizacion-id="{{ $cotizacion->id }}" 
                                             data-user-id="{{ $cotizacion->user_id }}"
                                             data-date="{{ $cotizacion->created_at->format('Y-m-d') }}"
                                             draggable="true">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="badge bg-primary">{{ $cotizacion->codigo }}</span>
                                                    <span class="text-{{ $cotizacion->moneda === 'Soles' ? 'success' : 'primary' }} fw-bold">
                                                        {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($cotizacion->total, 2) }}
                                                    </span>
                                                </div>
                                                <h6 class="font-weight-bold mb-1">
                                                    @if($cotizacion->cliente->tipo_cliente === 'natural')
                                                        {{ $cotizacion->cliente->nombres }} {{ $cotizacion->cliente->apellido_paterno }}
                                                    @else
                                                        {{ $cotizacion->cliente->razon_social }}
                                                    @endif
                                                </h6>
                                                <p class="small text-muted mb-2">
                                                    <i class="fas fa-car me-1"></i> {{ $cotizacion->detalles->count() }} vehículo(s)
                                                    <br>
                                                    <i class="fas fa-user me-1"></i> {{ $cotizacion->usuario->name }}
                                                    <br>
                                                    <i class="fas fa-calendar me-1"></i> {{ $cotizacion->created_at->format('d/m/Y') }}
                                                </p>
                                                @if($cotizacion->seguimientos->count() > 0)
                                                    <div class="border-top pt-2 mt-2">
                                                        <p class="small mb-1 fw-semibold">Últimos seguimientos:</p>
                                                        @foreach($cotizacion->seguimientos->take(2) as $seguimiento)
                                                            <div class="d-flex align-items-start small mb-1 seguimiento-item">
                                                                @if($seguimiento->tipo === 'nota')
                                                                    <i class="fas fa-sticky-note text-warning me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'llamada')
                                                                    <i class="fas fa-phone text-success me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'reunion')
                                                                    <i class="fas fa-handshake text-primary me-2 mt-1"></i>
                                                                @elseif($seguimiento->tipo === 'email')
                                                                    <i class="fas fa-envelope text-info me-2 mt-1"></i>
                                                                @else
                                                                    <i class="fas fa-comment text-secondary me-2 mt-1"></i>
                                                                @endif
                                                                <div class="text-truncate" style="max-width: 180px;" title="{{ $seguimiento->contenido }}">
                                                                    {{ $seguimiento->contenido }}
                                                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $seguimiento->fecha_seguimiento->format('d/m/Y H:i') }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                                    <a href="{{ route('admin.ventas.cotizaciones.show', $cotizacion) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-success add-seguimiento-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#modalSeguimiento"
                                                            data-id="{{ $cotizacion->id }}"
                                                            data-type="cotizacion"
                                                            data-cotizacion-codigo="{{ $cotizacion->codigo }}"
                                                            data-cliente="{{ $cotizacion->cliente->tipo_cliente === 'natural' ? $cotizacion->cliente->nombres . ' ' . $cotizacion->cliente->apellido_paterno : $cotizacion->cliente->razon_social }}">
                                                        <i class="fas fa-plus"></i> Seguimiento
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @foreach($cotizacionesPorEstado as $otroEstadoData)
                                                                @if($otroEstadoData['estado']->id != 1)
                                                                    <li>
                                                                        <form action="{{ route('admin.ventas.cotizaciones.cambiar-estado', $cotizacion) }}" method="POST" class="cambiar-estado-form">
                                                                            @csrf
                                                                            <input type="hidden" name="estado_id" value="{{ $otroEstadoData['estado']->id }}">
                                                                            <button type="submit" class="dropdown-item">
                                                                                <span class="badge bg-{{ $otroEstadoData['estado']->color }} me-2"></span>
                                                                                Mover a {{ $otroEstadoData['estado']->nombre }}
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Separador/Título de Sección de Ventas -->
                    <!--div class="col-12 col-md-3 kanban-column section-divider" data-column-type="section-title">
                        <div class="card h-100 bg-light border-0">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <h5 class="text-uppercase mb-0 fw-bold text-secondary">Proceso de Ventas</h5>
                                    <p class="text-muted small mb-0">Estados de negociación personalizables</p>
                                    <button class="btn btn-sm btn-outline-secondary mt-3" data-bs-toggle="modal" data-bs-target="#modalColumnaPersonalizada">
                                        <i class="fas fa-plus"></i> Agregar Estado
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div-->

                    <!-- Columnas dinámicas de estados de cotización -->
                    @foreach($cotizacionesPorEstado as $estadoData)
                        @if($estadoData['estado']->id != 1)
                            <div class="col-12 col-md-3 kanban-column" data-estado-id="{{ $estadoData['estado']->id }}" data-column-type="sales-stage">
                                <div class="card h-100">
                                    <div class="card-header bg-{{ $estadoData['estado']->color }} bg-opacity-25 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">
                                            {{ $estadoData['estado']->nombre }}
                                            <span class="badge bg-{{ $estadoData['estado']->color }} rounded-pill ms-2">
                                                {{ $estadoData['cotizaciones']->count() }}
                                            </span>
                                        </h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item edit-column-btn" href="#" data-bs-toggle="modal" data-bs-target="#modalColumnaPersonalizada" data-column-id="{{ $estadoData['estado']->id }}" data-nombre="{{ $estadoData['estado']->nombre }}" data-color="{{ $estadoData['estado']->color }}">Editar columna</a></li>
                                                <li><a class="dropdown-item delete-column-btn" href="#" data-column-id="{{ $estadoData['estado']->id }}">Eliminar columna</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body p-2 kanban-items-container" data-droppable="sales-stage">
                                        @if($estadoData['cotizaciones']->isEmpty())
                                            <div class="text-center p-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                                <p class="mb-0">No hay cotizaciones en este estado</p>
                                            </div>
                                        @else
                                            @foreach($estadoData['cotizaciones'] as $cotizacion)
                                                <div class="card mb-2 kanban-item quote-item" 
                                                     data-cotizacion-id="{{ $cotizacion->id }}" 
                                                     data-user-id="{{ $cotizacion->user_id }}"
                                                     data-date="{{ $cotizacion->created_at->format('Y-m-d') }}"
                                                     draggable="true">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="badge bg-{{ $estadoData['estado']->color }}">{{ $cotizacion->codigo }}</span>
                                                            <span class="text-{{ $cotizacion->moneda === 'Soles' ? 'success' : 'primary' }} fw-bold">
                                                                {{ $cotizacion->moneda === 'Soles' ? 'S/ ' : 'US$ ' }}{{ number_format($cotizacion->total, 2) }}
                                                            </span>
                                                        </div>
                                                        <h6 class="font-weight-bold mb-1">
                                                            @if($cotizacion->cliente->tipo_cliente === 'natural')
                                                                {{ $cotizacion->cliente->nombres }} {{ $cotizacion->cliente->apellido_paterno }}
                                                            @else
                                                                {{ $cotizacion->cliente->razon_social }}
                                                            @endif
                                                        </h6>
                                                        <p class="small text-muted mb-2">
                                                            <i class="fas fa-car me-1"></i> {{ $cotizacion->detalles->count() }} vehículo(s)
                                                            <br>
                                                            <i class="fas fa-user me-1"></i> {{ $cotizacion->usuario->name }}
                                                            <br>
                                                            <i class="fas fa-calendar me-1"></i> {{ $cotizacion->created_at->format('d/m/Y') }}
                                                        </p>
                                                        @if($cotizacion->seguimientos->count() > 0)
                                                            <div class="border-top pt-2 mt-2">
                                                                <p class="small mb-1 fw-semibold">Últimos seguimientos:</p>
                                                                @foreach($cotizacion->seguimientos->take(2) as $seguimiento)
                                                                    <div class="d-flex align-items-start small mb-1 seguimiento-item">
                                                                        @if($seguimiento->tipo === 'nota')
                                                                            <i class="fas fa-sticky-note text-warning me-2 mt-1"></i>
                                                                        @elseif($seguimiento->tipo === 'llamada')
                                                                            <i class="fas fa-phone text-success me-2 mt-1"></i>
                                                                        @elseif($seguimiento->tipo === 'reunion')
                                                                            <i class="fas fa-handshake text-primary me-2 mt-1"></i>
                                                                        @elseif($seguimiento->tipo === 'email')
                                                                            <i class="fas fa-envelope text-info me-2 mt-1"></i>
                                                                        @else
                                                                            <i class="fas fa-comment text-secondary me-2 mt-1"></i>
                                                                        @endif
                                                                        <div class="text-truncate" style="max-width: 180px;" title="{{ $seguimiento->contenido }}">
                                                                            {{ $seguimiento->contenido }}
                                                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $seguimiento->fecha_seguimiento->format('d/m/Y H:i') }}</div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                                            <a href="{{ route('admin.ventas.cotizaciones.show', $cotizacion) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-success add-seguimiento-btn" 
                                                                    data-bs-toggle="modal" data-bs-target="#modalSeguimiento"
                                                                    data-id="{{ $cotizacion->id }}"
                                                                    data-type="cotizacion"
                                                                    data-cotizacion-codigo="{{ $cotizacion->codigo }}"
                                                                    data-cliente="{{ $cotizacion->cliente->tipo_cliente === 'natural' ? $cotizacion->cliente->nombres . ' ' . $cotizacion->cliente->apellido_paterno : $cotizacion->cliente->razon_social }}">
                                                                <i class="fas fa-plus"></i> Seguimiento
                                                            </button>
                                                            @if($estadoData['estado']->nombre !== 'Convertida' && $estadoData['estado']->nombre !== 'Rechazada')
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                        <i class="fas fa-exchange-alt"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                        @foreach($cotizacionesPorEstado as $otroEstadoData)
                                                                            @if($otroEstadoData['estado']->id != $estadoData['estado']->id)
                                                                                <li>
                                                                                    <form action="{{ route('admin.ventas.cotizaciones.cambiar-estado', $cotizacion) }}" method="POST" class="cambiar-estado-form">
                                                                                        @csrf
                                                                                        <input type="hidden" name="estado_id" value="{{ $otroEstadoData['estado']->id }}">
                                                                                        <button type="submit" class="dropdown-item">
                                                                                            <span class="badge bg-{{ $otroEstadoData['estado']->color }} me-2"></span>
                                                                                            Mover a {{ $otroEstadoData['estado']->nombre }}
                                                                                        </button>
                                                                                    </form>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Estadísticas Tab -->
        <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Cotizaciones por Estado</h5>
                        </div>
                        <div class="card-body">
                            <div id="chart-estados" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Ventas Mensuales</h5>
                        </div>
                        <div class="card-body">
                            <div id="chart-ventas-mensuales" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Rendimiento de Ventas</h5>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-secondary period-selector active" data-period="week">Semana</button>
                                <button class="btn btn-sm btn-outline-secondary period-selector" data-period="month">Mes</button>
                                <button class="btn btn-sm btn-outline-secondary period-selector" data-period="quarter">Trimestre</button>
                                <button class="btn btn-sm btn-outline-secondary period-selector" data-period="year">Año</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <h3 class="text-primary mb-0" id="stat-oportunidades">{{ $oportunidades->count() }}</h3>
                                            <p class="text-muted mb-0">Oportunidades</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <h3 class="text-info mb-0" id="stat-cotizaciones">
                                                @php
                                                    $totalCotizaciones = 0;
                                                    foreach ($cotizacionesPorEstado as $estadoData) {
                                                        $totalCotizaciones += $estadoData['cotizaciones']->count();
                                                    }
                                                    echo $totalCotizaciones;
                                                @endphp
                                            </h3>
                                            <p class="text-muted mb-0">Cotizaciones</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <h3 class="text-success mb-0" id="stat-ventas">0</h3>
                                            <p class="text-muted mb-0">Ventas Cerradas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body text-center">
                                            <h3 class="text-danger mb-0" id="stat-perdidas">0</h3>
                                            <p class="text-muted mb-0">Ventas Perdidas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Oportunidad -->
<div class="modal fade" id="modalCrearOportunidad" tabindex="-1" aria-labelledby="modalCrearOportunidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearOportunidadLabel">Nueva Oportunidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="oportunidadForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione un cliente</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="probabilidad" class="form-label">Probabilidad (%) *</label>
                            <input type="number" class="form-control" id="probabilidad" name="probabilidad" min="0" max="100" value="50" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="valor_estimado" class="form-label">Valor Estimado</label>
                            <input type="number" class="form-control" id="valor_estimado" name="valor_estimado" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="moneda" class="form-label">Moneda *</label>
                            <select class="form-select" id="moneda" name="moneda" required>
                                <option value="Soles">Soles</option>
                                <option value="Dólares">Dólares</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_cierre_estimada" class="form-label">Fecha de Cierre Estimada</label>
                            <input type="date" class="form-control" id="fecha_cierre_estimada" name="fecha_cierre_estimada">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Oportunidad -->
<div class="modal fade" id="modalVerOportunidad" tabindex="-1" aria-labelledby="modalVerOportunidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerOportunidadLabel">Detalles de la Oportunidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Título</h6>
                        <p id="detalle-titulo"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Cliente</h6>
                        <p id="detalle-cliente"></p>
                    </div>
                    <div class="col-md-4">
                        <h6>Probabilidad</h6>
                        <p id="detalle-probabilidad"></p>
                    </div>
                    <div class="col-md-4">
                        <h6>Valor Estimado</h6>
                        <p id="detalle-valor"></p>
                    </div>
                    <div class="col-md-4">
                        <h6>Moneda</h6>
                        <p id="detalle-moneda"></p>
                    </div>
                    <div class="col-md-12">
                        <h6>Descripción</h6>
                        <p id="detalle-descripcion"></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Fecha de Cierre Estimada</h6>
                        <p id="detalle-fecha-cierre"></p>
                    </div>
                    <div class="col-md-12 mt-3">
                        <h6>Seguimientos</h6>
                        <div id="detalle-seguimientos"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary edit-opportunity-btn" data-bs-toggle="modal" data-bs-target="#modalCrearOportunidad">Editar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSeguimientoLabel">Agregar Seguimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="seguimientoForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Elemento: <span id="seguimiento-info"></span></label>
                        <input type="hidden" id="seguimiento-id" name="id">
                        <input type="hidden" id="seguimiento-type" name="type">
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Seguimiento *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="nota">Nota</option>
                            <option value="llamada">Llamada</option>
                            <option value="reunion">Reunión</option>
                            <option value="email">Email</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contenido" class="form-label">Contenido *</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_seguimiento" class="form-label">Fecha de Seguimiento *</label>
                        <input type="datetime-local" class="form-control" id="fecha_seguimiento" name="fecha_seguimiento" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="recordatorio" name="recordatorio">
                        <label class="form-check-label" for="recordatorio">Establecer Recordatorio</label>
                    </div>
                    <div class="mb-3" id="div-fecha-recordatorio" style="display: none;">
                        <label for="fecha_recordatorio" class="form-label">Fecha de Recordatorio</label>
                        <input type="datetime-local" class="form-control" id="fecha_recordatorio" name="fecha_recordatorio" value="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Columna Personalizada -->
<div class="modal fade" id="modalColumnaPersonalizada" tabindex="-1" aria-labelledby="modalColumnaPersonalizadaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalColumnaPersonalizadaLabel">Nueva Columna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="columnaForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre_columna" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre_columna" name="nombre" required>
                        <input type="hidden" id="column_id" name="column_id">
                    </div>
                    <div class="mb-3">
                        <label for="color_columna" class="form-label">Color *</label>
                        <select class="form-select" id="color_columna" name="color" required>
                            <option value="primary">Azul</option>
                            <option value="success">Verde</option>
                            <option value="warning">Amarillo</option>
                            <option value="danger">Rojo</option>
                            <option value="info">Cian</option>
                            <option value="secondary">Gris</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .kanban-board {
        min-height: calc(100vh - 200px);
    }
    .kanban-column {
        min-width: 320px;
    }
    .kanban-items-container {
        max-height: calc(100vh - 300px);
        overflow-y: auto;
    }
    .kanban-item {
        transition: all 0.3s;
        cursor: grab;
    }
    .kanban-item:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transform: translateY(-2px);
    }
    .kanban-item.dragging {
        opacity: 0.7;
        transform: scale(1.02);
    }
    .section-divider {
        min-width: 200px;
    }
    .seguimiento-item {
        word-break: break-word;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script>
$(document).ready(function() {
    // Inicializar búsqueda de clientes con Select2
    $('#cliente_id').select2({
    ajax: {
        url: "{{ route('admin.ventas.cotizaciones.buscarClientes') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return { term: params.term };
        },
        processResults: function (data) {
            return {
                results: data
            };
        }
    },
    placeholder: 'Buscar cliente',
    minimumInputLength: 2
});

    // Configurar formulario de seguimiento
    $('.add-seguimiento-btn').click(function() {
    const id = $(this).data('id');
    const type = $(this).data('type');
    const info = type === 'cotizacion' ? $(this).data('cotizacion-codigo') + ' - ' + $(this).data('cliente') : 'OP-' + id + ' - ' + $(this).data('titulo');
    
    // Corregir la URL para que coincida con la estructura de ruta definida
    $('#seguimientoForm').attr('action', type === 'cotizacion' 
    ? '{{ route("admin.ventas.cotizaciones.seguimiento.agregar", ["cotizacion" => "__ID__"]) }}'.replace('__ID__', id)
    : '{{ route("admin.ventas.oportunidades.seguimiento", ["oportunidad" => "__ID__"]) }}'.replace('__ID__', id));
    
    $('#seguimiento-info').text(info);
    $('#seguimiento-id').val(id);
    $('#seguimiento-type').val(type);
});

    // Mostrar/ocultar fecha de recordatorio
    $('#recordatorio').change(function() {
        if($(this).is(':checked')) {
            $('#div-fecha-recordatorio').show();
            $('#fecha_recordatorio').prop('required', true);
        } else {
            $('#div-fecha-recordatorio').hide();
            $('#fecha_recordatorio').prop('required', false);
        }
    });

    // Buscar global
    $('#search-global').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.kanban-item').each(function() {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchTerm));
        });
    });

    // Filtros
    $('.filter-option').click(function(e) {
        e.preventDefault();
        const filter = $(this).data('filter');
        $('.kanban-item').show();

        if (filter === 'recent') {
            const sevenDaysAgo = moment().subtract(7, 'days').format('YYYY-MM-DD');
            $('.kanban-item').each(function() {
                if ($(this).data('date') < sevenDaysAgo) {
                    $(this).hide();
                }
            });
        } else if (filter === 'mine') {
            const currentUser = {{ Auth::id() }};
            $('.kanban-item').each(function() {
                if ($(this).data('user-id') !== currentUser) {
                    $(this).hide();
                }
            });
        } else if (filter === 'opportunities') {
            $('.quote-item').hide();
        } else if (filter === 'quotes') {
            $('.opportunity-item').hide();
        }
    });

    // Drag and Drop con SortableJS
    $('.kanban-items-container').each(function() {
        new Sortable(this, {
            group: 'kanban',
            animation: 150,
            draggable: '.kanban-item',
            onStart: function(evt) {
                $(evt.item).addClass('dragging');
            },
            onEnd: function(evt) {
                $(evt.item).removeClass('dragging');
                const item = $(evt.item);
                const itemId = item.data('cotizacion-id');
                const newColumn = $(evt.to).closest('.kanban-column');
                const newEstadoId = newColumn.data('estado-id');

                if (itemId && newEstadoId && newColumn.data('column-type') === 'sales-stage') {
                    $.ajax({
                        url: '{{ route("admin.ventas.cotizaciones.cambiar-estado", "") }}/' + itemId,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            estado_id: newEstadoId
                        },
                        success: function() {
                            updateCounts();
                        },
                        error: function(xhr) {
                            alert('Error al mover la cotización: ' + xhr.responseJSON.message);
                            window.location.reload();
                        }
                    });
                }
            }
        });
    });

    // Crear/Editar oportunidad
    $('#oportunidadForm').submit(function(e) {
        e.preventDefault();
        const action = $('#oportunidad_id').val() 
            ? '{{ url("admin/ventas/oportunidades") }}/' + $('#oportunidad_id').val()
            : '{{ route("admin.ventas.oportunidades.store") }}';
        const method = $('#oportunidad_id').val() ? 'PUT' : 'POST';

        $.ajax({
            url: action,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#modalCrearOportunidad').modal('hide');
                window.location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.error);
            }
        });
    });

    // Ver oportunidad
    $('.view-opportunity-btn').click(function() {
        const id = $(this).data('id');
        $.ajax({
            url: '{{ url("admin/ventas/oportunidades") }}/' + id,
            method: 'GET',
            success: function(data) {
                $('#detalle-titulo').text(data.titulo);
                $('#detalle-cliente').text(data.cliente.tipo_cliente === 'natural' 
                    ? `${data.cliente.nombres} ${data.cliente.apellido_paterno}`
                    : data.cliente.razon_social);
                $('#detalle-probabilidad').text(data.probabilidad + '%');
                $('#detalle-valor').text(data.valor_estimado ? (data.moneda === 'Soles' ? 'S/ ' : '$') + data.valor_estimado : 'No especificado');
                $('#detalle-moneda').text(data.moneda);
                $('#detalle-descripcion').text(data.descripcion || 'Sin descripción');
                $('#detalle-fecha-cierre').text(data.fecha_cierre_estimada ? moment(data.fecha_cierre_estimada).format('DD/MM/YYYY') : 'No especificada');

                let seguimientosHtml = '';
                data.seguimientos.forEach(s => {
                    const icon = {
                        'nota': 'sticky-note text-warning',
                        'llamada': 'phone text-success',
                        'reunion': 'handshake text-primary',
                        'email': 'envelope text-info',
                        'otro': 'comment text-secondary'
                    }[s.tipo];
                    seguimientosHtml += `
                        <div class="d-flex align-items-start small mb-2">
                            <i class="fas fa-${icon} me-2 mt-1"></i>
                            <div>
                                ${s.contenido}
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    ${moment(s.fecha_seguimiento).format('DD/MM/YYYY HH:mm')}
                                </div>
                            </div>
                        </div>`;
                });
                $('#detalle-seguimientos').html(seguimientosHtml || 'Sin seguimientos');
                
                $('.edit-opportunity-btn').data('id', id);
            },
            error: function() {
                alert('Error al cargar los detalles');
            }
        });
    });

    // Editar oportunidad
    $('.edit-opportunity-btn').click(function() {
        const id = $(this).data('id');
        $.ajax({
            url: '{{ url("admin/ventas/oportunidades") }}/' + id,
            method: 'GET',
            success: function(data) {
                $('#modalCrearOportunidadLabel').text('Editar Oportunidad');
                $('#oportunidad_id').val(data.id);
                $('#titulo').val(data.titulo);
                $('#cliente_id').append(new Option(
                    data.cliente.tipo_cliente === 'natural' 
                        ? `${data.cliente.nombres} ${data.cliente.apellido_paterno}`
                        : data.cliente.razon_social,
                    data.cliente_id,
                    true,
                    true
                )).trigger('change');
                $('#probabilidad').val(data.probabilidad);
                $('#valor_estimado').val(data.valor_estimado);
                $('#moneda').val(data.moneda);
                $('#descripcion').val(data.descripcion);
                $('#fecha_cierre_estimada').val(data.fecha_cierre_estimada ? moment(data.fecha_cierre_estimada).format('YYYY-MM-DD') : '');
            }
        });
    });

    // Convertir oportunidad a cotización
    $('.convert-to-quote-btn').click(function() {
        const clienteId = $(this).data('cliente-id');
        window.location.href = '{{ route("admin.ventas.cotizaciones.create") }}?cliente_id=' + clienteId;
    });

    // Crear/Editar columna
    $('.edit-column-btn').click(function() {
        $('#modalColumnaPersonalizadaLabel').text('Editar Columna');
        $('#nombre_columna').val($(this).data('nombre'));
        $('#color_columna').val($(this).data('color'));
        $('#column_id').val($(this).data('column-id'));
    });

    $('.add-column-btn').click(function() {
        $('#modalColumnaPersonalizadaLabel').text('Nueva Columna');
        $('#columnaForm')[0].reset();
        $('#column_id').val('');
    });

    $('#columnaForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.ventas.cotizaciones.manage-column") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#modalColumnaPersonalizada').modal('hide');
                window.location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.error);
            }
        });
    });

    // Eliminar columna
    $('.delete-column-btn').click(function() {
        if (confirm('¿Seguro que desea eliminar esta columna?')) {
            const id = $(this).data('column-id');
            $.ajax({
                url: '{{ url("admin/ventas/cotizaciones/column") }}/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.error);
                }
            });
        }
    });

    // Enviar seguimiento
    $('#seguimientoForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Cerrar el modal
                $('#modalSeguimiento').modal('hide');
                
                // Limpia los campos del formulario
                $('#contenido').val('');
                $('#recordatorio').prop('checked', false);
                $('#div-fecha-recordatorio').hide();
                
                // Si la respuesta contiene información del seguimiento creado
                if (response.success && response.seguimiento) {
                    const seguimiento = response.seguimiento;
                    const cotizacionId = seguimiento.cotizacion_id;
                    
                    // Buscar la tarjeta de cotización correspondiente
                    const $cotizacionCard = $(`.kanban-item[data-cotizacion-id="${cotizacionId}"]`);
                    
                    // Determinar el icono según el tipo de seguimiento
                    let iconClass = 'fa-comment text-secondary';
                    if (seguimiento.tipo === 'nota') iconClass = 'fa-sticky-note text-warning';
                    else if (seguimiento.tipo === 'llamada') iconClass = 'fa-phone text-success';
                    else if (seguimiento.tipo === 'reunion') iconClass = 'fa-handshake text-primary';
                    else if (seguimiento.tipo === 'email') iconClass = 'fa-envelope text-info';
                    
                    // Crear el HTML para el nuevo seguimiento
                    const seguimientoHtml = `
                        <div class="d-flex align-items-start small mb-1 seguimiento-item">
                            <i class="fas ${iconClass} me-2 mt-1"></i>
                            <div class="text-truncate" style="max-width: 180px;" title="${seguimiento.contenido}">
                                ${seguimiento.contenido}
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    ${new Date(seguimiento.fecha_seguimiento).toLocaleString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'})}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Verificar si ya existe una sección de seguimientos
                    let $seguimientosContainer = $cotizacionCard.find('.border-top:contains("Últimos seguimientos")');
                    
                    if ($seguimientosContainer.length === 0) {
                        // Si no existe, crear la sección
                        $seguimientosContainer = $(`
                            <div class="border-top pt-2 mt-2">
                                <p class="small mb-1 fw-semibold">Últimos seguimientos:</p>
                            </div>
                        `);
                        $cotizacionCard.find('.card-body p.small.text-muted').after($seguimientosContainer);
                    }
                    
                    // Agregar el nuevo seguimiento al principio
                    $seguimientosContainer.append(seguimientoHtml);
                    
                    // Si hay más de 2 seguimientos, elimina el más antiguo
                    if ($seguimientosContainer.find('.seguimiento-item').length > 2) {
                        $seguimientosContainer.find('.seguimiento-item').last().remove();
                    }
                    
                    // Mostrar notificación
                    toastr.success('Seguimiento agregado correctamente');
                } else {
                    // Mostrar notificación en lugar de recargar
                    toastr.success('Seguimiento agregado correctamente');
                }
            },
            error: function(xhr) {
                console.error('Error al enviar seguimiento:', xhr);
                alert('Error al guardar el seguimiento: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido'));
            }
        });
    });

    // Actualizar contadores
    function updateCounts() {
        $('.kanban-column').each(function() {
            const count = $(this).find('.kanban-item').length;
            $(this).find('.badge.rounded-pill').text(count);
        });
    }
});
</script>
<script>
    // Añade esto a tu sección de scripts existente

// Inicialización de gráficos cuando se cambia a la pestaña de estadísticas
$('#stats-tab').on('shown.bs.tab', function (e) {
    initCharts();
});

// Función para inicializar todos los gráficos
function initCharts() {
    initEstadosChart();
    initVentasMensualesChart();
}

// Gráfico de cotizaciones por estado
function initEstadosChart() {
    // Preparar datos para el gráfico
    const estadosLabels = [];
    const estadosData = [];
    const estadosColors = [];
    
    @foreach($cotizacionesPorEstado as $estadoData)
        estadosLabels.push('{{ $estadoData['estado']->nombre }}');
        estadosData.push({{ $estadoData['cotizaciones']->count() }});
        estadosColors.push(getColorForBootstrapClass('{{ $estadoData['estado']->color }}'));
    @endforeach
    
    // Crear el gráfico usando Chart.js
    const ctxEstados = document.getElementById('chart-estados').getContext('2d');
    if (window.estadosChart) {
        window.estadosChart.destroy();
    }
    
    window.estadosChart = new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: estadosLabels,
            datasets: [{
                data: estadosData,
                backgroundColor: estadosColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
}

// Gráfico de ventas mensuales
function initVentasMensualesChart() {
    // Datos de ejemplo para el gráfico de ventas mensuales
    // En una aplicación real, estos datos vendrían de una consulta a la base de datos
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const datosVentas = [
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10,
        Math.floor(Math.random() * 50) + 10
    ];
    
    // Crear el gráfico usando Chart.js
    const ctxVentas = document.getElementById('chart-ventas-mensuales').getContext('2d');
    if (window.ventasChart) {
        window.ventasChart.destroy();
    }
    
    window.ventasChart = new Chart(ctxVentas, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Ventas',
                data: datosVentas,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Función para convertir clases de color de Bootstrap en colores para Chart.js
function getColorForBootstrapClass(bootstrapClass) {
    const colorMap = {
        'primary': '#0d6efd',
        'secondary': '#6c757d',
        'success': '#198754',
        'danger': '#dc3545',
        'warning': '#ffc107',
        'info': '#0dcaf0',
        'dark': '#212529',
        'light': '#f8f9fa'
    };
    
    return colorMap[bootstrapClass] || '#0d6efd'; // Default to primary if not found
}

// Manejo de filtros de período para estadísticas
$('.period-selector').click(function(e) {
    e.preventDefault();
    $('.period-selector').removeClass('active');
    $(this).addClass('active');
    
    // Aquí puedes implementar la lógica para actualizar los datos según el período seleccionado
    // Por ahora, vamos a simular con datos aleatorios
    
    const period = $(this).data('period');
    updateStatsByPeriod(period);
});

function updateStatsByPeriod(period) {
    // Esto es solo para demostración - normalmente consultarías al servidor para datos reales
    let oportunidades, cotizaciones, ventas, perdidas;
    
    switch(period) {
        case 'week':
            oportunidades = Math.floor(Math.random() * 10) + 5;
            cotizaciones = Math.floor(Math.random() * 8) + 3;
            ventas = Math.floor(Math.random() * 5) + 1;
            perdidas = Math.floor(Math.random() * 3) + 1;
            break;
        case 'month':
            oportunidades = Math.floor(Math.random() * 40) + 15;
            cotizaciones = Math.floor(Math.random() * 30) + 12;
            ventas = Math.floor(Math.random() * 20) + 8;
            perdidas = Math.floor(Math.random() * 15) + 5;
            break;
        case 'quarter':
            oportunidades = Math.floor(Math.random() * 100) + 50;
            cotizaciones = Math.floor(Math.random() * 80) + 40;
            ventas = Math.floor(Math.random() * 60) + 30;
            perdidas = Math.floor(Math.random() * 40) + 20;
            break;
        case 'year':
            oportunidades = Math.floor(Math.random() * 300) + 150;
            cotizaciones = Math.floor(Math.random() * 250) + 120;
            ventas = Math.floor(Math.random() * 180) + 90;
            perdidas = Math.floor(Math.random() * 120) + 60;
            break;
    }
    
    $('#stat-oportunidades').text(oportunidades);
    $('#stat-cotizaciones').text(cotizaciones);
    $('#stat-ventas').text(ventas);
    $('#stat-perdidas').text(perdidas);
    
    // También podríamos actualizar los gráficos con datos basados en el período
    // pero eso requeriría datos del servidor
}
</script>
@endpush