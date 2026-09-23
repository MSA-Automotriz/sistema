@extends('admin.layouts.app')

@section('title', 'Almacenes')

@section('header', 'Registro de Almacenes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            Total de Almacenes: {{ $totalAlmacenes }}
                        </h2>
                        <p class="text-muted small mb-0">Gestiona los almacenes y subalmacenes desde aquí</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.almacenes.create') }}" class="btn btn-primary d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Almacén
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
                        <thead class="table-light" :class="darkMode ? 'table-dark' : ''">
                            <tr>
                                <th scope="col" class="text-uppercase small">#</th>
                                <th scope="col" class="text-uppercase small">Nombre</th>
                                <th scope="col" class="text-uppercase small">Dirección</th>
                                <th scope="col" class="text-uppercase small">Vehículos</th>
                                <th scope="col" class="text-uppercase small">Centro de Costo</th>
                                <th scope="col" class="text-uppercase small text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($almacenes as $index => $almacen)
                                @include('admin.almacenes.partials.almacen-row', ['almacen' => $almacen, 'index' => $almacenes->firstItem() + $index, 'level' => 0])
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="height: 3rem; width: 3rem;" class="text-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-muted mb-2">No hay almacenes registrados</p>
                                            <a href="{{ route('admin.almacenes.create') }}" class="btn btn-sm btn-link text-decoration-none">
                                                Agregar un nuevo almacén
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $almacenes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection