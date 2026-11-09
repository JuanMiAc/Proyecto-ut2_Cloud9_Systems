<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T06 — Alta inválida que se rechaza y no deja registros.
 */
class T06AltaInvalidaTest extends TestCase
{
    use RefreshDatabase;

    public function test_alta_invalida_region_incompatible_no_persiste(): void
    {
        $payload = [
            'proveedor_id' => 1, // AWS
            'region_id' => 4,    // West Europe (solo Azure)
            'tipo_id' => 1,
            'nombre' => 'Producto T06 Invalido',
            'descripcion' => 'Region mala',
            'precio' => 10,
            'disponible' => '1',
        ];

        $response = $this->from(route('productos.create'))
            ->post(route('productos.store'), $payload);

        $response->assertRedirect(route('productos.create'));
        $response->assertSessionHasErrors('region_id');

        $this->assertDatabaseMissing('productos', [
            'nombre' => 'Producto T06 Invalido',
        ]);
        $this->assertSame(0, Producto::query()->count());
    }

    public function test_alta_invalida_precio_no_entero_no_persiste(): void
    {
        $payload = [
            'proveedor_id' => 1,
            'region_id' => 1,
            'tipo_id' => 1,
            'nombre' => 'Producto precio decimal',
            'descripcion' => 'Precio malo',
            'precio' => '12.5',
            'disponible' => '1',
        ];

        $response = $this->from(route('productos.create'))
            ->post(route('productos.store'), $payload);

        $response->assertRedirect(route('productos.create'));
        $response->assertSessionHasErrors('precio');
        $this->assertSame(0, Producto::query()->count());
    }
}
