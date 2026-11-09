<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProyectoRequest;
use App\Http\Requests\UpdateProyectoRequest;
use App\Models\Proyecto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProyectoController extends Controller
{
    public function index(): View
    {
        $proyectos = Proyecto::query()
            ->withCount(['requisitos', 'arquitecturas'])
            ->orderByDesc('id')
            ->get();

        return view('proyectos.index', compact('proyectos'));
    }

    public function create(): View
    {
        return view('proyectos.create');
    }

    public function store(StoreProyectoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $requisitos = $data['requisitos'] ?? [];
        unset($data['requisitos']);

        DB::transaction(function () use ($data, $requisitos): void {
            $proyecto = Proyecto::query()->create($data);

            foreach ($requisitos as $texto) {
                $proyecto->requisitos()->create(['requisito' => $texto]);
            }
        });

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto): JsonResponse
    {
        $proyecto->load(['requisitos', 'arquitecturas']);

        return response()->json([
            'id' => $proyecto->id,
            'nombre' => $proyecto->nombre,
            'descripcion' => $proyecto->descripcion,
            'presupuesto' => $proyecto->presupuesto,
            'estado' => $proyecto->estado,
            'requisitos' => $proyecto->requisitos->pluck('requisito'),
            'arquitecturas' => $proyecto->arquitecturas->map(fn ($a) => [
                'id' => $a->id,
                'nombre' => $a->nombre,
            ]),
            'puede_eliminarse' => $proyecto->puedeEliminarse(),
            'created_at' => $proyecto->created_at?->format('d/m/Y H:i'),
        ]);
    }

    public function edit(Proyecto $proyecto): View
    {
        $proyecto->load('requisitos');

        return view('proyectos.edit', compact('proyecto'));
    }

    public function update(UpdateProyectoRequest $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $request->validated();
        $requisitos = $data['requisitos'] ?? [];
        unset($data['requisitos']);

        DB::transaction(function () use ($proyecto, $data, $requisitos): void {
            $proyecto->update($data);
            $proyecto->requisitos()->delete();

            foreach ($requisitos as $texto) {
                $proyecto->requisitos()->create(['requisito' => $texto]);
            }
        });

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto): RedirectResponse
    {
        if (! $proyecto->puedeEliminarse()) {
            return back()->with('error', 'No se puede eliminar un proyecto con arquitecturas asociadas.');
        }

        $proyecto->delete();

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }
}
