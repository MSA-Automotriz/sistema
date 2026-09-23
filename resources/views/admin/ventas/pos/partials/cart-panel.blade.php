{{-- resources/views/admin/ventas/pos/partials/cart-panel.blade.php --}}
<div class="sticky-sidebar">
    <div class="card border-0 shadow-lg">
            <!-- Sección de cliente -->
            @include('admin.ventas.pos.partials.cart.cliente-section')
            
            <!-- Lista de items -->
            @include('admin.ventas.pos.partials.cart.items-section')
            
            <!-- Totales -->
            @include('admin.ventas.pos.partials.cart.totales-section')

            <!-- Configuración de venta -->
            @include('admin.ventas.pos.partials.cart.config-section')
            
            <!-- Botones de acción -->
            @include('admin.ventas.pos.partials.cart.actions-section')
        </div>
    </div>
</div>