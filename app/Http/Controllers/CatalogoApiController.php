<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Support\Catalogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogoApiController extends Controller
{
    public function regiones(Request $request): JsonResponse
    {
        $proveedorId = $request->integer('proveedor_id');

        if (! isset(Catalogo::PROVEEDORES[$proveedorId])) {
            return response()->json(['regiones' => []]);
        }

        return response()->json([
            'regiones' => Catalogo::regionesDeProveedor($proveedorId),
        ]);
    }

    public function tipos(Request $request): JsonResponse
    {
        $data = $request->validate([
            'proveedor_id' => ['required', 'integer', Rule::in(Catalogo::proveedorIds())],
            'region_id' => ['required', 'integer', Rule::in(Catalogo::regionIds())],
            'solo_disponibles' => ['sometimes', 'boolean'],
        ]);

        if (! Catalogo::regionCompatible((int) $data['proveedor_id'], (int) $data['region_id'])) {
            return response()->json(['tipos' => []]);
        }

        $query = Producto::query()
            ->where('proveedor_id', $data['proveedor_id'])
            ->where('region_id', $data['region_id']);

        if ($request->boolean('solo_disponibles')) {
            $query->where('disponible', true);
        }

        $tipoIds = $query->distinct()->orderBy('tipo_id')->pluck('tipo_id');

        $tipos = [];
        foreach ($tipoIds as $tipoId) {
            $tipos[$tipoId] = Catalogo::tipo((int) $tipoId);
        }

        return response()->json(['tipos' => $tipos]);
    }

    public function productos(Request $request): JsonResponse
    {
        $data = $request->validate([
            'proveedor_id' => ['required', 'integer', Rule::in(Catalogo::proveedorIds())],
            'region_id' => ['required', 'integer', Rule::in(Catalogo::regionIds())],
            'tipo_id' => ['required', 'integer', Rule::in(Catalogo::tipoIds())],
            'solo_disponibles' => ['sometimes', 'boolean'],
        ]);

        if (! Catalogo::regionCompatible((int) $data['proveedor_id'], (int) $data['region_id'])) {
            return response()->json(['productos' => []]);
        }

        $query = Producto::query()
            ->where('proveedor_id', $data['proveedor_id'])
            ->where('region_id', $data['region_id'])
            ->where('tipo_id', $data['tipo_id'])
            ->orderBy('nombre');

        if ($request->boolean('solo_disponibles')) {
            $query->where('disponible', true);
        }

        $productos = $query->get()->map(fn (Producto $producto) => [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'disponible' => $producto->disponible,
            'protegido' => $producto->protegido,
            'proveedor' => $producto->proveedor_nombre,
            'region' => $producto->region_nombre,
            'tipo' => $producto->tipo_nombre,
            'puede_editarse' => $producto->puedeEditarse(),
            'puede_eliminarse' => $producto->puedeEliminarse(),
            'show_url' => route('productos.show', $producto),
            'edit_url' => route('productos.edit', $producto),
            'destroy_url' => route('productos.destroy', $producto),
        ]);

        return response()->json(['productos' => $productos]);
    }
}
