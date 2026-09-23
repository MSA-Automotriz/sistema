@extends('admin.layouts.app')

@section('title', 'Roles')

@section('header', 'Roles')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Gestión de Roles</h3>
                    <a href="{{ route('admin.usuarios.roles.create') }}" class="btn btn-primary float-end">Crear Rol</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $rol)
                                <tr>
                                    <td>{{ $rol->id }}</td>
                                    <td>{{ $rol->name }}</td>
                                    <td>{{ $rol->description ?? 'Sin descripción' }}</td>
                                    <td>
                                        <a href="{{ route('admin.usuarios.roles.show', $rol) }}" class="btn btn-info btn-sm">Ver</a>
                                        <a href="{{ route('admin.usuarios.roles.edit', $rol) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <form action="{{ route('admin.usuarios.roles.destroy', $rol) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este rol?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay roles registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection