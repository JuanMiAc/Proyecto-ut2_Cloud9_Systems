@extends('layouts.app')

@section('title', 'Editar producto — Cloud 9 Systems')

@section('content')
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('productos.index', ['proveedor_id' => $producto->proveedor_id, 'region_id' => $producto->region_id, 'tipo_id' => $producto->tipo_id]) }}" class="btn btn-secondary confirm-nav">← Volver al catálogo</a>
        <a href="{{ route('productos.create') }}" class="btn btn-primary confirm-nav">+ Crear producto</a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">Editar producto</div>
        <form method="POST" action="{{ route('productos.update', $producto) }}" class="space-y-5 p-5" data-dependent-filters>
            @csrf
            @method('PUT')

            <div class="filters-bar">
                <div>
                    <label class="mb-1 block text-sm font-semibold">1. Proveedor *</label>
                    <select name="proveedor_id" class="form-select" required>
                        @foreach ($proveedores as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('proveedor_id', $producto->proveedor_id) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">2. Región *</label>
                    <select name="region_id" class="form-select" required>
                        @foreach ($regiones as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('region_id', $producto->region_id) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">3. Categoría *</label>
                    <select name="tipo_id" class="form-select" required>
                        @foreach ($tipos as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('tipo_id', $producto->tipo_id) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="form-input" maxlength="150" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Descripción *</label>
                <input type="text" name="descripcion" value="{{ old('descripcion', $producto->descripcion) }}" class="form-input" maxlength="30" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Precio (entero) *</label>
                <input type="number" name="precio" value="{{ old('precio', $producto->precio) }}" class="form-input" min="0" step="1" required>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="disponible" value="1" @checked(old('disponible', $producto->disponible))>
                Disponible
            </label>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary confirm-nav">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
