{{-- resources/views/admin/ventas/pos/partials/header.blade.php --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient-primary text-black">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">
                            <i class="fas fa-cash-register me-2"></i>
                            Punto de Venta
                        </h4>
                        <small class="opacity-75">Sistema de ventas rápido y eficiente</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex justify-content-md-end gap-3 mt-2 mt-md-0">
                            <div class="text-center">
                                <div class="fw-bold">{{ date('d/m/Y') }}</div>
                                <small class="opacity-75">Fecha</small>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold" id="current-time">{{ date('H:i') }}</div>
                                <small class="opacity-75">Hora</small>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="opacity-75">Vendedor</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>