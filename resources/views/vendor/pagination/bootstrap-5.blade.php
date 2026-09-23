@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-muted small">
            Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
            <span class="mx-2">|</span>
            {{ $paginator->total() }} resultados
        </div>

        <div class="d-flex gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button type="button" class="btn btn-sm btn-light border" disabled style="opacity: 0.5;">
                    ← Anterior
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-light border">
                    ← Anterior
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-light border">
                    Siguiente →
                </a>
            @else
                <button type="button" class="btn btn-sm btn-light border" disabled style="opacity: 0.5;">
                    Siguiente →
                </button>
            @endif
        </div>
    </div>
@endif
