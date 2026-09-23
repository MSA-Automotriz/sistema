@extends('admin.layouts.app')

@section('title', 'Editar Usuario')

@section('header', 'Editar Usuario')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Editar Usuario: {{ $usuario->name }}</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.usuarios.usuarios.update', $usuario) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="roles" class="form-label">Roles</label>
                            <select name="roles[]" id="roles" class="form-control" multiple>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id }}" {{ $usuario->roles->contains($rol->id) ? 'selected' : '' }}>{{ $rol->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Mantén presionado Ctrl para seleccionar múltiples roles.</small>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                            <a href="{{ route('admin.usuarios.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection