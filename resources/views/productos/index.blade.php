@extends('layouts.app')

@section('title', 'Catálogo — Cloud 9 Systems')

@section('content')
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="m-0 text-2xl font-bold text-slate-900">Catálogo de productos</h1>
            <p class="m-0 text-slate-600">Seleccione proveedor, región y categoría para listar productos.</p>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-primary confirm-nav">+ Crear producto</a>
    </div>

    <div class="card mb-5 p-4">
        <form class="filters-bar" data-ajax-filters="catalogo" data-results-target="#filtros-resultados" data-dependent-filters>
            <div>
                <label class="mb-1 block text-sm font-semibold">1. Proveedor</label>
                <select name="proveedor_id" class="form-select">
                    <option value="">Seleccione proveedor</option>
                    @foreach ($proveedores as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">2. Región</label>
                <select name="region_id" class="form-select" disabled>
                    <option value="">Seleccione región</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">3. Categoría</label>
                <select name="tipo_id" class="form-select" disabled>
                    <option value="">Seleccione categoría</option>
                </select>
            </div>
        </form>
    </div>

    <div id="filtros-resultados">
        <div class="info-box">Complete proveedor, región compatible y categoría para ver los productos.</div>
    </div>
@endsection
