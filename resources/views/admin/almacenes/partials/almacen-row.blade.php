<tr>
    <td>{{ $index }}</td>
    <td>
        <span style="padding-left: {{ $level * 20 }}px;">
            @if ($almacen->children->count() > 0)
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
            {{ $almacen->nombre }}
        </span>
    </td>
    <td>{{ $almacen->direccion }}</td>
    <td>{{ $almacen->es_vehiculos ? 'Sí' : 'No' }}</td>
    <td>{{ $almacen->centroCosto->codigo }} - {{ $almacen->centroCosto->nombre }}</td>
    <td class="text-end">
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.almacenes.edit', $almacen) }}" class="btn btn-outline-warning" title="Editar">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <form action="{{ route('admin.almacenes.destroy', $almacen) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este almacén? Esto también afectará a sus subalmacenes.')" class="btn btn-outline-danger" title="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>

@foreach ($almacen->allChildren as $childIndex => $child)
    @include('admin.almacenes.partials.almacen-row', ['almacen' => $child, 'index' => $index . '.' . ($childIndex + 1), 'level' => $level + 1])
@endforeach