@extends('admin.layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Mi Perfil</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            @if ($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                     class="img-fluid rounded-circle" alt="{{ $user->name }}">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-3">{{ $user->name }}</h4>
                            <p class="text-muted mb-2">
                                <strong>Email:</strong> {{ $user->email }}
                            </p>
                            @if ($user->roles->isNotEmpty())
                                <p class="text-muted mb-3">
                                    <strong>Roles:</strong> {{ $user->roles->pluck('name')->join(', ') }}
                                </p>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar Perfil</a>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h5 class="mb-3">Información de la Cuenta</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Miembro desde</p>
                                <p class="fw-semibold">{{ $user->created_at->format('d de F de Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-2">Última actualización</p>
                                <p class="fw-semibold">{{ $user->updated_at->format('d de F de Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
