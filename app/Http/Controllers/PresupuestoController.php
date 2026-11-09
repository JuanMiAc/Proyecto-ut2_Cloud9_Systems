<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresupuestoRequest;
use App\Models\Presupuesto;
use App\Services\CarritoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PresupuestoController extends Controller
{
    public function __construct(private CarritoService $carrito) {}

    public function index(): View
    {
        $presupuestos = Presupuesto::query()
            ->with('productos')
            ->orderByDesc('id')
            ->get();

        return view('presupuestos.index', compact('presupuestos'));
    }

    public function store(StorePresupuestoRequest $request): RedirectResponse
    {
        $validacion = $this->carrito->validarSeleccionCompleta();

        if (! $validacion['ok']) {
            return redirect()
                ->route('carrito.index')
                ->with('error', $validacion['message']);
        }

        /** @var \Illuminate\Support\Collection $productos */
        $productos = $validacion['productos'];
        $importe = (int) $productos->sum('precio');

        DB::transaction(function () use ($request, $productos, $importe): void {
            $presupuesto = Presupuesto::query()->create([
                'nombre' => $request->validated('nombre'),
                'tipo_pago' => $request->validated('tipo_pago'),
                'importe' => $importe,
            ]);

            $presupuesto->productos()->attach($productos->pluck('id'));
        });

        $this->carrito->limpiar();

        return redirect()
            ->route('presupuestos.index')
            ->with('success', 'Presupuesto creado correctamente.');
    }

    public function show(Presupuesto $presupuesto): JsonResponse
    {
        $presupuesto->load('productos');

        return response()->json([
            'id' => $presupuesto->id,
            'nombre' => $presupuesto->nombre,
            'importe' => $presupuesto->importe,
            'tipo_pago' => $presupuesto->tipo_pago,
            'created_at' => $presupuesto->created_at?->format('d/m/Y H:i'),
            'productos' => $presupuesto->productos->map(fn ($p) => [
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
