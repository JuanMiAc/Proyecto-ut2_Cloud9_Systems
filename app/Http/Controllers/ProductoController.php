<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use App\Support\Catalogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
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
                ->orderBy('nombre')
                ->get();
        }

        return view('productos.index', [
            'productos' => $productos,
            'proveedores' => Catalogo::PROVEEDORES,
            'regiones' => $regiones,
            'tipos' => Catalogo::TIPOS,
            'proveedorId' => $proveedorId,
            'regionId' => $regionId,
            'tipoId' => $tipoId,
            'filtrosCompletos' => $proveedorId && $regionId && $tipoId,
        ]);
    }

    public function create(Request $request): View
    {
        $proveedorId = $request->integer('proveedor_id') ?: null;
        $regionId = $request->integer('region_id') ?: null;
        $tipoId = $request->integer('tipo_id') ?: null;
        $regiones = $proveedorId ? Catalogo::regionesDeProveedor($proveedorId) : [];

        if ($proveedorId && $regionId && ! Catalogo::regionCompatible($proveedorId, $regionId)) {
            $regionId = null;
        }

        return view('productos.create', [
            'proveedores' => Catalogo::PROVEEDORES,
            'regiones' => $regiones,
            'tipos' => Catalogo::TIPOS,
            'proveedorId' => $proveedorId,
            'regionId' => $regionId,
            'tipoId' => $tipoId,
        ]);
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['protegido'] = false;
        $data['disponible'] = $data['disponible'] ?? true;

        Producto::query()->create($data);

        return redirect()
            ->route('productos.index', [
                'proveedor_id' => $data['proveedor_id'],
                'region_id' => $data['region_id'],
                'tipo_id' => $data['tipo_id'],
            ])
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto): JsonResponse
    {
        return response()->json([
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'disponible' => $producto->disponible,
            'protegido' => $producto->protegido,
            'proveedor' => $producto->proveedor_nombre,
            'region' => $producto->region_nombre,
            'tipo' => $producto->tipo_nombre,
            'asociado' => $producto->estaAsociado(),
        ]);
    }

    public function edit(Producto $producto): View|RedirectResponse
    {
        if (! $producto->puedeEditarse()) {
            return redirect()
                ->route('productos.index')
                ->with('error', 'Este producto no se puede editar.');
        }

        $regiones = Catalogo::regionesDeProveedor($producto->proveedor_id);

        return view('productos.edit', [
            'producto' => $producto,
            'proveedores' => Catalogo::PROVEEDORES,
            'regiones' => $regiones,
            'tipos' => Catalogo::TIPOS,
        ]);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()
            ->route('productos.index', [
                'proveedor_id' => $producto->proveedor_id,
                'region_id' => $producto->region_id,
                'tipo_id' => $producto->tipo_id,
            ])
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        if (! $producto->puedeEliminarse()) {
            return back()->with('error', 'Este producto no se puede eliminar.');
        }

        $filtros = [
            'proveedor_id' => $producto->proveedor_id,
            'region_id' => $producto->region_id,
            'tipo_id' => $producto->tipo_id,
        ];

        $producto->delete();

        return redirect()
            ->route('productos.index', $filtros)
            ->with('success', 'Producto eliminado correctamente.');
    }
}
