@extends('admin.layouts.app')

@section('title', 'Nuevo Rol')

@section('header', 'Nuevo Rol')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Editar Rol: {{ $rol->name }}</h3>
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

                    <form action="{{ route('admin.usuarios.roles.update', $rol) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Rol</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $rol->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (opcional)</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $rol->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Permisos</strong></label>
                            <p class="text-muted small mb-3">Selecciona los módulos a los que este rol tendrá acceso:</p>

                            @forelse ($permissions as $module => $perms)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input module-checkbox" type="checkbox" id="module-{{ $module }}" data-module="{{ $module }}" onchange="toggleModule('{{ $module }}')" {{ $perms->every(fn($p) => $rol->permissions->contains($p->id)) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="module-{{ $module }}">
                                                {{ ucfirst($module) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($perms as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm-{{ $permission->id }}" data-module="{{ $module }}" {{ $rol->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                            {{ $permission->display_name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No hay permisos disponibles</p>
                            @endforelse

                            @error('permissions') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Actualizar Rol</button>
                            <a href="{{ route('admin.usuarios.roles.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>

                    <script>
                        function toggleModule(module) {
                            const checkbox = document.getElementById(`module-${module}`);
                            const permCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                            permCheckboxes.forEach(cb => cb.checked = checkbox.checked);
                        }

                        // Actualizar checkbox del módulo si todos los permisos están seleccionados
                        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const module = this.dataset.module;
                                const moduleCheckbox = document.getElementById(`module-${module}`);
                                const modulePerms = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                                const allChecked = Array.from(modulePerms).every(cb => cb.checked);
                                moduleCheckbox.checked = allChecked;
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection