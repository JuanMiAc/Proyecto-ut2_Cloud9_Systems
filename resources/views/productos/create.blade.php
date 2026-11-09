@extends('layouts.app')

@section('title', 'Crear producto — Cloud 9 Systems')

@section('content')
    <div class="mb-4">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary confirm-nav">← Volver al catálogo</a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">Crear producto</div>
        <form method="POST" action="{{ route('productos.store') }}" class="space-y-5 p-5" data-dependent-filters>
            @csrf

            <div class="filters-bar">
                <div>
                    <label class="mb-1 block text-sm font-semibold">1. Proveedor *</label>
                    <select name="proveedor_id" class="form-select" required>
                        <option value="">Seleccione proveedor</option>
                        @foreach ($proveedores as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('proveedor_id', $proveedorId) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">2. Región *</label>
                    <select name="region_id" class="form-select" required>
                        <option value="">Seleccione región</option>
                        @foreach ($regiones as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('region_id', $regionId) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">3. Categoría *</label>
                    <select name="tipo_id" class="form-select" required>
                        <option value="">Seleccione categoría</option>
                        @foreach ($tipos as $id => $nombre)
                            <option value="{{ $id }}" @selected(old('tipo_id', $tipoId) == $id)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-input" maxlength="150" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Descripción *</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="form-input" maxlength="30" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Precio (entero) *</label>
                <input type="number" name="precio" value="{{ old('precio', 0) }}" class="form-input" min="0" step="1" required>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="disponible" value="1" @checked(old('disponible', true))>
                Disponible
            </label>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">Guardar producto</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary confirm-nav">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
