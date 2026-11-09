@extends('layouts.app')

@section('title', 'Presupuestos — Cloud 9 Systems')

@section('content')
    <div class="mb-5">
        <h1 class="m-0 text-2xl font-bold text-slate-900">Presupuestos</h1>
        <p class="m-0 text-slate-600">Listado de presupuestos emitidos desde el carrito. Solo consulta.</p>
    </div>

    <div class="card p-4">
        @forelse ($presupuestos as $presupuesto)
            <div class="product-row mb-3">
                <div>
                    <div class="font-semibold text-slate-900">{{ $presupuesto->nombre }}</div>
                    <div class="text-sm text-slate-500">
                        P{{ str_pad((string) $presupuesto->id, 3, '0', STR_PAD_LEFT) }}
                        · {{ $presupuesto->created_at?->format('d/m/Y') }}
                        · {{ $presupuesto->tipo_pago }}
                    </div>
                    <div class="mt-1 text-lg font-bold">{{ $presupuesto->importe }} €</div>
                </div>
                <button
                    type="button"
                    class="btn btn-ghost"
                    data-detail-url="{{ route('presupuestos.show', $presupuesto) }}"
                    data-detail-type="presupuesto"
                    data-detail-title="Detalle del presupuesto"
                >Ver detalle</button>
            </div>
        @empty
            <div class="info-box">No hay presupuestos todavía. Créelos desde el carrito cuando la selección esté completa.</div>
        @endforelse
    </div>
@endsection
