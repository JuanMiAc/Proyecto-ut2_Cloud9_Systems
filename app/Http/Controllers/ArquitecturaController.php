<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArquitecturaRequest;
use App\Models\Arquitectura;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArquitecturaController extends Controller
{
    public function __construct(private CarritoService $carrito) {}

    public function index(): View
    {
        $arquitecturas = Arquitectura::query()
            ->with(['proyecto', 'productos'])
            ->orderByDesc('id')
            ->get();

        return view('arquitecturas.index', compact('arquitecturas'));
    }

    public function store(StoreArquitecturaRequest $request): RedirectResponse
    {
        $validacion = $this->carrito->validarSeleccionCompleta();

        if (! $validacion['ok']) {
            return redirect()
                ->route('carrito.index')
                ->with('error', $validacion['message']);
        }

        /** @var \Illuminate\Support\Collection $productos */
        $productos = $validacion['productos'];

        DB::transaction(function () use ($request, $productos): void {
            $arquitectura = Arquitectura::query()->create([
                'nombre' => $request->validated('nombre'),
                'proyecto_id' => $request->validated('proyecto_id'),
                'estado' => true,
            ]);

            $arquitectura->productos()->attach($productos->pluck('id'));
        });

        $this->carrito->limpiar();

        return redirect()
            ->route('arquitecturas.index')
            ->with('success', 'Arquitectura creada correctamente.');
    }

    public function show(Arquitectura $arquitectura): JsonResponse
    {
        $arquitectura->load(['proyecto', 'productos']);

        return response()->json([
            'id' => $arquitectura->id,
            'nombre' => $arquitectura->nombre,
            'estado' => $arquitectura->estado,
            'proyecto' => $arquitectura->proyecto?->nombre,
            'importe_total' => $arquitectura->importe_total,
            'created_at' => $arquitectura->created_at?->format('d/m/Y H:i'),
            'productos' => $arquitectura->productos->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'tipo' => $p->tipo_nombre,
                'proveedor' => $p->proveedor_nombre,
                'region' => $p->region_nombre,
                'precio' => $p->precio,
            ]),
        ]);
    }
}
