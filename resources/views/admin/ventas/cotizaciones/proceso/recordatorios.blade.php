@extends('layouts.admin')

@section('title', 'Recordatorios de Seguimiento')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-warning me-2"></i> Recordatorios de Seguimiento
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recordatorios->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recordatorios as $recordatorio)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge me-2 
                                                    @if($recordatorio->fecha_recordatorio->isPast()) 
                                                        bg-danger 
                                                    @else 
                                                        @if($recordatorio->fecha_recordatorio->isToday())
                                                            bg-warning text-dark
                                                        @else
                                                            bg-success
                                                        @endif
                                                    @endif">
                                                    <i class="fas fa-calendar-day me-1"></i>
                                                    {{ $recordatorio->fecha_recordatorio->format('d/m/Y H:i') }}
                                                    @if($recordatorio->fecha_recordatorio->isPast())
                                                        (Vencido)
                                                    @elseif($recordatorio->fecha_recordatorio->isToday())
                                                        (Hoy)
                                                    @else
                                                        ({{ $recordatorio->fecha_recordatorio->diffForHumans() }})
                                                    @endif
                                                </span>
                                                <span class="badge 
                                                    @if($recordatorio->tipo === 'nota') bg-warning text-dark
                                                    @elseif($recordatorio->tipo === 'llamada') bg-success
                                                    @elseif($recordatorio->tipo === 'reunion') bg-primary
                                                    @elseif($recordatorio->tipo === 'email') bg-info
                                                    @else bg-secondary
                                                    @endif me-2">
                                                    @if($recordatorio->tipo === 'nota')
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                    @elseif($recordatorio->tipo === 'llamada')
                                                        <i class="fas fa-phone-alt me-1"></i>
                                                    @elseif($recordatorio->tipo === 'reunion')
                                                        <i class="fas fa-handshake me-1"></i>
                                                    @elseif($recordatorio->tipo === 'email')
                                                        <i class="fas fa-envelope me-1"></i>
                                                    @else
                                                        <i class="fas fa-comment me-1"></i>
                                                    @endif
                                                    {{ ucfirst($recordatorio->tipo) }}
                                                </span>
                                                <span class="fw-medium">
                                                    Cotización #{{ $recordatorio->cotizacion->numero }}
                                                </span>
                                            </div>
                                            <div class="mb-1">
                                                <strong class="text-primary">
                                                    {{ $recordatorio->cotizacion->cliente->nombre_completo }}
                                                </strong>
                                            </div>
                                            <p class="mb-0 text-muted">
                                                {{ $recordatorio->contenido }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i> {{ $recordatorio->usuario->name }}
                                                <i class="fas fa-clock ms-2 me-1"></i> {{ $recordatorio->fecha_seguimiento->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                                            <a href="{{ route('admin.ventas.cotizaciones.show', $recordatorio->cotizacion) }}" 
                                              class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-eye me-1"></i> Ver cotización
                                            </a>
                                            <form action="{{ route('admin.ventas.recordatorios.completar', $recordatorio->id) }}" 
                                                  method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check me-1"></i> Completar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No hay recordatorios pendientes</h5>
                            <p class="text-muted">
                                Los recordatorios aparecerán aquí cuando los crees en los seguimientos de cotizaciones.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection