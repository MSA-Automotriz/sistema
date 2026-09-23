@extends('admin.layouts.app')

@section('title', 'Crear Técnico')

@section('header', 'Nuevo Técnico')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Registrar Nuevo Técnico
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.mantenimiento.tecnicos.store') }}" method="POST">
                    @csrf

                    <!-- Información de Usuario -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2"></i>Información de Usuario
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Técnico -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-tools me-2"></i>Información Profesional
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                       id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="especialidad" class="form-label">Especialidad</label>
                                <input type="text" class="form-control @error('especialidad') is-invalid @enderror"
                                       id="especialidad" name="especialidad" value="{{ old('especialidad') }}"
                                       placeholder="Ej: Mecánica, Electricidad, Pintura">
                                @error('especialidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cedula_profesional" class="form-label">Cédula Profesional</label>
                                <input type="text" class="form-control @error('cedula_profesional') is-invalid @enderror"
                                       id="cedula_profesional" name="cedula_profesional" value="{{ old('cedula_profesional') }}">
                                @error('cedula_profesional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono') }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="telefono_emergencia" class="form-label">Teléfono de Emergencia</label>
                                <input type="text" class="form-control @error('telefono_emergencia') is-invalid @enderror"
                                       id="telefono_emergencia" name="telefono_emergencia" value="{{ old('telefono_emergencia') }}">
                                @error('telefono_emergencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                                <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                       id="fecha_ingreso" name="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}">
                                @error('fecha_ingreso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select @error('estado') is-invalid @enderror"
                                        id="estado" name="estado" required>
                                    <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="vacaciones" {{ old('estado') == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                                    <option value="licencia" {{ old('estado') == 'licencia' ? 'selected' : '' }}>Licencia</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="certificaciones" class="form-label">Certificaciones</label>
                                <textarea class="form-control @error('certificaciones') is-invalid @enderror"
                                          id="certificaciones" name="certificaciones" rows="3"
                                          placeholder="Lista las certificaciones del técnico">{{ old('certificaciones') }}</textarea>
                                @error('certificaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="habilidades" class="form-label">Habilidades</label>
                                <textarea class="form-control @error('habilidades') is-invalid @enderror"
                                          id="habilidades" name="habilidades" rows="3"
                                          placeholder="Lista las habilidades técnicas">{{ old('habilidades') }}</textarea>
                                @error('habilidades')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notas" class="form-label">Notas Adicionales</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror"
                                          id="notas" name="notas" rows="3"
                                          placeholder="Notas adicionales sobre el técnico">{{ old('notas') }}</textarea>
                                @error('notas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.mantenimiento.tecnicos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Guardar Técnico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
