<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\CarritoService;
use App\Support\Catalogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    public function __construct(private CarritoService $carrito) {}

    public function index(Request $request): View
    {
        $proveedorId = $request->integer('proveedor_id') ?: null;
        $regionId = $request->integer('region_id') ?: null;
        $tipoId = $request->integer('tipo_id') ?: null;

        $regiones = $proveedorId ? Catalogo::regionesDeProveedor($proveedorId) : [];

        if ($proveedorId && $regionId && ! Catalogo::regionCompatible($proveedorId, $regionId)) {
            $regionId = null;
            $tipoId = null;
        }

        $productos = collect();
        if ($proveedorId && $regionId && $tipoId) {
            $productos = Producto::query()
                ->where('proveedor_id', $proveedorId)
                ->where('region_id', $regionId)
                ->where('tipo_id', $tipoId)
                ->where('disponible', true)
                ->orderBy('nombre')
                ->get();
        }

        $seleccion = $this->carrito->obtener();
        $seleccionados = $this->carrito->productosSeleccionados();

        return view('carrito.index', [
            'productos' => $productos,
            'proveedores' => Catalogo::PROVEEDORES,
            'regiones' => $regiones,
            'tipos' => Catalogo::TIPOS,
            'proveedorId' => $proveedorId,
            'regionId' => $regionId,
            'tipoId' => $tipoId,
            'filtrosCompletos' => $proveedorId && $regionId && $tipoId,
            'seleccion' => $seleccion,
            'seleccionados' => $seleccionados,
            'completo' => $this->carrito->estaCompleto(),
            'total' => $this->carrito->total(),
            'proyectos' => \App\Models\Proyecto::query()->orderBy('nombre')->get(['id', 'nombre']),
            'tiposPago' => Catalogo::TIPOS_PAGO,
        ]);
    }

    public function anadir(Request $request): RedirectResponse
    {
        $request->validate([
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
        ]);

        $producto = Producto::query()->findOrFail($request->integer('producto_id'));

        if (! $producto->disponible) {
            return back()->with('error', 'No se puede seleccionar un producto no disponible.');
        }

        $this->carrito->anadir($producto);

        return back()->with('success', 'Producto añadido al carrito.');
    }

    public function quitar(Request $request): RedirectResponse
    {
        $request->validate([
            'tipo_id' => ['required', 'integer', \Illuminate\Validation\Rule::in(Catalogo::tipoIds())],
        ]);

        $this->carrito->quitar($request->integer('tipo_id'));

        return back()->with('success', 'Producto quitado del carrito.');
    }

    public function limpiar(): JsonResponse|RedirectResponse
    {
        $this->carrito->limpiar();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }
}
