<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seguimiento de Pedidos - MSA</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .tracking-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }

        .tracking-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .tracking-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .form-container {
            padding: 2rem;
        }

        .result-container {
            padding: 2rem;
            display: none;
        }

        .progress-bar-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .estado-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .estado-pendiente { background: #ffeaa7; color: #2d3436; }
        .estado-pagado { background: #00b894; color: white; }
        .estado-pendiente_stock { background: #fdcb6e; color: #2d3436; }
        .estado-en_compra { background: #74b9ff; color: white; }
        .estado-listo_entrega { background: #55a3ff; color: white; }
        .estado-despachado { background: #00cec9; color: white; }
        .estado-cancelado { background: #e17055; color: white; }

        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            height: 100%;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.2rem;
            top: 1rem;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            border: 3px solid white;
        }

        .items-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }

        .btn-track {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="tracking-card">
                        <div class="tracking-header">
                            <h1><i class="fas fa-search me-3"></i>Seguimiento de Pedidos</h1>
                            <p class="mb-0">Ingrese los datos de su pedido para conocer su estado</p>
                        </div>

                        <!-- Formulario de búsqueda -->
                        <div class="form-container" id="search-form">
                            <form id="tracking-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-barcode me-2"></i>Código de Venta
                                        </label>
                                        <input type="text" class="form-control form-control-lg"
                                               id="codigo" name="codigo" placeholder="Ej: VTA-202501000001" required>
                                        <small class="text-muted">El código que aparece en su ticket/factura</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-id-card me-2"></i>Documento de Identidad
                                        </label>
                                        <input type="text" class="form-control form-control-lg"
                                               id="documento" name="documento" placeholder="RUC/DNI" required>
                                        <small class="text-muted">Su RUC o DNI registrado en la venta</small>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-track btn-lg">
                                        <i class="fas fa-search me-2"></i>Rastrear Pedido
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Resultados del tracking -->
                        <div class="result-container" id="tracking-results">
                            <!-- El contenido se carga dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configurar toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "timeOut": "5000"
            };

            $('#tracking-form').on('submit', function(e) {
                e.preventDefault();
                buscarVenta();
            });

            function buscarVenta() {
                const $form = $('#tracking-form');
                const $btn = $form.find('button[type="submit"]');
                const originalText = $btn.html();

                // Deshabilitar botón y mostrar loading
                $btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-2"></i>Buscando...');

                $.ajax({
                    url: '{{ route("tracking.buscar") }}',
                    type: 'POST',
                    data: {
                        codigo: $('#codigo').val(),
                        documento: $('#documento').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            mostrarResultados(response.venta);
                            toastr.success('Pedido encontrado exitosamente');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        const message = response?.message || 'Error al buscar el pedido';
                        toastr.error(message);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            }

            function mostrarResultados(venta) {
                const html = generarHTMLResultados(venta);
                $('#tracking-results').html(html).show();

                // Scroll hacia los resultados
                $('html, body').animate({
                    scrollTop: $('#tracking-results').offset().top - 100
                }, 1000);
            }

            function generarHTMLResultados(venta) {
                const estadoClass = `estado-${venta.estado}`;

                return `
                    <div class="border-bottom pb-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3><i class="fas fa-receipt me-2"></i>Pedido ${venta.codigo}</h3>
                            <button class="btn btn-outline-secondary" onclick="nuevaBusqueda()">
                                <i class="fas fa-search me-2"></i>Nueva Búsqueda
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong> ${venta.cliente}</p>
                                <p><strong>Fecha:</strong> ${venta.fecha}</p>
                                <p><strong>Total:</strong> ${venta.moneda === 'Dólares' ? '$' : 'S/'} ${venta.total}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Estado:</strong> <span class="estado-badge ${estadoClass}">${venta.estado}</span></p>
                                <p><strong>Progreso:</strong></p>
                                <div class="progress mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         style="width: ${venta.progreso}%">${venta.progreso}%</div>
                                </div>
                                <p><strong>Entrega estimada:</strong> ${venta.fecha_estimada_entrega}</p>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>${venta.estado_descripcion}
                        </div>
                    </div>

                    ${venta.saldo_pendiente > 0 ? `
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Información de Pago</h6>
                            <p class="mb-1"><strong>Monto abonado:</strong> ${venta.moneda === 'Dólares' ? '$' : 'S/'} ${venta.monto_abonado}</p>
                            <p class="mb-0"><strong>Saldo pendiente:</strong> ${venta.moneda === 'Dólares' ? '$' : 'S/'} ${venta.saldo_pendiente}</p>
                        </div>
                    ` : ''}

                    ${venta.requerimientos_compra.length > 0 ? `
                        <div class="alert alert-info">
                            <h6><i class="fas fa-shopping-cart me-2"></i>Órdenes de Compra</h6>
                            <p>Su pedido requiere compra de productos adicionales:</p>
                            ${venta.requerimientos_compra.map(req => `
                                <div class="d-flex justify-content-between">
                                    <span>Orden #${req.id}</span>
                                    <span class="badge bg-secondary">${req.estado}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}

                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-list me-2"></i>Productos</h5>
                            <div class="items-list">
                                ${venta.items.map(item => `
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <div>
                                            <strong>${item.nombre}</strong><br>
                                            <small>Cantidad: ${item.cantidad}</small>
                                        </div>
                                        <div class="text-end">
                                            ${venta.moneda === 'Dólares' ? '$' : 'S/'} ${item.subtotal}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-history me-2"></i>Historial</h5>
                            <div class="timeline">
                                ${venta.historial_estados.length > 0 ?
                                    venta.historial_estados.map(historial => `
                                        <div class="timeline-item">
                                            <div class="d-flex justify-content-between">
                                                <strong>${historial.estado_nuevo}</strong>
                                                <small class="text-muted">${historial.fecha}</small>
                                            </div>
                                            ${historial.comentario ? `<p class="mb-0 mt-1">${historial.comentario}</p>` : ''}
                                        </div>
                                    `).join('') :
                                    '<div class="timeline-item">No hay historial disponible</div>'
                                }
                            </div>
                        </div>
                    </div>

                    ${venta.observaciones ? `
                        <div class="mt-4">
                            <h6><i class="fas fa-sticky-note me-2"></i>Observaciones</h6>
                            <p class="text-muted">${venta.observaciones}</p>
                        </div>
                    ` : ''}
                `;
            }

            window.nuevaBusqueda = function() {
                $('#tracking-results').hide();
                $('#codigo').val('');
                $('#documento').val('');
                $('html, body').animate({
                    scrollTop: $('#search-form').offset().top - 100
                }, 1000);
            };
        });
    </script>
</body>
</html>