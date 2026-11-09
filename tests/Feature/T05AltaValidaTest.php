<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T05 — Alta válida que se guarda correctamente.
 */
class T05AltaValidaTest extends TestCase
{
    use RefreshDatabase;

    public function test_alta_valida_de_producto_se_persiste(): void
    {
        $payload = [
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 2,
            'nombre' => 'Producto T05 Valido',
            'descripcion' => 'Alta valida T05',
            'precio' => 25,
            'disponible' => '1',
        ];

        $response = $this->post(route('productos.store'), $payload);

        $response->assertRedirect(route('productos.index', [
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 2,
        ]));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto T05 Valido',
            'descripcion' => 'Alta valida T05',
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 2,
            'precio' => 25,
            'disponible' => 1,
            'protegido' => 0,
        ]);

        $this->assertSame(1, Producto::query()->count());
    }
}
