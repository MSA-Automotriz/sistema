@php
// Obtenemos los recordatorios pendientes para el usuario actual
$recordatorios = \App\Models\SeguimientoCotizacion::where('recordatorio', true)
    ->where(function($query) {
        $query->whereNull('fecha_recordatorio')
            ->orWhere('fecha_recordatorio', '>=', now()->subDays(3));
    })
    ->where(function($query) {
        $query->where('user_id', auth()->id())
            ->orWhereHas('cotizacion', function($q) {
                $q->where('user_id', auth()->id());
            });
    })
    ->with(['cotizacion', 'cotizacion.cliente', 'usuario'])
    ->orderBy('fecha_recordatorio')
    ->limit(5)
    ->get();
@endphp

<div class="card mb-4">
    <div class="card-header bg-warning-subtle">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-bell text-warning me-2"></i> Recordatorios pendientes
            </h5>
            <span class="badge bg-warning text-dark">{{ $recordatorios->count() }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($recordatorios as $recordatorio)
                <a href="{{ route('admin.ventas.cotizaciones.show', $recordatorio->cotizacion) }}" 
                   class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">
                                <span class="fw-medium">{{ $recordatorio->cotizacion->cliente->nombre_completo }}</span>
                                <span class="badge 
                                    @if($recordatorio->fecha_recordatorio->isPast()) 
                                        bg-danger 
                                    @else 
                                        @if($recordatorio->fecha_recordatorio->isToday())
                                            bg-warning text-dark
                                        @else
                                            bg-success
                                        @endif
                                    @endif ms-2">
                                    @if($recordatorio->fecha_recordatorio->isPast())
                                        Vencido
                                    @elseif($recordatorio->fecha_recordatorio->isToday())
                                        Hoy
                                    @else
                                        Pendiente
                                    @endif
                                </span>
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="badge 
                                    @if($recordatorio->tipo === 'nota') bg-warning text-dark
                                    @elseif($recordatorio->tipo === 'llamada') bg-success
                                    @elseif($recordatorio->tipo === 'reunion') bg-primary
                                    @elseif($recordatorio->tipo === 'email') bg-info
                                    @else bg-secondary
                                    @endif me-2">
                                    @if($recordatorio->tipo === 'nota')
                                        <i class="fas fa-sticky-note"></i>
                                    @elseif($recordatorio->tipo === 'llamada')
                                        <i class="fas fa-phone-alt"></i>
                                    @elseif($recordatorio->tipo === 'reunion')
                                        <i class="fas fa-handshake"></i>
                                    @elseif($recordatorio->tipo === 'email')
                                        <i class="fas fa-envelope"></i>
                                    @else
                                        <i class="fas fa-comment"></i>
                                    @endif
                                </span>
                                <small class="text-muted">
                                    <i class="fas fa-hashtag me-1"></i> Cotización #{{ $recordatorio->cotizacion->numero }}
                                </small>
                            </div>
                            <p class="mb-0 text-truncate" style="max-width: 400px;">
                                <i class="fas fa-comment-dots me-1"></i> {{ $recordatorio->contenido }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="fw-medium">{{ $recordatorio->fecha_recordatorio->format('d/m/Y') }}</div>
                            <small>{{ $recordatorio->fecha_recordatorio->format('H:i') }}</small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">No tienes recordatorios pendientes</p>
                </div>
            @endforelse
        </div>
    </div>
    @if($recordatorios->count() > 0)
        <div class="card-footer text-center">
            <a href="{{ route('admin.ventas.recordatorios') }}" class="btn btn-sm btn-outline-warning">
                Ver todos los recordatorios
            </a>
        </div>
    @endif
</div>