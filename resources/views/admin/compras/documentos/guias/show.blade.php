@extends('admin.layouts.app')

@section('title', 'Detalle de Guía de Entrega')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detalle de Guía de Entrega</h5>
                    <div>
                        <a href="{{ route('admin.guias.edit', 1) }}" class="btn btn-sm btn-primary me-2" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('admin.guias.index') }}" class="btn btn-sm btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Módulo en desarrollo:</strong> La vista de detalles de guía de entrega está siendo implementada.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información General</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Número:</th>
                                    <td>GE-000001</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha:</th>
                                    <td>{{ date('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Proveedor:</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Estado:</th>
                                    <td>
                                        <span class="badge bg-secondary">En desarrollo</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información Adicional</h6>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="bg-light" style="width: 200px;">Creado por:</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Fecha de Creación:</th>
                                    <td>{{ date('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Observaciones:</th>
                                    <td>Módulo en proceso de implementación</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection