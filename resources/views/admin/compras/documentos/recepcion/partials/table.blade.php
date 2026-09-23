<div class="card dashboard-card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($ordenes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">
                                <i class="fas fa-hashtag me-2 text-primary"></i>Orden
                            </th>
                            <th class="py-3 px-4">
                                <i class="fas fa-building me-2 text-primary"></i>Proveedor
                            </th>
                            <th class="py-3 px-4">
                                <i class="fas fa-calendar me-2 text-primary"></i>Fecha
                            </th>
                            <th class="py-3 px-4">
                                <i class="fas fa-chart-pie me-2 text-primary"></i>Estado
                            </th>
                            <th class="py-3 px-4 text-center">
                                <i class="fas fa-boxes me-2 text-primary"></i>Items
                            </th>
                            <th class="py-3 px-4 text-center">
                                <i class="fas fa-check-circle me-2 text-primary"></i>Recibidos
                            </th>
                            <th class="py-3 px-4 text-center">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>Progreso
                            </th>
                            @if($mostrarAcciones)
                            <th class="py-3 px-4 text-center">
                                <i class="fas fa-cogs me-2 text-primary"></i>Acciones
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                        <tr style="border-bottom: 1px solid #f1f5f9;" class="table-row-hover">
                            <td class="py-4 px-4">
                                <div class="d-flex flex-column">
                                    <strong class="text-primary fw-bold">#{{ $orden->codigo }}</strong>
                                    <small class="text-muted">ID: {{ $orden->id }}</small>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-building text-muted"></i>
                                        </div>
                                    </div>
                                    <span class="fw-medium">{{ $orden->proveedor->nombre ?? 'Sin proveedor' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $orden->created_at->format('d/m/Y') }}</span>
                                    <small class="text-muted">{{ $orden->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $estado = $orden->estado_recepcion ?? 'pendiente';
                                    $badgeClass = $estado == 'completo' ? 'success' : ($estado == 'parcial' ? 'warning' : 'secondary');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }} px-3 py-2 text-uppercase fw-bold" 
                                      style="border-radius: 50px; font-size: 0.75rem; letter-spacing: 0.5px;">
                                    {{ ucfirst($estado) }}
                                </span>
                            </td>
                            <td class="text-center py-4 px-4">
                                <div class="card border-0 d-inline-block" 
                                     style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-radius: 12px;">
                                    <div class="card-body p-2">
                                        <h4 class="mb-0 fw-bold text-primary">{{ $orden->detalles->sum('cantidad_en_compra') }}</h4>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4 px-4">
                                <div class="card border-0 d-inline-block" 
                                     style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border-radius: 12px;">
                                    <div class="card-body p-2">
                                        <h4 class="mb-0 fw-bold text-success">{{ $orden->detalles->sum('cantidad_recibida') }}</h4>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $totalItems = $orden->detalles->sum('cantidad_en_compra');
                                    $itemsRecibidos = $orden->detalles->sum('cantidad_recibida');
                                    $porcentaje = $totalItems > 0 ? round(($itemsRecibidos / $totalItems) * 100, 1) : 0;
                                    $progressColor = $porcentaje == 100 ? 'success' : ($porcentaje > 0 ? 'warning' : 'secondary');
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-3" style="height: 10px; border-radius: 50px;">
                                        <div class="progress-bar bg-{{ $progressColor }}" 
                                             role="progressbar" 
                                             style="width: {{ $porcentaje }}%; border-radius: 50px;" 
                                             aria-valuenow="{{ $porcentaje }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="fw-bold text-primary">{{ $porcentaje }}%</small>
                                </div>
                            </td>
                            @if($mostrarAcciones)
                            <td class="text-center py-4 px-4">
                                @if($orden->estado_recepcion != 'completo' && $orden->estado_recepcion != 'completo_con_faltantes')
                                <a href="{{ route('admin.recepcion.show', $orden->id) }}" 
                                   class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm transition hover:scale-105 border-0">
                                    <i class="fas fa-box me-2"></i> Recepcionar
                                </a>
                                @endif
                                
                                @if($orden->detalles->sum('cantidad_recibida') > 0)
                                <a href="{{ route('admin.recepcion.detalle', $orden->id) }}" 
                                   class="btn fw-bold" 
                                   style="border-radius: 12px;">
                                    <i class="fas fa-eye me-1"></i>
                                </a>
                                @endif
                                
                                @if($orden->estado_recepcion == 'completo')
                                <span class="badge bg-success px-3 py-2">
                                    <i class="fas fa-check me-1"></i> Completado
                                </span>
                                @elseif($orden->estado_recepcion == 'completo_con_faltantes')
                                <span class="badge bg-warning px-3 py-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Completo c/Faltantes
                                </span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5" 
                 style="background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%); 
                        border-radius: 0 0 16px 16px;">
                <div class="mb-4">
                    <i class="fas fa-inbox text-primary" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
                <h3 class="text-primary mb-2">{{ $titulo ?? 'Sin registros' }}</h3>
                <p class="text-muted mb-0">No hay órdenes en esta categoría</p>
            </div>
        @endif
    </div>
</div>

<style>
.table-row-hover:hover {
    background-color: rgba(102, 126, 234, 0.05) !important;
    transform: translateX(0px);
    transition: all 0.3s ease;
}
</style>