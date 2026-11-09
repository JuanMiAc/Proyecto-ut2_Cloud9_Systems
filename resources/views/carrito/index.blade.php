@extends('layouts.app')

@section('title', 'Carrito — Cloud 9 Systems')

@section('content')
    <div class="mb-5">
        <h1 class="m-0 text-2xl font-bold text-slate-900">Carrito / Configurador</h1>
        <p class="m-0 text-slate-600">Seleccione exactamente un producto de cada categoría. Filtros: proveedor → región → categoría.</p>
    </div>

    <div class="grid gap-5" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="space-y-4">
            <div class="card p-4">
                <form class="filters-bar" data-ajax-filters="carrito" data-results-target="#carrito-resultados" data-dependent-filters>
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

            <div class="card p-4">
                <h2 class="mt-0 mb-3 text-lg font-semibold">Productos disponibles</h2>
                <div id="carrito-resultados">
                    <div class="info-box">Complete los tres filtros para listar productos disponibles.</div>
                </div>
            </div>
        </div>

        <aside class="card p-4 h-fit sticky top-24">
            <h2 class="mt-0 mb-3 text-lg font-semibold">Selección actual</h2>
            <div class="mb-4 flex flex-col gap-2">
                @foreach ($tipos as $tipoId => $tipoNombre)
                    @php
                        $productoId = $seleccion[$tipoId] ?? null;
                        $producto = $productoId ? $seleccionados->get($productoId) : null;
                    @endphp
                    <div class="cart-slot">
                        <div class="mb-1 flex items-center justify-between gap-2">
                            <strong>{{ $tipoNombre }}</strong>
                            @if ($producto)
                                <span class="badge badge-ok">Completado</span>
                            @else
                                <span class="badge badge-warn">Pendiente</span>
                            @endif
                        </div>
                        @if ($producto)
                            <div class="text-sm text-slate-700">{{ $producto->nombre }} · {{ $producto->precio }} €</div>
                            <form method="POST" action="{{ route('carrito.quitar') }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="tipo_id" value="{{ $tipoId }}">
                                <button type="submit" class="btn btn-danger">Quitar</button>
                            </form>
                        @else
                            <div class="text-sm text-slate-500">Seleccione un producto.</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mb-4 text-lg font-bold">Total: {{ $total }} €</div>

            @if ($completo)
                <div class="space-y-4">
                    <form method="POST" action="{{ route('presupuestos.store') }}" class="space-y-2 rounded-lg border border-slate-200 p-3">
                        @csrf
                        <h3 class="m-0 text-base font-semibold">Crear presupuesto</h3>
                        <input type="text" name="nombre" class="form-input" maxlength="150" placeholder="Nombre del presupuesto" required>
                        <select name="tipo_pago" class="form-select" required>
                            <option value="">Tipo de pago</option>
                            @foreach ($tiposPago as $tipoPago)
                                <option value="{{ $tipoPago }}">{{ $tipoPago }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary w-full">Crear presupuesto ({{ $total }} €)</button>
                    </form>

                    <form method="POST" action="{{ route('arquitecturas.store') }}" class="space-y-2 rounded-lg border border-slate-200 p-3">
                        @csrf
                        <h3 class="m-0 text-base font-semibold">Crear arquitectura</h3>
                        <input type="text" name="nombre" class="form-input" maxlength="20" placeholder="Nombre (máx. 20)" required>
                        <select name="proyecto_id" class="form-select">
                            <option value="">Sin proyecto (independiente)</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success w-full">Crear arquitectura</button>
                    </form>
                </div>
            @else
                <div class="info-box">Complete las seis categorías para crear un presupuesto o una arquitectura.</div>
            @endif
        </aside>
    </div>
@endsection
