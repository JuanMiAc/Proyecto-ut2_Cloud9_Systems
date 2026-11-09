<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PreparesCloud9Data;
use Tests\TestCase;

/**
 * T03 — Una regla real que bloquea una operación.
 */
class T03ReglaBloqueoTest extends TestCase
{
    use PreparesCloud9Data;
    use RefreshDatabase;

    public function test_no_se_puede_eliminar_producto_protegido(): void
    {
        $producto = $this->crearProducto([
            'nombre' => 'Producto protegido T03',
            'protegido' => true,
            'disponible' => true,
        ]);

        $response = $this->from(route('productos.index', [
            'proveedor_id' => $producto->proveedor_id,
            'region_id' => $producto->region_id,
            'tipo_id' => $producto->tipo_id,
        ]))->delete(route('productos.destroy', $producto));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Producto protegido T03',
            'protegido' => 1,
        ]);
    }

    public function test_no_se_puede_editar_producto_protegido(): void
    {
        $producto = $this->crearProducto([
            'nombre' => 'Producto protegido edit T03',
            'protegido' => true,
        ]);

        $response = $this->get(route('productos.edit', $producto));

        $response->assertRedirect(route('productos.index'));
        $response->assertSessionHas('error');
    }
}
