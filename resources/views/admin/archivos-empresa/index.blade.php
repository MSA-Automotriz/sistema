@extends('admin.layouts.app')

@section('title', 'Archivos de la Empresa')

@section('header', 'Archivos de la Empresa')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 fw-bold mb-0" :class="darkMode ? 'text-light' : 'text-dark'">
                        Archivos de la Empresa
                    </h2>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Upload form --}}
                <div class="card mb-4" :class="darkMode ? 'bg-dark border-secondary' : ''">
                    <div class="card-header" :class="darkMode ? 'bg-dark text-light border-secondary' : ''">
                        <strong>Subir nuevo archivo</strong>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.archivos-empresa.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex gap-3 align-items-end">
                                <div class="flex-grow-1">
                                    <label class="form-label" :class="darkMode ? 'text-light' : ''">Seleccionar archivo <small class="text-muted">(máx. 10 MB)</small></label>
                                    <input type="file" name="archivo" class="form-control @error('archivo') is-invalid @enderror" required>
                                    @error('archivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload me-1"></i> Subir
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- File list --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle" :class="darkMode ? 'table-dark' : ''">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivos as $archivo)
                                <tr>
                                    <td>
                                        <a href="{{ $archivo['url'] }}" target="_blank" rel="noopener noreferrer">
                                            {{ $archivo['nombre'] }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($archivo['size'] / 1024, 1) }} KB</td>
                                    <td>{{ \Carbon\Carbon::createFromTimestamp($archivo['fecha'])->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ $archivo['url'] }}" class="btn btn-sm btn-outline-secondary" download>
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <form action="{{ route('admin.archivos-empresa.destroy', $archivo['nombre']) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este archivo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay archivos subidos aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
