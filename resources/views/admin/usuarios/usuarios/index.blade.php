@extends('admin.layouts.app')

@section('title', 'Usuario')

@section('header', 'Usuario')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Gestión de Usuarios</h3>
                    <a href="{{ route('admin.usuarios.usuarios.create') }}" class="btn btn-primary float-end">Crear Usuario</a>
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
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        {{ $usuario->roles->pluck('name')->implode(', ') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.usuarios.usuarios.show', $usuario) }}" class="btn btn-info btn-sm">Ver</a>
                                        <a href="{{ route('admin.usuarios.usuarios.edit', $usuario) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <form action="{{ route('admin.usuarios.usuarios.destroy', $usuario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
