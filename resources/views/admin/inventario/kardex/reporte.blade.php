@extends('admin.layouts.app')

@section('title', 'Kardex - Reporte de Movimientos')

@section('header', 'Reporte Kardex')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #06b6d4;
        --surface: #ffffff;
        --surface-hover: #f8fafc;
        --border-light: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .kardex-container {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        padding: 1rem;
    }

    .main-header {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        margin-bottom: 2rem;
        overflow: hidden;
        border-left: 4px solid #667eea;
    }

    .header-gradient {
        background: var(--primary-gradient);
        padding: 1.5rem 2rem;
        color: white;
    }

    .header-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-subtitle {
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
        font-weight: 400;
    }

    .header-actions {
        padding: 1rem 2rem;
        background: var(--surface);
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .btn-modern {
        border-radius: 10px;
        padding: 0.625rem 1.25rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 0.875rem;
        cursor: pointer;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.3);
    }

    .btn-primary-modern:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-secondary-modern {
        background: #64748b;
        color: white;
    }

    .btn-secondary-modern:hover {
        background: #475569;
        transform: translateY(-1px);
        color: white;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    .info-card {
        background: var(--surface);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-card);
        transition: all 0.2s ease;
        border: 1px solid var(--border-light);
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .card-header-modern {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-light);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .icon-product {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .icon-stock {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
    }

    .stock-table {
        width: 100%;
        border-collapse: collapse;
    }

    .stock-table th {
        background: var(--surface-hover);
        color: var(--text-secondary);
        font-weight: 600;
        padding: 0.75rem;
        text-align: left;
        font-size: 0.875rem;
        border-bottom: 2px solid var(--border-light);
    }

    .stock-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
    }

    .stock-value {
        font-weight: 600;
        text-align: right;
    }

    .stock-available {
        color: var(--success-color);
    }

    .stock-reserved {
        color: var(--warning-color);
    }

    .stock-minimum {
        color: var(--danger-color);
    }

    .movements-section {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 2rem;
        border: 1px solid var(--border-light);
    }

    .movements-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-light);
    }

    .movements-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .movements-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .movements-table th {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        font-weight: 600;
        padding: 1rem 0.75rem;
        text-align: left;
    }

    .movements-table td {
        padding: 0.875rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
        background: white;
    }

    .movements-table tr:hover td {
        background: var(--surface-hover);
    }

    .movement-date {
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .movement-type {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .movement-entrada {
        background: #d1fae5;
        color: #065f46;
    }

    .movement-salida {
        background: #fee2e2;
        color: #991b1b;
    }

    .movement-ajuste {
        background: #fef3c7;
        color: #92400e;
    }

    .document-code {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        background: #f1f5f9;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .quantity-entry {
        color: var(--success-color);
        font-weight: 700;
    }

    .quantity-exit {
        color: var(--danger-color);
        font-weight: 700;
    }

    .stock-current {
        background: var(--surface-hover);
        font-weight: 700;
        color: var(--text-primary);
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
    }

    .value-amount {
        font-weight: 600;
        color: var(--text-primary);
    }

    .user-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .text-end {
        text-align: right;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="kardex-container">
    <!-- Header Principal -->
    <div class="main-header">
        <div class="header-gradient">
            <h1 class="header-title">
                <i class="fas fa-chart-line"></i>
                Reporte Kardex
            </h1>
            <p class="header-subtitle">
                Control detallado de movimientos de inventario - {{ $parte->codigo }}
            </p>
        </div>
        <div class="header-actions">
            <button class="btn-modern btn-primary-modern" onclick="window.print()">
                <i class="fas fa-print"></i>
                Imprimir
            </button>
            <a href="{{ route('admin.inventario.kardex.form') }}" class="btn-modern btn-secondary-modern">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Grid de Información -->
    <div class="info-grid">
        <!-- Información del Producto -->
        <div class="info-card">
            <div class="card-header-modern">
                <div class="card-icon icon-product">
                    <i class="fas fa-cube"></i>
                </div>
                <h3 class="card-title">Información del Producto</h3>
            </div>
            
            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">Código</span>
                    <span class="info-value">{{ $parte->codigo }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Nombre</span>
                    <span class="info-value">{{ $parte->nombre }}</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Unidad</span>
                    <span class="info-value">{{ $parte->unidad->nombre }}</span>
                </li>
                @if(isset($parte->categoria))
                <li class="info-item">
                    <span class="info-label">Categoría</span>
                    <span class="info-value">{{ $parte->categoria->nombre }}</span>
                </li>
                @endif
            </ul>
        </div>

        <!-- Stock Actual -->
        <div class="info-card">
            <div class="card-header-modern">
                <div class="card-icon icon-stock">
                    <i class="fas fa-warehouse"></i>
                </div>
                <h3 class="card-title">Stock por Almacén</h3>
            </div>
            
            @if($stockActual->count() > 0)
                <table class="stock-table">
                    <thead>
                        <tr>
                            <th>Almacén</th>
                            <th>Disponible</th>
                            <th>Reservado</th>
                            <th>Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockActual as $stock)
                        <tr>
                            <td>{{ $stock->almacen->nombre }}</td>
                            <td class="stock-value stock-available">
                                {{ number_format($stock->stock_disponible, 2) }}
                            </td>
                            <td class="stock-value stock-reserved">
                                {{ number_format($stock->stock_reservado, 2) }}
                            </td>
                            <td class="stock-value stock-minimum">
                                {{ number_format($stock->stock_minimo, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <p>No hay stock registrado en ningún almacén</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="movements-section">
        <div class="movements-header">
            <div class="card-icon icon-product">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h3 class="movements-title">
                Movimientos {{ $almacen ? 'en '.$almacen->nombre : 'en todos los almacenes' }}
            </h3>
        </div>
        
        @if($movimientos->count() > 0)
            <div class="table-responsive">
                <table class="movements-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Almacén</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Existencia</th>
                            <th>Costo Unit.</th>
                            <th>Valor Total</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                        <tr>
                            <td class="movement-date">
                                {{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @php
                                    $tipoClass = 'movement-ajuste';
                                    if($movimiento->tipoMovimiento->afecta_stock > 0) {
                                        $tipoClass = 'movement-entrada';
                                    } elseif($movimiento->tipoMovimiento->afecta_stock < 0) {
                                        $tipoClass = 'movement-salida';
                                    }
                                @endphp
                                <span class="movement-type {{ $tipoClass }}">
                                    {{ $movimiento->tipoMovimiento->nombre }}
                                </span>
                            </td>
                            <td>
                                <span class="document-code">{{ $movimiento->documento_referencia }}</span>
                            </td>
                            <td>{{ $movimiento->almacen->nombre }}</td>
                            <td class="text-end">
                                @if($movimiento->tipoMovimiento->afecta_stock > 0)
                                    <span class="quantity-entry">
                                        +{{ number_format($movimiento->cantidad, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($movimiento->tipoMovimiento->afecta_stock < 0)
                                    <span class="quantity-exit">
                                        -{{ number_format($movimiento->cantidad, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="stock-current">{{ number_format($movimiento->stock_resultante, 2) }}</span>
                            </td>
                            <td class="text-end">S/ {{ number_format($movimiento->costo_unitario, 2) }}</td>
                            <td class="text-end">
                                <span class="value-amount">
                                    S/ {{ number_format($movimiento->cantidad * $movimiento->costo_unitario, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="user-badge">
                                    <i class="fas fa-user"></i>
                                    {{ $movimiento->usuario->name }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h4>No hay movimientos registrados</h4>
                <p>Este producto aún no tiene movimientos en el rango de fechas seleccionado</p>
            </div>
        @endif
    </div>
</div>

<script>
    // Mejorar la experiencia de impresión
    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.table-responsive').forEach(function(element) {
            element.style.overflow = 'visible';
        });
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.table-responsive').forEach(function(element) {
            element.style.overflow = 'auto';
        });
    });

    // Animaciones suaves al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.info-card, .movements-section');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection