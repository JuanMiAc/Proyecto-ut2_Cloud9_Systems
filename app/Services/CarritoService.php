<?php

namespace App\Services;

use App\Models\Producto;
use App\Support\Catalogo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CarritoService
{
    public const SESSION_KEY = 'carrito';

    public function obtener(): array
    {
        $carrito = Session::get(self::SESSION_KEY, []);

        $resultado = [];
        foreach (Catalogo::tipoIds() as $tipoId) {
            $resultado[$tipoId] = $carrito[$tipoId] ?? null;
        }

        return $resultado;
    }

    public function productosSeleccionados(): Collection
    {
        $ids = array_filter($this->obtener());

        if ($ids === []) {
            return collect();
        }

        return Producto::query()->whereIn('id', $ids)->get()->keyBy('id');
    }

    public function anadir(Producto $producto): void
    {
        $carrito = $this->obtener();
        $carrito[$producto->tipo_id] = $producto->id;
        Session::put(self::SESSION_KEY, $carrito);
    }

    public function quitar(int $tipoId): void
    {
        $carrito = $this->obtener();
        $carrito[$tipoId] = null;
        Session::put(self::SESSION_KEY, $carrito);
    }

    public function limpiar(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function estaCompleto(): bool
    {
        foreach ($this->obtener() as $productoId) {
            if ($productoId === null) {
                return false;
            }
        }

        return true;
    }

    public function idsSeleccionados(): array
    {
        return array_values(array_filter($this->obtener()));
    }

    public function total(): int
    {
        return (int) $this->productosSeleccionados()->sum('precio');
    }

    /**
     * @return array{ok: bool, productos?: Collection, message?: string}
     */
    public function validarSeleccionCompleta(): array
    {
        if (! $this->estaCompleto()) {
            return ['ok' => false, 'message' => 'Debe seleccionar exactamente un producto de cada categoría.'];
        }

        $ids = $this->idsSeleccionados();
        $productos = Producto::query()->whereIn('id', $ids)->get();

        if ($productos->count() !== 6) {
            return ['ok' => false, 'message' => 'Hay productos del carrito que ya no existen.'];
        }

        if ($productos->contains(fn (Producto $p) => ! $p->disponible)) {
            return ['ok' => false, 'message' => 'Hay productos no disponibles en el carrito.'];
        }

        $tipos = $productos->pluck('tipo_id')->unique()->sort()->values();
        if ($tipos->count() !== 6 || $tipos->all() !== Catalogo::tipoIds()) {
            return ['ok' => false, 'message' => 'El carrito debe contener un producto de cada categoría.'];
        }

        foreach ($this->obtener() as $tipoId => $productoId) {
            $producto = $productos->firstWhere('id', $productoId);
            if (! $producto || $producto->tipo_id !== $tipoId) {
                return ['ok' => false, 'message' => 'La selección del carrito no es coherente con las categorías.'];
            }
        }

        return ['ok' => true, 'productos' => $productos];
    }
}
