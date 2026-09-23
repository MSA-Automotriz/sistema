<option value="{{ $almacen->id }}" {{ old('parent_id') == $almacen->id ? 'selected' : '' }}>
    {{ str_repeat('— ', $level) }}{{ $almacen->nombre }}
</option>

@foreach ($almacen->allChildren as $child)
    @include('admin.almacenes.partials.almacen-options', ['almacen' => $child, 'level' => $level + 1])
@endforeach