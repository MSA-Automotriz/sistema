@extends('admin.layouts.app')

@section('title', 'Establecimientos')

@section('header', 'Establecimientos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 fw-bold mb-0" :class="darkMode ? 'text-light' : 'text-dark'">
                        Gestión de Establecimientos
                    </h2>
                    <a href="{{ route('admin.establecimientos.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i> Nuevo Establecimiento
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($establecimientos as $establecimiento)
                                <tr>
                                    <td>{{ $establecimiento->id }}</td>
                                    <td>{{ $establecimiento->nombre }}</td>
                                    <td>{{ $establecimiento->direccion }}</td>
                                    <td>{{ $establecimiento->telefono }}</td>
                                    <td>
                                        <a href="{{ route('admin.establecimientos.edit', $establecimiento) }}" class="btn btn-sm btn-info me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.establecimientos.destroy', $establecimiento) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este establecimiento?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay establecimientos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $establecimientos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection