@extends('layouts.app')

@section('title', 'Proyectos — Cloud 9 Systems')

@section('content')
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="m-0 text-2xl font-bold text-slate-900">Proyectos</h1>
            <p class="m-0 text-slate-600">Gestione proyectos, requisitos y su relación con arquitecturas.</p>
        </div>
        <a href="{{ route('proyectos.create') }}" class="btn btn-primary confirm-nav">+ Crear proyecto</a>
    </div>

    <div class="card p-4">
        @forelse ($proyectos as $proyecto)
            <div class="product-row mb-3">
                <div>
                    <div class="font-semibold text-slate-900">{{ $proyecto->nombre }}</div>
                    <div class="text-sm text-slate-500">
                        ID {{ $proyecto->id }} · Presupuesto {{ $proyecto->presupuesto }} € ·
                        {{ $proyecto->estado ? 'Activo' : 'Inactivo' }} ·
                        {{ $proyecto->requisitos_count }} requisitos ·
                        {{ $proyecto->arquitecturas_count }} arquitecturas
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="btn btn-ghost"
                        data-detail-url="{{ route('proyectos.show', $proyecto) }}"
                        data-detail-type="proyecto"
                        data-detail-title="Detalle del proyecto"
                    >Ver detalle</button>
                    <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-secondary confirm-nav">Editar</a>
                    @if ($proyecto->puedeEliminarse())
                        <form method="POST" action="{{ route('proyectos.destroy', $proyecto) }}" onsubmit="return confirm('¿Eliminar este proyecto y sus requisitos?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="info-box">Todavía no hay proyectos. Cree el primero para asociar arquitecturas opcionales.</div>
        @endforelse
    </div>
@endsection
