@extends('layouts.app')

@section('title', 'Arquitecturas — Cloud 9 Systems')

@section('content')
    <div class="mb-5">
        <h1 class="m-0 text-2xl font-bold text-slate-900">Arquitecturas</h1>
        <p class="m-0 text-slate-600">Registros permanentes e inmutables creados desde el carrito.</p>
    </div>

    <div class="card p-4">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Proyecto</th>
                        <th>Estado</th>
                        <th>Importe total</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($arquitecturas as $arquitectura)
                        <tr>
                            <td>{{ $arquitectura->id }}</td>
                            <td>{{ $arquitectura->nombre }}</td>
                            <td>{{ $arquitectura->proyecto?->nombre ?? '—' }}</td>
                            <td>
                                @if ($arquitectura->estado)
                                    <span class="badge badge-ok">Activa</span>
                                @else
                                    <span class="badge badge-muted">Inactiva</span>
                                @endif
                            </td>
                            <td>{{ $arquitectura->importe_total }} €</td>
                            <td>{{ $arquitectura->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-ghost"
                                    data-detail-url="{{ route('arquitecturas.show', $arquitectura) }}"
                                    data-detail-type="arquitectura"
                                    data-detail-title="Detalle de arquitectura"
                                >Ver detalle</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="info-box">No hay arquitecturas. Complételas desde el carrito.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="info-box mt-4">
            Las arquitecturas pueden asociarse a un proyecto o ser independientes. No se editan ni eliminan.
        </div>
    </div>
@endsection
