<?php

namespace Tests\Support;

use App\Models\Producto;
use App\Services\CarritoService;
use App\Support\Catalogo;
use Illuminate\Support\Collection;

trait PreparesCloud9Data
{
    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function crearProducto(array $overrides = []): Producto
    {
        return Producto::query()->create(array_merge([
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 1,
            'nombre' => 'Producto test',
            'descripcion' => 'Desc test',
            'precio' => 10,
            'disponible' => true,
            'protegido' => false,
        ], $overrides));
    }

    /**
     * Seis productos disponibles, uno por categoría, para llenar el carrito.
     *
     * @param  list<int>  $precios
     * @return Collection<int, Producto>
     */
    protected function crearSeisProductosCarrito(array $precios = [10, 20, 30, 40, 50, 60]): Collection
    {
        $productos = collect();

        foreach (Catalogo::tipoIds() as $indice => $tipoId) {
            $productos->put($tipoId, $this->crearProducto([
                'tipo_id' => $tipoId,
                'nombre' => 'Prod tipo '.$tipoId,
                'descripcion' => 'Cat '.$tipoId,
                'precio' => $precios[$indice] ?? 10,
            ]));
        }

        return $productos;
    }

    /**
     * @param  Collection<int, Producto>  $productosPorTipo
     */
    protected function llenarCarrito(Collection $productosPorTipo): void
    {
        $carrito = [];

        foreach ($productosPorTipo as $tipoId => $producto) {
            $carrito[$tipoId] = $producto->id;
        }

        session([CarritoService::SESSION_KEY => $carrito]);
    }
}
