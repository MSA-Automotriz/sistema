@extends('admin.layouts.app')

@section('title', 'Archivos de la Empresa')

@section('header', 'Archivos de la Empresa')

@section('content')
<div class="row" x-data="{ search: '' }">
    <div class="col-12">
        <div class="card border-0 shadow-sm" :class="darkMode ? 'bg-dark text-light border-secondary' : ''">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h2 class="h4 fw-bold mb-1" :class="darkMode ? 'text-light' : 'text-dark'">
                            <i class="fa fa-folder-open text-primary me-2"></i> Archivos de la Empresa
                        </h2>
                        <p class="text-muted small mb-0">Gestione y comparta los documentos y archivos corporativos de la empresa.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-exclamation-triangle me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Upload form --}}
                <div class="card mb-4 border" :class="darkMode ? 'bg-dark border-secondary' : 'bg-light border-light-subtle'">
                    <div class="card-header bg-transparent py-3" :class="darkMode ? 'border-secondary text-light' : 'border-light-subtle'">
                        <strong class="d-flex align-items-center">
                            <i class="fa fa-cloud-upload-alt text-primary me-2"></i> Subir nuevo archivo
                        </strong>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.archivos-empresa.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label fw-medium" :class="darkMode ? 'text-light' : 'text-dark'">
                                        Seleccionar archivo <small class="text-muted">(máx. 20 MB)</small> <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="archivo" class="form-control @error('archivo') is-invalid @enderror" 
                                           id="inputArchivo" required
                                           @change="if(!$refs.customName.value) { const file = $event.target.files[0]; if(file) { $refs.customName.placeholder = file.name.replace(/\.[^/.]+$/, ''); } }">
                                    @error('archivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-medium" :class="darkMode ? 'text-light' : 'text-dark'">
                                        Nombre para mostrar <small class="text-muted">(opcional)</small>
                                    </label>
                                    <input type="text" name="nombre_personalizado" x-ref="customName" 
                                           class="form-control @error('nombre_personalizado') is-invalid @enderror" 
                                           placeholder="Nombre del archivo (dejar vacío para mantener el original)">
                                    @error('nombre_personalizado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                                        <i class="fa fa-upload me-1"></i> Subir
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Filter & Total --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div class="text-muted small">
                        Total de archivos: <strong>{{ count($archivos) }}</strong>
                    </div>
                    @if(count($archivos) > 0)
                        <div style="max-width: 280px; width: 100%;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" :class="darkMode ? 'bg-dark border-secondary text-light' : ''">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" x-model="search" class="form-control" 
                                       :class="darkMode ? 'bg-dark border-secondary text-light' : ''"
                                       placeholder="Buscar archivo...">
                            </div>
                        </div>
                    @endif
                </div>

                {{-- File list --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" :class="darkMode ? 'table-dark' : ''">
                        <thead :class="darkMode ? 'table-dark' : 'table-light'">
                            <tr>
                                <th style="min-width: 250px;">Nombre del Archivo</th>
                                <th style="width: 140px;">Tamaño</th>
                                <th style="width: 180px;">Fecha de Subida</th>
                                <th class="text-end" style="width: 150px;">Acciones</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivos as $archivo)
                                @php
                                    $ext = $archivo['extension'] ?? '';
                                    $icon = 'fa-file text-secondary';
                                    if (in_array($ext, ['pdf'])) {
                                        $icon = 'fa-file-pdf text-danger';
                                    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                        $icon = 'fa-file-image text-primary';
                                    } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                        $icon = 'fa-file-excel text-success';
                                    } elseif (in_array($ext, ['doc', 'docx'])) {
                                        $icon = 'fa-file-word text-info';
                                    } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                        $icon = 'fa-file-archive text-warning';
                                    } elseif (in_array($ext, ['txt', 'rtf', 'md'])) {
                                        $icon = 'fa-file-alt text-secondary';
                                    }
                                @endphp
                                <tr x-show="!search || '{{ strtolower(addslashes($archivo['nombre'])) }}'.includes(search.toLowerCase())">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fa {{ $icon }} fs-4 me-2"></i>
                                            <div>
                                                <a href="{{ route('admin.archivos-empresa.show', $archivo['nombre']) }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer" 
                                                   class="fw-semibold text-decoration-none text-primary"
                                                   title="Click para ver archivo">
                                                    {{ $archivo['nombre'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($archivo['size'] >= 1048576)
                                            <span class="badge bg-secondary-subtle text-secondary fw-normal">
                                                {{ number_format($archivo['size'] / 1048576, 2) }} MB
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary fw-normal">
                                                {{ number_format($archivo['size'] / 1024, 1) }} KB
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        <i class="fa fa-clock me-1 opacity-75"></i>
                                        {{ \Carbon\Carbon::createFromTimestamp($archivo['fecha'])->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.archivos-empresa.show', $archivo['nombre']) }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="btn btn-outline-primary" 
                                               title="Ver archivo">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.archivos-empresa.download', $archivo['nombre']) }}" 
                                               class="btn btn-outline-success" 
                                               title="Descargar archivo">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.archivos-empresa.destroy', $archivo['nombre']) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este archivo? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Eliminar archivo">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fa fa-folder-open fs-1 text-muted mb-2 d-block opacity-50"></i>
                                        No hay archivos subidos aún.
                                    </td>
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

